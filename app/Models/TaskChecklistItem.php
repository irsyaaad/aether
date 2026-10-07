<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskChecklistItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'task_checklist_items';

    protected $fillable = [
        'card_id',
        'title',
        'is_done',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function card()
    {
        return $this->belongsTo(TaskCard::class, 'card_id', 'id');
    }
}
