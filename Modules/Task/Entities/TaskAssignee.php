<?php

namespace Modules\Task\Entities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Entities\Karyawan;
use Modules\Task\Services\TaskStatusService;

class TaskAssignee extends Model
{
    use HasFactory;

    protected $table = 'task_assignees';

    protected $fillable = [
        'task_id',
        'employee_id',
        'assigned_at',
        'assigned_by',
        'start_date',
        'due_date',
        'status',
        'progress',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'task_id'      => 'integer',
        'employee_id'  => 'integer',
        'assigned_by'  => 'integer',
        'assigned_at'  => 'datetime',
        'start_date'   => 'date',
        'due_date'     => 'date',
        'progress'     => 'integer',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    protected $appends = [
        'is_overdue',
        'status_label',
        'status_badge_class',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function employee()
    {
        return $this->belongsTo(Karyawan::class, 'employee_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id_user');
    }

    /**
     * Relationship to Task Submission for this assignment instance
     */
    public function submission()
    {
        return $this->hasOne(TaskSubmission::class, 'task_assignee_id');
    }

    /**
     * Relationship to Task Submissions for this assignment instance
     */
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'task_assignee_id');
    }

    /**
     * Relationship to Task Data Entries for this plotting instance
     */
    public function dataEntries()
    {
        return $this->hasMany(TaskDataEntry::class, 'task_assignee_id');
    }

    /**
     * Relationship to Task Stock Updates for this plotting instance
     */
    public function stockUpdates()
    {
        return $this->hasMany(TaskStockUpdate::class, 'task_assignee_id');
    }

    /**
     * Get effective due date (from assignee record or fallback to master task)
     */
    public function getEffectiveDueDateAttribute(): ?Carbon
    {
        return $this->due_date ?? $this->task?->due_date;
    }

    /**
     * Get effective start date
     */
    public function getEffectiveStartDateAttribute(): ?Carbon
    {
        return $this->start_date ?? $this->task?->start_date;
    }

    /**
     * Check if this assignment is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED, TaskStatusService::STATUS_EXPIRED])) {
            return false;
        }

        $dueDate = $this->effective_due_date;
        if (!$dueDate) {
            return false;
        }

        return Carbon::parse($dueDate)->endOfDay()->isPast();
    }

    /**
     * Get label for status
     */
    public function getStatusLabelAttribute(): string
    {
        return TaskStatusService::getStatusLabel($this->status ?: TaskStatusService::STATUS_ASSIGNED);
    }

    /**
     * Get badge class for status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return TaskStatusService::getStatusBadgeClass($this->status ?: TaskStatusService::STATUS_ASSIGNED);
    }

    /**
     * Scope for overdue assignments
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', [TaskStatusService::STATUS_COMPLETED, TaskStatusService::STATUS_CANCELLED, TaskStatusService::STATUS_EXPIRED])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString());
    }

    /**
     * Scope for actionable assignments.
     * An employee executes tasks assigned to them directly.
     */
    public function scopeActionable($query)
    {
        return $query->whereHas('task', function ($q) {
            $q->whereNull('deleted_at');
        });
    }
}
