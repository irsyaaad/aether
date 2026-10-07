<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Master\Entities\RegProvince;
use Modules\Master\Entities\RegRegency;
use Yajra\DataTables\Facades\DataTables;

class KotaKabController extends Controller
{
    use HasAccessControl;

    public function index()
    {
        if ($denied = $this->requireAccess('wilayah-kota-kab.index')) {
            return $denied;
        }

        $data['provinces'] = RegProvince::orderBy('name')->get();
        return view('master::kota-kab.index', $data);
    }

    public function create()
    {
        if ($denied = $this->requireAccess('wilayah-kota-kab.create')) {
            return $denied;
        }

        $data['provinces'] = RegProvince::orderBy('name')->get();
        return view('master::kota-kab.create', $data);
    }

    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('wilayah-kota-kab.create')) {
            return $denied;
        }

        $request->validate([
            'id'          => 'required|string|max:10|unique:reg_regencies,id',
            'province_id' => 'required|exists:reg_provinces,id',
            'name'        => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            RegRegency::create([
                'id'          => $request->id,
                'province_id' => $request->province_id,
                'name'        => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-kota-kab.index')
                ->with('success', 'Data Kota/Kabupaten berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        if ($denied = $this->requireAccess('wilayah-kota-kab.update')) {
            return $denied;
        }

        $kotaKab = RegRegency::findOrFail($id);
        $data['provinces'] = RegProvince::orderBy('name')->get();
        $data['kotaKab'] = $kotaKab;
        return view('master::kota-kab.create', $data);
    }

    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('wilayah-kota-kab.update')) {
            return $denied;
        }

        $request->validate([
            'province_id' => 'required|exists:reg_provinces,id',
            'name'        => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $kotaKab = RegRegency::findOrFail($id);
            $kotaKab->update([
                'province_id' => $request->province_id,
                'name'        => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-kota-kab.index')
                ->with('success', 'Data Kota/Kabupaten berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($denied = $this->requireAccess('wilayah-kota-kab.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            $kotaKab = RegRegency::findOrFail($id);
            $kotaKab->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Kota/Kabupaten berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        $query = RegRegency::with('province');

        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('provinsi', fn ($row) => $row->province->name ?? '-')
            ->addColumn('jumlah_kecamatan', function ($row) {
                return $row->districts()->count();
            })
            ->addColumn('action', function ($row) {
                $edit = route('wilayah-kota-kab.edit', $row->id);

                return '
                    <div class="flex items-center gap-2 justify-center">
                        <a href="' . $edit . '" class="kt-btn kt-btn-sm kt-btn-outline" title="Edit">
                            <i class="ki-filled ki-notepad-edit text-xs"></i>
                        </a>
                        <button class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive" title="Hapus" onclick="deleteKotaKab(\'' . $row->id . '\')">
                            <i class="ki-filled ki-trash text-xs"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getRegenciesByProvince($provinceId)
    {
        $regencies = RegRegency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($regencies);
    }
}
