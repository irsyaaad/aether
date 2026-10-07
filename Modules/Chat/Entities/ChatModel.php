<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatModel extends Model
{
    use HasFactory;

    protected $table = 'chats';
    protected $fillable = ['room_id', 'user_id','sender', 'message'];
    
    protected static function newFactory()
    {
        return \Modules\Chat\Database\factories\ChatModelFactory::new();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id_user');
    }
}
