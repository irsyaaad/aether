<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserApi extends Authenticatable
{
    use Notifiable;

    protected $table = 'users_api';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name', 'username', 'password',
    ];

    // protected $hidden = [
    //     'password', 'remember_token',
    // ];

    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}
