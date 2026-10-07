<?php
use Illuminate\Support\Facades\Cache;
if (!function_exists('check_access')) {
    function check_access($nama)
    {
        $data = \App\Models\RoleMenu::checkAccess($nama);
        return $data;
    }
}
if (!function_exists('must_access')) {
    function must_access($nama)
    {
        $data = \App\Models\RoleMenu::checkAccess($nama);
        if ($data) {
            return true;
        } else {
            return redirect('do')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ' . $nama . '. Hubungi Admin untuk Aktivasi Kembali');
        }
    }
}
