<?php

namespace Modules\Task\Entities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Karyawan;
use Modules\Task\Services\TaskStatusService;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'company_id',
        'task_number',
        'title',
        'description',
        'category_id',
        'priority_id',
        'task_type',
        'output_type',
        'target_type',
        'target_amount',
        'target_unit',
        'target_count',
        'list_columns',
        'custom_fields',
        'selected_toko_ids',
        'selected_produk_ids',
        'achieved_amount',
        'is_template',
        'parent_task_id',
        'recurring_date',
        'status',
        'progress',
        'created_by',
        'start_date',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'company_id'          => 'integer',
        'category_id'         => 'integer',
        'priority_id'         => 'integer',
        'is_template'         => 'boolean',
        'parent_task_id'      => 'integer',
        'recurring_date'      => 'date',
        'target_amount'       => 'float',
        'target_count'        => 'integer',
        'list_columns'        => 'array',
        'custom_fields'       => 'array',
        'selected_toko_ids'   => 'array',
        'selected_produk_ids' => 'array',
        'achieved_amount'     => 'float',
        'progress'            => 'integer',
        'created_by'          => 'integer',
        'start_date'          => 'date',
        'due_date'            => 'date',
        'completed_at'    => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    protected $appends = [
        'is_overdue',
        'status_label',
        'status_badge_class',
        'task_type_label',
        'task_type_badge_class',
        'output_type_label',
        'output_type_badge_class',
    ];

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relationship to TaskCategory
     */
    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    /**
     * Relationship to TaskPriority
     */
    public function priority()
    {
        return $this->belongsTo(TaskPriority::class, 'priority_id');
    }

    /**
     * Relationship to User (Creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Relationship to TaskAssignee records
     */
    public function assignees()
    {
        return $this->hasMany(TaskAssignee::class, 'task_id');
    }

    /**
     * Many-to-many relationship with Karyawan through task_assignees
     */
    public function assignedEmployees()
    {
        return $this->belongsToMany(Karyawan::class, 'task_assignees', 'task_id', 'employee_id')
            ->withPivot(['assigned_at', 'assigned_by', 'start_date', 'due_date', 'status', 'progress', 'completed_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Relationship to TaskFollower records
     */
    public function followers()
    {
        return $this->hasMany(TaskFollower::class, 'task_id');
    }

    /**
     * Many-to-many relationship with Karyawan through task_followers
     */
    public function followerEmployees()
    {
        return $this->belongsToMany(Karyawan::class, 'task_followers', 'task_id', 'employee_id')
            ->withPivot(['followed_at'])
            ->withTimestamps();
    }

    /**
     * Computed Attribute: is_overdue
     * Overdue is defined as: due_date < today AND status NOT IN ('completed', 'cancelled')
     */
    public function getIsOverdueAttribute(): bool
    {
        return TaskStatusService::isOverdue($this);
    }

    /**
     * Human-readable label for status
     */
    public function getStatusLabelAttribute(): string
    {
        return TaskStatusService::getStatusLabel($this->status);
    }

    /**
     * Tailwind badge class for status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return TaskStatusService::getStatusBadgeClass($this->status);
    }

    /**
     * Scope for filtering by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for filtering overdue tasks
     */
    public function scopeOverdue($query)
    {
        return TaskStatusService::scopeOverdue($query);
    }

    /**
     * Scope for filtering non-overdue tasks
     */
    public function scopeNotOverdue($query)
    {
        return TaskStatusService::scopeNotOverdue($query);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Relationship to Parent Template Task
     */
    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    /**
     * Relationship to Generated Instances
     */
    public function recurringInstances()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    /**
     * Get label for task_type
     */
    public function getTaskTypeLabelAttribute(): string
    {
        return match ($this->task_type) {
            'harian'     => 'Harian',
            'bulanan'    => 'Bulanan',
            'insidentil' => 'Insidentil',
            default      => ucfirst($this->task_type ?? 'Insidentil'),
        };
    }

    /**
     * Get Tailwind badge class for task_type
     */
    public function getTaskTypeBadgeClassAttribute(): string
    {
        return match ($this->task_type) {
            'harian'     => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            'bulanan'    => 'bg-purple-50 text-purple-700 border-purple-200',
            'insidentil' => 'bg-gray-100 text-gray-700 border-gray-200',
            default      => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Scopes for task_type
     */
    public function scopeHarian($query)
    {
        return $query->where('task_type', 'harian');
    }

    public function scopeBulanan($query)
    {
        return $query->where('task_type', 'bulanan');
    }

    public function scopeInsidentil($query)
    {
        return $query->where('task_type', 'insidentil');
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }


    /**
     * Relationship to Task Submissions
     * Catatan: Acuan submission adalah per-ploting tugas (task_assignee_id).
     */
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'task_id');
    }

    /**
     * Relationship to Tabular Data Entries
     */
    public function dataEntries()
    {
        return $this->hasMany(TaskDataEntry::class, 'task_id');
    }

    /**
     * Relationship to Stock Updates
     */
    public function stockUpdates()
    {
        return $this->hasMany(TaskStockUpdate::class, 'task_id');
    }

    /**
     * Get Toko models assigned to this task
     */
    public function getSelectedTokosModelsAttribute()
    {
        if (empty($this->selected_toko_ids)) {
            return \Modules\Master\Entities\Toko::where('company_id', $this->company_id)->active()->get();
        }
        return \Modules\Master\Entities\Toko::whereIn('id', $this->selected_toko_ids)->get();
    }

    /**
     * Get Produk models assigned to this task
     */
    public function getSelectedProduksModelsAttribute()
    {
        if (empty($this->selected_produk_ids)) {
            return \Modules\Master\Entities\Produk::where('company_id', $this->company_id)->active()->get();
        }
        return \Modules\Master\Entities\Produk::whereIn('id', $this->selected_produk_ids)->get();
    }

    public static function ensureSchema(): void
    {
        static $checked = false;
        if (!$checked) {
            $checked = true;
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('tasks') && !\Illuminate\Support\Facades\Schema::hasColumn('tasks', 'custom_fields')) {
                    \Illuminate\Support\Facades\Schema::table('tasks', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->json('custom_fields')->nullable()->after('list_columns');
                    });
                }
            } catch (\Throwable $e) {
                // Ignore if already created
            }
        }
    }

    /**
     * Label for output_type
     */
    public function getOutputTypeLabelAttribute(): string
    {
        return match ($this->output_type) {
            'target'       => 'Target Angka / Nilai',
            'data_list'    => 'List Jawaban / Data',
            'stock_update' => 'Update Stock Kosong',
            'full_custom'  => 'Full Custom',
            default        => 'Checklist (Selesai / Belum)',
        };
    }

    /**
     * Tailwind badge class for output_type
     */
    public function getOutputTypeBadgeClassAttribute(): string
    {
        return match ($this->output_type) {
            'target'       => 'bg-amber-50 text-amber-700 border-amber-200',
            'data_list'    => 'bg-blue-50 text-blue-700 border-blue-200',
            'stock_update' => 'bg-rose-50 text-rose-700 border-rose-200',
            'full_custom'  => 'bg-purple-50 text-purple-700 border-purple-200',
            default        => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }

    /**
     * Formatted string for target
     */
    public function getFormattedTargetAttribute(): string
    {
        if ($this->output_type !== 'target' || empty($this->target_amount)) {
            return '-';
        }

        if ($this->target_type === 'currency') {
            return 'Rp ' . number_format($this->target_amount, 0, ',', '.') . ' ' . ($this->target_unit ?: '');
        }

        return number_format($this->target_amount, 0, ',', '.') . ' ' . ($this->target_unit ?: '');
    }

    /**
     * Formatted string for achieved
     */
    public function getFormattedAchievedAttribute(): string
    {
        if ($this->output_type !== 'target') {
            return '-';
        }

        if ($this->target_type === 'currency') {
            return 'Rp ' . number_format($this->achieved_amount ?: 0, 0, ',', '.') . ' ' . ($this->target_unit ?: '');
        }

        return number_format($this->achieved_amount ?: 0, 0, ',', '.') . ' ' . ($this->target_unit ?: '');
    }

    /**
     * Calculate progress percentage based on target or list count
     */
    public function calculateAutoProgress(): int
    {
        if ($this->output_type === 'target' && $this->target_amount > 0) {
            $ratio = ($this->achieved_amount / $this->target_amount) * 100;
            return (int)min(100, max(0, round($ratio)));
        }

        if ($this->output_type === 'data_list' && $this->target_count > 0) {
            $totalEntries = $this->dataEntries()->count();
            $ratio = ($totalEntries / $this->target_count) * 100;
            return (int)min(100, max(0, round($ratio)));
        }

        return (int)$this->progress;
    }
}
