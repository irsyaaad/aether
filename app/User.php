<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    protected $table      = 'users';
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
                                   // public $incrementing = false;
                                   // public $keyType = 'string';

    protected $fillable = [
        'name', 'email', 'username', 'password', 'karyawan_id', 'is_aktif', 'created_by', 'updated_by',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (\Auth::check()) {
                $model->created_by = \Auth::id();
                $model->updated_by = \Auth::id();
            }
        });

        static::updating(function ($model) {
            if (\Auth::check()) {
                $model->updated_by = \Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by', 'id_user');
    }

    public function updater()
    {
        return $this->belongsTo(self::class, 'updated_by', 'id_user');
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function RoleMenu()
    {
        return $this->hasMany('App\Models\RoleMenu', 'id_user', 'id_user');
    }

    public function RoleUser()
    {
        return $this->hasMany('App\Models\RoleUser', 'id_user', 'id_user');
    }

    public function Perusahaan()
    {
        return $this->belongsTo('App\Models\Perusahaan', 'id_perush', 'id_perush');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id_user');
    }

    public function karyawan()
    {
        return $this->belongsTo('Modules\Sdm\Entities\Karyawan', 'karyawan_id', 'id');
    }

    public function pelanggan()
    {
        return $this->belongsTo('App\Models\Pelanggan', 'id_pelanggan', 'id_pelanggan');
    }

    public function sopir()
    {
        return $this->belongsTo('Modules\Operasional\Entities\Sopir', 'id_sopir', 'id_sopir');
    }

    public static function generatedId($id_perush)
    {
        $id = self::where("id_perush", $id_perush)->orderBy("last_id", "desc")->get()->first();

        $last = 0;
        if ($id != null) {
            $last = (Int) $id->last_id + 1;
        }

        return $last;
    }

    /**
     * Get kas/bank yang dapat diakses oleh user ini
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleKasBank()
    {
        return \Modules\Keuangan\Entities\Ac4::where('head', 1)
            ->where('level', 1)
            ->orderBy('nama')
            ->get();
    }

    /**
     * Check apakah user ini dapat mengakses kas/bank tertentu
     *
     * @param int $kasBankId
     * @return bool
     */
    public function canAccessKasBank($kasBankId)
    {
        return true;
    }
}
