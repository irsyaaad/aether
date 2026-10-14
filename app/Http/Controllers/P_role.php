<?php
namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleKasBank;
use App\Models\RoleMenu;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Entities\Ac4;
use Response;
use Yajra\DataTables\Facades\DataTables;

class P_role extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data["role"] = Role::get();

        return view("admin.role.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = config('constant.permissions');
        $apps = $this->getAvailableApps();

        return view("admin.role.create", compact('permissions', 'apps'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        try {

            DB::beginTransaction();

            $role             = new Role();
            $role->nm_role    = $request->nm_role;
            $role->app_id     = $request->app_id;
            $role->id_creator = Auth::user()->id_user;

            if (strtolower($role->nm_role) == "administrator") {
                return redirect()->back()->with('error', 'Akses Terbatas');
            }

            $role->save();

            // Save permissions if any
            if (!empty($request->permissions) && is_array($request->permissions)) {
                $allowed = $this->getAllowedPermissions();
                $validPermissions = array_intersect($request->permissions, $allowed);
                foreach ($validPermissions as $perm) {
                    RoleMenu::create([
                        'id_role'    => $role->id_role,
                        'permission' => $perm,
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Data role Gagal Disimpan' . $e->getMessage());
        }

        return redirect("roles")->with('success', 'Data role berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with('roleMenu')->findOrFail($id);

        // ambil semua nama route
        $permissions = config('constant.permissions');

        // ambil permission role yang aktif
        $rolePermissions = $role->roleMenu->pluck('permission')->toArray();

        $apps = $this->getAvailableApps();

        return view('admin.role.edit', compact('role', 'permissions', 'rolePermissions', 'apps'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (class_exists(\Debugbar::class)) {
            \Debugbar::disable();
        }
        $role = Role::with('roleMenu')->findOrFail($id);
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_role' => [
                'required',
                'regex:/^[a-zA-Z\s]+$/',
                'min:4',
                'max:50',
                Rule::unique('role', 'nm_role')->ignore($id, 'id_role'),
            ],
            'app_id' => ['nullable', 'string'],
        ]);

        try {
            // save to user
            DB::beginTransaction();

            $role              = Role::findOrFail($id);
            $role->nm_role     = $request->nm_role;
            if ($request->filled('app_id')) {
                $role->app_id  = $request->app_id;
            }

            if (strtolower($role->nm_role) == "administrator") {
                return redirect()->back()->with('error', 'Akses Terbatas');
            }
            $role->save();

            DB::commit();
            return redirect()->route('roles.show', $role->id_role)->with('success', 'Data role berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Data role gagal diperbarui. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $role = Role::findOrFail($id);
            if (strtolower($role->nm_role) == "administrator") {
                return redirect()->back()->with('error', 'Akses Terbatas');
            }
            $role->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Data masih digunakan di table lain' . $e->getMessage(),
            ]);
        }
    }

    public function duplicate($id)
    {
        try {
            DB::beginTransaction();
            $role                 = Role::findOrFail($id);
            $newRole              = new Role();
            $newRole->nm_role     = $role->nm_role . " copy";
            $newRole->description = $role->description;
            $newRole->app_id      = $role->app_id;
            $newRole->id_creator  = Auth::user()->id_user;
            $newRole->save();

            $roleMenu = RoleMenu::where('id_role', $id)->get();
            foreach ($roleMenu as $key => $value) {
                RoleMenu::create([
                    'id_role'    => $newRole->id_role,
                    'permission' => $value->permission,
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diduplicate.',
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Data masih digunakan di table lain' . $e->getMessage(),
            ]);
        }

    }

    public function get_data(Request $request)
    {
        $data = Role::query();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('app_id', function ($row) {
                return strtoupper((string) $row->app_id);
            })
            ->addColumn('action', function ($row) {
                $show = route('roles.show', $row->id_role);
                $delete = route('roles.destroy', $row->id_role);

                return '
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="' . $show . '" class="p-1.5 text-gray-400 hover:text-primary transition-colors rounded-full hover:bg-primary/10" title="Detail & Hak Akses">
                            <i data-lucide="sliders" class="h-4 w-4"></i>
                        </a>
                        <button type="button" class="btn-delete p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded-full hover:bg-red-50" title="Hapus Role" data-url="' . $delete . '" data-id="' . $row->id_role . '" data-name="' . e($row->nm_role) . '">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Update kas/bank access for role
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateKasBank(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            if ($role->id_role != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Terbatas',
                ]);
            }

            // Hapus kas/bank yang sudah ada
            RoleKasBank::where('id_role', $id)->delete();

            // Simpan kas/bank yang baru dipilih
            if ($request->kas_bank && is_array($request->kas_bank)) {
                foreach ($request->kas_bank as $kasBankId) {
                    RoleKasBank::create([
                        'id_role'     => $id,
                        'kas_bank_id' => $kasBankId,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akses Kas/Bank berhasil diperbarui',
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui akses Kas/Bank: ' . $e->getMessage(),
            ]);
        }
    }

    private function getAvailableApps(): array
    {
        return [
            'aether' => 'Aether Web',
        ];
    }

    /**
     * Get all valid permission keys defined in constant.php
     *
     * @return array
     */
    private function getAllowedPermissions(): array
    {
        $allPermissions = config('constant.permissions', []);
        $allowed = [];

        foreach ($allPermissions as $module => $submodules) {
            foreach ($submodules as $prefix => $routes) {
                if (!isset($routes['actions'])) {
                    continue;
                }
                foreach ($routes['actions'] as $actionKey => $label) {
                    $allowed[] = $prefix . '.' . $actionKey;
                }
            }
        }

        return $allowed;
    }
}
