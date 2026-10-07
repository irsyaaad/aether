<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBoard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'task_boards';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function columns()
    {
        return $this->hasMany(TaskColumn::class, 'board_id', 'id')->orderBy('sort_order');
    }

    public function cards()
    {
        return $this->hasMany(TaskCard::class, 'board_id', 'id');
    }
}
