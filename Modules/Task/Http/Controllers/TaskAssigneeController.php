<?php

namespace Modules\Task\Http\Controllers;

use App\Traits\HasAccessControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Departemen;
use Modules\Master\Entities\Karyawan;
use Modules\Task\Entities\Task;
use Modules\Task\Entities\TaskAssignee;
use Modules\Task\Entities\TaskCategory;
use Modules\Task\Entities\TaskDataEntry;
use Modules\Task\Entities\TaskStockUpdate;
use Modules\Task\Entities\TaskSubmission;
use Modules\Task\Services\TaskStatusService;
use Yajra\DataTables\Facades\DataTables;

class TaskAssigneeController extends Controller
{
    use HasAccessControl;

    /**
     * Get current user's company context.
     */
    protected function getUserCompanyId(): ?int
    {
        $user = Auth::user();
        $isSuperAdmin = session('role')['id_role'] ?? null;

        if ((int)$isSuperAdmin === 1) {
            return null;
        }

        if ($user && $user->karyawan_id) {
            $employee = Karyawan::find($user->karyawan_id);
            if ($employee && $employee->company_id) {
                return (int)$employee->company_id;
            }
        }

        return null;
    }

    /**
     * Display Task Assignment index: Table of employees who have tasks in the date range
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return $denied;
        }

        $userCompanyId = $this->getUserCompanyId();

        $companies = $userCompanyId
            ? Company::where('id', $userCompanyId)->get()
            : Company::where('is_active', true)->orderBy('name')->get();

        $departments = Departemen::where('is_active', true)->orderBy('name')->get();

        // Default date range: today
        $defaultStartDate = Carbon::today()->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();

        $statuses = TaskStatusService::getAvailableStatuses();

        return view('task::assignment.index', compact(
            'companies',
            'departments',
            'statuses',
            'userCompanyId',
            'defaultStartDate',
            'defaultEndDate'
        ));
    }

    /**
     * Server-side DataTables of task assignments or employees
     */
    public function getData(Request $request)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $view = $request->input('view', 'assignments');
        if ($view === 'employees') {
            return $this->getEmployeesData($request);
        }

