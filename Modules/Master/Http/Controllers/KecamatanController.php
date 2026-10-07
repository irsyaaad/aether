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
use Yajra\DataTables\Facades\DataTables;

class KecamatanController extends Controller
{
    use HasAccessControl;

    public function index()
    {
        if ($denied = $this->requireAccess('wilayah-kecamatan.index')) {
            return $denied;
        }

        $data['provinces'] = RegProvince::orderBy('name')->get();
        return view('master::kecamatan.index', $data);
    }

    public function create()
    {
        if ($denied = $this->requireAccess('wilayah-kecamatan.create')) {
            return $denied;
        }

        $data['provinces'] = RegProvince::orderBy('name')->get();
        return view('master::kecamatan.create', $data);
    }

    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('wilayah-kecamatan.create')) {
            return $denied;
        }

        $request->validate([
            'id'         => 'required|string|max:10|unique:reg_districts,id',
            'regency_id' => 'required|exists:reg_regencies,id',
            'name'       => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            RegDistrict::create([
                'id'         => $request->id,
                'regency_id' => $request->regency_id,
                'name'       => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-kecamatan.index')
                ->with('success', 'Data Kecamatan berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        if ($denied = $this->requireAccess('wilayah-kecamatan.update')) {
            return $denied;
        }

        $kecamatan = RegDistrict::findOrFail($id);
        $data['provinces'] = RegProvince::orderBy('name')->get();
        $data['kecamatan'] = $kecamatan;
        return view('master::kecamatan.create', $data);
    }

    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('wilayah-kecamatan.update')) {
            return $denied;
        }

        $request->validate([
            'regency_id' => 'required|exists:reg_regencies,id',
            'name'       => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $kecamatan = RegDistrict::findOrFail($id);
            $kecamatan->update([
                'regency_id' => $request->regency_id,
                'name'       => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-kecamatan.index')
                ->with('success', 'Data Kecamatan berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($denied = $this->requireAccess('wilayah-kecamatan.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            $kecamatan = RegDistrict::findOrFail($id);
            $kecamatan->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Kecamatan berhasil dihapus',
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
        $query = RegDistrict::with('regency.province');

        if ($request->filled('province_id')) {
            $query->whereHas('regency', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        if ($request->filled('regency_id')) {
            $query->where('regency_id', $request->regency_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kota_kab', fn ($row) => $row->regency->name ?? '-')
            ->addColumn('provinsi', fn ($row) => $row->regency->province->name ?? '-')
            ->addColumn('jumlah_desa', function ($row) {
                return $row->villages()->count();
            })
            ->addColumn('action', function ($row) {
                $edit = route('wilayah-kecamatan.edit', $row->id);

                return '
                    <div class="flex items-center gap-2 justify-center">
                        <a href="' . $edit . '" class="kt-btn kt-btn-sm kt-btn-outline" title="Edit">
                            <i class="ki-filled ki-notepad-edit text-xs"></i>
                        </a>
                        <button class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive" title="Hapus" onclick="deleteKecamatan(\'' . $row->id . '\')">
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
}
