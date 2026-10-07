<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Modules\Permohonan\Entities\Permohonan;
use Redirect;
use Session;

class LoginController extends Controller
{
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            $redirectUrl = $this->getRedirectUrlForRole(session('role'));
            return redirect($redirectUrl);
        }

        if ((bool) config('services.persada.redirect_to_hris_on_expire', false)) {
            $hrisUrl = rtrim((string) config('services.persada.hris_url', 'http://hris.persada.test'), '/');

            if ($hrisUrl !== '') {
                return Redirect::away($hrisUrl);
            }
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'captcha' => 'required|captcha',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $loginInput = $request->input('email');
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        // Cek jika environment adalah local
        if (App::environment('local')) {
            // Cari user berdasarkan email ATAU username
            $user = User::where('email', $loginInput)
                ->orWhere('username', $loginInput)
                ->first();

            if ($user) {
                Auth::login($user);
                $role_user = RoleUser::where("id_user", Auth::user()->id_user)->OrderBy("created_at", "asc")->first();
                $notif = "User tidak terdaftar di Role Apapun";
                if ($role_user == null) {
                    return redirect('auth/logout/' . $notif);
                }
                $roleModel = Role::find($role_user->id_role);
                $role["role"] = $roleModel ? $roleModel->toArray() : [
                    'id_role' => $role_user->id_role,
                    'nm_role' => 'Employee',
                    'app_id'  => 'saas',
                ];
                Session($role);

                $redirectUrl = $this->getRedirectUrlForRole($role["role"]);
                return redirect($redirectUrl)->with('success', 'Selamat Datang ' . Auth::user()->nm_user);
            }

            return back()->withErrors(['email' => 'User tidak ditemukan.'])->withInput();
        }

        // Coba autentikasi dengan email atau username
        $authPassed = Auth::attempt([$isEmail ? 'email' : 'username' => $loginInput, 'password' => $request->password])
            || Auth::attempt([$isEmail ? 'username' : 'email' => $loginInput, 'password' => $request->password]);

        if ($authPassed) {
            $role_user = RoleUser::where("id_user", Auth::user()->id_user)->OrderBy("created_at", "asc")->first();
            $notif = "User tidak terdaftar di Role Apapun";
            if ($role_user == null) {
                return redirect('auth/logout/' . $notif);
            }
            $roleModel = Role::find($role_user->id_role);
            $role["role"] = $roleModel ? $roleModel->toArray() : [
                'id_role' => $role_user->id_role,
                'nm_role' => 'Employee',
                'app_id'  => 'saas',
            ];
            Session($role);

            $redirectUrl = $this->getRedirectUrlForRole($role["role"]);
            return redirect($redirectUrl)->with('success', 'Selamat Datang ' . Auth::user()->nm_user);
        } else {
            return redirect()->back()->with('error', 'Username/Email atau Password tidak terdaftar')->withInput();
        }
    }

    /**
     * Get redirect destination based on user role.
     *
     * @param array|null $role
     * @return string
     */
    private function getRedirectUrlForRole(?array $role): string
    {
        $roleName = $role['nm_role'] ?? '';
        if (strcasecmp($roleName, 'Employee') === 0 || strcasecmp($roleName, 'Karyawan') === 0) {
            return 'my-tasks';
        }

        return 'dashboard';
    }
}
