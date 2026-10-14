<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Response;
use App\Models\RoleMenu;
use Validator;
use App\Http\Requests\RoleMenuRequest;
use App\Models\RoleUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use App\Models\Perusahaan;
use Auth;
use Session;
use App\Models\Module;

class P_rolemenu extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = RoleMenu::with('role');

        if ($request->filled('role_id')) {
            $query->where('id_role', $request->role_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('permission', 'like', "%{$search}%")
                  ->orWhereHas('role', function ($qr) use ($search) {
                      $qr->where('nm_role', 'like', "%{$search}%");
                  });
            });
        }

        $data["data"] = $query->paginate(25)->withQueryString();
        $data["roles"] = Role::orderBy('nm_role')->get();
        return view("role-menu", $data);
    }

    public function create() {}

    public function show($id)
    {
        abort(404);
    }

    public function update(Request $request, $id_role)
    {
        try {
            // save to user
            DB::beginTransaction();
            $role = Role::findOrFail($id_role);

            // hapus semua izin lama
            RoleMenu::where('id_role', $id_role)->delete();

            // simpan yang baru
            if ($request->permissions && is_array($request->permissions)) {
                foreach ($request->permissions as $perm) {
                    RoleMenu::create([
                        'id_role' => $id_role,
                        'permission' => $perm,
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('roles.show', $id_role)->with('success', 'Hak akses role berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui hak akses: ' . $e->getMessage());
        }

        return redirect('user')->with('success', 'Data User Disimpan');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // $user = User::findOrFail($id);
            // $user->delete();
            RoleUser::where('id_user', $id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data masih digunakan di table lain');
        }

        return redirect()->back()->with('success', 'Data User Dihapus');
    }

    public function logout($id = null)
    {
        Auth::logout();

        if (isset($id) && $id != null) {
            return redirect('auth/login')->with('error', $id);
        } else {
            return redirect('auth/login')->with('error', 'Anda keluar Aplikasi');
        }
    }
}
