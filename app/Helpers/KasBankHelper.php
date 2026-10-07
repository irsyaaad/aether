<?php

namespace App\Helpers;

use App\Models\RoleKasBank;
use Illuminate\Support\Facades\Auth;

class KasBankHelper
{
    /**
     * Get kas/bank options untuk dropdown berdasarkan role user saat ini
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getKasBankOptions()
    {
        return RoleKasBank::getKasBankForCurrentUser();
    }

    /**
     * Check apakah user dapat mengakses kas/bank tertentu
     *
     * @param int $kasBankId
     * @return bool
     */
    public static function canAccessKasBank($kasBankId)
    {
        return RoleKasBank::canAccessKasBank($kasBankId);
    }

    /**
     * Get kas/bank name berdasarkan ID jika user memiliki akses
     *
     * @param int $kasBankId
     * @return string
     */
    public static function getKasBankName($kasBankId)
    {
        if (!self::canAccessKasBank($kasBankId)) {
            return 'Akses Terbatas';
        }

        $kasBank = RoleKasBank::getKasBankForCurrentUser()
            ->where('id_ac', $kasBankId)
            ->first();

        return $kasBank ? $kasBank->id_ac . ' - ' . $kasBank->nama : 'Tidak Ditemukan';
    }

    /**
     * Filter array kas/bank berdasarkan akses user
     *
     * @param array $kasBankIds
     * @return array
     */
    public static function filterAccessibleKasBank($kasBankIds)
    {
        $allowedIds = RoleKasBank::getKasBankIdsForCurrentUser();
        return array_intersect($kasBankIds, $allowedIds);
    }
}