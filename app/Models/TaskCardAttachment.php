<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskCardAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'task_card_attachments';

    protected $fillable = [
        'card_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'created_by',
        'updated_by',
    ];

    public function card()
    {
        return $this->belongsTo(TaskCard::class, 'card_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}

