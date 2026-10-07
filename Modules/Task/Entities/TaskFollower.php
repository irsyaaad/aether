<?php

namespace Modules\Task\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Entities\Karyawan;

class TaskFollower extends Model
{
    use HasFactory;

    protected $table = 'task_followers';

    protected $fillable = [
        'task_id',
        'employee_id',
        'followed_at',
    ];

    protected $casts = [
        'task_id'     => 'integer',
        'employee_id' => 'integer',
        'followed_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function employee()
    {
        return $this->belongsTo(Karyawan::class, 'employee_id');
    }
}
