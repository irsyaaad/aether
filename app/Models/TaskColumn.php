<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskColumn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'task_columns';

    protected $fillable = [
        'board_id',
        'name',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    public function board()
    {
        return $this->belongsTo(TaskBoard::class, 'board_id', 'id');
    }

    public function cards()
    {
        return $this->hasMany(TaskCard::class, 'column_id', 'id')->orderBy('position');
    }
}