        return $this->getAssignmentsData($request);
    }

    /**
     * DataTables data for Task Assignments grid (with Tenggat Waktu, Progress, Status)
     */
    protected function getAssignmentsData(Request $request)
    {
        $userCompanyId = $this->getUserCompanyId();
        $startDate = $request->filled('start_date') ? $request->start_date : null;
        $endDate = $request->filled('end_date') ? $request->end_date : null;

        $query = TaskAssignee::with([
            'task.category',
            'task.priority',
            'employee.department',
            'employee.position',
            'employee.company',
        ])->whereHas('task', function ($q) {
            $q->whereNull('parent_task_id');
        })->whereHas('employee', function ($q) {
            $q->where('is_active', true);
        });

        // Multi-company isolation
        if ($userCompanyId) {
            $query->whereHas('employee', function ($q) use ($userCompanyId) {
                $q->where('company_id', $userCompanyId);
            });
        } elseif ($request->filled('company_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('task_assignees.status', $request->status);
            }
        }

        // Date range condition
        if ($startDate && $endDate) {
            $query->where(function ($sub) use ($startDate, $endDate) {
                $sub->whereBetween(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), [$startDate, $endDate])
                    ->orWhereBetween('task_assignees.due_date', [$startDate, $endDate])
                    ->orWhere(function ($overlap) use ($startDate, $endDate) {
                        $overlap->where('task_assignees.start_date', '<=', $endDate)
                                ->where('task_assignees.due_date', '>=', $startDate);
                    });
            });
        } elseif ($startDate) {
            $query->where(function ($sub) use ($startDate) {
                $sub->where(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), '>=', $startDate)
                    ->orWhere('task_assignees.due_date', '>=', $startDate);
            });
        } elseif ($endDate) {
            $query->where(function ($sub) use ($endDate) {
                $sub->where(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), '<=', $endDate);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('task_info', function ($row) {
                $task = $row->task;
                if (!$task) {
                    return '<span class="text-xs text-gray-400 italic">Tugas Dihapus</span>';
                }
                $url = route('tasks.show', $task->id);
                $priorityBadge = $task->priority
                    ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border ' . $task->priority->badge_class . '">' . e($task->priority->name) . '</span>'
                    : '';
                $categoryName = $task->category ? e($task->category->name) : 'Tanpa Kategori';
                $typeBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border ' . ($task->task_type_badge_class ?? 'bg-gray-100 text-gray-700') . '">' . ($task->task_type_label ?? 'Insidentil') . '</span>';

                return '<div class="flex flex-col max-w-xs">
                            <div class="flex items-center flex-wrap gap-1">
                                <a href="' . $url . '" class="font-semibold text-gray-900 text-sm hover:text-primary transition-colors truncate">' . e($task->title) . '</a>
                                ' . $priorityBadge . '
                                ' . $typeBadge . '
                            </div>
                            <div class="flex items-center gap-2 text-[11px] text-gray-500 mt-0.5">
                                <span><i data-lucide="tag" class="w-3 h-3 inline mr-0.5 text-gray-400"></i>' . $categoryName . '</span>
                            </div>
                        </div>';
            })
            ->addColumn('employee_info', function ($row) {
                $emp = $row->employee;
                if (!$emp) {
                    return '<span class="text-xs text-gray-400 italic">-</span>';
                }
                $initials = strtoupper(substr($emp->display_name, 0, 2));
                $avatar = $emp->avatar_url
                    ? '<img src="' . e($emp->avatar_url) . '" alt="' . e($emp->display_name) . '" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0">'
                    : '<div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0">' . e($initials) . '</div>';

                $dept = $emp->department ? e($emp->department->name) : ($emp->jabatan ?: '-');
                $nik = $emp->employee_number ?: ($emp->nik_ktp ?: '-');

                return '<div class="flex items-center gap-2.5">
                            ' . $avatar . '
                            <div class="flex flex-col min-w-0">
                                <span class="font-medium text-gray-900 text-xs truncate">' . e($emp->display_name) . '</span>
                                <span class="text-[11px] text-gray-500 truncate">' . $nik . ' • ' . $dept . '</span>
                            </div>
                        </div>';
            })
            ->editColumn('due_date', function ($row) {
                $dueDate = $row->effective_due_date;
                if (!$dueDate) {
                    return '<span class="text-xs text-gray-400 italic">-</span>';
                }
                $dateFormatted = Carbon::parse($dueDate)->format('d M Y');
                $isOverdue = $row->is_overdue;
                $colorClass = $isOverdue ? 'text-red-600 font-bold' : 'text-gray-700 font-medium';
                $overdueBadge = $isOverdue
                    ? '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200 mt-0.5"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Terlambat</span>'
                    : '';

                return '<div class="flex flex-col text-xs ' . $colorClass . '">
                            <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i> ' . $dateFormatted . '</span>
                            <span class="text-[10px] text-gray-400">' . (Carbon::parse($dueDate)->diffForHumans()) . '</span>
                            ' . $overdueBadge . '
                        </div>';
            })
            ->editColumn('progress', function ($row) {
                $progress = (int)$row->progress;
                $barColor = $progress == 100 ? 'bg-emerald-500' : ($progress > 50 ? 'bg-blue-500' : 'bg-amber-500');

                return '<div class="w-24 sm:w-28 space-y-1">
                            <div class="flex justify-between text-[11px] font-medium text-gray-600">
                                <span>Progress</span>
                                <span class="font-bold">' . $progress . '%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                <div class="' . $barColor . ' h-1.5 rounded-full transition-all" style="width: ' . $progress . '%"></div>
                            </div>
                        </div>';
            })
            ->editColumn('status', function ($row) {
                $label = $row->status_label;
                $badgeClass = $row->status_badge_class;

                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border ' . $badgeClass . '">
                            ' . e($label) . '
                        </span>';
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('tasks.assignment.submission', $row->id);
                $editUrl = route('tasks.assignment.edit', $row->employee_id);
                $escapedEmpName = htmlspecialchars(addslashes($row->employee ? $row->employee->display_name : ''), ENT_QUOTES, 'UTF-8');
                $escapedTaskTitle = htmlspecialchars(addslashes($row->task ? $row->task->title : ''), ENT_QUOTES, 'UTF-8');

                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="' . $detailUrl . '" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm" title="Lihat Detail Submission Plotingan Ini">
                            <i data-lucide="eye" class="w-3.5 h-3.5 text-gray-500"></i>
                            <span class="hidden sm:inline">Detail</span>
                        </a>
                        <a href="' . $editUrl . '" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm" title="Edit Plotting Karyawan">
                            <i data-lucide="clipboard-edit" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Edit Plot</span>
                        </a>
                        <button type="button" onclick="deleteSingleAssignment(' . $row->id . ', \'' . $escapedEmpName . '\', \'' . $escapedTaskTitle . '\')" class="inline-flex items-center p-1.5 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Penugasan">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->filled('search.value')) {
                    $search = $request->input('search.value');
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('task', function ($qt) use ($search) {
                            $qt->where('title', 'like', "%{$search}%")
                               ->orWhere('task_number', 'like', "%{$search}%");
                        })->orWhereHas('employee', function ($qe) use ($search) {
                            $qe->where('name', 'like', "%{$search}%")
                               ->orWhere('nama_lengkap', 'like', "%{$search}%")
                               ->orWhere('employee_number', 'like', "%{$search}%")
                               ->orWhere('nik_ktp', 'like', "%{$search}%");
                        });
                    });
                }
            })
            ->rawColumns(['task_info', 'employee_info', 'due_date', 'progress', 'status', 'action'])
            ->make(true);
    }

    /**
     * DataTables data for Employee Overview recap
     */
    protected function getEmployeesData(Request $request)
    {
        $userCompanyId = $this->getUserCompanyId();
        $startDate = $request->filled('start_date') ? $request->start_date : null;
        $endDate = $request->filled('end_date') ? $request->end_date : null;

        $taskDateCondition = function ($q) use ($startDate, $endDate) {
            $q->whereNull('tasks.parent_task_id');

            if ($startDate && $endDate) {
                $q->where(function ($sub) use ($startDate, $endDate) {
                    $sub->whereBetween(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), [$startDate, $endDate])
                        ->orWhereBetween('task_assignees.due_date', [$startDate, $endDate])
                        ->orWhere(function ($overlap) use ($startDate, $endDate) {
                            $overlap->where('task_assignees.start_date', '<=', $endDate)
                                    ->where('task_assignees.due_date', '>=', $startDate);
                        });
                });
            } elseif ($startDate) {
                $q->where(function ($sub) use ($startDate) {
                    $sub->where(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), '>=', $startDate)
                        ->orWhere('task_assignees.due_date', '>=', $startDate);
                });
            } elseif ($endDate) {
                $q->where(function ($sub) use ($endDate) {
                    $sub->where(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), '<=', $endDate);
                });
            }
        };

        // Query employees who have task assignments within the date range
        $query = Karyawan::with(['company', 'department', 'position'])
            ->where('is_active', true)
            ->whereHas('assignedTasks', $taskDateCondition)
            ->withCount([
                'assignedTasks as total_tasks_count' => $taskDateCondition,
                'assignedTasks as active_tasks_count' => function ($q) use ($taskDateCondition) {
                    $taskDateCondition($q);
                    $q->whereNotIn('task_assignees.status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED]);
                },
                'assignedTasks as completed_tasks_count' => function ($q) use ($taskDateCondition) {
                    $taskDateCondition($q);
                    $q->where('task_assignees.status', TaskStatusService::STATUS_COMPLETED);
                },
                'assignedTasks as overdue_tasks_count' => function ($q) use ($taskDateCondition) {
                    $taskDateCondition($q);
                    $q->whereNotIn('task_assignees.status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
                        ->whereNotNull('task_assignees.due_date')
                        ->whereDate('task_assignees.due_date', '<', now()->toDateString());
                }
            ]);

        // Multi-company isolation
        if ($userCompanyId) {
            $query->where('company_id', $userCompanyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('employee_info', function ($row) {
                $avatar = '';
                if ($row->avatar_url) {
                    $avatar = '<img src="' . e($row->avatar_url) . '" alt="' . e($row->display_name) . '" class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0">';
                } else {
                    $initials = strtoupper(substr($row->display_name, 0, 2));
                    $avatar = '<div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0">' . e($initials) . '</div>';
                }

                $nik = $row->employee_number ?: ($row->nik_ktp ?: '-');

                return '<div class="flex items-center gap-3">
                            ' . $avatar . '
                            <div class="flex flex-col min-w-0">
                                <span class="font-semibold text-gray-900 text-sm truncate">' . e($row->display_name) . '</span>
                                <span class="text-xs text-gray-500 font-mono">' . e($nik) . '</span>
                            </div>
                        </div>';
            })
            ->addColumn('position_department', function ($row) {
                $pos = $row->position ? e($row->position->name) : e($row->jabatan ?: '-');
                $dept = $row->department ? e($row->department->name) : 'Tanpa Departemen';

                return '<div class="flex flex-col text-xs">
                            <span class="font-medium text-gray-800">' . $pos . '</span>
                            <span class="text-gray-500 mt-0.5">' . $dept . '</span>
                        </div>';
            })
            ->addColumn('company_name', function ($row) {
                if (!$row->company) {
                    return '<span class="text-gray-400 text-xs">-</span>';
                }
                return '<div class="flex items-center gap-1.5 text-xs text-gray-700 font-medium">
                            <div class="w-2 h-2 rounded-full bg-primary shrink-0"></div>
                            <span>' . e($row->company->name) . '</span>
                        </div>';
            })
            ->addColumn('tasks_summary', function ($row) {
                $total = (int)$row->total_tasks_count;
                $active = (int)$row->active_tasks_count;
                $completed = (int)$row->completed_tasks_count;
                $overdue = (int)$row->overdue_tasks_count;

                $overdueHtml = '';
                if ($overdue > 0) {
                    $overdueHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-700 border border-red-200">
                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i> ' . $overdue . ' Terlambat
                                    </span>';
                }

                return '<div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                                    <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                                    ' . $total . ' Tugas Diberikan
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    ' . $active . ' Aktif
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ' . $completed . ' Selesai
                                </span>
                                ' . $overdueHtml . '
                            </div>
                        </div>';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('tasks.assignment.edit', $row->id);
                $escapedName = htmlspecialchars(addslashes($row->display_name), ENT_QUOTES, 'UTF-8');

                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" onclick="showEmployeeTaskDetail(' . $row->id . ', \'' . $escapedName . '\')" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm" title="Lihat Rincian Tugas">
                            <i data-lucide="eye" class="w-3.5 h-3.5 text-gray-500"></i>
                            <span class="hidden sm:inline">Rincian</span>
                        </button>
                        <a href="' . $editUrl . '" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm" title="Edit Plotting Tugas">
                            <i data-lucide="clipboard-edit" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Edit</span>
                        </a>
                        <button type="button" onclick="deleteAssignment(' . $row->id . ', \'' . $escapedName . '\')" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors shadow-sm" title="Hapus Penugasan">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['employee_info', 'position_department', 'company_name', 'tasks_summary', 'action'])
            ->make(true);
    }

    /**
     * Show form to plot new tasks to an employee (Create Task Assignment)
     */
    public function create(Request $request)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return $denied;
        }

        $userCompanyId = $this->getUserCompanyId();

        $companies = $userCompanyId
            ? Company::where('id', $userCompanyId)->get()
            : Company::where('is_active', true)->orderBy('name')->get();

        $departments = Departemen::where('is_active', true)->orderBy('name')->get();

        // Get active employees
        $employeesQuery = Karyawan::with(['company', 'department', 'position'])
            ->where('is_active', true)
            ->orderBy('name');
        if ($userCompanyId) {
            $employeesQuery->where('company_id', $userCompanyId);
        }
        $employees = $employeesQuery->get();

        // Task categories
        $categories = TaskCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Master tasks list (only original/master tasks)
        $tasksQuery = Task::with(['category', 'priority', 'assignedEmployees'])
            ->whereNull('parent_task_id')
            ->whereNotIn('status', [TaskStatusService::STATUS_CANCELLED])
            ->orderBy('title', 'asc');
        if ($userCompanyId) {
            $tasksQuery->where('company_id', $userCompanyId);
        }
        $tasks = $tasksQuery->get();

        $selectedEmployeeId = $request->query('employee_id');
        $selectedEmployeeIds = (array)($request->query('employee_ids') ?: ($selectedEmployeeId ? [$selectedEmployeeId] : []));

        return view('task::assignment.create', compact(
            'employees',
            'tasks',
            'categories',
            'companies',
            'departments',
            'userCompanyId',
            'selectedEmployeeId',
            'selectedEmployeeIds'
        ));
    }

    /**
     * Store new task assignments for one or multiple employees
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return $denied;
        }

        $employeeIds = $request->input('employee_ids', []);
        if (empty($employeeIds) && $request->filled('employee_id')) {
            $employeeIds = [$request->input('employee_id')];
        }
        $employeeIds = array_values(array_unique(array_filter(array_map('intval', (array)$employeeIds))));
        $request->merge(['employee_ids' => $employeeIds]);

        $request->validate([
            'employee_ids'    => 'required|array|min:1',
            'employee_ids.*'  => 'exists:karyawan,id',
            'assignment_date' => 'required|date',
            'due_date'        => 'required|date|after_or_equal:assignment_date',
            'task_ids'        => 'required|array|min:1',
            'task_ids.*'      => 'exists:tasks,id',
            'due_dates'       => 'nullable|array',
            'notes'           => 'nullable|array',
        ], [
            'employee_ids.required'    => 'Silakan pilih minimal satu karyawan yang akan ditugaskan.',
            'employee_ids.min'         => 'Silakan pilih minimal satu karyawan yang akan ditugaskan.',
            'assignment_date.required' => 'Tanggal assignment wajib diisi.',
            'due_date.required'        => 'Deadline penugasan wajib diisi.',
            'due_date.after_or_equal'  => 'Deadline tidak boleh sebelum tanggal assignment.',
            'task_ids.required'        => 'Pilih minimal satu tugas yang akan diberikan.',
            'task_ids.min'             => 'Pilih minimal satu tugas yang akan diberikan.',
        ]);

        $employees = Karyawan::whereIn('id', $employeeIds)->get();
        if ($employees->isEmpty()) {
            return redirect()->back()->withInput()->with('error', 'Karyawan yang dipilih tidak ditemukan.');
        }

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId) {
            foreach ($employees as $emp) {
                if ((int)$emp->company_id !== (int)$userCompanyId) {
                    abort(403, 'Anda tidak memiliki akses ke karyawan perusahaan ini: ' . $emp->display_name);
                }
            }
        }

        $selectedTaskIds = array_values(array_unique(array_map('intval', $request->input('task_ids', []))));
        $assignmentDate = $request->input('assignment_date');
        $globalDueDate = $request->input('due_date');

        try {
            DB::beginTransaction();

            $userId = Auth::user()->id_user ?? Auth::id();
            $tasks = Task::whereIn('id', $selectedTaskIds)->get()->keyBy('id');

            foreach ($employees as $employee) {
                foreach ($selectedTaskIds as $taskId) {
                    $task = $tasks->get($taskId);
                    if (!$task) continue;

                    $taskDueDate = !empty($request->due_dates[$taskId]) ? $request->due_dates[$taskId] : $globalDueDate;
                    $note = $request->notes[$taskId] ?? null;

                    // Check if an uncompleted assignment on the exact same assignment_date already exists
                    $assignee = TaskAssignee::where('task_id', $taskId)
                        ->where('employee_id', $employee->id)
                        ->where('start_date', $assignmentDate)
                        ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED])
                        ->first();

                    if ($assignee) {
                        $assignee->update([
                            'assigned_at'  => now(),
                            'assigned_by'  => $userId,
                            'due_date'     => $taskDueDate,
                            'status'       => TaskStatusService::STATUS_ASSIGNED,
                            'progress'     => 0,
                            'completed_at' => null,
                            'notes'        => $note,
                        ]);
                    } else {
                        try {
                            $assignee = TaskAssignee::create([
                                'task_id'      => $taskId,
                                'employee_id'  => $employee->id,
                                'assigned_at'  => now(),
                                'assigned_by'  => $userId,
                                'start_date'   => $assignmentDate,
                                'due_date'     => $taskDueDate,
                                'status'       => TaskStatusService::STATUS_ASSIGNED,
                                'progress'     => 0,
                                'completed_at' => null,
                                'notes'        => $note,
                            ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Fallback if unique constraint (task_id, employee_id) is still active in DB
                            $assignee = TaskAssignee::where('task_id', $taskId)
                                ->where('employee_id', $employee->id)
                                ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED])
                                ->first();

                            if ($assignee) {
                                $assignee->update([
                                    'assigned_at'  => now(),
                                    'assigned_by'  => $userId,
                                    'start_date'   => $assignmentDate,
                                    'due_date'     => $taskDueDate,
                                    'status'       => TaskStatusService::STATUS_ASSIGNED,
                                    'progress'     => 0,
                                    'completed_at' => null,
                                    'notes'        => $note,
                                ]);
                            } else {
                                throw $e;
                            }
                        }
                    }

                    // Ensure fresh TaskSubmission record for this specific plotting instance
                    $submission = TaskSubmission::where('task_assignee_id', $assignee->id)->first();
                    if (!$submission) {
                        TaskSubmission::create([
                            'task_assignee_id' => $assignee->id,
                            'task_id'          => $taskId,
                            'employee_id'      => $employee->id,
                            'created_by'       => $userId,
                            'work_date'        => $assignmentDate,
                            'status'           => TaskStatusService::STATUS_ASSIGNED,
                            'progress'         => 0,
                        ]);
                    } else {
                        // Reset submission fields to clean initial state for this new plotting
                        $submission->update([
                            'status'              => TaskStatusService::STATUS_ASSIGNED,
                            'progress'            => 0,
                            'achieved_amount'     => 0,
                            'completion_url'      => null,
                            'completion_file'     => null,
                            'completion_notes'    => null,
                            'completed_at'        => null,
                            'work_date'           => $assignmentDate,
                        ]);
                        // Delete previous data entries and stock updates if re-plotting this assignment
                        TaskDataEntry::where('task_submission_id', $submission->id)->delete();
                        TaskStockUpdate::where('task_submission_id', $submission->id)->delete();
                    }
                }
            }

            DB::commit();

            $taskCount = count($selectedTaskIds);
            $empCount = $employees->count();
            if ($empCount === 1) {
                $msg = "Penugasan untuk {$employees->first()->display_name} berhasil disimpan ({$taskCount} tugas terploting).";
            } else {
                $msg = "Penugasan untuk {$empCount} karyawan berhasil disimpan ({$taskCount} tugas terploting per karyawan).";
            }

            return redirect()->route('tasks.assignment.index')
                ->with('success', $msg);
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Gagal menyimpan penugasan: ' . $th->getMessage());
        }
    }

    /**
     * Show dedicated task plotting page for a specific employee
     */
    public function plot($employeeId)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return $denied;
        }

        $employee = Karyawan::with(['company', 'department', 'position'])->findOrFail($employeeId);

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId && (int)$employee->company_id !== (int)$userCompanyId) {
            abort(403, 'Anda tidak memiliki akses ke karyawan perusahaan ini.');
        }

        // Categories for filter
        $categories = TaskCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load all master tasks
        $tasks = Task::with(['category', 'priority', 'assignedEmployees'])
            ->whereNull('parent_task_id')
            ->whereNotIn('status', [TaskStatusService::STATUS_CANCELLED])
            ->orderBy('title', 'asc')
            ->get();

        // Currently assigned task map for this employee (active/uncompleted master tasks only)
        $assignedTaskMap = TaskAssignee::where('employee_id', $employee->id)
            ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
            ->whereHas('task', function ($q) {
                $q->whereNull('parent_task_id');
            })
            ->get()
            ->keyBy('task_id');
        $assignedTaskIds = $assignedTaskMap->keys()->toArray();

        return view('task::assignment.plot', compact('employee', 'categories', 'tasks', 'assignedTaskIds', 'assignedTaskMap'));
    }

    /**
     * Save task plotting for an employee
     */
    public function savePlot(Request $request, $employeeId)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return $denied;
        }

        $employee = Karyawan::findOrFail($employeeId);

        $request->validate([
            'task_ids'    => 'nullable|array',
            'task_ids.*'  => 'exists:tasks,id',
            'due_dates'   => 'nullable|array',
            'start_dates' => 'nullable|array',
            'notes'       => 'nullable|array',
        ]);

        $selectedTaskIds = array_values(array_unique(array_map('intval', $request->input('task_ids', []))));

        try {
            DB::beginTransaction();

            $userId = Auth::user()->id_user ?? Auth::id();

            // Only track ACTIVE (uncompleted) assignments for this employee
            $activeAssignees = TaskAssignee::where('employee_id', $employee->id)
                ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
                ->whereHas('task', function ($q) {
                    $q->whereNull('parent_task_id');
                })
                ->get()
                ->keyBy('task_id');

            $currentActiveTaskIds = $activeAssignees->keys()->toArray();

            $tasksToAdd = array_diff($selectedTaskIds, $currentActiveTaskIds);
            $tasksToKeep = array_intersect($selectedTaskIds, $currentActiveTaskIds);
            $tasksToRemove = array_diff($currentActiveTaskIds, $selectedTaskIds);

            // 1. Add new assignments (for new tasks or replotting master tasks where previous plotting was completed)
            foreach ($tasksToAdd as $taskId) {
                $task = Task::find($taskId);
                if (!$task) continue;

                $dueDate = !empty($request->due_dates[$taskId]) ? $request->due_dates[$taskId] : ($task->due_date ? $task->due_date->toDateString() : null);
                $startDate = !empty($request->start_dates[$taskId]) ? $request->start_dates[$taskId] : ($task->start_date ? $task->start_date->toDateString() : now()->toDateString());
                $note = $request->notes[$taskId] ?? null;

                $assignee = TaskAssignee::create([
                    'task_id'      => $taskId,
                    'employee_id'  => $employee->id,
                    'assigned_at'  => now(),
                    'assigned_by'  => $userId,
                    'start_date'   => $startDate,
                    'due_date'     => $dueDate,
                    'status'       => TaskStatusService::STATUS_ASSIGNED,
                    'progress'     => 0,
                    'completed_at' => null,
                    'notes'        => $note,
                ]);

                if ($assignee) {
                    TaskSubmission::create([
                        'task_assignee_id' => $assignee->id,
                        'task_id'          => $taskId,
                        'employee_id'      => $employee->id,
                        'created_by'       => $userId,
                        'work_date'        => $startDate ?: now()->toDateString(),
                        'status'           => TaskStatusService::STATUS_ASSIGNED,
                        'progress'         => 0,
                    ]);
                }
            }

            // 2. Update existing kept active assignments (due date, start date, notes)
            foreach ($tasksToKeep as $taskId) {
                $assignee = $activeAssignees->get($taskId);
                if ($assignee) {
                    $newStartDate = !empty($request->start_dates[$taskId]) ? $request->start_dates[$taskId] : ($assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString());
                    $newDueDate = !empty($request->due_dates[$taskId]) ? $request->due_dates[$taskId] : ($assignee->due_date ? $assignee->due_date->toDateString() : null);
                    $newNote = $request->notes[$taskId] ?? $assignee->notes;

                    $assignee->start_date = $newStartDate;
                    $assignee->due_date = $newDueDate;
                    $assignee->notes = $newNote;
                    $assignee->save();
                }
            }

            // 3. Remove unselected active assignments (never touch completed historical records!)
            foreach ($tasksToRemove as $taskId) {
                $assignee = $activeAssignees->get($taskId);
                if ($assignee) {
                    $sub = TaskSubmission::where('task_assignee_id', $assignee->id)->first();
                    if ($sub) {
                        TaskDataEntry::where('task_submission_id', $sub->id)->delete();
                        TaskStockUpdate::where('task_submission_id', $sub->id)->delete();
                        $sub->delete();
                    }
                    $assignee->delete();
                }
            }

            DB::commit();

            return redirect()->route('tasks.assignment.index')
                ->with('success', "Plotting tugas untuk {$employee->display_name} berhasil disimpan (" . count($selectedTaskIds) . " tugas terploting).");
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Gagal menyimpan plotting tugas: ' . $th->getMessage());
        }
    }

    /**
     * Edit task plotting for a specific employee
     */
    public function edit($employeeId)
    {
        return $this->plot($employeeId);
    }

    /**
     * Update task plotting for an employee
     */
    public function update(Request $request, $employeeId)
    {
        return $this->savePlot($request, $employeeId);
    }

    /**
     * Delete all task assignments for an employee or a specific single task assignment
     */
    public function destroy(Request $request, $employeeId)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Check if single assignment deletion
            if ($request->input('type') === 'single' || $request->filled('assignment_id')) {
                $assignmentId = $request->input('assignment_id') ?: $employeeId;
                $assignment = TaskAssignee::with(['employee', 'task'])->findOrFail($assignmentId);

                $userCompanyId = $this->getUserCompanyId();
                if ($userCompanyId && $assignment->employee && (int)$assignment->employee->company_id !== (int)$userCompanyId) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                $taskTitle = $assignment->task->title ?? 'Tugas';
                $empName = $assignment->employee->display_name ?? 'Karyawan';
                $assignment->delete();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => "Penugasan tugas \"{$taskTitle}\" untuk {$empName} berhasil dihapus."
                ]);
            }

            $employee = Karyawan::findOrFail($employeeId);

            $userCompanyId = $this->getUserCompanyId();
            if ($userCompanyId && (int)$employee->company_id !== (int)$userCompanyId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query = TaskAssignee::where('employee_id', $employee->id)
                ->whereHas('task', function ($q) {
                    $q->whereNull('parent_task_id');
                });

            if ($startDate && $endDate) {
                $query->where(function ($sub) use ($startDate, $endDate) {
                    $sub->whereBetween(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), [$startDate, $endDate])
                        ->orWhereBetween('task_assignees.due_date', [$startDate, $endDate]);
                });
            }

            $deletedCount = $query->delete();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Berhasil menghapus {$deletedCount} penugasan tugas untuk {$employee->display_name}."
                ]);
            }

            return redirect()->route('tasks.assignment.index')
                ->with('success', "Berhasil menghapus penugasan tugas untuk {$employee->display_name}.");
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus penugasan: ' . $th->getMessage());
        }
    }

    /**
     * Get detail list of assigned tasks for an employee (for AJAX modal)
     */
    public function detail(Request $request, $employeeId)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $employee = Karyawan::with(['company', 'department', 'position'])->findOrFail($employeeId);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $assignments = TaskAssignee::with(['task.category', 'task.priority'])
            ->where('employee_id', $employee->id)
            ->whereHas('task', function ($q) {
                $q->whereNull('parent_task_id');
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->where(function ($sub) use ($startDate, $endDate) {
                    $sub->whereBetween(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), [$startDate, $endDate])
                        ->orWhereBetween('task_assignees.due_date', [$startDate, $endDate])
                        ->orWhere(function ($overlap) use ($startDate, $endDate) {
                            $overlap->where('task_assignees.start_date', '<=', $endDate)
                                    ->where('task_assignees.due_date', '>=', $startDate);
                        });
                });
            })
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->display_name,
                'nik' => $employee->employee_number ?: ($employee->nik_ktp ?: '-'),
                'avatar_url' => $employee->avatar_url,
                'department' => $employee->department->name ?? 'Tanpa Departemen',
                'position' => $employee->position->name ?? ($employee->jabatan ?: '-'),
                'company' => $employee->company->name ?? '-',
            ],
            'assignments' => $assignments->map(function ($item) {
                return [
                    'id' => $item->id,
                    'task_id' => $item->task_id,
                    'task_number' => $item->task->task_number ?? '-',
                    'task_title' => $item->task->title ?? '-',
                    'task_type' => $item->task->task_type ?? 'insidentil',
                    'task_type_label' => $item->task->task_type_label ?? 'Insidentil',
                    'task_type_badge' => $item->task->task_type_badge_class ?? 'bg-gray-100 text-gray-700',
                    'category_name' => $item->task->category->name ?? '-',
                    'priority_name' => $item->task->priority->name ?? '-',
                    'priority_badge' => $item->task->priority->badge_class ?? 'bg-gray-100 text-gray-700',
                    'start_date' => $item->start_date ? $item->start_date->format('d M Y') : ($item->assigned_at ? $item->assigned_at->format('d M Y') : '-'),
                    'due_date' => $item->due_date ? $item->due_date->format('d M Y') : '-',
                    'is_overdue' => $item->is_overdue,
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'status_badge' => $item->status_badge_class,
                    'progress' => $item->progress,
                    'url' => route('tasks.show', $item->task_id),
                ];
            })
        ]);
    }

    /**
     * Display dedicated submission page for a specific task assignment / plotting record (task_assignees.id)
     */
    public function showSubmission($id)
    {
        if (!$this->hasAccess('tasks.update') && !$this->hasAccess('tasks.read')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat detail submission penugasan ini.');
        }

        $assignee = TaskAssignee::with([
            'task.category',
            'task.priority',
            'assigner',
            'employee.department',
            'employee.position',
            'submission.creator',
            'submission.stockUpdates.toko',
            'submission.stockUpdates.produk',
            'submission.dataEntries.toko',
            'submission.dataEntries.produk',
        ])->findOrFail($id);

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId && $assignee->employee && (int)$assignee->employee->company_id !== (int)$userCompanyId) {
            abort(403, 'Anda tidak memiliki akses ke penugasan karyawan perusahaan ini.');
        }

        $task = $assignee->task;
        $employee = $assignee->employee;
        $submission = $assignee->submission;

        return view('task::assignment.submission', compact('assignee', 'task', 'employee', 'submission'));
    }

    /**
     * Get submission detail for a specific task assignment / plotting record (task_assignees.id)
     */
    public function submissionDetail($id)
    {
        if (!$this->hasAccess('tasks.update') && !$this->hasAccess('tasks.read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $assignee = TaskAssignee::with([
            'task.category',
            'task.priority',
            'employee.department',
            'employee.position',
            'submission.creator',
            'submission.stockUpdates.toko',
            'submission.stockUpdates.produk',
            'submission.dataEntries',
        ])->findOrFail($id);

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId && $assignee->employee && (int)$assignee->employee->company_id !== (int)$userCompanyId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task = $assignee->task;
        $emp = $assignee->employee;
        $sub = $assignee->submission;

        // Stock updates mapping
        $stockUpdates = [];
        if ($sub && $sub->stockUpdates) {
            foreach ($sub->stockUpdates as $stk) {
                $stockUpdates[] = [
                    'toko'         => $stk->toko->name ?? '-',
                    'produk'       => $stk->produk->name ?? '-',
                    'status'       => $stk->stock_status,
                    'status_label' => $stk->stock_status_label,
                    'badge_class'  => $stk->stock_status_badge_class,
                    'stock'        => $stk->current_stock,
                    'notes'        => $stk->notes ?: '-',
                    'time'         => $stk->created_at ? $stk->created_at->format('d/m/Y H:i') : '-',
                ];
            }
        }

        // Data entries mapping
        $dataEntries = [];
        if ($sub && $sub->dataEntries) {
            foreach ($sub->dataEntries as $entry) {
                $dataEntries[] = [
                    'id'         => $entry->id,
                    'data'       => $entry->entry_data,
                    'created_at' => $entry->created_at ? $entry->created_at->format('d/m/Y H:i') : '-',
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'          => $assignee->id,
                'task_title'  => $task->title ?? '-',
                'task_type'   => $task->task_type_label ?? 'Insidentil',
                'output_type' => $task->output_type ?? 'checklist',
                'output_type_label' => $task->output_type_label ?? 'Checklist',
                'category'    => $task->category->name ?? 'Tanpa Kategori',
                'priority'    => $task->priority->name ?? 'Normal',
                'priority_badge' => $task->priority->badge_class ?? 'bg-gray-100 text-gray-700',
                'employee'    => [
                    'id'          => $emp->id ?? 0,
                    'name'        => $emp->display_name ?? '-',
                    'nik'         => $emp->employee_number ?: ($emp->nik_ktp ?: '-'),
                    'department'  => $emp->department->name ?? 'Tanpa Departemen',
                    'position'    => $emp->position->name ?? ($emp->jabatan ?: '-'),
                    'avatar_url'  => $emp->avatar_url ?? null,
                ],
                'status'         => $assignee->status,
                'status_label'   => $assignee->status_label,
                'status_badge'   => $assignee->status_badge_class,
                'progress'       => (int)$assignee->progress,
                'start_date'     => $assignee->effective_start_date ? $assignee->effective_start_date->format('d M Y') : '-',
                'due_date'       => $assignee->effective_due_date ? $assignee->effective_due_date->format('d M Y') : '-',
                'is_overdue'     => $assignee->is_overdue,
                'notes'          => $assignee->notes ?: '-',
                'submission'     => $sub ? [
                    'id'                  => $sub->id,
                    'status'              => $sub->status,
                    'status_label'        => $sub->status_label,
                    'progress'            => (int)$sub->progress,
                    'achieved_amount'     => $sub->achieved_amount,
                    'achieved_formatted'  => number_format($sub->achieved_amount, 0, ',', '.'),
                    'completion_url'      => $sub->completion_url,
                    'completion_file_url' => $sub->completion_file_url,
                    'completion_notes'    => $sub->completion_notes,
                    'completed_at'        => $sub->completed_at ? $sub->completed_at->format('d M Y, H:i') : null,
                    'stock_updates'       => $stockUpdates,
                    'data_entries'        => $dataEntries,
                    'creator_name'        => $sub->creator->nm_user ?? null,
                ] : null,
            ]
        ]);
    }

    /**
     * Legacy Ajax helpers (kept for backward compatibility)
     */
    public function getEmployeeTasks($employeeId)
    {
        $employee = Karyawan::findOrFail($employeeId);
        $assignedTasks = Task::with(['priority', 'category'])
            ->whereHas('assignees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })
            ->get();

        return response()->json(['status' => 'success', 'assigned_tasks' => $assignedTasks]);
    }

    public function assignTask(Request $request)
    {
        return response()->json(['status' => 'success']);
    }

    public function unassignTask(Request $request)
    {
        return response()->json(['status' => 'success']);
    }

    public function batchAssign(Request $request)
    {
        return response()->json(['status' => 'success']);
    }
}
