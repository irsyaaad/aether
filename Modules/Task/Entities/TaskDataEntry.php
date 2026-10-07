<?php

namespace Modules\Task\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Entities\Karyawan;

class TaskDataEntry extends Model
{
    use HasFactory;

    protected $table = 'task_data_entries';

    protected $fillable = [
        'task_submission_id',
        'task_assignee_id',
        'task_id',
        'employee_id',
        'created_by',
        'toko_id',
        'produk_id',
        'entry_data',
    ];

    protected $casts = [
        'task_submission_id' => 'integer',
        'task_assignee_id'   => 'integer',
        'task_id'            => 'integer',
        'employee_id'        => 'integer',
        'created_by'         => 'integer',
        'toko_id'            => 'integer',
        'produk_id'          => 'integer',
        'entry_data'         => 'array',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    /**
     * Relationship to Toko
     */
    public function toko()
    {
        return $this->belongsTo(\Modules\Master\Entities\Toko::class, 'toko_id');
    }

    /**
     * Relationship to Produk / Barang
     */
    public function produk()
    {
        return $this->belongsTo(\Modules\Master\Entities\Produk::class, 'produk_id');
    }

    /**
     * Relationship to Task Submission
     */
    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class, 'task_submission_id');
    }

    /**
     * Relationship to Task Assignee
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
     * Relationship to Employee (who submitted the entry)
     */
    public function employee()
    {
        return $this->belongsTo(Karyawan::class, 'employee_id');
    }

    /**
     * Relationship to User (who logged the entry)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Get effective toko_id (from column or entry_data)
     */
    public function getEffectiveTokoIdAttribute(): ?int
    {
        return $this->toko_id ?: ($this->entry_data['toko_id'] ?? null);
    }

    /**
     * Get effective produk_id (from column or entry_data)
     */
    public function getEffectiveProdukIdAttribute(): ?int
    {
        return $this->produk_id ?: ($this->entry_data['produk_id'] ?? ($this->entry_data['barang_id'] ?? null));
    }
}
