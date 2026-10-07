<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Keuangan\Entities\Ac4;

class RoleKasBank extends Model
{
    use HasFactory;

    protected $table = 'role_kas_bank';
    
    protected $fillable = [
        'id_role',
        'kas_bank_id',
    ];

    /**
     * Relationship dengan Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /**
     * Relationship dengan Kas/Bank (Ac4)
     */
    public function kasBank()
    {
        return $this->belongsTo(Ac4::class, 'kas_bank_id', 'id_ac');
    }

    /**
     * Get kas/bank yang dapat diakses oleh user yang sedang login
     * berdasarkan role mereka
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getKasBankForCurrentUser()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('role_kas_bank')) {
            return Ac4::orderBy('nama')->get();
        }

        if (!Auth::check()) {
            return collect();
        }

        $userId = Auth::user()->id_user;
        
        // Ambil role user yang sedang login
        $userRoles = DB::table('role_user')
            ->where('id_user', $userId)
            ->whereNull('deleted_at')
            ->pluck('id_role');

        if ($userRoles->isEmpty()) {
            return collect();
        }

        // Ambil kas/bank yang dapat diakses berdasarkan role
        $kasBankIds = self::whereIn('id_role', $userRoles)
            ->pluck('kas_bank_id')
            ->unique();

        if ($kasBankIds->isEmpty()) {
            return collect();
        }

        // Return data kas/bank lengkap
        return Ac4::whereIn('id_ac', $kasBankIds)
            ->orderBy('nama')
            ->get();
    }

    /**
     * Get kas/bank IDs yang dapat diakses oleh user yang sedang login
     *
     * @return array
     */
    public static function getKasBankIdsForCurrentUser()
    {
        return self::getKasBankForCurrentUser()->pluck('id_ac')->toArray();
    }

    /**
     * Check apakah user yang sedang login dapat mengakses kas/bank tertentu
     *
     * @param int $kasBankId
     * @return bool
     */
    public static function canAccessKasBank($kasBankId)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('role_kas_bank')) {
            return true;
        }

        $allowedKasBankIds = self::getKasBankIdsForCurrentUser();
        return in_array($kasBankId, $allowedKasBankIds);
    }

    /**
     * Get query builder untuk kas/bank yang dapat diakses user saat ini
     * Berguna untuk filter di controller
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getKasBankQueryForCurrentUser()
    {
        $kasBankIds = self::getKasBankIdsForCurrentUser();
        
        return Ac4::whereIn('id_ac', $kasBankIds);
    }

    /**
     * Scope untuk filter berdasarkan role tertentu
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $roleId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRole($query, $roleId)
    {
        return $query->where('id_role', $roleId);
    }

    /**
     * Scope untuk filter berdasarkan kas/bank tertentu
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $kasBankId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForKasBank($query, $kasBankId)
    {
        return $query->where('kas_bank_id', $kasBankId);
    }
}