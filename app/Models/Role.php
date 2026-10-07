<?php

namespace App\Models;

use App\Models\RoleMenu;
use App\Models\RoleKasBank;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'id_role';
    // public $incrementing = false; // karena bukan auto increment
    protected $fillable = ['nm_role', 'description', 'id_creator', 'app_id'];

    public function roleMenu()
    {
        return $this->hasMany(RoleMenu::class, 'id_role', 'id_role');
    }

    public function roleKasBank()
    {
        return $this->hasMany(RoleKasBank::class, 'id_role', 'id_role');
    }

    // shortcut: ambil permissions (string) dari role_menu
    public function permissions()
    {
        return $this->roleMenu->pluck('permission');
    }

    // shortcut: ambil kas/bank yang bisa diakses role
    public function kasBankAccess()
    {
        return $this->roleKasBank->pluck('kas_bank_id');
    }
}
