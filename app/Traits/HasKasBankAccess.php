<?php

namespace App\Traits;

use App\Models\RoleKasBank;
use Illuminate\Support\Facades\Auth;

trait HasKasBankAccess
{
    /**
     * Filter query berdasarkan kas/bank yang dapat diakses user saat ini
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $kasBankColumn nama kolom kas_bank_id di tabel
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function filterByUserKasBankAccess($query, $kasBankColumn = 'kas_bank_id')
    {
        $allowedKasBankIds = RoleKasBank::getKasBankIdsForCurrentUser();
        
        if (empty($allowedKasBankIds)) {
            // Jika user tidak memiliki akses kas/bank, return empty result
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($kasBankColumn, $allowedKasBankIds);
    }

    /**
     * Get kas/bank options untuk dropdown/select berdasarkan akses user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getKasBankOptions()
    {
        return RoleKasBank::getKasBankForCurrentUser();
    }

    /**
     * Validate apakah user dapat mengakses kas/bank tertentu
     *
     * @param int $kasBankId
     * @return bool
     */
    protected function validateKasBankAccess($kasBankId)
    {
        return RoleKasBank::canAccessKasBank($kasBankId);
    }

    /**
     * Throw exception jika user tidak dapat mengakses kas/bank
     *
     * @param int $kasBankId
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeKasBankAccess($kasBankId)
    {
        if (!$this->validateKasBankAccess($kasBankId)) {
            abort(403, 'Anda tidak memiliki akses ke Kas/Bank ini.');
        }
    }
}