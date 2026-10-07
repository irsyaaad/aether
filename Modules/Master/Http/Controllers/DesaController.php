<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Master\Entities\RegDistrict;
use Modules\Master\Entities\RegProvince;
use Modules\Master\Entities\RegRegency;
use Modules\Master\Entities\RegVillage;
use Yajra\DataTables\Facades\DataTables;

class DesaController extends Controller
{
    use HasAccessControl;

    public function index()
    {
        if ($denied = $this->requireAccess('wilayah-desa.index')) {
            return $denied;
        }

        $data['provinces'] = RegProvince::orderBy('name')->get();
        return view('master::desa.index', $data);
    }

    public function create()
    {
        if ($denied = $this->requireAccess('wilayah-desa.create')) {
            return $denied;
        }

        $data['provinces'] = RegProvince::orderBy('name')->get();
        return view('master::desa.create', $data);
    }

    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('wilayah-desa.create')) {
            return $denied;
        }

        $request->validate([
            'id'         => 'required|string|max:20|unique:reg_villages,id',
            'district_id' => 'required|exists:reg_districts,id',
            'name'       => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            RegVillage::create([
                'id'         => $request->id,
                'district_id' => $request->district_id,
                'name'       => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-desa.index')
                ->with('success', 'Data Desa berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        if ($denied = $this->requireAccess('wilayah-desa.update')) {
            return $denied;
        }

        $desa = RegVillage::findOrFail($id);
        $data['provinces'] = RegProvince::orderBy('name')->get();
        $data['desa'] = $desa;
        return view('master::desa.create', $data);
    }

    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('wilayah-desa.update')) {
            return $denied;
        }

        $request->validate([
            'district_id' => 'required|exists:reg_districts,id',
            'name'       => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $desa = RegVillage::findOrFail($id);
            $desa->update([
                'district_id' => $request->district_id,
                'name'       => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-desa.index')
                ->with('success', 'Data Desa berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($denied = $this->requireAccess('wilayah-desa.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            $desa = RegVillage::findOrFail($id);
            $desa->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Desa berhasil dihapus',
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
        $query = RegVillage::with('district.regency');

        if ($request->filled('province_id')) {
            $query->whereHas('district.regency', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        if ($request->filled('regency_id')) {
            $query->whereHas('district', function ($q) use ($request) {
                $q->where('regency_id', $request->regency_id);
            });
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kecamatan', fn ($row) => $row->district->name ?? '-')
            ->addColumn('kota_kab', fn ($row) => $row->district->regency->name ?? '-')
            ->addColumn('provinsi', fn ($row) => $row->district->regency->province->name ?? '-')
            ->addColumn('action', function ($row) {
                $edit = route('wilayah-desa.edit', $row->id);

                return '
                    <div class="flex items-center gap-2 justify-center">
                        <a href="' . $edit . '" class="kt-btn kt-btn-sm kt-btn-outline" title="Edit">
                            <i class="ki-filled ki-notepad-edit text-xs"></i>
                        </a>
                        <button class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive" title="Hapus" onclick="deleteDesa(\'' . $row->id . '\')">
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

    public function getDistrictsByRegency($regencyId)
    {
        $districts = RegDistrict::where('regency_id', $regencyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($districts);
    }

    public function getVillagesByDistrict($districtId)
    {
        $villages = RegVillage::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($villages);
    }
}
