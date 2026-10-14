<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\RoleUser;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Default role ID untuk user baru yang mendaftar secara mandiri.
     * ID 99 merepresentasikan role "Karyawan" / Member.
     */
    public const DEFAULT_ROLE_ID = 99;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function create(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = new User();
            $user->username = $request->email;
            $user->email = $request->email;
            $user->nm_user = $request->full_name;
            $user->password = Hash::make($request->password);
            if (Schema::hasColumn('users', 'is_aktif')) {
                $user->is_aktif = 1;
            }
            if (Schema::hasColumn('users', 'jenis_kelamin')) {
                $user->jenis_kelamin = $request->jenis_kelamin;
            }
            if (Schema::hasColumn('users', 'tgl_lahir')) {
                $user->tgl_lahir = $request->tgl_lahir;
            }
            $user->save();

            // Set role otomatis 99 untuk user baru yang mendaftar
            $role = new RoleUser();
            $role->id_user = $user->id_user;
            $role->id_role = self::DEFAULT_ROLE_ID;
            $role->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User registration failed: ' . $e->getMessage(), [
                'exception' => $e,
                'email'     => $request->email,
            ]);
            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'User Gagal Register: ' . $e->getMessage());
        }

        return redirect('auth/login')->with('success', 'User Sukses Register');
    }
}
