<?php

namespace App\Http\Middleware;

use Closure;

class ChekUser
{
    public function handle($request, Closure $next)
    {
        // return $next($request);

        $role = Session("role")["id_role"];
        $segment1 = $request->segment(1);
        $segment2 = $request->segment(2);
        $segment3 = $request->segment(3);
        if ($role == 1) {
            return $next($request);
        } else {
            $access = 'show-' . $segment1;
            // dd($access);

            if (isset($segment2) && !is_numeric($segment2) && (!in_array($segment2, ['detail', 'show']))) {
                $access = $segment2;
            }

            if (isset($segment3) && (!in_array($segment3, ['detail', 'show']))) {
                $access = $segment3;
            }

            if (isset($segment3) && is_numeric($segment2) && (in_array($segment3, ['edit']))) {
                $access = $segment1 . '-' . $segment3;
            }
            $data = \App\Models\RoleMenu::checkAccess($access);
            if ($data) {
                return $next($request);
            } else {
                // abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
                return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ' . $access . '. Hubungi Admin untuk Aktivasi Kembali');
            }
        }

    }
}
