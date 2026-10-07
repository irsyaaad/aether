<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class adminPad
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array(Session("role")["id_role"], [1, 14])) {
            return $next($request);
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ' . $request->segment(1) . '. Hubungi Admin untuk Aktivasi Kembali');
        }
    }
}
