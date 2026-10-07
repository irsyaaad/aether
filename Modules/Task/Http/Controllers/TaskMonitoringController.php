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
use Modules\Task\Entities\TaskSubmission;
use Modules\Task\Services\TaskStatusService;
use Yajra\DataTables\Facades\DataTables;

class TaskMonitoringController extends Controller
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
     * Check if user has permission to view monitoring
     */
    protected function checkMonitoringAccess(): bool
    {
        return $this->hasAccess('tasks.monitoring.index')
            || $this->hasAccess('task-monitoring.index')
            || $this->hasAccess('tasks.update')
            || $this->hasAccess('tasks.index');
    }

    /**
     * Display Task Monitoring index: Real-time tracking of employee submissions per task plotting
     */
    public function index(Request $request)
    {
        if (!$this->checkMonitoringAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses halaman Monitoring Submission.');
        }

        $userCompanyId = $this->getUserCompanyId();

        $companies = $userCompanyId
            ? Company::where('id', $userCompanyId)->get()
            : Company::where('is_active', true)->orderBy('name')->get();

        $departments = Departemen::where('is_active', true)->orderBy('name')->get();

        // Default date range: current month
        $defaultStartDate = Carbon::now()->startOfMonth()->toDateString();
        $defaultEndDate = Carbon::now()->endOfMonth()->toDateString();

        // Stats calculation for KPI cards
        $statsQuery = TaskAssignee::whereHas('task', function ($q) {
            $q->whereNull('parent_task_id');
        })->whereHas('employee', function ($q) {
            $q->where('is_active', true);
        });

        if ($userCompanyId) {
            $statsQuery->whereHas('employee', function ($q) use ($userCompanyId) {
                $q->where('company_id', $userCompanyId);
            });
        }

        // Apply default date range for initial stats
        $statsQuery->where(function ($sub) use ($defaultStartDate, $defaultEndDate) {
            $sub->whereBetween(DB::raw('COALESCE(task_assignees.start_date, DATE(task_assignees.assigned_at))'), [$defaultStartDate, $defaultEndDate])
                ->orWhereBetween('task_assignees.due_date', [$defaultStartDate, $defaultEndDate])
                ->orWhere(function ($overlap) use ($defaultStartDate, $defaultEndDate) {
                    $overlap->where('task_assignees.start_date', '<=', $defaultEndDate)
                            ->where('task_assignees.due_date', '>=', $defaultStartDate);
                });
        });

        $totalPlotted = (clone $statsQuery)->count();
        $assignedCount = (clone $statsQuery)->where('status', TaskStatusService::STATUS_ASSIGNED)->count();
        $inProgressCount = (clone $statsQuery)->where('status', TaskStatusService::STATUS_IN_PROGRESS)->count();
        $completedCount = (clone $statsQuery)->where('status', TaskStatusService::STATUS_COMPLETED)->count();
        $overdueCount = (clone $statsQuery)->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED, TaskStatusService::STATUS_EXPIRED])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        $stats = [
            'total'       => $totalPlotted,
            'assigned'    => $assignedCount,
            'in_progress' => $inProgressCount,
            'completed'   => $completedCount,
            'overdue'     => $overdueCount,
        ];

        $statuses = TaskStatusService::getAvailableStatuses();

        $outputTypes = [
            'checklist'    => 'Checklist (Selesai/Belum)',
            'target'       => 'Target Angka / Nilai',
            'data_list'    => 'List Jawaban / Data',
            'stock_update' => 'Update Stock Kosong',
            'full_custom'  => 'Full Custom',
        ];

        return view('task::monitoring.index', compact(
            'companies',
            'departments',
            'statuses',
            'outputTypes',
            'stats',
            'userCompanyId',
            'defaultStartDate',
            'defaultEndDate'
        ));
    }

    /**
     * Server-side DataTables data for task submissions monitoring
     */
    public function getData(Request $request)
    {
        if (!$this->checkMonitoringAccess()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userCompanyId = $this->getUserCompanyId();
        $startDate = $request->filled('start_date') ? $request->start_date : null;
        $endDate = $request->filled('end_date') ? $request->end_date : null;

        $query = TaskAssignee::with([
            'task.category',
            'task.priority',
            'employee.department',
            'employee.position',
            'employee.company',
            'submission.stockUpdates',
            'submission.dataEntries',
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

        // Department filter
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('task_assignees.status', $request->status);
            }
        }

        // Output type filter
        if ($request->filled('output_type')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('output_type', $request->output_type);
            });
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

        $query->orderBy('task_assignees.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('task_info', function ($row) {
                $task = $row->task;
                if (!$task) {
                    return '<span class="text-xs text-gray-400 italic">Tugas Dihapus</span>';
                }
                $url = route('tasks.show', ['task' => $task->id, 'employee_id' => $row->employee_id]);
                $priorityBadge = $task->priority
                    ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border ' . $task->priority->badge_class . '">' . e($task->priority->name) . '</span>'
                    : '';
                $categoryName = $task->category ? e($task->category->name) : 'Tanpa Kategori';
                $typeBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border ' . ($task->task_type_badge_class ?? 'bg-gray-100 text-gray-700') . '">' . ($task->task_type_label ?? 'Insidentil') . '</span>';
                $outputBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">' . ($task->output_type_label ?? 'Checklist') . '</span>';

                return '<div class="flex flex-col max-w-xs space-y-1">
                            <div class="flex items-center flex-wrap gap-1">
                                <a href="' . $url . '" class="font-bold text-gray-900 text-xs sm:text-sm hover:text-primary transition-colors line-clamp-1" title="' . e($task->title) . '">' . e($task->title) . '</a>
                            </div>
                            <div class="flex items-center flex-wrap gap-1">
                                ' . $priorityBadge . '
                                ' . $typeBadge . '
                                ' . $outputBadge . '
                            </div>
                            <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
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
                                <span class="font-semibold text-gray-900 text-xs truncate">' . e($emp->display_name) . '</span>
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

                $startDate = $row->effective_start_date ? Carbon::parse($row->effective_start_date)->format('d M Y') : null;
                $startHtml = $startDate ? '<span class="text-[10px] text-gray-400">Mulai: ' . $startDate . '</span>' : '';

                return '<div class="flex flex-col text-xs ' . $colorClass . '">
                            <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i> ' . $dateFormatted . '</span>
                            ' . $startHtml . '
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
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border ' . $row->status_badge_class . '">
                            ' . e($row->status_label) . '
                        </span>';
            })
            ->addColumn('submission_preview', function ($row) {
                $sub = $row->submission;
                $task = $row->task;

                if (!$sub && $row->progress == 0) {
                    return '<span class="text-xs text-gray-400 italic">Belum ada submitan</span>';
                }

                $items = [];

                // Output metrics snippet
                if ($task && $task->output_type === 'target') {
                    $achieved = $sub ? number_format($sub->achieved_amount, 0, ',', '.') : 0;
                    $items[] = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <i data-lucide="target" class="w-3 h-3 text-amber-600"></i> Capaian: ' . $achieved . '
                                </span>';
                } elseif ($task && $task->output_type === 'stock_update') {
                    $kosongCount = $sub ? $sub->stockUpdates->where('stock_status', 'kosong')->count() : 0;
                    $items[] = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                    <i data-lucide="package-x" class="w-3 h-3 text-rose-600"></i> ' . $kosongCount . ' Produk Kosong
                                </span>';
                } elseif ($task && $task->output_type === 'data_list') {
                    $dataCount = $sub ? $sub->dataEntries->count() : 0;
                    $items[] = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    <i data-lucide="table" class="w-3 h-3 text-blue-600"></i> ' . $dataCount . ' Baris Data
                                </span>';
                }

                // Proof items (File, URL, Notes)
                if ($sub) {
                    if ($sub->completion_file) {
                        $items[] = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <i data-lucide="image" class="w-3 h-3 text-emerald-600"></i> Foto Bukti
                                    </span>';
                    }
                    if ($sub->completion_url) {
                        $items[] = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                        <i data-lucide="link" class="w-3 h-3 text-sky-600"></i> Tautan Link
                                    </span>';
                    }
                    if ($sub->completion_notes) {
                        $escapedNotes = e(mb_strimwidth($sub->completion_notes, 0, 40, '...'));
                        $items[] = '<span class="text-[11px] text-gray-500 italic block truncate max-w-[180px]" title="' . e($sub->completion_notes) . '">
                                        "' . $escapedNotes . '"
                                    </span>';
                    }
                    if ($sub->completed_at) {
                        $items[] = '<span class="text-[10px] text-gray-400 font-mono block">
                                        Selesai: ' . $sub->completed_at->format('d/m/Y H:i') . '
                                    </span>';
                    }
                }

                if (empty($items)) {
                    return '<span class="text-xs text-gray-400 italic">Dalam proses</span>';
                }

                return '<div class="flex flex-col gap-1">' . implode('', $items) . '</div>';
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('tasks.show', ['task' => $row->task_id, 'employee_id' => $row->employee_id]);

                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" onclick="showSubmissionModal(' . $row->id . ')" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm" title="Lihat Bukti Pengerjaan">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span>Lihat Bukti</span>
                        </button>
                        <a href="' . $detailUrl . '" target="_blank" class="inline-flex items-center gap-1 px-2 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm" title="Buka Detail Tugas di Tab Baru">
                            <i data-lucide="external-link" class="w-3.5 h-3.5 text-gray-500"></i>
                        </a>
                    </div>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->filled('search.value')) {
                    $search = $request->input('search.value');
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('task', function ($qt) use ($search) {
                            $qt->where('title', 'like', "%{$search}%");
                        })->orWhereHas('employee', function ($qe) use ($search) {
                            $qe->where('name', 'like', "%{$search}%")
                               ->orWhere('nama_lengkap', 'like', "%{$search}%")
                               ->orWhere('employee_number', 'like', "%{$search}%")
                               ->orWhere('nik_ktp', 'like', "%{$search}%");
                        });
                    });
                }
            })
            ->rawColumns(['task_info', 'employee_info', 'due_date', 'progress', 'status', 'submission_preview', 'action'])
            ->make(true);
    }

    /**
     * Get complete detail of an employee submission for the modal popup
     */
    public function detail($id)
    {
        if (!$this->checkMonitoringAccess()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $assignee = TaskAssignee::with([
            'task.category',
            'task.priority',
            'employee.department',
            'employee.position',
            'employee.company',
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
                'custom_fields'     => $task->custom_fields ?? [],
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
                    'custom_data'         => $sub->custom_data ?? null,
                    'completed_at'        => $sub->completed_at ? $sub->completed_at->format('d M Y, H:i') : null,
                    'stock_updates'       => $stockUpdates,
                    'data_entries'        => $dataEntries,
                    'creator_name'        => $sub->creator->nm_user ?? null,
                ] : null,
                'task_url' => route('tasks.show', ['task' => $task->id, 'employee_id' => $emp->id ?? null]),
            ]
        ]);
    }
}
