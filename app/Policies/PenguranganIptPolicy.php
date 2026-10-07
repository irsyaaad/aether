<?php

namespace App\Policies;

use Modules\Permohonan\Entities\PenguranganIpt;
use App\User;
use Illuminate\Auth\Access\Response;

class PenguranganIptPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PenguranganIpt $penguranganIpt): bool
    {
        $PemohonUser = auth()->user();
    
        // Jika session role == 99, user hanya bisa melihat permohonannya sendiri
        if (Session('role')['id_role'] == 99) {
            return $PemohonUser->id_user === $penguranganIpt->id_user;
        }

        // Jika session role ≠ 99, izinkan melihat semua permohonan
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PenguranganIpt $penguranganIpt): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PenguranganIpt $penguranganIpt): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PenguranganIpt $penguranganIpt): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PenguranganIpt $penguranganIpt): bool
    {
        //
    }
}
