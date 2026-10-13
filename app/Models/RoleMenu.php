<?php

namespace App\Models;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    protected $table = 'role_menu';
    protected $primaryKey = 'id_rm';
    protected $fillable = ['id_role', 'permission'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function permissionDetail()
    {
        return $this->belongsTo(Permission::class, 'permission', 'name');
    }

    public static function checkAccess($permission)
    {
        $roleId = session('role')['id_role'] ?? null;
        if (!$roleId && \Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $roleUser = \Illuminate\Support\Facades\DB::table('role_user')
                ->where('id_user', $user->id_user ?? $user->id ?? 0)
                ->whereNull('deleted_at')
                ->first();
            $roleId = $roleUser ? $roleUser->id_role : null;
        }

        if ($roleId == 1) {
            return true;
        }

        if ($roleId) {
            $data = self::where('id_role', $roleId)
                ->where('permission', $permission)
                ->first();
            return !is_null($data);
        }

        return false;
    }
}
