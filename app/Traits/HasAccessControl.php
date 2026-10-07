<?php

namespace App\Traits;

use App\Models\RoleMenu;

trait HasAccessControl
{
    /**
     * Check access and abort if not authorized
     * Use in controller methods for clean access control
     *
     * @param string $permission Permission name (e.g., 'do.index', 'do.create')
     * @param string|null $redirectTo Custom redirect URL (default: back or 'do')
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect if no access, null if authorized
     */
    protected function checkAccess(string $permission, ?string $redirectTo = null)
    {
        if (RoleMenu::checkAccess($permission)) {
            return null;
        }

        $redirect = $redirectTo ?? url()->previous() ?? 'do';
        
        return redirect($redirect)
            ->with('error', "Anda tidak memiliki izin untuk mengakses halaman {$permission}. Hubungi Admin untuk Aktivasi Kembali");
    }

    /**
     * Check access and return boolean
     *
     * @param string $permission
     * @return bool
     */
    protected function hasAccess(string $permission): bool
    {
        return (bool) RoleMenu::checkAccess($permission);
    }

    /**
     * Require access - returns redirect response if not authorized
     * Usage: if ($denied = $this->requireAccess('do.index')) return $denied;
     *
     * @param string $permission
     * @param string|null $redirectTo
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function requireAccess(string $permission, ?string $redirectTo = null)
    {
        return $this->checkAccess($permission, $redirectTo);
    }

    /**
     * Abort with 403 if no access (for API or strict access control)
     *
     * @param string $permission
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeAccess(string $permission): void
    {
        if (!RoleMenu::checkAccess($permission)) {
            abort(403, "Anda tidak memiliki izin untuk mengakses {$permission}");
        }
    }
}
