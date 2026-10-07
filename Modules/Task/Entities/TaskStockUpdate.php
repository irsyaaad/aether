<?php

namespace Modules\Task\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Entities\Karyawan;
use Modules\Master\Entities\Produk;
use Modules\Master\Entities\Toko;

class TaskStockUpdate extends Model
{
    use HasFactory;

    protected $table = 'task_stock_updates';

    protected $fillable = [
        'task_submission_id',
        'task_assignee_id',
        'task_id',
        'toko_id',
        'produk_id',
        'employee_id',
        'created_by',
        'stock_status',
        'current_stock',
        'notes',
    ];

    protected $casts = [
        'task_submission_id' => 'integer',
        'task_assignee_id'   => 'integer',
        'task_id'            => 'integer',
        'toko_id'            => 'integer',
        'produk_id'          => 'integer',
        'employee_id'        => 'integer',
        'created_by'         => 'integer',
        'current_stock'      => 'integer',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

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
     * Relationship to Toko
     */
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    /**
     * Relationship to Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Relationship to Employee
     */
    public function employee()
    {
        return $this->belongsTo(Karyawan::class, 'employee_id');
    }

    /**
     * Relationship to User
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Badge class for stock status
     */
    public function getStockStatusBadgeClassAttribute(): string
    {
        return match ($this->stock_status) {
            'kosong'   => 'bg-red-50 text-red-700 border-red-200',
            'menipis'  => 'bg-amber-50 text-amber-700 border-amber-200',
            'tersedia' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            default    => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Label for stock status
     */
    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'kosong'   => 'Stok Kosong',
            'menipis'  => 'Stok Menipis',
            'tersedia' => 'Stok Tersedia / Diisi',
            default    => ucfirst($this->stock_status),
        };
    }
}
