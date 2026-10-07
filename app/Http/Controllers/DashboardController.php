<?php
namespace App\Http\Controllers;

use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Departemen;
use Modules\Master\Entities\Karyawan;
use Modules\Master\Entities\Position;
use Modules\Task\Entities\Task;
use Modules\Task\Entities\TaskAssignee;
use Modules\Task\Entities\TaskCategory;
use Modules\Task\Entities\TaskPriority;
use Modules\Task\Entities\TaskSubmission;
use Modules\Task\Services\TaskRecurringService;
use Modules\Task\Services\TaskStatusService;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    /**
     * Display employee performance monitoring dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Auto-generate recurring tasks if service exists
        try {
            if (class_exists(TaskRecurringService::class)) {
                TaskRecurringService::generateAllDueRecurringTasks();
            }
        } catch (\Throwable $e) {
            \Log::warning('Dashboard recurring generation warning: ' . $e->getMessage());
        }

        if ($request->ajax() && ($request->has('draw') || $request->filled('recent_submissions_datatable') || $request->filled('employee_performance_datatable'))) {
            if ($request->get('table') === 'recent_submissions' || $request->filled('recent_submissions_datatable')) {
                return $this->getRecentSubmissionsData($request);
            }
            return $this->getEmployeePerformanceData($request);
        }

        $user = Auth::user();
        $isSuperAdmin = ((int)(session('role')['id_role'] ?? 0) === 1);

        // Resolve company scope
        $selectedCompanyId = $request->filled('company_id') ? (int)$request->get('company_id') : null;
        if (!$isSuperAdmin && !$selectedCompanyId) {
            if ($user && $user->karyawan_id) {
                $emp = Karyawan::find($user->karyawan_id);
                $selectedCompanyId = $emp?->company_id;
            }
        }

        $selectedDepartmentId = $request->filled('department_id') ? (int)$request->get('department_id') : null;
        $selectedCategoryId   = $request->filled('category_id') ? (int)$request->get('category_id') : null;
        $dateFrom             = $request->filled('date_from') ? $request->get('date_from') : null;
        $dateTo               = $request->filled('date_to') ? $request->get('date_to') : null;

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        // Department list for filter
        $departmentsQuery = Departemen::where('is_active', true);
        if ($selectedCompanyId) {
            $departmentsQuery->where(function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId)->orWhereNull('company_id');
            });
        }
        $departments = $departmentsQuery->orderBy('name')->get();

        // Category list for filter
        $categoriesQuery = TaskCategory::where('is_active', true);
        if ($selectedCompanyId) {
            $categoriesQuery->where(function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId)->orWhereNull('company_id');
            });
        }
        $categories = $categoriesQuery->orderBy('name')->get();

        // Employees query
        $karyawanQuery = Karyawan::with(['department', 'position', 'company'])->where('is_active', true);
        if ($selectedCompanyId) {
            $karyawanQuery->where('company_id', $selectedCompanyId);
        }
        if ($selectedDepartmentId) {
            $karyawanQuery->where('department_id', $selectedDepartmentId);
        }
        $employees = $karyawanQuery->get();
        $totalEmployees = $employees->count();

        // Base task query for master tasks (do not filter out recurring tasks via is_template)
        $taskQuery = Task::whereNull('parent_task_id');
        if ($selectedCompanyId) {
            $taskQuery->where('company_id', $selectedCompanyId);
        }
        if ($selectedCategoryId) {
            $taskQuery->where('category_id', $selectedCategoryId);
        }

        // Apply department and plotting date filters on tasks via assignees
        if ($selectedDepartmentId || $dateFrom || $dateTo) {
            $taskQuery->whereHas('assignees', function ($q) use ($selectedDepartmentId, $dateFrom, $dateTo) {
                if ($selectedDepartmentId) {
                    $q->whereHas('employee', function ($sub) use ($selectedDepartmentId) {
                        $sub->where('department_id', $selectedDepartmentId);
                    });
                }
                if ($dateFrom) {
                    $q->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '>=', $dateFrom);
                }
                if ($dateTo) {
                    $q->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '<=', $dateTo);
                }
            });
        }

        // Preload actionable task_assignees to avoid N+1 queries with filters applied
        $assigneeQuery = TaskAssignee::actionable()
            ->with(['task'])
            ->whereHas('task', function ($q) use ($selectedCompanyId, $selectedCategoryId) {
                if ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                }
                if ($selectedCategoryId) {
                    $q->where('category_id', $selectedCategoryId);
                }
            });

        if ($selectedDepartmentId) {
            $assigneeQuery->whereHas('employee', function ($q) use ($selectedDepartmentId) {
                $q->where('department_id', $selectedDepartmentId);
            });
        }
        if ($dateFrom) {
            $assigneeQuery->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '>=', $dateFrom);
        }
        if ($dateTo) {
            $assigneeQuery->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '<=', $dateTo);
        }

        // Summary KPI Counts (Aligned with employee performance assignments & master tasks)
        $assigneeTotalCount = (clone $assigneeQuery)->count();
        if ($assigneeTotalCount > 0) {
            $totalTasks      = $assigneeTotalCount;
            $completedTasks  = (clone $assigneeQuery)->where('status', TaskStatusService::STATUS_COMPLETED)->count();
            $inProgressTasks = (clone $assigneeQuery)->where('status', TaskStatusService::STATUS_IN_PROGRESS)->count();
            $assignedTasks   = (clone $assigneeQuery)->where(function ($q) {
                $q->where('status', TaskStatusService::STATUS_ASSIGNED)
                  ->orWhereNull('status');
            })->count();
            $overdueTasks    = (clone $assigneeQuery)->overdue()->count();
        } else {
            $totalTasks      = (clone $taskQuery)->count();
            $completedTasks  = (clone $taskQuery)->where('status', TaskStatusService::STATUS_COMPLETED)->count();
            $inProgressTasks = (clone $taskQuery)->where('status', TaskStatusService::STATUS_IN_PROGRESS)->count();
            $assignedTasks   = (clone $taskQuery)->where('status', TaskStatusService::STATUS_ASSIGNED)->count();
            $overdueTasks    = (clone $taskQuery)->overdue()->count();
        }

        $today = Carbon::today()->toDateString();
        $todayTasks = (clone $assigneeQuery)->where(function ($q) use ($today) {
            $q->whereDate('due_date', $today)
              ->orWhereDate('start_date', $today)
              ->orWhereDate(DB::raw('COALESCE(assigned_at, created_at)'), $today);
        })->count();
        if ($todayTasks === 0) {
            $todayTasks = (clone $taskQuery)->where(function ($q) use ($today) {
                $q->whereDate('due_date', $today)
                  ->orWhereDate('start_date', $today);
            })->count();
        }

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        $allAssignees = $assigneeQuery->get()->groupBy('employee_id');

        $employeePerformance = $employees->map(function ($emp) use ($allAssignees) {
            $assigned = $allAssignees->get($emp->id, collect());
            $totalAssigned = $assigned->count();
            $completed = $assigned->where('status', TaskStatusService::STATUS_COMPLETED)->count();
            $inProgress = $assigned->where('status', TaskStatusService::STATUS_IN_PROGRESS)->count();
            $overdue = $assigned->filter(function ($item) {
                return $item->is_overdue;
            })->count();

            $rate = $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100) : 0;
            $avgProgress = $totalAssigned > 0 ? round($assigned->avg('progress') ?: 0) : 0;

            // Status label badge
            if ($totalAssigned === 0) {
                $perfStatus = 'Belum Ada Tugas';
                $perfClass = 'bg-gray-100 text-gray-600 border-gray-200';
            } elseif ($rate >= 80) {
                $perfStatus = 'Sangat Baik';
                $perfClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif ($rate >= 50) {
                $perfStatus = 'Cukup Baik';
                $perfClass = 'bg-sky-50 text-sky-700 border-sky-200';
            } else {
                $perfStatus = 'Perlu Evaluasi';
                $perfClass = 'bg-rose-50 text-rose-700 border-rose-200';
            }

            return (object)[
                'id'                 => $emp->id,
                'name'               => $emp->display_name,
                'avatar'             => $emp->avatar_url,
                'department'         => $emp->department?->name ?? 'Umum',
                'position'           => $emp->position?->name ?? '-',
                'total_tasks'        => $totalAssigned,
                'completed_tasks'    => $completed,
                'in_progress_tasks'  => $inProgress,
                'overdue_tasks'      => $overdue,
                'completion_rate'    => $rate,
                'avg_progress'       => $avgProgress,
                'performance_status' => $perfStatus,
                'performance_class'  => $perfClass,
            ];
        })->sortByDesc('completion_rate')->values();

        // Count of active employees with tasks
        $activeAssigneesCount = $employeePerformance->where('total_tasks', '>', 0)->count();

        // Dynamic Trend Data for ApexCharts (follows date filter if provided, otherwise default to 14 days)
        if ($dateFrom && $dateTo) {
            $startCarbon = Carbon::parse($dateFrom)->startOfDay();
            $endCarbon   = Carbon::parse($dateTo)->endOfDay();
            if ($startCarbon->diffInDays($endCarbon) > 60) {
                $startCarbon = $endCarbon->copy()->subDays(59);
            }
            $startDate = $startCarbon;
            $endDate   = $endCarbon;
            $trendDays = $startDate->diffInDays($endDate) + 1;
        } else {
            $trendDays = 14;
            $startDate = Carbon::today()->subDays($trendDays - 1)->startOfDay();
            $endDate   = Carbon::today()->endOfDay();
        }

        $trendDates           = [];
        $trendAssignedSeries  = [];
        $trendCompletedSeries = [];

        // Group plotted assignments by date
        $assignedPlotMap = (clone $assigneeQuery)
            ->whereBetween(DB::raw('DATE(COALESCE(assigned_at, created_at))'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw("DATE(COALESCE(assigned_at, created_at)) as tgl, count(*) as total")
            ->groupBy('tgl')
            ->pluck('total', 'tgl')
            ->toArray();

        // Group completed assignments by date
        $completedPlotMap = (clone $assigneeQuery)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw("DATE(completed_at) as tgl, count(*) as total")
            ->groupBy('tgl')
            ->pluck('total', 'tgl')
            ->toArray();

        for ($i = 0; $i < $trendDays; $i++) {
            $cur = $startDate->copy()->addDays($i);
            $key = $cur->toDateString();
            $trendDates[]           = $cur->translatedFormat('d M');
            $trendAssignedSeries[]  = (int)($assignedPlotMap[$key] ?? 0);
            $trendCompletedSeries[] = (int)($completedPlotMap[$key] ?? 0);
        }

        // Status breakdown for donut chart
        $statusBreakdown = [
            'completed'   => $completedTasks,
            'in_progress' => $inProgressTasks,
            'assigned'    => $assignedTasks,
            'overdue'     => $overdueTasks,
        ];

        // Task Type Breakdown
        $typeBreakdown = [
            'harian'     => (clone $taskQuery)->where('task_type', 'harian')->count(),
            'bulanan'    => (clone $taskQuery)->where('task_type', 'bulanan')->count(),
            'insidentil' => (clone $taskQuery)->where('task_type', 'insidentil')->count(),
        ];

        // Recent Submissions Feed (Latest 6, respecting filters)
        $submissionQuery = TaskSubmission::with(['employee.department', 'task.category'])
            ->whereHas('assignee', function ($q) use ($selectedCompanyId, $selectedDepartmentId, $selectedCategoryId, $dateFrom, $dateTo) {
                if ($selectedCompanyId) {
                    $q->whereHas('task', function ($sub) use ($selectedCompanyId) {
                        $sub->where('company_id', $selectedCompanyId);
                    });
                }
                if ($selectedDepartmentId) {
                    $q->whereHas('employee', function ($sub) use ($selectedDepartmentId) {
                        $sub->where('department_id', $selectedDepartmentId);
                    });
                }
                if ($selectedCategoryId) {
                    $q->whereHas('task', function ($sub) use ($selectedCategoryId) {
                        $sub->where('category_id', $selectedCategoryId);
                    });
                }
                if ($dateFrom) {
                    $q->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '>=', $dateFrom);
                }
                if ($dateTo) {
                    $q->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '<=', $dateTo);
                }
            });

        $recentSubmissions = $submissionQuery->latest('created_at')->take(6)->get();

        // Urgent & Overdue Watchlist (Top 5, respecting filters)
        $urgentTasks = (clone $taskQuery)
            ->with(['priority', 'assignedEmployees'])
            ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
            ->where(function ($q) {
                $q->whereNotNull('due_date')->whereDate('due_date', '<=', now()->addDays(2)->toDateString());
            })
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Personal employee summary if user is linked to an employee
        $myEmployee = null;
        $myTaskStats = null;
        if ($user) {
            if (!empty($user->karyawan_id)) {
                $myEmployee = Karyawan::find($user->karyawan_id);
            } elseif (!empty($user->email)) {
                $myEmployee = Karyawan::where('email', $user->email)->first();
            } elseif (!empty($user->nm_user)) {
                $myEmployee = Karyawan::where('name', $user->nm_user)->orWhere('nama_lengkap', $user->nm_user)->first();
            }

            if ($myEmployee) {
                $myAssignees = TaskAssignee::actionable()->where('employee_id', $myEmployee->id);
                $myTaskStats = (object)[
                    'total'     => (clone $myAssignees)->count(),
                    'today'     => (clone $myAssignees)->whereDate('due_date', $today)->count(),
                    'completed' => (clone $myAssignees)->where('status', TaskStatusService::STATUS_COMPLETED)->count(),
                    'overdue'   => (clone $myAssignees)->overdue()->count(),
                ];
            }
        }

        return view('dashboard', compact(
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'assignedTasks',
            'overdueTasks',
            'todayTasks',
            'completionRate',
            'totalEmployees',
            'activeAssigneesCount',
            'employeePerformance',
            'trendDates',
            'trendAssignedSeries',
            'trendCompletedSeries',
            'statusBreakdown',
            'typeBreakdown',
            'recentSubmissions',
            'urgentTasks',
            'companies',
            'departments',
            'categories',
            'selectedCompanyId',
            'selectedDepartmentId',
            'selectedCategoryId',
            'dateFrom',
            'dateTo',
            'isSuperAdmin',
            'myEmployee',
            'myTaskStats'
        ));
    }

    public function change_password()
    {
        return view('change-password');
    }

    public function save_change_password(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password'     => [
                'required',
                'string',
                'min:12',
                'regex:/[A-Z]/',            // Harus ada huruf besar
                'regex:/[a-z]/',            // Harus ada huruf kecil
                'regex:/[0-9]/',            // Harus ada angka
                'regex:/[!@#$%^&*()_+\-]/', // Harus ada karakter spesial
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (
            Auth::attempt([
                'username' => Auth::user()->username,
                'password' => $request->old_password,
            ])
        ) {
            try {
                DB::beginTransaction();
                $user           = User::findOrFail(Auth::user()->id_user);
                $user->password = Hash::make($request->password);
                $user->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Password tidak bisa diganti, Hubungi Administrator')->withInput();
            }
            return redirect('dashboard')->with('success', 'Password Berhasil di Ganti oleh ' . Auth::user()->nm_user);
        } else {
            return redirect()->back()->with('error', 'Password Salah')->withInput();
        }
    }

    public function forgot_password()
    {
        $data = [];
        return view('forgot-password', $data);
    }

    public function forgot_password_check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data         = [];
        $user         = User::where('username', $request->email)->first();
        $data['user'] = isset($user) ? $user : null;

        return view('forgot-password', $data);
    }

    public function forgot_password_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user'  => 'required',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/[A-Z]/',            // Harus ada huruf besar
                'regex:/[a-z]/',            // Harus ada huruf kecil
                'regex:/[0-9]/',            // Harus ada angka
                'regex:/[!@#$%^&*()_+\-]/', // Harus ada karakter spesial
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $userId = decrypt($request->id_user);
        try {
            DB::beginTransaction();
            $user           = User::findOrFail($userId);
            $user->password = Hash::make($request->password);
            $user->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Password tidak bisa diganti, Hubungi Administrator')->withInput();
        }
        return redirect('auth/login')->with('success', 'Password Berhasil di Ganti oleh ' . $user->nm_user);
    }

    public function generateQrCode()
    {
        return view('qrcode');
    }

    /**
     * Server-side DataTables endpoint for Employee Performance Monitoring & Leaderboard.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployeePerformanceData(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ((int)(session('role')['id_role'] ?? 0) === 1);

        $selectedCompanyId = $request->filled('company_id') ? (int)$request->get('company_id') : null;
        if (!$isSuperAdmin && !$selectedCompanyId) {
            if ($user && $user->karyawan_id) {
                $emp = Karyawan::find($user->karyawan_id);
                $selectedCompanyId = $emp?->company_id;
            }
        }

        $selectedDepartmentId = $request->filled('department_id') ? (int)$request->get('department_id') : null;
        $selectedCategoryId   = $request->filled('category_id') ? (int)$request->get('category_id') : null;
        $dateFrom             = $request->filled('date_from') ? $request->get('date_from') : null;
        $dateTo               = $request->filled('date_to') ? $request->get('date_to') : null;

        // Employees query
        $karyawanQuery = Karyawan::with(['department', 'position', 'company'])->where('is_active', true);
        if ($selectedCompanyId) {
            $karyawanQuery->where('company_id', $selectedCompanyId);
        }
        if ($selectedDepartmentId) {
            $karyawanQuery->where('department_id', $selectedDepartmentId);
        }
        $employees = $karyawanQuery->get();

        // Preload actionable task_assignees to avoid N+1 queries with filters applied
        $assigneeQuery = TaskAssignee::actionable()
            ->with(['task'])
            ->whereHas('task', function ($q) use ($selectedCompanyId, $selectedCategoryId) {
                if ($selectedCompanyId) {
                    $q->where('company_id', $selectedCompanyId);
                }
                if ($selectedCategoryId) {
                    $q->where('category_id', $selectedCategoryId);
                }
            });

        if ($selectedDepartmentId) {
            $assigneeQuery->whereHas('employee', function ($q) use ($selectedDepartmentId) {
                $q->where('department_id', $selectedDepartmentId);
            });
        }
        if ($dateFrom) {
            $assigneeQuery->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '>=', $dateFrom);
        }
        if ($dateTo) {
            $assigneeQuery->whereDate(DB::raw('COALESCE(assigned_at, created_at)'), '<=', $dateTo);
        }

        $allAssignees = $assigneeQuery->get()->groupBy('employee_id');

        $employeePerformance = $employees->map(function ($emp) use ($allAssignees) {
            $assigned = $allAssignees->get($emp->id, collect());
            $totalAssigned = $assigned->count();
            $completed = $assigned->where('status', TaskStatusService::STATUS_COMPLETED)->count();
            $inProgress = $assigned->where('status', TaskStatusService::STATUS_IN_PROGRESS)->count();
            $overdue = $assigned->filter(function ($item) {
                return $item->is_overdue;
            })->count();

            $rate = $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100) : 0;
            $avgProgress = $totalAssigned > 0 ? round($assigned->avg('progress') ?: 0) : 0;

            if ($totalAssigned === 0) {
                $perfStatus = 'Belum Ada Tugas';
                $perfClass = 'bg-gray-100 text-gray-600 border-gray-200';
            } elseif ($rate >= 80) {
                $perfStatus = 'Sangat Baik';
                $perfClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif ($rate >= 50) {
                $perfStatus = 'Cukup Baik';
                $perfClass = 'bg-sky-50 text-sky-700 border-sky-200';
            } else {
                $perfStatus = 'Perlu Evaluasi';
                $perfClass = 'bg-rose-50 text-rose-700 border-rose-200';
            }

            return (object)[
                'id'                 => $emp->id,
                'name'               => $emp->display_name,
                'avatar'             => $emp->avatar_url,
                'department'         => $emp->department?->name ?? 'Umum',
                'position'           => $emp->position?->name ?? '-',
                'total_tasks'        => $totalAssigned,
                'completed_tasks'    => $completed,
                'in_progress_tasks'  => $inProgress,
                'overdue_tasks'      => $overdue,
                'completion_rate'    => $rate,
                'avg_progress'       => $avgProgress,
                'performance_status' => $perfStatus,
                'performance_class'  => $perfClass,
            ];
        })->sortByDesc('completion_rate')->values();

        return DataTables::of($employeePerformance)
            ->addIndexColumn()
            ->addColumn('employee', function ($emp) {
                $avatarHtml = '';
                if ($emp->avatar) {
                    $avatarHtml = '<img src="' . e($emp->avatar) . '" alt="' . e($emp->name) . '" class="h-9 w-9 rounded-full object-cover border border-gray-200">';
                } else {
                    $initial = strtoupper(substr($emp->name, 0, 2));
                    $avatarHtml = '<div class="h-9 w-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">' . e($initial) . '</div>';
                }

                $rankBadge = '';
                if ($emp->total_tasks > 0 && $emp->completion_rate >= 80) {
                    $rankBadge = '<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 ml-1.5">Top Performer</span>';
                }

                return '<div class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                ' . $avatarHtml . '
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate flex items-center">' . e($emp->name) . $rankBadge . '</div>
                                <div class="text-xs text-muted-foreground">ID: #' . e($emp->id) . '</div>
                            </div>
                        </div>';
            })
            ->addColumn('department_position', function ($emp) {
                return '<div class="text-xs font-medium text-gray-800">' . e($emp->department) . '</div>
                        <div class="text-[11px] text-muted-foreground">' . e($emp->position) . '</div>';
            })
            ->addColumn('total_tasks_formatted', function ($emp) {
                return '<span class="font-bold text-gray-900">' . $emp->total_tasks . '</span>';
            })
            ->addColumn('completed_tasks_formatted', function ($emp) {
                return '<span class="font-semibold text-emerald-600">' . $emp->completed_tasks . '</span>';
            })
            ->addColumn('in_progress_tasks_formatted', function ($emp) {
                return '<span class="font-medium text-amber-600">' . $emp->in_progress_tasks . '</span>';
            })
            ->addColumn('overdue_tasks_formatted', function ($emp) {
                $color = $emp->overdue_tasks > 0 ? 'text-rose-600 font-bold' : 'text-gray-400';
                return '<span class="font-semibold ' . $color . '">' . $emp->overdue_tasks . '</span>';
            })
            ->addColumn('completion_rate_bar', function ($emp) {
                $barColor = $emp->completion_rate >= 80 ? 'bg-emerald-500' : ($emp->completion_rate >= 50 ? 'bg-sky-500' : 'bg-rose-500');
                return '<div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-gray-900">' . $emp->completion_rate . '%</span>
                            <span class="text-[11px] text-muted-foreground">' . $emp->completed_tasks . '/' . $emp->total_tasks . '</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="' . $barColor . ' h-2 rounded-full transition-all duration-300" style="width: ' . min(100, $emp->completion_rate) . '%"></div>
                        </div>';
            })
            ->addColumn('performance_badge', function ($emp) {
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border ' . $emp->performance_class . '">
                            ' . e($emp->performance_status) . '
                        </span>';
            })
            ->addColumn('action', function ($emp) {
                $url = route('tasks.assignment.plot', $emp->id);
                return '<a href="' . $url . '" class="inline-flex items-center gap-1 text-xs font-medium text-primary hover:underline" title="Lihat & Plot Tugas Pegawai">
                            <span>Plotting</span>
                            <i data-lucide="external-link" class="h-3 w-3"></i>
                        </a>';
            })
            ->rawColumns(['employee', 'department_position', 'total_tasks_formatted', 'completed_tasks_formatted', 'in_progress_tasks_formatted', 'overdue_tasks_formatted', 'completion_rate_bar', 'performance_badge', 'action'])
            ->make(true);
    }

    /**
     * Server-side DataTables endpoint for Recent Submissions (Aktivitas & Bukti Pengerjaan).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentSubmissionsData(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = ((int)(session('role')['id_role'] ?? 0) === 1);

        $selectedCompanyId = $request->filled('company_id') ? (int)$request->get('company_id') : null;
        if (!$isSuperAdmin && !$selectedCompanyId) {
            if ($user && $user->karyawan_id) {
                $emp = Karyawan::find($user->karyawan_id);
                $selectedCompanyId = $emp?->company_id;
            }
        }

        $selectedDepartmentId = $request->filled('department_id') ? (int)$request->get('department_id') : null;
        $selectedCategoryId   = $request->filled('category_id') ? (int)$request->get('category_id') : null;
        $dateFrom             = $request->filled('date_from') ? $request->get('date_from') : null;
        $dateTo               = $request->filled('date_to') ? $request->get('date_to') : null;

        $submissionQuery = TaskSubmission::with(['employee.department', 'task.category'])
            ->select('task_submissions.*');

        if ($selectedCompanyId) {
            $submissionQuery->whereHas('task', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            });
        }
        if ($selectedDepartmentId) {
            $submissionQuery->whereHas('employee', function ($q) use ($selectedDepartmentId) {
                $q->where('department_id', $selectedDepartmentId);
            });
        }
        if ($selectedCategoryId) {
            $submissionQuery->whereHas('task', function ($q) use ($selectedCategoryId) {
                $q->where('category_id', $selectedCategoryId);
            });
        }
        if ($dateFrom) {
            $submissionQuery->whereDate('task_submissions.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $submissionQuery->whereDate('task_submissions.created_at', '<=', $dateTo);
        }

        return DataTables::of($submissionQuery)
            ->addIndexColumn()
            ->filterColumn('employee', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('nama_lengkap', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('task_info', function ($query, $keyword) {
                $query->where(function ($sq) use ($keyword) {
                    $sq->whereHas('task', function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%");
                    })->orWhere('task_submissions.completion_notes', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('employee', function ($sub) {
                $emp = $sub->employee;
                $name = $emp?->display_name ?? 'Pegawai';
                $dept = $emp?->department?->name ?? 'Umum';

                $avatarHtml = '';
                if ($emp && $emp->avatar_url) {
                    $avatarHtml = '<img src="' . e($emp->avatar_url) . '" alt="' . e($name) . '" class="h-8 w-8 rounded-full object-cover border border-gray-200 shrink-0">';
                } else {
                    $initial = strtoupper(substr($name, 0, 2));
                    $avatarHtml = '<div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">' . e($initial) . '</div>';
                }

                return '<div class="flex items-center gap-2.5">
                            ' . $avatarHtml . '
                            <div class="min-w-0">
                                <div class="font-bold text-gray-900 truncate text-xs">' . e($name) . '</div>
                                <div class="text-[11px] text-muted-foreground truncate">' . e($dept) . '</div>
                            </div>
                        </div>';
            })
            ->addColumn('task_info', function ($sub) {
                $title = $sub->task?->title ?? ('Tugas #' . $sub->task_id);
                $catName = $sub->task?->category?->name;

                $catBadge = '';
                if ($catName) {
                    $catBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 mr-1">' . e($catName) . '</span>';
                }

                $notesHtml = '';
                if (!empty($sub->completion_notes)) {
                    $notesHtml = '<div class="text-[11px] text-gray-500 italic bg-gray-50 p-1.5 rounded border border-gray-100 mt-1 line-clamp-2">"' . e($sub->completion_notes) . '"</div>';
                }

                return '<div class="min-w-0">
                            <div class="font-medium text-gray-800 text-xs flex items-center flex-wrap gap-1">' . $catBadge . '<span class="truncate">' . e($title) . '</span></div>
                            ' . $notesHtml . '
                        </div>';
            })
            ->addColumn('status_proof', function ($sub) {
                $statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border ' . $sub->status_badge_class . '">' . e($sub->status_label) . '</span>';

                $proofHtml = '';
                $proofUrl = $sub->completion_file_url ?: $sub->completion_url;
                if ($proofUrl) {
                    $proofHtml = '<a href="' . e($proofUrl) . '" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-medium text-primary hover:underline">
                                    <i data-lucide="file-text" class="h-3 w-3"></i>
                                    <span>Lihat Bukti</span>
                                  </a>';
                }

                $achievedHtml = '';
                if (!empty($sub->achieved_amount) && $sub->achieved_amount > 0) {
                    $achievedHtml = '<span class="text-[11px] text-muted-foreground">Capaian: <strong class="text-gray-800">' . number_format($sub->achieved_amount) . '</strong></span>';
                }

                return '<div class="flex flex-col items-start gap-1">
                            ' . $statusBadge . '
                            ' . $proofHtml . '
                            ' . $achievedHtml . '
                        </div>';
            })
            ->addColumn('time_ago', function ($sub) {
                $created = $sub->created_at;
                if (!$created) return '-';

                $diff = $created->diffForHumans();
                $full = $created->translatedFormat('d M Y, H:i');

                return '<div class="text-right whitespace-nowrap">
                            <div class="text-xs font-semibold text-gray-800">' . e($diff) . '</div>
                            <div class="text-[10px] text-muted-foreground">' . e($full) . '</div>
                        </div>';
            })
            ->rawColumns(['employee', 'task_info', 'status_proof', 'time_ago'])
            ->make(true);
    }
}

