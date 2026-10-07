<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $roleName = session('role')['nm_role'] ?? '';
            if (strcasecmp($roleName, 'Employee') === 0 || strcasecmp($roleName, 'Karyawan') === 0) {
                return redirect('my-tasks');
            }
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
