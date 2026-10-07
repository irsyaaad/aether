<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\RoleUser;
use App\Services\AuditService;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data['roleOptions'] = Role::orderBy('app_id')->orderBy('nm_role')->get()->groupBy('app_id');
        return view("user", $data);
    }

    public function get_data(Request $request)
    {
        $users = User::with(['karyawan', 'RoleUser.role'])
            ->select('users.*')
            ->orderBy('users.nm_user', 'asc');

        if ($request->filled('role_id')) {
            $users->whereHas('RoleUser', function ($q) use ($request) {
                $q->where('id_role', $request->role_id);
            });
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('user_info', function ($row) {
                $initials = strtoupper(substr($row->nm_user ?? 'U', 0, 2));
                return '<div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-9 h-9 rounded-full bg-primary/10 text-primary font-semibold text-xs border border-primary/20 shrink-0">
                                ' . e($initials) . '
                            </div>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 text-sm">' . e($row->nm_user) . '</span>
                                <span class="text-xs text-gray-500 font-mono">' . e($row->username ?? $row->email) . '</span>
                            </div>
                        </div>';
            })
            ->addColumn('karyawan_info', function ($row) {
                if ($row->karyawan) {
                    return '<span class="inline-flex items-center gap-1.5 text-xs text-gray-700 font-medium">
                                <i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-emerald-500"></i> ' . e($row->karyawan->nama_lengkap) . '
                            </span>';
                }
                return '<span class="text-xs text-gray-400 italic">-</span>';
            })
            ->addColumn('role_summary', function ($row) {
                $roles = collect($row->RoleUser)
                    ->filter(fn ($roleUser) => $roleUser->role)
                    ->map(function ($roleUser) {
                        $role = $roleUser->role;

                        return '<span class="inline-flex items-center rounded border px-2 py-0.5 text-xs font-semibold bg-slate-100 text-slate-700 border-slate-200 mr-1 mb-1 font-mono">'
                            . e(strtoupper((string) $role->app_id)) . ' &bull; ' . e($role->nm_role)
                            . '</span>';
                    })
                    ->implode('');

                return $roles !== '' ? '<div class="flex flex-wrap items-center">' . $roles . '</div>' : '<span class="text-gray-400 text-xs">-</span>';
            })
            ->filterColumn('role_summary', function ($query, $keyword) {
                $query->whereHas('RoleUser.role', function ($roleQuery) use ($keyword) {
                    $roleQuery->where('nm_role', 'like', "%{$keyword}%")
                        ->orWhere('app_id', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="btn-edit p-1.5 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-primary/10" title="Edit Pengguna"
                            data-url="' . url(request()->segment(1), $row->id_user) . '"
                            data-temp="' . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . '">
                            <i data-lucide="edit-3" class="h-4 w-4"></i>
                        </button>
                        <button type="button" class="btn-add-role p-1.5 text-gray-400 hover:text-blue-600 transition-colors rounded-full hover:bg-blue-50" title="Tambah Role Aplikasi"
                            data-id="' . $row->id_user . '"
                            data-name="' . e($row->nm_user) . '"
                            data-temp="' . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . '">
                            <i data-lucide="shield-plus" class="h-4 w-4"></i>
                        </button>
                        <button type="button" class="btn-delete p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded-full hover:bg-red-50" title="Hapus Pengguna"
                            data-url="' . url(request()->segment(1), $row->id_user) . '"
                            data-name="' . e($row->nm_user) . '">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['user_info', 'karyawan_info', 'role_summary', 'action'])
            ->make(true);
    }

    public function create()
    {

    }

    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();
            $user              = new User();
            $user->username    = $request->email;
            $user->email       = $request->email;
            $user->nm_user     = $request->full_name;
            $user->password    = Hash::make($request->password);
            $user->karyawan_id = $request->karyawan_id ?? null;
            $user->save();
            // add roles
            $role          = new RoleUser();
            $role->id_user = DB::getPdo()->lastInsertId();
            $role->id_role = $request->id_role ?? 99;
            $role->save();

            AuditService::log('CREATE', 'users', $user->id_user, null, $user->toArray());

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data User berhasil disimpan',
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 'error',
                'message' => 'Data User Gagal Disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        abort(404);
    }

    public function update(UserRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user    = User::findOrFail($id);
            $oldData = $user->toArray();

            $user->username    = $request->email;
            $user->email       = $request->email;
            $user->nm_user     = $request->full_name;
            $user->karyawan_id = $request->karyawan_id ?? null;

            if (! empty($request->password)) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Update role utama yang sedang diedit
            $role = null;
            if ($request->filled('id_ru')) {
                $role = RoleUser::where('id_user', $id)->where('id_ru', $request->id_ru)->first();
            }

            if (! $role) {
                $role = RoleUser::where('id_user', $id)->first();
            }

            if ($role) {
                $role->id_role = $request->id_role ?? 99;
                $role->save();
            } else {
                $role          = new RoleUser();
                $role->id_user = $id;
                $role->id_role = $request->id_role ?? 99;
                $role->save();
            }

            AuditService::log('UPDATE', 'users', $id, $oldData, $user->fresh()->toArray());

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data User berhasil diupdate',
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 'error',
                'message' => 'Data User Gagal Disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function addRole(Request $request, $id)
    {
        $request->validate([
            'id_role' => 'required|exists:role,id_role',
        ]);

        try {
            DB::beginTransaction();

            $user = User::with('RoleUser.role')->findOrFail($id);
            $role = Role::findOrFail($request->id_role);

            $hasSameRole = RoleUser::where('id_user', $id)
                ->where('id_role', $role->id_role)
                ->exists();

            if ($hasSameRole) {
                throw ValidationException::withMessages([
                    'id_role' => 'Role tersebut sudah dimiliki user.',
                ]);
            }

            $hasSameApp = collect($user->RoleUser)
                ->contains(fn ($roleUser) => $roleUser->role && $roleUser->role->app_id === $role->app_id);

            if ($hasSameApp) {
                throw ValidationException::withMessages([
                    'id_role' => 'User sudah memiliki role untuk aplikasi ' . strtoupper((string) $role->app_id) . '.',
                ]);
            }

            $roleUser = new RoleUser();
            $roleUser->id_user = $id;
            $roleUser->id_role = $role->id_role;
            $roleUser->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Role aplikasi berhasil ditambahkan ke user.',
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan role aplikasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $user     = User::findOrFail($id);
            $userData = $user->toArray();

            AuditService::log('DELETE', 'users', $id, $userData, null);

            $user->delete();
            RoleUser::where('id_user', $id)->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data User berhasil dihapus',
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 'error',
                'message' => 'Data masih digunakan di table lain',
            ], 500);
        }
    }

    public function generateUsers()
    {
        try {
            DB::beginTransaction();

            // Get all active karyawan who don't have users yet
            $karyawanWithoutUsers = \Modules\Sdm\Entities\Karyawan::whereNotIn('id', function ($query) {
                $query->select('karyawan_id')
                    ->from('users')
                    ->whereNotNull('karyawan_id');
            })
                ->where('status_kerja', 'aktif')
                ->whereNotNull('nomor_hp')
                ->where('nomor_hp', '!=', '')
                ->get();

            if ($karyawanWithoutUsers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada karyawan aktif yang belum memiliki user',
                ]);
            }

            $createdCount = 0;
            $errors       = [];

            foreach ($karyawanWithoutUsers as $karyawan) {
                try {
                    // Create new user
                    $user              = new User();
                    $user->username    = $karyawan->nomor_hp;
                    $user->email       = $karyawan->email ?: $karyawan->nomor_hp . '@generated.com';
                    $user->nm_user     = $karyawan->nama_lengkap;
                    $user->password    = Hash::make($karyawan->nomor_hp);
                    $user->karyawan_id = $karyawan->id;
                    $user->save();

                    // Add default role (99 = default role)
                    $role          = new \App\Models\RoleUser();
                    $role->id_user = $user->id_user;
                    $role->id_role = 99; // Default role
                    $role->save();

                    $createdCount++;
                } catch (Exception $e) {
                    $errors[] = "Error creating user for {$karyawan->nama_lengkap}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil membuat {$createdCount} user dari " . $karyawanWithoutUsers->count() . " karyawan",
                'created_count' => $createdCount,
                'errors'        => $errors,
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate users: ' . $e->getMessage(),
            ]);
        }
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

    /**
     * Switch aplikasi (ERP/HRIS) dan update session role sesuai app_id.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $appId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchApp(Request $request, string $appId)
    {
        $appId = strtolower(trim($appId));

        if (!in_array($appId, ['erp', 'hris'], true)) {
            abort(404);
        }

        $userId = Auth::user()->id_user;

        $roleUser = RoleUser::query()
            ->with('role')
            ->where('id_user', $userId)
            ->whereHas('role', function ($query) use ($appId) {
                $query->where('app_id', $appId);
            })
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$roleUser || !$roleUser->role) {
            return redirect()->back()->with('error', 'Role untuk aplikasi ' . strtoupper($appId) . ' tidak ditemukan. Hubungi Admin.');
        }

        session(['role' => $roleUser->role->toArray()]);

        $targetBaseUrl = $this->resolveAppBaseUrl($request, $appId);

        return redirect()->to(rtrim($targetBaseUrl, '/') . '/dashboard')
            ->with('success', 'Berpindah aplikasi ke ' . strtoupper($appId));
    }

    private function resolveAppBaseUrl(Request $request, string $appId): string
    {
        $scheme = $request->getScheme();
        $host   = $request->getHost();
        $port   = $request->getPort();

        if ($appId === 'hris' && !str_starts_with($host, 'hris.')) {
            $host = 'hris.' . $host;
        }

        if ($appId === 'erp' && str_starts_with($host, 'hris.')) {
            $host = substr($host, 5);
        }

        $defaultPort = $scheme === 'https' ? 443 : 80;
        $portPart    = ($port && $port !== $defaultPort) ? ':' . $port : '';

        return $scheme . '://' . $host . $portPart;
    }
}
