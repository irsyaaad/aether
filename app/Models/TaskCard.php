<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskCard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'task_cards';

    protected $fillable = [
        'board_id',
        'column_id',
        'title',
        'description',
        'priority',
        'start_date',
        'due_date',
        'labels',
        'position',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date'   => 'date',
    ];

    public function board()
    {
        return $this->belongsTo(TaskBoard::class, 'board_id', 'id');
    }

    public function column()
    {
        return $this->belongsTo(TaskColumn::class, 'column_id', 'id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'task_card_members', 'card_id', 'user_id', 'id', 'id_user');
    }

    public function checklistItems()
    {
        return $this->hasMany(TaskChecklistItem::class, 'card_id', 'id')->orderBy('sort_order');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'card_id', 'id')->latest();
    }

    public function attachments()
    {
        return $this->hasMany(TaskCardAttachment::class, 'card_id', 'id')->latest();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
