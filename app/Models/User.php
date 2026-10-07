<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = 'users';

    protected $dates      = ['deleted_at'];
    protected $primaryKey = 'id_user';
    protected $guard_name = 'web'; // OPTIONAL tapi aman

    /**
     * Check if user has a specific role (custom override to bypass missing Spatie roles table)
     */
    public function hasRole($role, $guard = null)
    {
        if (is_array($role)) {
            return $this->hasAnyRole($role, $guard);
        }

        $roleName = strtolower($role);
        
        if ($roleName === 'admin' || $roleName === 'administrator') {
            return \DB::table('role_user')
                ->where('id_user', $this->id_user)
                ->where('id_role', 1)
                ->whereNull('deleted_at')
                ->exists();
        }

        return \DB::table('role_user')
            ->join('role', 'role_user.id_role', '=', 'role.id_role')
            ->where('role_user.id_user', $this->id_user)
            ->where(\DB::raw('lower(role.nm_role)'), $roleName)
            ->whereNull('role_user.deleted_at')
            ->exists();
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole(...$roles)
    {
        if (is_array($roles[0] ?? null)) {
            $roles = $roles[0];
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    protected $fillable = [
        'nm_user',
        'email',
        'username',
        'password',
        'telp',
        'jenis_kelamin',
        'tgl_lahir',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleUser()
    {
        return $this->belongsTo('App\Models\RoleUser', 'id_user', 'id_user');
    }

    public function karyawan()
    {
        return $this->belongsTo(\Modules\Sdm\Entities\Karyawan::class, 'karyawan_id', 'id');
    }

    public static function getDataPetugasSurvey()
    {
        return self::join('role_user', 'users.id_user', '=', 'role_user.id_user')
            ->where('role_user.id_role', 9)
            ->where('bidang', '=', 'P2BMD')
            ->orderBy('users.id_user', 'DESC')
            ->get();
    }
}
