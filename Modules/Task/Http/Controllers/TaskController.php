<?php

namespace Modules\Task\Http\Controllers;

use App\Traits\HasAccessControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Karyawan;
use Modules\Task\Entities\Task;
use Modules\Task\Entities\TaskAssignee;
use Modules\Task\Entities\TaskCategory;
use Modules\Task\Entities\TaskDataEntry;
use Modules\Task\Entities\TaskPriority;
use Modules\Task\Entities\TaskStockUpdate;
use Modules\Task\Entities\TaskSubmission;
use Modules\Task\Services\TaskNumberService;
use Modules\Task\Services\TaskStatusService;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    use HasAccessControl;

    /**
     * Get current user's company context.
     * Returns company_id if user is restricted, or null if user has superadmin role.
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
     * Display listing of tasks with summary statistics
     */
    public function index()
    {
        if ($denied = $this->requireAccess('tasks.index')) {
            return $denied;
        }

        $userCompanyId = $this->getUserCompanyId();

        $companies = $userCompanyId
            ? Company::where('id', $userCompanyId)->get()
            : Company::where('is_active', true)->orderBy('name')->get();

        // Clean up any remaining auto-generated child instances so only single master tasks exist
        Task::whereNotNull('parent_task_id')->forceDelete();

        $categories = TaskCategory::where('is_active', true)->orderBy('name')->get();
        $priorities = TaskPriority::where('is_active', true)->orderBy('level')->get();
        $employees = Karyawan::where('is_active', true)->orderBy('name')->get();
        $statuses = TaskStatusService::getAvailableStatuses();
        $taskTypes = \Modules\Task\Services\TaskRecurringService::getTypes();

        $statsQuery = Task::whereNull('parent_task_id');
        if ($userCompanyId) {
            $statsQuery->where('company_id', $userCompanyId);
        }

        // Summary counts across master tasks
        $stats = [
            'total'       => (clone $statsQuery)->count(),
            'in_progress' => (clone $statsQuery)->where('status', TaskStatusService::STATUS_IN_PROGRESS)->count(),
            'completed'   => (clone $statsQuery)->where('status', TaskStatusService::STATUS_COMPLETED)->count(),
            'overdue'     => (clone $statsQuery)->overdue()->count(),
        ];

        return view('task::tasks.index', compact('companies', 'categories', 'priorities', 'employees', 'statuses', 'taskTypes', 'stats', 'userCompanyId'));
    }

    /**
     * Server-side DataTables data with rich filtering
     */
    public function getData(Request $request)
    {
        if ($denied = $this->requireAccess('tasks.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userCompanyId = $this->getUserCompanyId();

        $query = Task::with([
            'company',
            'category',
            'priority',
            'assignedEmployees',
            'creator',
        ]);

        // Multi-company isolation
        if ($userCompanyId) {
            $query->where('company_id', $userCompanyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Only show single master tasks (no auto-generated child duplicates)
        $query->whereNull('parent_task_id');

        // Filters
        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('priority_id')) {
            $query->where('priority_id', $request->priority_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Overdue Filter
        if ($request->filled('overdue')) {
            if ($request->overdue === '1') {
                $query->overdue();
            } elseif ($request->overdue === '0') {
                $query->notOverdue();
            }
        }

        // Output Type Filter
        if ($request->filled('output_type')) {
            $query->where('output_type', $request->output_type);
        }

        // Assignee Filter
        if ($request->filled('employee_id')) {
            $query->whereHas('assignees', function ($q) use ($request) {
                $q->where('employee_id', $request->employee_id);
            });
        }

        // Date Range Filters
        if ($request->filled('due_date_start')) {
            $query->whereDate('due_date', '>=', $request->due_date_start);
        }
        if ($request->filled('due_date_end')) {
            $query->whereDate('due_date', '<=', $request->due_date_end);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('task_number', function ($row) {
                $url = route('tasks.show', $row->id);
                $overdueBadge = $row->is_overdue
                    ? '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200 ml-1.5"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Terlambat</span>'
                    : '';

                return '<div class="flex flex-col">
                            <div class="flex items-center">
                                <a href="' . $url . '" class="font-mono text-xs font-bold text-primary hover:underline">' . e($row->task_number) . '</a>
                                ' . $overdueBadge . '
                            </div>
                        </div>';
            })
            ->editColumn('title', function ($row) {
                $url = route('tasks.show', $row->id);
                $categoryName = $row->category ? e($row->category->name) : 'Tanpa Kategori';
                $typeBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border ' . $row->task_type_badge_class . '">' . $row->task_type_label . '</span>';
                $outputBadge = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border ' . $row->output_type_badge_class . '">' . $row->output_type_label . '</span>';

                return '<div class="flex flex-col max-w-xs">
                            <div class="flex items-center flex-wrap gap-1">
                                <a href="' . $url . '" class="font-medium text-gray-900 text-sm hover:text-primary transition-colors truncate">' . e($row->title) . '</a>
                                ' . $typeBadge . '
                                ' . $outputBadge . '
                            </div>
                            <span class="text-[11px] text-gray-400 mt-0.5"><i data-lucide="tag" class="w-3 h-3 inline mr-1"></i>' . $categoryName . '</span>
                        </div>';
            })
            ->addColumn('priority_badge', function ($row) {
                if (!$row->priority) {
                    return '<span class="text-gray-400 text-xs">-</span>';
                }
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border ' . $row->priority->badge_class . '">
                            ' . e($row->priority->name) . '
                        </span>';
            })
            ->addColumn('assignees_list', function ($row) {
                $assignees = $row->assignedEmployees;
                if ($assignees->isEmpty()) {
                    return '<span class="text-xs text-gray-400 italic">Belum ada</span>';
                }

                $html = '<div class="flex items-center -space-x-2 overflow-hidden">';
                $displayed = $assignees->take(3);
                foreach ($displayed as $emp) {
                    $name = e($emp->display_name);
                    $initials = strtoupper(substr($name, 0, 2));
                    if ($emp->avatar_url) {
                        $html .= '<img class="inline-block h-7 w-7 rounded-full ring-2 ring-white object-cover" src="' . e($emp->avatar_url) . '" title="' . $name . '" alt="' . $name . '">';
                    } else {
                        $html .= '<div class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold ring-2 ring-white" title="' . $name . '">' . $initials . '</div>';
                    }
                }
                if ($assignees->count() > 3) {
                    $html .= '<span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold ring-2 ring-white">+' . ($assignees->count() - 3) . '</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->editColumn('due_date', function ($row) {
                if (!$row->due_date) {
                    return '<span class="text-xs text-gray-400 italic">-</span>';
                }
                $dateFormatted = $row->due_date->format('d M Y');
                $isOverdue = $row->is_overdue;
                $colorClass = $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-600';

                return '<div class="flex flex-col text-xs ' . $colorClass . '">
                            <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> ' . $dateFormatted . '</span>
                            <span class="text-[10px] text-gray-400 mt-0.5">' . ($row->due_date->diffForHumans()) . '</span>
                        </div>';
            })
            ->editColumn('progress', function ($row) {
                $progress = (int)$row->progress;
                $barColor = $progress == 100 ? 'bg-emerald-500' : ($progress > 50 ? 'bg-blue-500' : 'bg-amber-500');

                return '<div class="w-28 space-y-1">
                            <div class="flex justify-between text-[11px] font-medium text-gray-600">
                                <span>Progress</span>
                                <span>' . $progress . '%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                <div class="' . $barColor . ' h-1.5 rounded-full" style="width: ' . $progress . '%"></div>
                            </div>
                        </div>';
            })
            ->editColumn('status', function ($row) {
                return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ' . $row->status_badge_class . '">
                            ' . e($row->status_label) . '
                        </span>';
            })
            ->addColumn('action', function ($row) {
                $showUrl = route('tasks.show', $row->id);
                $editUrl = route('tasks.edit', $row->id);

                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="' . $showUrl . '" class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors rounded-full hover:bg-blue-50" title="Detail">
                            <i data-lucide="eye" class="h-4 w-4"></i>
                        </a>
                        <a href="' . $editUrl . '" class="p-1.5 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-primary/10" title="Edit">
                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                        </a>
                        <button type="button" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded-full hover:bg-red-50" title="Hapus" onclick="deleteTask(' . $row->id . ', \'' . e(addslashes($row->task_number)) . '\')">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['task_number', 'title', 'priority_badge', 'assignees_list', 'due_date', 'progress', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show form for creating a new task
     */
    public function create()
    {
        if ($denied = $this->requireAccess('tasks.create')) {
            return $denied;
        }

        $userCompanyId = $this->getUserCompanyId();

        $companies = $userCompanyId
            ? Company::where('id', $userCompanyId)->get()
            : Company::where('is_active', true)->orderBy('name')->get();

        $defaultCompanyId = $userCompanyId ?: ($companies->first()->id ?? null);

        $categories = $defaultCompanyId
            ? TaskCategory::where('company_id', $defaultCompanyId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        $priorities = $defaultCompanyId
            ? TaskPriority::where('company_id', $defaultCompanyId)->where('is_active', true)->orderBy('level')->get()
            : collect();

        $employees = $defaultCompanyId
            ? Karyawan::where('company_id', $defaultCompanyId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        $statuses = TaskStatusService::getAvailableStatuses();
        $taskTypes = \Modules\Task\Services\TaskRecurringService::getTypes();
        $tokos = \Modules\Master\Entities\Toko::where('is_active', true)->orderBy('name')->get();
        $produks = \Modules\Master\Entities\Produk::where('is_active', true)->orderBy('name')->get();

        return view('task::tasks.create', compact('companies', 'categories', 'priorities', 'employees', 'statuses', 'taskTypes', 'defaultCompanyId', 'userCompanyId', 'tokos', 'produks'));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('tasks.create')) {
            return $denied;
        }

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId) {
            $request->merge(['company_id' => $userCompanyId]);
        }

        $request->validate([
            'company_id'   => 'required|exists:companies,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'task_type'      => 'required|in:harian,bulanan,insidentil',
            'output_type'         => 'required|in:checklist,target,data_list,stock_update,full_custom',
            'target_type'         => 'nullable|in:currency,number',
            'target_amount'       => 'nullable|numeric|min:0',
            'target_unit'         => 'nullable|string|max:50',
            'target_count'        => 'nullable|integer|min:1',
            'list_columns'        => 'nullable|array',
            'list_columns.*'      => 'nullable|string|max:100',
            'custom_fields'       => 'nullable|array',
            'custom_fields.*.label' => 'nullable|string|max:150',
            'custom_fields.*.type'  => 'nullable|in:text,number,date,time,datetime,textarea,file,url,email',
            'custom_fields.*.required' => 'nullable|in:0,1,true,false',
            'selected_toko_ids'   => 'nullable|array',
            'selected_toko_ids.*' => 'exists:tokos,id',
            'selected_produk_ids' => 'nullable|array',
            'selected_produk_ids.*' => 'exists:produks,id',
            'category_id'         => 'nullable|exists:task_categories,id',
            'priority_id'         => 'required|exists:task_priorities,id',
            'status'              => 'required|in:' . implode(',', array_keys(TaskStatusService::getAvailableStatuses())),
            'progress'            => 'nullable|integer|min:0|max:100',
            'start_date'          => 'nullable|date',
            'due_date'          => 'nullable|date|after_or_equal:start_date',
            'assignees'         => 'nullable|array',
            'assignees.*'       => 'exists:karyawan,id',
            'followers'         => 'nullable|array',
            'followers.*'       => 'exists:karyawan,id',
        ]);

        Task::ensureSchema();
        TaskSubmission::ensureSchema();

        try {
            DB::beginTransaction();

            $companyId = (int)$request->company_id;
            $userId = Auth::user()->id_user ?? Auth::id();

            // Prevent rapid double submit (identical title, company, creator within 5 seconds)
            $recentDuplicate = Task::where('company_id', $companyId)
                ->where('title', $request->title)
                ->where('created_by', $userId)
                ->whereNull('parent_task_id')
                ->where('created_at', '>=', now()->subSeconds(5))
                ->first();

            if ($recentDuplicate) {
                return redirect()->route('tasks.show', $recentDuplicate->id)
                    ->with('success', "Tugas {$recentDuplicate->task_number} berhasil dibuat.");
            }

            $taskNumber = TaskNumberService::generate($companyId);

            $completedAt = ($request->status === TaskStatusService::STATUS_COMPLETED) ? now() : null;
            $isTemplate = in_array($request->task_type, [\Modules\Task\Services\TaskRecurringService::TYPE_HARIAN, \Modules\Task\Services\TaskRecurringService::TYPE_BULANAN]);

            $outputType = $request->output_type ?: 'checklist';
            $listColumns = null;
            if ($outputType === 'data_list') {
                $listColumns = array_values(array_filter((array)$request->list_columns));
                if (empty($listColumns)) {
                    $listColumns = ['Nama', 'Kontak / Identitas', 'Catatan'];
                }
            }

            $customFields = null;
            if ($outputType === 'full_custom' && $request->has('custom_fields')) {
                $rawFields = (array)$request->custom_fields;
                $cleanedFields = [];
                $usedKeys = [];
                foreach ($rawFields as $idx => $f) {
                    $label = trim($f['label'] ?? '');
                    if ($label === '') continue;
                    $allowedTypes = ['text', 'number', 'date', 'time', 'datetime', 'textarea', 'file', 'url', 'email'];
                    $type = in_array($f['type'] ?? '', $allowedTypes) ? $f['type'] : 'text';
                    $required = !empty($f['required']) && ($f['required'] == '1' || $f['required'] === true || $f['required'] === 'true');

                    $baseKey = Str::slug($label, '_');
                    if (empty($baseKey)) {
                        $baseKey = 'field_' . ($idx + 1);
                    }
                    $key = $baseKey;
                    $counter = 1;
                    while (in_array($key, $usedKeys)) {
                        $key = $baseKey . '_' . (++$counter);
                    }
                    $usedKeys[] = $key;

                    $cleanedFields[] = [
                        'key'      => $key,
                        'label'    => $label,
                        'type'     => $type,
                        'required' => $required,
                    ];
                }
                $customFields = !empty($cleanedFields) ? $cleanedFields : null;
            }

            $task = Task::create([
                'company_id'          => $companyId,
                'task_number'         => $taskNumber,
                'title'               => $request->title,
                'description'         => $request->description,
                'task_type'           => $request->task_type,
                'output_type'         => $outputType,
                'target_type'         => $outputType === 'target' ? ($request->target_type ?: 'currency') : null,
                'target_amount'       => $outputType === 'target' ? (float)$request->target_amount : null,
                'target_unit'         => $outputType === 'target' ? $request->target_unit : null,
                'target_count'        => $outputType === 'data_list' ? (int)$request->target_count : null,
                'list_columns'        => $listColumns,
                'custom_fields'       => $customFields,
                'selected_toko_ids'   => $outputType === 'stock_update' ? ($request->selected_toko_ids ?: null) : null,
                'selected_produk_ids' => $outputType === 'stock_update' ? ($request->selected_produk_ids ?: null) : null,
                'achieved_amount'     => 0,
                'is_template'         => $isTemplate,
                'category_id'         => $request->category_id ?: null,
                'priority_id'         => $request->priority_id,
                'status'              => $request->status,
                'progress'            => (int)($request->progress ?? 0),
                'created_by'      => $userId,
                'start_date'      => $request->start_date ?: null,
                'due_date'        => $request->due_date ?: null,
                'completed_at'    => $completedAt,
            ]);

            // Sync assignees
            if ($request->filled('assignees')) {
                $assigneeData = [];
                foreach ($request->assignees as $empId) {
                    $assigneeData[$empId] = [
                        'assigned_at' => now(),
                        'assigned_by' => $userId,
                    ];
                }
                $task->assignedEmployees()->sync($assigneeData);
            }

            // Sync followers
            if ($request->filled('followers')) {
                $followerData = [];
                foreach ($request->followers as $empId) {
                    $followerData[$empId] = [
                        'followed_at' => now(),
                    ];
                }
                $task->followerEmployees()->sync($followerData);
            }

            DB::commit();

            return redirect()->route('tasks.show', $task->id)
                ->with('success', "Tugas {$task->task_number} berhasil dibuat.");
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Gagal membuat tugas: ' . $th->getMessage());
        }
    }

    /**
     * Display task detail
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('tasks.index')) {
            return $denied;
        }

        $task = Task::with([
            'company',
            'category',
            'priority',
            'creator',
            'assignedEmployees.department',
            'assignedEmployees.position',
            'followerEmployees.department',
            'followerEmployees.position',
            'dataEntries.employee',
            'dataEntries.creator',
            'stockUpdates.toko',
            'stockUpdates.produk',
            'stockUpdates.employee',
            'stockUpdates.creator',
        ])->findOrFail($id);

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId && $task->company_id !== $userCompanyId) {
            abort(403, 'Anda tidak memiliki akses ke tugas perusahaan ini.');
        }

        $statuses = TaskStatusService::getAvailableStatuses();
        $availableTokos = $task->selected_tokos_models;
        $availableProduks = $task->selected_produks_models;

        // Total summary counts for employee recap segment
        $totalEmployeesCount = TaskAssignee::where('task_id', $task->id)->whereNotNull('employee_id')->distinct('employee_id')->count('employee_id');
        $totalPlotingsCount = TaskAssignee::where('task_id', $task->id)->count();

        return view('task::tasks.show', compact('task', 'statuses', 'availableTokos', 'availableProduks', 'totalEmployeesCount', 'totalPlotingsCount'));
    }

    /**
     * Show form for editing task
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return $denied;
        }

        $task = Task::with(['assignedEmployees', 'followerEmployees'])->findOrFail($id);

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId && $task->company_id !== $userCompanyId) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tugas perusahaan ini.');
        }

        $companies = $userCompanyId
            ? Company::where('id', $userCompanyId)->get()
            : Company::where('is_active', true)->orderBy('name')->get();

        $categories = TaskCategory::where('company_id', $task->company_id)->where('is_active', true)->orderBy('name')->get();
        $priorities = TaskPriority::where('company_id', $task->company_id)->where('is_active', true)->orderBy('level')->get();
        $employees = Karyawan::where('company_id', $task->company_id)->where('is_active', true)->orderBy('name')->get();
        $statuses = TaskStatusService::getAvailableStatuses();
        $taskTypes = \Modules\Task\Services\TaskRecurringService::getTypes();
        $tokos = \Modules\Master\Entities\Toko::where('is_active', true)->orderBy('name')->get();
        $produks = \Modules\Master\Entities\Produk::where('is_active', true)->orderBy('name')->get();

        $selectedAssignees = $task->assignedEmployees->pluck('id')->toArray();
        $selectedFollowers = $task->followerEmployees->pluck('id')->toArray();

        return view('task::tasks.edit', compact('task', 'companies', 'categories', 'priorities', 'employees', 'statuses', 'taskTypes', 'selectedAssignees', 'selectedFollowers', 'userCompanyId', 'tokos', 'produks'));
    }

    /**
     * Update specified task
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return $denied;
        }

        $task = Task::findOrFail($id);

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId && $task->company_id !== $userCompanyId) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah tugas ini.');
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'task_type'      => 'required|in:harian,bulanan,insidentil',
            'output_type'         => 'required|in:checklist,target,data_list,stock_update,full_custom',
            'target_type'         => 'nullable|in:currency,number',
            'target_amount'       => 'nullable|numeric|min:0',
            'target_unit'         => 'nullable|string|max:50',
            'target_count'        => 'nullable|integer|min:1',
            'list_columns'        => 'nullable|array',
            'list_columns.*'      => 'nullable|string|max:100',
            'custom_fields'       => 'nullable|array',
            'custom_fields.*.label' => 'nullable|string|max:150',
            'custom_fields.*.type'  => 'nullable|in:text,number,date,time,datetime,textarea,file,url,email',
            'custom_fields.*.required' => 'nullable|in:0,1,true,false',
            'selected_toko_ids'   => 'nullable|array',
            'selected_toko_ids.*' => 'exists:tokos,id',
            'selected_produk_ids' => 'nullable|array',
            'selected_produk_ids.*' => 'exists:produks,id',
            'category_id'         => 'nullable|exists:task_categories,id',
            'priority_id'         => 'required|exists:task_priorities,id',
            'status'              => 'required|in:' . implode(',', array_keys(TaskStatusService::getAvailableStatuses())),
            'progress'            => 'required|integer|min:0|max:100',
            'start_date'          => 'nullable|date',
            'due_date'            => 'nullable|date|after_or_equal:start_date',
            'assignees'           => 'nullable|array',
            'assignees.*'         => 'exists:karyawan,id',
            'followers'           => 'nullable|array',
            'followers.*'         => 'exists:karyawan,id',
        ]);

        Task::ensureSchema();
        TaskSubmission::ensureSchema();

        try {
            DB::beginTransaction();

            $completedAt = $task->completed_at;
            if ($request->status === TaskStatusService::STATUS_COMPLETED && $task->status !== TaskStatusService::STATUS_COMPLETED) {
                $completedAt = now();
            } elseif ($request->status !== TaskStatusService::STATUS_COMPLETED) {
                $completedAt = null;
            }

            $isTemplate = in_array($request->task_type, [\Modules\Task\Services\TaskRecurringService::TYPE_HARIAN, \Modules\Task\Services\TaskRecurringService::TYPE_BULANAN]);

            $outputType = $request->output_type ?: $task->output_type;
            $listColumns = $task->list_columns;
            if ($outputType === 'data_list' && $request->has('list_columns')) {
                $listColumns = array_values(array_filter((array)$request->list_columns));
                if (empty($listColumns)) {
                    $listColumns = ['Nama', 'Kontak / Identitas', 'Catatan'];
                }
            }

            $customFields = $task->custom_fields;
            if ($outputType === 'full_custom') {
                if ($request->has('custom_fields')) {
                    $rawFields = (array) $request->custom_fields;
                    $cleanedFields = [];
                    $usedKeys = [];
                    foreach ($rawFields as $idx => $f) {
                        $label = trim($f['label'] ?? '');
                        if ($label === '') continue;
                        $allowedTypes = ['text', 'number', 'date', 'time', 'datetime', 'textarea', 'file', 'url', 'email'];
                        $type = in_array($f['type'] ?? '', $allowedTypes) ? $f['type'] : 'text';
                        $required = !empty($f['required']) && ($f['required'] == '1' || $f['required'] === true || $f['required'] === 'true');

                        $baseKey = Str::slug($label, '_');
                        if (empty($baseKey)) {
                            $baseKey = 'field_' . ($idx + 1);
                        }
                        $key = $baseKey;
                        $counter = 1;
                        while (in_array($key, $usedKeys)) {
                            $key = $baseKey . '_' . (++$counter);
                        }
                        $usedKeys[] = $key;

                        $cleanedFields[] = [
                            'key'      => $key,
                            'label'    => $label,
                            'type'     => $type,
                            'required' => $required,
                        ];
                    }
                    $customFields = !empty($cleanedFields) ? $cleanedFields : null;
                }
            } else {
                $customFields = null;
            }

            $task->update([
                'title'               => $request->title,
                'description'         => $request->description,
                'task_type'           => $request->task_type,
                'output_type'         => $outputType,
                'target_type'         => $outputType === 'target' ? ($request->target_type ?: 'currency') : null,
                'target_amount'       => $outputType === 'target' ? (float)$request->target_amount : null,
                'target_unit'         => $outputType === 'target' ? $request->target_unit : null,
                'target_count'        => $outputType === 'data_list' ? (int)$request->target_count : null,
                'list_columns'        => $listColumns,
                'custom_fields'       => $customFields,
                'selected_toko_ids'   => $outputType === 'stock_update' ? ($request->selected_toko_ids ?: null) : null,
                'selected_produk_ids' => $outputType === 'stock_update' ? ($request->selected_produk_ids ?: null) : null,
                'is_template'         => $isTemplate,
                'category_id'         => $request->category_id ?: null,
                'priority_id'         => $request->priority_id,
                'status'              => $request->status,
                'progress'            => (int)$request->progress,
                'start_date'          => $request->start_date ?: null,
                'due_date'            => $request->due_date ?: null,
                'completed_at'        => $completedAt,
            ]);

            $userId = Auth::user()->id_user ?? Auth::id();

            // Sync assignees
            $assigneeData = [];
            if ($request->filled('assignees')) {
                foreach ($request->assignees as $empId) {
                    $assigneeData[$empId] = [
                        'assigned_at' => now(),
                        'assigned_by' => $userId,
                    ];
                }
            }
            $task->assignedEmployees()->sync($assigneeData);

            // Sync followers
            $followerData = [];
            if ($request->filled('followers')) {
                foreach ($request->followers as $empId) {
                    $followerData[$empId] = [
                        'followed_at' => now(),
                    ];
                }
            }
            $task->followerEmployees()->sync($followerData);

            DB::commit();

            return redirect()->route('tasks.show', $task->id)
                ->with('success', "Tugas {$task->task_number} berhasil diperbarui.");
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Gagal memperbarui tugas: ' . $th->getMessage());
        }
    }

    /**
     * Quick update task progress
     */
    public function updateProgress(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        try {
            $task = Task::findOrFail($id);
            $progress = (int)$request->progress;

            $data = ['progress' => $progress];
            if ($progress === 100 && $task->status !== TaskStatusService::STATUS_COMPLETED) {
                $data['status'] = TaskStatusService::STATUS_COMPLETED;
                $data['completed_at'] = now();
            }

            $task->update($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Progress tugas berhasil diperbarui.',
                'data'    => $task->fresh(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui progress: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick update task status
     */
    public function updateStatus(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(TaskStatusService::getAvailableStatuses())),
        ]);

        try {
            $task = Task::findOrFail($id);
            $newStatus = $request->status;

            $data = ['status' => $newStatus];
            if ($newStatus === TaskStatusService::STATUS_COMPLETED) {
                $data['completed_at'] = now();
                $data['progress'] = 100;
            } elseif ($task->status === TaskStatusService::STATUS_COMPLETED && $newStatus !== TaskStatusService::STATUS_COMPLETED) {
                $data['completed_at'] = null;
            }

            $task->update($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Status tugas berhasil diperbarui.',
                'data'    => $task->fresh(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui status: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified task
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('tasks.delete')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();
            $task = Task::findOrFail($id);
            $taskNumber = $task->task_number;
            $task->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => "Tugas {$taskNumber} berhasil dihapus.",
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus tugas: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update achieved target for numeric target tasks
     */
    public function updateTarget(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'achieved_amount' => 'required|numeric|min:0',
        ]);

        try {
            $task = Task::findOrFail($id);
            $task->achieved_amount = (float)$request->achieved_amount;

            // Recalculate progress
            $task->progress = $task->calculateAutoProgress();

            if ($task->progress >= 100 && $task->status !== TaskStatusService::STATUS_COMPLETED) {
                $task->status = TaskStatusService::STATUS_COMPLETED;
                $task->completed_at = now();
            }

            $task->save();

            return response()->json([
                'status'             => 'success',
                'message'            => 'Realisasi capaian target berhasil diperbarui.',
                'achieved_amount'    => $task->achieved_amount,
                'formatted_achieved' => $task->formatted_achieved,
                'progress'           => $task->progress,
                'task_status'        => $task->status,
                'status_label'       => $task->status_label,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Get data entries for server-side DataTable
     */
    public function getDataEntries(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task = Task::findOrFail($id);

        $query = TaskDataEntry::with(['employee', 'creator'])
            ->where('task_id', $task->id)
            ->select('task_data_entries.*');

        $columns = $task->list_columns ?: ['Nama Afiliator', 'Kontak WhatsApp', 'Catatan'];

        $dataTable = DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($row) {
                return e($row->employee->display_name ?? ($row->creator->nm_user ?? '-'));
            })
            ->addColumn('created_time', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" onclick="deleteEntry(' . $row->id . ')" class="p-1 text-gray-400 hover:text-red-600 rounded transition-colors" title="Hapus Baris">' .
                    '<i data-lucide="trash-2" class="w-3.5 h-3.5"></i>' .
                    '</button>';
            });

        foreach ($columns as $idx => $col) {
            $dataTable->addColumn('col_' . $idx, function ($row) use ($col) {
                $val = $row->entry_data[$col] ?? '-';
                return is_array($val) ? e(json_encode($val)) : e($val);
            });
        }

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $dataTable->filter(function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('entry_data', 'like', "%{$search}%")
                      ->orWhereHas('employee', function ($eq) use ($search) {
                          $eq->where('name', 'like', "%{$search}%")
                             ->orWhere('nama_lengkap', 'like', "%{$search}%")
                             ->orWhere('employee_number', 'like', "%{$search}%");
                      })
                      ->orWhereHas('creator', function ($cq) use ($search) {
                          $cq->where('nm_user', 'like', "%{$search}%")
                             ->orWhere('username', 'like', "%{$search}%");
                      });
                });
            });
        }

        return $dataTable->rawColumns(['action'])->make(true);
    }

    /**
     * Server-side DataTables for employee recap of a task
     */
    public function getEmployeeRecap(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task = Task::findOrFail($id);

        $userCompanyId = $this->getUserCompanyId();
        if ($userCompanyId && (int)$task->company_id !== (int)$userCompanyId) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $taskId = $task->id;

        $query = Karyawan::withTrashed()
            ->with([
                'department',
                'position',
                'company',
                'taskAssignees' => function ($q) use ($taskId) {
                    $q->where('task_id', $taskId)
                      ->with([
                          'submission.dataEntries',
                          'submission.stockUpdates',
                          'dataEntries',
                          'stockUpdates',
                      ]);
                }
            ])
            ->whereIn('id', function ($sub) use ($taskId) {
                $sub->select('employee_id')
                    ->from('task_assignees')
                    ->where('task_id', $taskId)
                    ->whereNotNull('employee_id');
            })
            ->select('karyawan.*');

        $dataTable = DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('employee_info', function ($row) {
                $avatar = '';
                if ($row->avatar_url) {
                    $avatar = '<img src="' . e($row->avatar_url) . '" alt="' . e($row->display_name) . '" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0">';
                } else {
                    $initials = strtoupper(substr($row->display_name ?? 'K', 0, 2));
                    $avatar = '<div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">' . e($initials) . '</div>';
                }
                $dept = $row->department ? ' • ' . e($row->department->name) : '';
                $pos = $row->position ? e($row->position->name) : 'Staf';
                $nik = e($row->nik ?: ($row->employee_number ?: '-'));

                return '
                    <div class="flex items-center gap-3">
                        ' . $avatar . '
                        <div>
                            <div class="font-bold text-gray-900 text-xs">' . e($row->display_name) . '</div>
                            <div class="text-[10px] text-gray-400 font-mono">
                                ' . $nik . ' • ' . $pos . $dept . '
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('ploting_count', function ($row) {
                $totalPlotings = $row->taskAssignees->count();
                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-500"></i>
                            ' . $totalPlotings . ' Ploting
                        </span>';
            })
            ->addColumn('progress_bar', function ($row) {
                $empAssignees = $row->taskAssignees;
                $totalPlotings = $empAssignees->count();
                $completedCount = $empAssignees->where('status', TaskStatusService::STATUS_COMPLETED)->count();

                $overallPercentage = $totalPlotings > 0 ? (int) round($empAssignees->avg('progress') ?? 0) : 0;
                if ($completedCount === $totalPlotings && $totalPlotings > 0) {
                    $overallPercentage = 100;
                }

                $barColor = $overallPercentage >= 100 ? 'bg-emerald-500' : ($overallPercentage >= 50 ? 'bg-blue-500' : 'bg-primary');

                return '
                    <div class="space-y-1.5 w-48">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="font-mono ' . ($overallPercentage >= 100 ? 'text-emerald-600 font-bold' : 'text-gray-700') . '">' . $overallPercentage . '%</span>
                            <span class="text-[10px] text-gray-400 font-normal">(' . $completedCount . '/' . $totalPlotings . ' selesai)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="' . $barColor . ' h-2 rounded-full transition-all duration-500" style="width: ' . min(100, $overallPercentage) . '%"></div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('total_data_rows', function ($row) use ($task) {
                $empAssignees = $row->taskAssignees;
                $totalDataRows = $empAssignees->sum(function($a) use ($task) {
                    if ($task->output_type === 'full_custom') {
                        $cData = $a->submission?->custom_data;
                        if (!empty($cData) && is_array($cData)) {
                            $firstK = array_key_first($cData);
                            return ($firstK !== null && is_numeric($firstK)) ? count($cData) : 1;
                        }
                        return 0;
                    } elseif ($task->output_type === 'stock_update') {
                        if ($a->submission && $a->submission->stockUpdates) {
                            return $a->submission->stockUpdates->count();
                        }
                        return $a->stockUpdates ? $a->stockUpdates->count() : 0;
                    } else {
                        if ($a->submission && $a->submission->dataEntries) {
                            return $a->submission->dataEntries->count();
                        }
                        return $a->dataEntries ? $a->dataEntries->count() : 0;
                    }
                });

                return '
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <i data-lucide="table" class="w-3.5 h-3.5 text-blue-500"></i>
                        <span class="font-mono text-sm">' . $totalDataRows . '</span> Baris Data
                    </span>
                ';
            })
            ->addColumn('status_badge', function ($row) {
                $empAssignees = $row->taskAssignees;
                $totalPlotings = $empAssignees->count();
                $completedCount = $empAssignees->where('status', TaskStatusService::STATUS_COMPLETED)->count();

                if ($completedCount === $totalPlotings && $totalPlotings > 0) {
                    return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                                Selesai
                            </span>';
                } elseif ($completedCount > 0) {
                    return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <i data-lucide="clock" class="w-3 h-3 text-amber-600"></i>
                                Sebagian
                            </span>';
                } else {
                    return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <i data-lucide="loader" class="w-3 h-3 text-blue-600"></i>
                                Proses
                            </span>';
                }
            })
            ->addColumn('action', function ($row) {
                $firstAssignee = $row->taskAssignees->first();
                if (!$firstAssignee) {
                    return '<span class="text-gray-400 text-xs">-</span>';
                }
                $url = route('tasks.assignment.submission', $firstAssignee->id);
                $hasSubmission = !empty($firstAssignee->submission_id) || $firstAssignee->submission !== null;

                if ($hasSubmission) {
                    return '
                        <a href="' . $url . '" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition-colors shadow-2xs">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Lihat Bukti
                        </a>
                    ';
                }

                return '
                    <a href="' . $url . '" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors shadow-2xs">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                        Detail
                    </a>
                ';
            });

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $dataTable->filter(function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('employee_number', 'like', "%{$search}%")
                      ->orWhere('nik_ktp', 'like', "%{$search}%")
                      ->orWhereHas('position', function ($pq) use ($search) {
                          $pq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('department', function ($dq) use ($search) {
                          $dq->where('name', 'like', "%{$search}%");
                      });
                });
            });
        }

        return $dataTable->rawColumns(['employee_info', 'ploting_count', 'progress_bar', 'total_data_rows', 'status_badge', 'action'])->make(true);
    }

    /**
     * Store a dynamic tabular data entry for data_list tasks
     */
    public function storeDataEntry(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'entry_data' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $task = Task::findOrFail($id);
            $user = Auth::user();
            $employeeId = $user->karyawan_id ?: ($task->assignedEmployees->first()->id ?? null);

            if (!$employeeId) {
                $firstEmp = Karyawan::first();
                $employeeId = $firstEmp ? $firstEmp->id : 1;
            }

            $entry = TaskDataEntry::create([
                'task_id'     => $task->id,
                'employee_id' => $employeeId,
                'created_by'  => $user->id_user ?? Auth::id(),
                'entry_data'  => $request->entry_data,
            ]);

            // Auto-calculate progress if target_count is defined
            if ($task->target_count > 0) {
                $task->progress = $task->calculateAutoProgress();
                if ($task->progress >= 100 && $task->status !== TaskStatusService::STATUS_COMPLETED) {
                    $task->status = TaskStatusService::STATUS_COMPLETED;
                    $task->completed_at = now();
                }
                $task->save();
            }

            DB::commit();

            return response()->json([
                'status'      => 'success',
                'message'     => 'Data jawaban berhasil ditambahkan.',
                'entry'       => $entry->load(['employee', 'creator']),
                'total_count' => $task->dataEntries()->count(),
                'progress'    => $task->progress,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Destroy a data entry
     */
    public function destroyDataEntry($entryId)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $entry = TaskDataEntry::findOrFail($entryId);
            $task = $entry->task;

            $entry->delete();

            if ($task && $task->target_count > 0) {
                $task->progress = $task->calculateAutoProgress();
                $task->save();
            }

            DB::commit();

            return response()->json([
                'status'      => 'success',
                'message'     => 'Baris data berhasil dihapus.',
                'total_count' => $task ? $task->dataEntries()->count() : 0,
                'progress'    => $task ? $task->progress : 0,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Store a stock update entry for stock_update tasks
     */
    public function storeStockUpdate(Request $request, $id)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'toko_id'       => 'required|exists:tokos,id',
            'produk_id'     => 'required|exists:produks,id',
            'stock_status'  => 'required|in:kosong,menipis,tersedia',
            'current_stock' => 'nullable|integer|min:0',
            'notes'         => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $task = Task::findOrFail($id);
            $user = Auth::user();
            $employeeId = $user->karyawan_id ?: ($task->assignedEmployees->first()->id ?? null);

            if (!$employeeId) {
                $firstEmp = Karyawan::first();
                $employeeId = $firstEmp ? $firstEmp->id : 1;
            }

            $update = TaskStockUpdate::create([
                'task_id'       => $task->id,
                'toko_id'       => (int)$request->toko_id,
                'produk_id'     => (int)$request->produk_id,
                'employee_id'   => $employeeId,
                'created_by'    => $user->id_user ?? Auth::id(),
                'stock_status'  => $request->stock_status,
                'current_stock' => (int)($request->current_stock ?? 0),
                'notes'         => $request->notes,
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
     * Delete a stock update entry
     */
    public function destroyStockUpdate($updateId)
    {
        if ($denied = $this->requireAccess('tasks.update')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();
            $update = TaskStockUpdate::findOrFail($updateId);
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
     * Get tokos for Select2 Ajax search
     */
    public function getTokosSelect2(Request $request)
    {
        $search = $request->q ?? $request->term ?? '';
        $taskId = $request->task_id ?? null;
        $userCompanyId = $this->getUserCompanyId();

        $query = \Modules\Master\Entities\Toko::query()->where('is_active', true);

        if ($userCompanyId) {
            $query->where('company_id', $userCompanyId);
        } elseif ($taskId) {
            $task = Task::find($taskId);
            if ($task && $task->company_id) {
                $query->where('company_id', $task->company_id);
            }
        }

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
                    'id'   => $toko->name,
                    'text' => $toko->name . ($toko->code ? ' (' . $toko->code . ')' : ''),
                    'code' => $toko->code,
                    'name' => $toko->name,
                ];
            });

        return response()->json([
            'results' => $tokos,
        ]);
    }
}

