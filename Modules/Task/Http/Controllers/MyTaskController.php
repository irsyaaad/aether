<?php

namespace Modules\Task\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Master\Entities\Karyawan;
use Modules\Master\Entities\Produk;
use Modules\Master\Entities\Toko;
use Modules\Task\Entities\Task;
use Modules\Task\Entities\TaskAssignee;
use Modules\Task\Entities\TaskCategory;
use Modules\Task\Entities\TaskDataEntry;
use Modules\Task\Entities\TaskPriority;
use Modules\Task\Entities\TaskStockUpdate;
use Modules\Task\Entities\TaskSubmission;
use Modules\Task\Services\TaskRecurringService;
use Modules\Task\Services\TaskStatusService;
use Yajra\DataTables\Facades\DataTables;

class MyTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Resolve employee associated with current user
     */
    protected function getEmployeeForUser(): ?Karyawan
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // 1. Direct karyawan_id on users table
        if (!empty($user->karyawan_id)) {
            $emp = Karyawan::find($user->karyawan_id);
            if ($emp) {
                return $emp;
            }
        }

        // 2. Match by email
        if (!empty($user->email)) {
            $emp = Karyawan::where('email', $user->email)->first();
            if ($emp) {
                return $emp;
            }
        }

        // 3. Match by name
        if (!empty($user->nm_user)) {
            $emp = Karyawan::where('name', $user->nm_user)
                ->orWhere('nama_lengkap', $user->nm_user)
                ->first();
            if ($emp) {
                return $emp;
            }
        }

        return null;
    }

    /**
     * Resolve task assignment (plotting) record for employee.
     * Supports either task_assignees.id (primary) or tasks.id (fallback).
     * Handles active vs history requests gracefully.
     */
    protected function resolveAssignee($id, Karyawan $employee): ?TaskAssignee
    {
        $isHistoryRequest = request()->has('history') || request()->input('view') === 'history';

        // 1. Primary: resolve by task_assignees.id
        $assignee = TaskAssignee::with([
            'task.category',
            'task.priority',
            'task.creator',
            'task.assignedEmployees',
        ])
        ->where('employee_id', $employee->id)
        ->where('id', $id)
        ->first();

        if ($assignee) {
            // If viewing a completed record without ?view=history, but an active uncompleted assignment
            // exists for this master task, route to the active assignment so user gets the active workspace
            if (!$isHistoryRequest && $assignee->status === TaskStatusService::STATUS_COMPLETED) {
                $activeSibling = TaskAssignee::with([
                    'task.category',
                    'task.priority',
                    'task.creator',
                    'task.assignedEmployees',
                ])
                ->where('employee_id', $employee->id)
                ->where('task_id', $assignee->task_id)
                ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
                ->latest('id')
                ->first();

                if ($activeSibling) {
                    return $activeSibling;
                }
            }

            return $assignee;
        }

        // 2. Fallback: resolve by tasks.id (prefer active uncompleted first, then latest completed)
        $activeByTask = TaskAssignee::with([
            'task.category',
            'task.priority',
            'task.creator',
            'task.assignedEmployees',
        ])
        ->where('employee_id', $employee->id)
        ->where('task_id', $id)
        ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
        ->latest('id')
        ->first();

        if ($activeByTask) {
            return $activeByTask;
        }

        return TaskAssignee::with([
            'task.category',
            'task.priority',
            'task.creator',
            'task.assignedEmployees',
        ])
        ->where('employee_id', $employee->id)
        ->where('task_id', $id)
        ->latest('id')
        ->first();
    }

    /**
     * Auto-repair corrupted assignments where a completed historical assignment
     * was accidentally overwritten during re-plotting.
     * Restores yesterday's completed assignment and ensures an active clean assignment exists for today.
     */
    protected function repairHistoricalAssignments(int $employeeId): void
    {
        // 0. Ensure unique constraint on task_assignees is safely removed in DB
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('task_assignees')) {
                \Illuminate\Support\Facades\Schema::table('task_assignees', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->dropUnique(['task_id', 'employee_id']);
                });
            }
        } catch (\Throwable $e) {
            // Constraint already dropped or does not exist
        }

        // 1. Re-link any orphaned submissions where task_assignee_id is NULL
        $orphanedSubmissions = TaskSubmission::where('employee_id', $employeeId)
            ->whereNull('task_assignee_id')
            ->get();

        foreach ($orphanedSubmissions as $orphan) {
            $matchingAssignee = TaskAssignee::where('task_id', $orphan->task_id)
                ->where('employee_id', $employeeId)
                ->whereDoesntHave('submission')
                ->latest('id')
                ->first();

            if ($matchingAssignee) {
                $orphan->task_assignee_id = $matchingAssignee->id;
                $orphan->save();
                TaskDataEntry::where('task_submission_id', $orphan->id)->whereNull('task_assignee_id')->update(['task_assignee_id' => $matchingAssignee->id]);
                TaskStockUpdate::where('task_submission_id', $orphan->id)->whereNull('task_assignee_id')->update(['task_assignee_id' => $matchingAssignee->id]);
            }
        }

        // 2. One-time recovery for assignments corrupted by the previous 50% bug
        $corrupted50 = TaskAssignee::with(['submission', 'dataEntries'])
            ->where('employee_id', $employeeId)
            ->where('progress', 50)
            ->where('status', TaskStatusService::STATUS_IN_PROGRESS)
            ->whereHas('task', function ($q) {
                $q->where('output_type', 'data_list');
            })
            ->get();

        foreach ($corrupted50 as $buggy) {
            // Restore the completed assignment
            $buggy->status = TaskStatusService::STATUS_COMPLETED;
            $buggy->progress = 100;
            $buggy->completed_at = now();
            $buggy->save();

            if ($buggy->submission) {
                $buggy->submission->status = TaskStatusService::STATUS_COMPLETED;
                $buggy->submission->progress = 100;
                $buggy->submission->completed_at = now();
                $buggy->submission->save();
            }

            // Ensure the re-plotted active assignment exists for today
            $hasActive = TaskAssignee::where('task_id', $buggy->task_id)
                ->where('employee_id', $employeeId)
                ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
                ->exists();

            if (!$hasActive) {
                $newAssignee = TaskAssignee::create([
                    'task_id'      => $buggy->task_id,
                    'employee_id'  => $employeeId,
                    'assigned_at'  => now(),
                    'assigned_by'  => $buggy->assigned_by ?? 1,
                    'start_date'   => now()->toDateString(),
                    'due_date'     => $buggy->due_date ?: now()->toDateString(),
                    'status'       => TaskStatusService::STATUS_ASSIGNED,
                    'progress'     => 0,
                    'completed_at' => null,
                    'notes'        => $buggy->notes,
                ]);

                TaskSubmission::create([
                    'task_assignee_id' => $newAssignee->id,
                    'task_id'          => $buggy->task_id,
                    'employee_id'      => $employeeId,
                    'created_by'       => $buggy->assigned_by ?? 1,
                    'work_date'        => now()->toDateString(),
                    'status'           => TaskStatusService::STATUS_ASSIGNED,
                    'progress'         => 0,
                ]);
            }
        }
    }

    /**
     * Auto-expire assignments that have passed due date without being completed
     */
    protected function autoExpireOverdueAssignments(?int $employeeId = null): void
    {
        $query = TaskAssignee::whereNotIn('status', [
            TaskStatusService::STATUS_COMPLETED,
            TaskStatusService::STATUS_CANCELLED,
            TaskStatusService::STATUS_EXPIRED,
        ])
        ->whereNotNull('due_date')
        ->whereDate('due_date', '<', now()->toDateString());

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $query->update(['status' => TaskStatusService::STATUS_EXPIRED]);
    }

    /**
     * Display list of tasks assigned to logged in employee
     */
    public function index()
    {
        $employee = $this->getEmployeeForUser();
        $employeeId = $employee ? $employee->id : 0;

        // Auto-repair any overwritten assignments and auto-expire overdue assignments
        if ($employeeId) {
            $this->repairHistoricalAssignments($employeeId);
            $this->autoExpireOverdueAssignments($employeeId);
        }

        // Statistics for this employee based on actionable task_assignees
        $baseQuery = TaskAssignee::actionable()->where('employee_id', $employeeId);

        $totalTasks = (clone $baseQuery)->count();

        $dailyTasksToday = (clone $baseQuery)
            ->whereHas('task', function ($q) {
                $q->where('task_type', TaskRecurringService::TYPE_HARIAN);
            })
            ->whereDate('due_date', now()->toDateString())
            ->count();

        $completedTasks = (clone $baseQuery)
            ->where('status', TaskStatusService::STATUS_COMPLETED)
            ->count();

        $expiredTasks = (clone $baseQuery)
            ->where('status', TaskStatusService::STATUS_EXPIRED)
            ->count();

        $activeTasks = (clone $baseQuery)
            ->whereIn('status', [TaskStatusService::STATUS_ASSIGNED, TaskStatusService::STATUS_IN_PROGRESS])
            ->count();

        $stats = [
            'total'       => $totalTasks,
            'active'      => $activeTasks,
            'daily_today' => $dailyTasksToday,
            'completed'   => $completedTasks,
            'expired'     => $expiredTasks,
        ];

        $categories = TaskCategory::where('is_active', true)->orderBy('name')->get();
        $priorities = TaskPriority::where('is_active', true)->orderBy('level')->get();
        $statuses = TaskStatusService::getAvailableStatuses();
        $taskTypes = TaskRecurringService::getTypes();

        return view('task::my-tasks.index', compact('employee', 'stats', 'categories', 'priorities', 'statuses', 'taskTypes'));
    }

    /**
     * DataTables endpoint for employee's assigned tasks
     */
    public function getData(Request $request)
    {
        $employee = $this->getEmployeeForUser();
        if (!$employee) {
            return DataTables::of(TaskAssignee::whereRaw('1 = 0'))
                ->addIndexColumn()
                ->make(true);
        }

        $employeeId = $employee->id;

        // Auto-repair any overwritten assignments and ensure any overdue assignment is marked expired
        $this->repairHistoricalAssignments($employeeId);
        $this->autoExpireOverdueAssignments($employeeId);

        $query = TaskAssignee::actionable()
            ->with([
                'task.category',
                'task.priority',
                'task.creator',
            ])
            ->where('employee_id', $employeeId);

        // Tab Filter: active (default), completed, expired, all
        $tab = $request->input('tab', 'active');
        if ($tab === 'active') {
            $query->whereIn('status', [TaskStatusService::STATUS_ASSIGNED, TaskStatusService::STATUS_IN_PROGRESS]);
        } elseif ($tab === 'completed') {
            $query->where('status', TaskStatusService::STATUS_COMPLETED);
        } elseif ($tab === 'expired') {
            $query->where('status', TaskStatusService::STATUS_EXPIRED);
        }
        // If tab is 'all', do not constrain by status unless specified in filters below

        // Filter: Specific Status Dropdown (overrides or refines if selected)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Task Type
        if ($request->filled('task_type')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('task_type', $request->task_type);
            });
        }

        // Filter: Output Type
        if ($request->filled('output_type')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('output_type', $request->output_type);
            });
        }

        // Filter: Priority
        if ($request->filled('priority_id')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('priority_id', $request->priority_id);
            });
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter: Today only
        if ($request->filled('today_only') && $request->today_only == '1') {
            $query->whereDate('due_date', now()->toDateString());
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('task_number', function ($assignee) {
                $task = $assignee->task;
                if (!$task) return '-';
                $url = route('tasks.my-tasks.show', $assignee->id);
                if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
                    $url .= '?view=history';
                }
                return '<a href="' . $url . '" class="font-mono text-xs font-bold text-primary hover:underline">' . e($task->task_number) . '</a>';
            })
            ->addColumn('title', function ($assignee) {
                $task = $assignee->task;
                if (!$task) return '-';
                $url = route('tasks.my-tasks.show', $assignee->id);
                if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
                    $url .= '?view=history';
                }
                $html = '<div class="flex flex-col gap-1">';
                $html .= '<a href="' . $url . '" class="font-semibold text-xs text-gray-900 hover:text-primary transition-colors line-clamp-1">' . e($task->title) . '</a>';

                $html .= '<div class="flex flex-wrap items-center gap-1.5">';
                // Type badge
                $typeBadge = match ($task->task_type) {
                    'harian'    => '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200"><i data-lucide="repeat" class="w-3 h-3 mr-0.5 inline"></i> Harian</span>',
                    'bulanan'   => '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200"><i data-lucide="calendar" class="w-3 h-3 mr-0.5 inline"></i> Bulanan</span>',
                    default     => '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200"><i data-lucide="zap" class="w-3 h-3 mr-0.5 inline"></i> Insidentil</span>',
                };
                $html .= $typeBadge;

                // Output type badge
                $outputBadge = match ($task->output_type) {
                    'target'       => '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Target Angka</span>',
                    'data_list'    => '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">List Data</span>',
                    'stock_update' => '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Update Stok</span>',
                    default        => '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Checklist</span>',
                };
                $html .= $outputBadge;

                if ($task->category) {
                    $html .= '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600">' . e($task->category->name) . '</span>';
                }

                $html .= '</div></div>';
                return $html;
            })
            ->addColumn('priority_badge', function ($assignee) {
                $task = $assignee->task;
                if (!$task || !$task->priority) return '-';
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold ' . $task->priority->badge_class . '">' . e($task->priority->name) . '</span>';
            })
            ->addColumn('due_date', function ($assignee) {
                $dueDate = $assignee->effective_due_date;
                if (!$dueDate) return '<span class="text-gray-400 text-xs">-</span>';
                $formatted = $dueDate->format('d M Y');
                if ($assignee->is_overdue) {
                    return '<span class="text-red-600 font-bold text-xs flex items-center gap-1"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> ' . $formatted . '</span>';
                }
                return '<span class="text-gray-600 text-xs font-mono">' . $formatted . '</span>';
            })
            ->addColumn('progress', function ($assignee) {
                $pct = $assignee->progress ?? 0;
                $color = $pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-blue-500' : 'bg-primary');
                return '
                    <div class="w-24">
                        <div class="flex items-center justify-between text-[10px] font-semibold text-gray-600 mb-0.5">
                            <span>' . $pct . '%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div class="' . $color . ' h-1.5 rounded-full" style="width: ' . min(100, $pct) . '%"></div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('status', function ($assignee) {
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold ' . $assignee->status_badge_class . '">' . e($assignee->status_label) . '</span>';
            })
            ->addColumn('action', function ($assignee) {
                $task = $assignee->task;
                if (!$task) return '';
                $url = route('tasks.my-tasks.show', $assignee->id);
                if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
                    return '
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="' . $url . '?view=history" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors shadow-2xs">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                Lihat Hasil
                            </a>
                        </div>
                    ';
                }
                if ($assignee->status === TaskStatusService::STATUS_EXPIRED) {
                    return '
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="' . $url . '" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors shadow-2xs">
                                <i data-lucide="eye" class="w-3.5 h-3.5 text-gray-500"></i>
                                Rincian
                            </a>
                        </div>
                    ';
                }
                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="' . $url . '" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg shadow-xs transition-colors">
                            <i data-lucide="play-circle" class="w-3.5 h-3.5"></i>
                            Kerjakan
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['task_number', 'title', 'priority_badge', 'due_date', 'progress', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show task execution workspace for employee
     */
    public function show($id)
    {
        $employee = $this->getEmployeeForUser();
        if (!$employee) {
            abort(403, 'Akun Anda belum terhubung dengan data karyawan.');
        }

        // Auto-repair any overwritten assignments
        $this->repairHistoricalAssignments($employee->id);

        $assignee = $this->resolveAssignee($id, $employee);
        if (!$assignee || !$assignee->task) {
            abort(404, 'Penugasan tugas tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // If accessed with master task_id or an old completed assignee ID (and not requesting history),
        // redirect to the active assignment URL so the URL reflects the actual plotting ID
        if ($assignee->id != (int)$id) {
            return redirect()->route('tasks.my-tasks.show', array_merge(['id' => $assignee->id], request()->query()));
        }

        $task = $assignee->task;

        // Check if this assignment has passed due date without being completed
        if ($assignee->effective_due_date && \Carbon\Carbon::parse($assignee->effective_due_date)->startOfDay()->lt(now()->startOfDay()) && !in_array($assignee->status, [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED, TaskStatusService::STATUS_EXPIRED])) {
            $assignee->status = TaskStatusService::STATUS_EXPIRED;
            $assignee->save();
        }

        $availableTokos = $task->selected_tokos_models;
        $availableProduks = $task->selected_produks_models;

        // Find or initialize submission record for this specific assignment instance
        $submission = TaskSubmission::where('task_assignee_id', $assignee->id)->first();
        if (!$submission) {
            $submission = TaskSubmission::create([
                'task_assignee_id' => $assignee->id,
                'task_id'          => $task->id,
                'employee_id'      => $employee->id,
                'created_by'       => Auth::user()->id_user ?? Auth::id(),
                'work_date'        => $assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString(),
                'status'           => $assignee->status ?: TaskStatusService::STATUS_ASSIGNED,
                'progress'         => (int)$assignee->progress,
            ]);
        }

        // For active assigned tasks, guarantee a completely clean initial state
        if ($assignee->status === TaskStatusService::STATUS_ASSIGNED && $assignee->progress == 0) {
            if ($submission->completed_at !== null || $submission->status === TaskStatusService::STATUS_COMPLETED || $submission->progress > 0) {
                $submission->update([
                    'status'           => TaskStatusService::STATUS_ASSIGNED,
                    'progress'         => 0,
                    'achieved_amount'  => 0,
                    'completion_url'   => null,
                    'completion_file'  => null,
                    'completion_notes' => null,
                    'completed_at'     => null,
                    'work_date'        => $assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString(),
                ]);
                TaskDataEntry::where('task_submission_id', $submission->id)->delete();
                TaskStockUpdate::where('task_submission_id', $submission->id)->delete();
                $submission->refresh();
            }
        }

        if ($assignee->status === TaskStatusService::STATUS_EXPIRED && $submission->status !== TaskStatusService::STATUS_COMPLETED) {
            $submission->status = TaskStatusService::STATUS_EXPIRED;
            $submission->save();
        }

        // Sibling assignment links (for seamless navigation between today's active task and historical completed task)
        $activeSibling = null;
        $completedSibling = null;

        if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
            $activeSibling = TaskAssignee::where('employee_id', $employee->id)
                ->where('task_id', $task->id)
                ->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED])
                ->latest('id')
                ->first();
        } else {
            $completedSibling = TaskAssignee::where('employee_id', $employee->id)
                ->where('task_id', $task->id)
                ->where('status', TaskStatusService::STATUS_COMPLETED)
                ->latest('id')
                ->first();
        }

        // Strictly isolate stock updates and data entries to this employee's submission
        $myStockUpdates = TaskStockUpdate::where('task_submission_id', $submission->id)->get();

        $myDataEntries = TaskDataEntry::with(['toko', 'produk', 'employee', 'creator'])
            ->where('task_submission_id', $submission->id)
            ->get();

        return view('task::my-tasks.show', compact('task', 'employee', 'assignee', 'availableTokos', 'availableProduks', 'submission', 'myStockUpdates', 'myDataEntries', 'activeSibling', 'completedSibling'));
    }

    /**
     * Complete or toggle checklist task status with proof (URL or Photo) in task_submissions
     */
    public function completeTask(Request $request, $id)
    {
        TaskSubmission::ensureSchema();

        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $assignee = $this->resolveAssignee($id, $employee);
            if (!$assignee || !$assignee->task) {
                return response()->json(['status' => 'error', 'message' => 'Penugasan tugas tidak ditemukan.'], 404);
            }
            $task = $assignee->task;

            $submission = TaskSubmission::firstOrCreate(
                [
                    'task_assignee_id' => $assignee->id,
                ],
                [
                    'task_id'     => $task->id,
                    'employee_id' => $employee->id,
                    'created_by'  => Auth::user()->id_user ?? Auth::id(),
                    'work_date'   => $assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString(),
                ]
            );

            // Action: Cancel completion / Buka Kunci untuk Edit (kembalikan ke sedang dikerjakan)
            if ($request->has('uncomplete') && $request->uncomplete == '1') {
                $submission->status = TaskStatusService::STATUS_IN_PROGRESS;
                $submission->progress = 50;
                $submission->completed_at = null;
                // Preserve custom_data for full_custom tasks so previously entered data can be edited
                if ($task->output_type !== 'full_custom') {
                    $submission->completion_url = null;
                    $submission->completion_file = null;
                    $submission->completion_notes = null;
                }
                $submission->save();

                $assignee->status = TaskStatusService::STATUS_IN_PROGRESS;
                $assignee->progress = 50;
                $assignee->completed_at = null;
                $assignee->save();

                if ($task->task_type === 'sekali' && !$task->is_template) {
                    $avgProgress = (int) round(TaskAssignee::where('task_id', $task->id)->avg('progress') ?? 0);
                    $task->progress = $avgProgress;
                    $task->status = TaskStatusService::STATUS_IN_PROGRESS;
                    $task->completed_at = null;
                    $task->save();
                }

                DB::commit();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status'     => 'success',
                        'message'    => 'Kunci tugas dibuka. Tugas dikembalikan ke status Sedang Dikerjakan agar dapat diedit.',
                        'submission' => $submission,
                        'progress'   => $submission->progress,
                    ]);
                }

                return redirect()->back()->with('success', 'Kunci tugas dibuka. Tugas dikembalikan ke status Sedang Dikerjakan agar dapat diedit.');
            }

            // Check if already completed and not uncompleting
            if (($submission->status === TaskStatusService::STATUS_COMPLETED || $assignee->status === TaskStatusService::STATUS_COMPLETED) && !$request->has('uncomplete')) {
                $errMsg = 'Tugas sudah selesai. Submission telah dikunci. Klik "Edit Kembali Data" jika ingin mengubahnya.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $errMsg], 422);
                }
                return redirect()->back()->with('error', $errMsg);
            }

            // Validation for proof (URL, File, Notes)
            $request->validate([
                'completion_url'   => 'nullable|url|max:2000',
                'completion_file'  => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
                'completion_notes' => 'nullable|string|max:1000',
            ]);

            // Handle Full Custom submissions (Multi-Row Support)
            if ($task->output_type === 'full_custom') {
                $customFields = $task->custom_fields ?? [];
                $rawCustomData = (array) $request->input('custom_data', []);

                // Determine if request is multi-row (e.g. custom_data[0][field_key]) or single-row (custom_data[field_key])
                $firstKey = array_key_first($rawCustomData);
                $isMultiRow = ($firstKey !== null && is_numeric($firstKey));

                $existingSubmissionData = $submission->custom_data ?? [];
                // Normalize existing data to array of rows
                $existingRows = [];
                if (!empty($existingSubmissionData) && is_array($existingSubmissionData)) {
                    $firstExistKey = array_key_first($existingSubmissionData);
                    if ($firstExistKey !== null && is_numeric($firstExistKey)) {
                        $existingRows = $existingSubmissionData;
                    } else {
                        $existingRows = [ $existingSubmissionData ];
                    }
                }

                $rowsToProcess = $isMultiRow ? array_values($rawCustomData) : [ $rawCustomData ];
                if (empty($rowsToProcess)) {
                    $rowsToProcess = [ [] ];
                }

                $processedRows = [];

                foreach ($rowsToProcess as $rIdx => $rowInputs) {
                    $rowNumber = $rIdx + 1;
                    $currentRowData = [];

                    foreach ($customFields as $cf) {
                        $key = $cf['key'] ?? '';
                        $label = $cf['label'] ?? 'Field';
                        $isRequired = !empty($cf['required']);
                        $type = $cf['type'] ?? 'text';

                        if ($type === 'file') {
                            // Check file upload
                            $uploadedFileObj = null;
                            if ($isMultiRow) {
                                if ($request->hasFile("custom_files.{$rIdx}.{$key}")) {
                                    $uploadedFileObj = $request->file("custom_files.{$rIdx}.{$key}");
                                }
                            } else {
                                if ($request->hasFile("custom_files.{$key}")) {
                                    $uploadedFileObj = $request->file("custom_files.{$key}");
                                }
                            }

                            if ($uploadedFileObj && $uploadedFileObj->isValid()) {
                                $path = $uploadedFileObj->store('tasks/custom_files', 'public');
                                $currentRowData[$key] = [
                                    'type'          => 'file',
                                    'path'          => $path,
                                    'original_name' => $uploadedFileObj->getClientOriginalName(),
                                    'url'           => \Illuminate\Support\Facades\Storage::url($path),
                                ];
                            } else {
                                // Check if existing file is available in previously saved data
                                $prevFile = $existingRows[$rIdx][$key] ?? null;
                                if (is_array($prevFile) && ($prevFile['type'] ?? '') === 'file' && !empty($prevFile['url'])) {
                                    $currentRowData[$key] = $prevFile;
                                } else {
                                    $currentRowData[$key] = null;
                                    if ($isRequired) {
                                        $msg = "Baris ke-{$rowNumber}: Field '{$label}' wajib mengunggah file.";
                                        if ($request->ajax() || $request->wantsJson()) {
                                            return response()->json(['status' => 'error', 'message' => $msg], 422);
                                        }
                                        return redirect()->back()->withInput()->with('error', $msg);
                                    }
                                }
                            }
                        } else {
                            $val = trim((string)($rowInputs[$key] ?? ''));
                            if ($isRequired && $val === '') {
                                $msg = "Baris ke-{$rowNumber}: Field '{$label}' wajib diisi.";
                                if ($request->ajax() || $request->wantsJson()) {
                                    return response()->json(['status' => 'error', 'message' => $msg], 422);
                                }
                                return redirect()->back()->withInput()->with('error', $msg);
                            }
                            $currentRowData[$key] = $val;
                        }
                    }

                    $processedRows[] = $currentRowData;
                }

                $submission->custom_data = $processedRows;
            }

            // Save uploaded proof photo if provided
            if ($request->hasFile('completion_file')) {
                $file = $request->file('completion_file');
                $path = $file->store('tasks/completion_proofs', 'public');
                $submission->completion_file = $path;
            }

            if ($request->filled('completion_url')) {
                $submission->completion_url = $request->completion_url;
            }

            if ($request->filled('completion_notes')) {
                $submission->completion_notes = $request->completion_notes;
            }

            $isMarkCompleted = true;
            if ($task->output_type === 'full_custom') {
                // If Full Custom: only mark completed if user explicitly clicked "Tandai Selesai"
                $isMarkCompleted = ($request->input('mark_complete') == '1' || $request->input('action') === 'complete');
            }

            if ($isMarkCompleted) {
                $submission->status = TaskStatusService::STATUS_COMPLETED;
                $submission->progress = 100;
                $submission->completed_at = now();
                $submission->save();

                $assignee->status = TaskStatusService::STATUS_COMPLETED;
                $assignee->progress = 100;
                $assignee->completed_at = now();
                $assignee->save();
            } else {
                $submission->status = TaskStatusService::STATUS_IN_PROGRESS;
                $submission->progress = 50;
                $submission->completed_at = null;
                $submission->save();

                $assignee->status = TaskStatusService::STATUS_IN_PROGRESS;
                $assignee->progress = 50;
                $assignee->completed_at = null;
                $assignee->save();
            }

            // Sync parent task status & progress ONLY for one-time non-template tasks
            if ($task->task_type === 'sekali' && !$task->is_template) {
                $totalAssignees = TaskAssignee::where('task_id', $task->id)->count();
                if ($totalAssignees > 0) {
                    $completedAssignees = TaskAssignee::where('task_id', $task->id)
                        ->where('status', TaskStatusService::STATUS_COMPLETED)
                        ->count();
                    $avgProgress = (int) round(TaskAssignee::where('task_id', $task->id)->avg('progress') ?? 0);
                    $task->progress = $avgProgress;
                    if ($completedAssignees >= $totalAssignees && $totalAssignees > 0) {
                        $task->status = TaskStatusService::STATUS_COMPLETED;
                        $task->completed_at = now();
                    } else {
                        $task->status = TaskStatusService::STATUS_IN_PROGRESS;
                        $task->completed_at = null;
                    }
                    $task->save();
                }
            }

            DB::commit();

            $successMsg = $isMarkCompleted ? 'Selamat! Tugas berhasil diselesaikan.' : 'Data pengerjaan berhasil disimpan. Tugas masih berstatus Sedang Dikerjakan.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'          => 'success',
                    'message'         => $successMsg,
                    'is_completed'    => $isMarkCompleted,
                    'submission'      => $submission,
                    'progress'        => $submission->progress,
                    'completion_file' => $submission->completion_file_url,
                    'completion_url'  => $submission->completion_url,
                ]);
            }

            return redirect()->back()->with('success', $successMsg);
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyelesaikan tugas: ' . $th->getMessage());
        }
    }

    /**
     * Update target realization value in task_submissions
     */
    public function updateTarget(Request $request, $id)
    {
        $request->validate([
            'achieved_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $assignee = $this->resolveAssignee($id, $employee);
            if (!$assignee || !$assignee->task) {
                return response()->json(['status' => 'error', 'message' => 'Penugasan tugas tidak ditemukan.'], 404);
            }

            if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $task = $assignee->task;

            $submission = TaskSubmission::firstOrCreate(
                [
                    'task_assignee_id' => $assignee->id,
                ],
                [
                    'task_id'     => $task->id,
                    'employee_id' => $employee->id,
                    'created_by'  => Auth::user()->id_user ?? Auth::id(),
                    'work_date'   => $assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString(),
                ]
            );

            $submission->achieved_amount = (float)$request->achieved_amount;
            $target = max(1, (float)$task->target_amount);
            $submission->progress = min(100, (int)round(($submission->achieved_amount / $target) * 100));

            if ($submission->progress >= 100) {
                $submission->status = TaskStatusService::STATUS_COMPLETED;
                $submission->completed_at = now();
            } else {
                $submission->status = TaskStatusService::STATUS_IN_PROGRESS;
            }
            $submission->save();

            $assignee->status = $submission->status;
            $assignee->progress = $submission->progress;
            $assignee->completed_at = $submission->completed_at;
            $assignee->save();

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => 'Capaian target berhasil diperbarui.',
                'progress'   => $submission->progress,
                'achieved'   => number_format($submission->achieved_amount, 0, ',', '.'),
                'submission' => $submission,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Store data entry for data_list tasks
     */
    public function storeDataEntry(Request $request, $id)
    {
        $request->validate([
            'entry_data'   => 'required|array',
            'entry_data.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $assignee = $this->resolveAssignee($id, $employee);
            if (!$assignee || !$assignee->task) {
                return response()->json(['status' => 'error', 'message' => 'Penugasan tugas tidak ditemukan.'], 404);
            }

            if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $task = $assignee->task;

            $user = Auth::user();

            $submission = TaskSubmission::firstOrCreate(
                [
                    'task_assignee_id' => $assignee->id,
                ],
                [
                    'task_id'     => $task->id,
                    'employee_id' => $employee->id,
                    'created_by'  => $user->id_user ?? Auth::id(),
                    'work_date'   => $assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString(),
                ]
            );

            if ($submission->status === TaskStatusService::STATUS_COMPLETED) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $entryData = $request->input('entry_data', []);
            $tokoId = $request->input('toko_id');
            $produkId = $request->input('produk_id') ?? $request->input('barang_id');

            // Auto-detect or resolve toko_id if not explicitly provided
            if (empty($tokoId)) {
                foreach ($entryData as $col => $val) {
                    if (preg_match('/toko|store|cabang|marketplace|channel/i', $col)) {
                        if (is_numeric($val) && Toko::where('id', (int)$val)->exists()) {
                            $tokoId = (int)$val;
                            $tokoName = Toko::where('id', (int)$val)->value('name');
                            if ($tokoName) {
                                $entryData[$col] = $tokoName;
                            }
                        } elseif (is_string($val) && !empty(trim($val))) {
                            $found = Toko::where('name', trim($val))
                                ->orWhere('code', trim($val))
                                ->value('id');
                            if ($found) {
                                $tokoId = $found;
                            }
                        }
                        break;
                    }
                }
            }

            // Auto-detect or resolve produk_id if not explicitly provided
            if (empty($produkId)) {
                foreach ($entryData as $col => $val) {
                    if (preg_match('/produk|barang|item|sku/i', $col)) {
                        if (is_numeric($val) && Produk::where('id', (int)$val)->exists()) {
                            $produkId = (int)$val;
                            $produkName = Produk::where('id', (int)$val)->value('name');
                            if ($produkName) {
                                $entryData[$col] = $produkName;
                            }
                        } elseif (is_string($val) && !empty(trim($val))) {
                            $found = Produk::where('name', trim($val))
                                ->orWhere('code', trim($val))
                                ->value('id');
                            if ($found) {
                                $produkId = $found;
                            }
                        }
                        break;
                    }
                }
            }

            // Save toko_id and produk_id into entry_data array
            if ($tokoId) {
                $entryData['toko_id'] = (int)$tokoId;
            }
            if ($produkId) {
                $entryData['produk_id'] = (int)$produkId;
                $entryData['barang_id'] = (int)$produkId;
            }

            // Auto-ensure columns in task_data_entries table
            if (!\Illuminate\Support\Facades\Schema::hasColumn('task_data_entries', 'toko_id')) {
                try {
                    \Illuminate\Support\Facades\Schema::table('task_data_entries', function (\Illuminate\Database\Schema\Blueprint $table) {
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('task_data_entries', 'toko_id')) {
                            $table->unsignedBigInteger('toko_id')->nullable()->after('employee_id');
                        }
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('task_data_entries', 'produk_id')) {
                            $table->unsignedBigInteger('produk_id')->nullable()->after('toko_id');
                        }
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Auto add columns to task_data_entries: ' . $e->getMessage());
                }
            }

            $entryAttributes = [
                'task_submission_id' => $submission->id,
                'task_assignee_id'   => $assignee->id,
                'task_id'            => $task->id,
                'employee_id'        => $employee->id,
                'created_by'         => $user->id_user ?? Auth::id(),
                'entry_data'         => $entryData,
            ];

            if ($tokoId && \Illuminate\Support\Facades\Schema::hasColumn('task_data_entries', 'toko_id')) {
                $entryAttributes['toko_id'] = (int)$tokoId;
            }
            if ($produkId && \Illuminate\Support\Facades\Schema::hasColumn('task_data_entries', 'produk_id')) {
                $entryAttributes['produk_id'] = (int)$produkId;
            }

            $entry = TaskDataEntry::create($entryAttributes);

            $submissionEntriesCount = TaskDataEntry::where('task_submission_id', $submission->id)->count();

            if ($task->target_count > 0) {
                $progress = min(100, (int)round(($submissionEntriesCount / $task->target_count) * 100));
                $status = ($progress >= 100) ? TaskStatusService::STATUS_COMPLETED : TaskStatusService::STATUS_IN_PROGRESS;

                $submission->progress = $progress;
                $submission->status = $status;
                if ($progress >= 100) {
                    $submission->completed_at = now();
                }
                $submission->save();

                $assignee->progress = $progress;
                $assignee->status = $status;
                if ($progress >= 100) {
                    $assignee->completed_at = now();
                }
                $assignee->save();
            } else {
                if ($submissionEntriesCount > 0 && in_array($assignee->status, [TaskStatusService::STATUS_ASSIGNED])) {
                    $assignee->status = TaskStatusService::STATUS_IN_PROGRESS;
                    $assignee->save();
                    $submission->status = TaskStatusService::STATUS_IN_PROGRESS;
                    $submission->save();
                }
            }

            DB::commit();

            return response()->json([
                'status'      => 'success',
                'message'     => 'Baris data berhasil ditambahkan.',
                'entry'       => $entry->load('employee', 'creator'),
                'total_count' => $submissionEntriesCount,
                'progress'    => $assignee->progress ?? $submission->progress,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Update data entry for data_list tasks
     */
    public function updateDataEntry(Request $request, $entryId)
    {
        $request->validate([
            'entry_data'   => 'required|array',
            'entry_data.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $entry = TaskDataEntry::where('employee_id', $employee->id)->findOrFail($entryId);
            $submission = TaskSubmission::find($entry->task_submission_id);
            $assignee = TaskAssignee::find($entry->task_assignee_id);

            // Check if task is locked/completed
            if (
                ($assignee && $assignee->status === TaskStatusService::STATUS_COMPLETED) ||
                ($submission && $submission->status === TaskStatusService::STATUS_COMPLETED)
            ) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $entryData = $request->input('entry_data', []);
            $tokoId = $request->input('toko_id');
            $produkId = $request->input('produk_id') ?? $request->input('barang_id');

            // Auto-detect or resolve toko_id if not explicitly provided
            if (empty($tokoId)) {
                foreach ($entryData as $col => $val) {
                    if (preg_match('/toko|store|cabang|marketplace|channel/i', $col)) {
                        if (is_numeric($val) && Toko::where('id', (int)$val)->exists()) {
                            $tokoId = (int)$val;
                            $tokoName = Toko::where('id', (int)$val)->value('name');
                            if ($tokoName) {
                                $entryData[$col] = $tokoName;
                            }
                        } elseif (is_string($val) && !empty(trim($val))) {
                            $found = Toko::where('name', trim($val))
                                ->orWhere('code', trim($val))
                                ->value('id');
                            if ($found) {
                                $tokoId = $found;
                            }
                        }
                        break;
                    }
                }
            }

            // Auto-detect or resolve produk_id if not explicitly provided
            if (empty($produkId)) {
                foreach ($entryData as $col => $val) {
                    if (preg_match('/produk|barang|item|sku/i', $col)) {
                        if (is_numeric($val) && Produk::where('id', (int)$val)->exists()) {
                            $produkId = (int)$val;
                            $produkName = Produk::where('id', (int)$val)->value('name');
                            if ($produkName) {
                                $entryData[$col] = $produkName;
                            }
                        } elseif (is_string($val) && !empty(trim($val))) {
                            $found = Produk::where('name', trim($val))
                                ->orWhere('code', trim($val))
                                ->value('id');
                            if ($found) {
                                $produkId = $found;
                            }
                        }
                        break;
                    }
                }
            }

            // Save toko_id and produk_id into entry_data array
            if ($tokoId) {
                $entryData['toko_id'] = (int)$tokoId;
            }
            if ($produkId) {
                $entryData['produk_id'] = (int)$produkId;
                $entryData['barang_id'] = (int)$produkId;
            }

            $updateData = [
                'entry_data' => $entryData,
            ];

            if ($tokoId && \Illuminate\Support\Facades\Schema::hasColumn('task_data_entries', 'toko_id')) {
                $updateData['toko_id'] = (int)$tokoId;
            }
            if ($produkId && \Illuminate\Support\Facades\Schema::hasColumn('task_data_entries', 'produk_id')) {
                $updateData['produk_id'] = (int)$produkId;
            }

            $entry->update($updateData);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Baris data berhasil diperbarui.',
                'entry'   => $entry->fresh(['employee', 'creator']),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Delete data entry
     */
    public function destroyDataEntry($entryId)
    {
        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $entry = TaskDataEntry::where('employee_id', $employee->id)->findOrFail($entryId);
            $submissionId = $entry->task_submission_id;
            $assigneeId = $entry->task_assignee_id;
            $task = $entry->task;

            $assignee = $assigneeId ? TaskAssignee::find($assigneeId) : null;
            $submission = $submissionId ? TaskSubmission::find($submissionId) : null;

            // Check if task is locked/completed
            if (
                ($assignee && $assignee->status === TaskStatusService::STATUS_COMPLETED) ||
                ($submission && $submission->status === TaskStatusService::STATUS_COMPLETED)
            ) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $entry->delete();

            $subEntriesCount = $submissionId ? TaskDataEntry::where('task_submission_id', $submissionId)->count() : 0;

            if ($task && $task->target_count > 0 && $submissionId) {
                $progress = min(100, (int)round(($subEntriesCount / $task->target_count) * 100));
                $status = ($progress >= 100) ? TaskStatusService::STATUS_COMPLETED : TaskStatusService::STATUS_IN_PROGRESS;

                $submission = TaskSubmission::find($submissionId);
                if ($submission) {
                    $submission->progress = $progress;
                    $submission->status = $status;
                    if ($progress < 100) {
                        $submission->completed_at = null;
                    }
                    $submission->save();
                }

                $assignee = $assigneeId ? TaskAssignee::find($assigneeId) : null;
                if ($assignee) {
                    $assignee->progress = $progress;
                    $assignee->status = $status;
                    if ($progress < 100) {
                        $assignee->completed_at = null;
                    }
                    $assignee->save();
                }
            }

            DB::commit();

            return response()->json([
                'status'      => 'success',
                'message'     => 'Baris data berhasil dihapus.',
                'total_count' => $subEntriesCount,
                'progress'    => $assignee?->progress ?? ($submission?->progress ?? 0),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Store stock update entry
     */
    public function storeStockUpdate(Request $request, $id)
    {
        $request->validate([
            'toko_id'       => 'required|exists:tokos,id',
            'produk_id'     => 'required|exists:produks,id',
            'stock_status'  => 'required|in:kosong,menipis,tersedia',
            'current_stock' => 'nullable|integer|min:0',
            'notes'         => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $assignee = $this->resolveAssignee($id, $employee);
            if (!$assignee || !$assignee->task) {
                return response()->json(['status' => 'error', 'message' => 'Penugasan tugas tidak ditemukan.'], 404);
            }

            if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $task = $assignee->task;

            $user = Auth::user();

            $submission = TaskSubmission::firstOrCreate(
                [
                    'task_assignee_id' => $assignee->id,
                ],
                [
                    'task_id'     => $task->id,
                    'employee_id' => $employee->id,
                    'created_by'  => $user->id_user ?? Auth::id(),
                    'work_date'   => $assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString(),
                ]
            );

            if ($submission->status === TaskStatusService::STATUS_COMPLETED) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $update = TaskStockUpdate::create([
                'task_submission_id' => $submission->id,
                'task_assignee_id'   => $assignee->id,
                'task_id'            => $task->id,
                'toko_id'            => (int)$request->toko_id,
                'produk_id'          => (int)$request->produk_id,
                'employee_id'        => $employee->id,
                'created_by'         => $user->id_user ?? Auth::id(),
                'stock_status'       => $request->stock_status,
                'current_stock'      => (int)($request->current_stock ?? 0),
                'notes'              => $request->notes,
            ]);

            if ($task->status === TaskStatusService::STATUS_DRAFT || $task->status === TaskStatusService::STATUS_ASSIGNED) {
                $task->status = TaskStatusService::STATUS_IN_PROGRESS;
                $task->save();
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Update status stok berhasil disimpan.',
                'entry'   => $update->load(['toko', 'produk', 'employee', 'creator']),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Delete stock update entry
     */
    public function destroyStockUpdate($updateId)
    {
        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $update = TaskStockUpdate::where('employee_id', $employee->id)->findOrFail($updateId);
            $assignee = $update->task_assignee_id ? TaskAssignee::find($update->task_assignee_id) : null;
            $submission = $update->task_submission_id ? TaskSubmission::find($update->task_submission_id) : null;

            if (
                ($assignee && $assignee->status === TaskStatusService::STATUS_COMPLETED) ||
                ($submission && $submission->status === TaskStatusService::STATUS_COMPLETED)
            ) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $update->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data update stok berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Update stock matrix (checkboxes per product & store)
     */
    public function updateStockMatrix(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $employee = $this->getEmployeeForUser();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Akun Anda belum terhubung dengan data karyawan.'], 403);
            }

            $assignee = $this->resolveAssignee($id, $employee);
            if (!$assignee || !$assignee->task) {
                return response()->json(['status' => 'error', 'message' => 'Penugasan tugas tidak ditemukan.'], 404);
            }

            if ($assignee->status === TaskStatusService::STATUS_COMPLETED) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            $task = $assignee->task;

            $user = Auth::user();

            $submission = TaskSubmission::firstOrCreate(
                [
                    'task_assignee_id' => $assignee->id,
                ],
                [
                    'task_id'     => $task->id,
                    'employee_id' => $employee->id,
                    'created_by'  => $user->id_user ?? Auth::id(),
                    'work_date'   => $assignee->start_date ? $assignee->start_date->toDateString() : now()->toDateString(),
                ]
            );

            if ($submission->status === TaskStatusService::STATUS_COMPLETED) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tugas sudah selesai. Submission telah dikunci dan tidak dapat diubah lagi.',
                ], 422);
            }

            // Mode 1: Single checkbox toggle (via AJAX realtime click)
            if ($request->has('single_toggle')) {
                $tokoId = (int)$request->toko_id;
                $produkId = (int)$request->produk_id;
                $isKosong = filter_var($request->is_kosong, FILTER_VALIDATE_BOOLEAN);

                if ($isKosong) {
                    TaskStockUpdate::updateOrCreate(
                        [
                            'task_submission_id' => $submission->id,
                            'toko_id'            => $tokoId,
                            'produk_id'          => $produkId,
                        ],
                        [
                            'task_assignee_id' => $assignee->id,
                            'task_id'          => $task->id,
                            'employee_id'      => $employee->id,
                            'created_by'       => $user->id_user ?? Auth::id(),
                            'stock_status'     => 'kosong',
                            'current_stock'    => 0,
                            'notes'            => 'Ditandai kosong via matriks stok',
                        ]
                    );
                } else {
                    TaskStockUpdate::where('task_submission_id', $submission->id)
                        ->where('toko_id', $tokoId)
                        ->where('produk_id', $produkId)
                        ->delete();
                }
            }
            // Mode 2: Batch form submission (matrix[produk_id][toko_id] = "kosong")
            else {
                $matrix = $request->input('matrix', []);

                $tokos = $task->selected_tokos_models->pluck('id')->toArray();
                $produks = $task->selected_produks_models->pluck('id')->toArray();

                // Delete existing records in this submission
                TaskStockUpdate::where('task_submission_id', $submission->id)->delete();

                $now = now();
                $records = [];
                foreach ($matrix as $prodId => $tokoMap) {
                    if (!in_array((int)$prodId, $produks)) continue;
                    foreach ($tokoMap as $tokoId => $val) {
                        if ($val === 'kosong' && in_array((int)$tokoId, $tokos)) {
                            $records[] = [
                                'task_submission_id' => $submission->id,
                                'task_assignee_id'   => $assignee->id,
                                'task_id'            => $task->id,
                                'toko_id'            => (int)$tokoId,
                                'produk_id'          => (int)$prodId,
                                'employee_id'        => $employee->id,
                                'created_by'         => $user->id_user ?? Auth::id(),
                                'stock_status'       => 'kosong',
                                'current_stock'      => 0,
                                'notes'              => 'Ditandai kosong via matriks stok',
                                'created_at'         => $now,
                                'updated_at'         => $now,
                            ];
                        }
                    }
                }

                if (!empty($records)) {
                    TaskStockUpdate::insert($records);
                }
            }

            if (!$request->has('single_toggle')) {
                $submission->status = TaskStatusService::STATUS_COMPLETED;
                $submission->progress = 100;
                $submission->completed_at = now();
            } else {
                $submission->status = TaskStatusService::STATUS_IN_PROGRESS;
                $submission->progress = 50;
            }
            $submission->save();

            if ($assignee) {
                $assignee->status = $submission->status;
                $assignee->progress = $submission->progress;
                $assignee->completed_at = $submission->completed_at;
                $assignee->save();
            }

            DB::commit();

            $totalKosong = TaskStockUpdate::where('task_submission_id', $submission->id)
                ->where('stock_status', 'kosong')
                ->count();

            return response()->json([
                'status'       => 'success',
                'message'      => 'Status stok berhasil diperbarui.',
                'total_kosong' => $totalKosong,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Get tokos for Select2 Ajax search
     */
    public function getTokosSelect2(Request $request)
    {
        $search = $request->q ?? $request->term ?? '';
        $taskId = $request->task_id ?? null;
        $employee = $this->getEmployeeForUser();

        $query = Toko::query()->where('is_active', true);

        // Filter by company if available
        if ($employee && $employee->company_id) {
            $query->where('company_id', $employee->company_id);
        } elseif ($taskId) {
            $task = Task::find($taskId);
            if ($task && $task->company_id) {
                $query->where('company_id', $task->company_id);
            }
        }

        // If specific task has selected_toko_ids configured, scope to them
        if ($taskId) {
            $task = Task::find($taskId);
            if ($task && !empty($task->selected_toko_ids)) {
                $query->whereIn('id', $task->selected_toko_ids);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('pic_name', 'like', '%' . $search . '%');
            });
        }

        $tokos = $query->orderBy('name')
            ->limit(30)
            ->get()
            ->map(function ($toko) {
                return [
                    'id'      => $toko->name,
                    'toko_id' => $toko->id,
                    'text'    => $toko->name . ($toko->code ? ' (' . $toko->code . ')' : ''),
                    'code'    => $toko->code,
                    'name'    => $toko->name,
                ];
            });

        return response()->json([
            'results' => $tokos,
        ]);
    }

    /**
     * Get produks for Select2 Ajax search
     */
    public function getProduksSelect2(Request $request)
    {
        $search = $request->q ?? $request->term ?? '';
        $taskId = $request->task_id ?? null;
        $employee = $this->getEmployeeForUser();

        $query = Produk::query()->where('is_active', true);

        // Filter by company if available
        if ($employee && $employee->company_id) {
            $query->where('company_id', $employee->company_id);
        } elseif ($taskId) {
            $task = Task::find($taskId);
            if ($task && $task->company_id) {
                $query->where('company_id', $task->company_id);
            }
        }

        // If specific task has selected_produk_ids configured, scope to them
        if ($taskId) {
            $task = Task::find($taskId);
            if ($task && !empty($task->selected_produk_ids)) {
                $query->whereIn('id', $task->selected_produk_ids);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $produks = $query->orderBy('name')
            ->limit(30)
            ->get()
            ->map(function ($produk) {
                return [
                    'id'        => $produk->name,
                    'produk_id' => $produk->id,
                    'text'      => $produk->name . ($produk->code ? ' (' . $produk->code . ')' : ''),
                    'code'      => $produk->code,
                    'name'      => $produk->name,
                ];
            });

        return response()->json([
            'results' => $produks,
        ]);
    }
}

