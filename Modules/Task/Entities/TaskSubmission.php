<?php

namespace Modules\Task\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\Karyawan;
use Modules\Task\Services\TaskStatusService;

class TaskSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_submissions';

    protected $fillable = [
        'task_assignee_id',
        'task_id',
        'employee_id',
        'created_by',
        'work_date',
        'status',
        'progress',
        'completion_url',
        'completion_file',
        'completion_notes',
        'custom_data',
        'achieved_amount',
        'completed_at',
    ];

    protected $casts = [
        'task_assignee_id' => 'integer',
        'task_id'          => 'integer',
        'employee_id'      => 'integer',
        'created_by'       => 'integer',
        'work_date'        => 'date',
        'progress'         => 'integer',
        'custom_data'      => 'array',
        'achieved_amount'  => 'float',
        'completed_at'     => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    public static function ensureSchema(): void
    {
        static $checked = false;
        if (!$checked) {
            $checked = true;
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('task_submissions') && !\Illuminate\Support\Facades\Schema::hasColumn('task_submissions', 'custom_data')) {
                    \Illuminate\Support\Facades\Schema::table('task_submissions', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->json('custom_data')->nullable()->after('completion_notes');
                    });
                }
            } catch (\Throwable $e) {
                // Ignore if already created
            }
        }
    }

    /**
     * Relationship to Task Assignee (Ploting Tugas)
     */
    public function taskAssignee()
    {
        return $this->belongsTo(TaskAssignee::class, 'task_assignee_id');
    }

    /**
     * Alias relationship to Task Assignee
     */
    public function assignee()
    {
        return $this->belongsTo(TaskAssignee::class, 'task_assignee_id');
    }

    /**
     * Relationship to Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Relationship to Employee (Karyawan)
     */
    public function employee()
    {
        return $this->belongsTo(Karyawan::class, 'employee_id');
    }

    /**
     * Relationship to User (who created / logged the submission)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Relationship to Task Data Entries for this submission
     */
    public function dataEntries()
    {
        return $this->hasMany(TaskDataEntry::class, 'task_submission_id');
    }

    /**
     * Relationship to Task Stock Updates for this submission
     */
    public function stockUpdates()
    {
        return $this->hasMany(TaskStockUpdate::class, 'task_submission_id');
    }

    /**
     * Get URL for uploaded completion proof file
     */
    public function getCompletionFileUrlAttribute(): ?string
    {
        if (!$this->completion_file) {
            return null;
        }
        return asset('storage/' . $this->completion_file);
    }

    /**
     * Get label for status
     */
    public function getStatusLabelAttribute(): string
    {
        return TaskStatusService::getStatusLabel($this->status);
    }

    /**
     * Get badge class for status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return TaskStatusService::getStatusBadgeClass($this->status);
    }
}
