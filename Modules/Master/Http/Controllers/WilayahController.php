<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Master\Entities\RegDistrict;
use Modules\Master\Entities\RegProvince;
use Modules\Master\Entities\RegRegency;
use Modules\Master\Entities\RegVillage;

class WilayahController extends Controller
{
    use HasAccessControl;

    public function index()
    {
        if ($denied = $this->requireAccess('wilayah-provinsi.index')) {
            return $denied;
        }

        return view('master::wilayah.index');
    }

    public function getChildren(Request $request)
    {
        $level = $request->level;
        $parentId = $request->parent_id;

        if ($level == 'provinsi') {
            $items = RegProvince::orderBy('name')
                ->select('id', 'name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'has_children' => true,
                        'count' => RegRegency::where('province_id', $item->id)->count(),
                    ];
                });
        } elseif ($level == 'kota_kab') {
            $items = RegRegency::where('province_id', $parentId)
                ->orderBy('name')
                ->select('id', 'name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'has_children' => true,
                        'count' => RegDistrict::where('regency_id', $item->id)->count(),
                    ];
                });
        } elseif ($level == 'kecamatan') {
            $items = RegDistrict::where('regency_id', $parentId)
                ->orderBy('name')
                ->select('id', 'name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'has_children' => true,
                        'count' => RegVillage::where('district_id', $item->id)->count(),
                    ];
                });
        } elseif ($level == 'desa') {
            $items = RegVillage::where('district_id', $parentId)
                ->orderBy('name')
                ->select('id', 'name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'has_children' => false,
                        'count' => 0,
                    ];
                });
        } else {
            return response()->json(['items' => []]);
        }

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $level = $request->level;

        $rules = [
            'name' => 'required|string|max:255',
        ];

        if ($level == 'provinsi') {
            $rules['id'] = 'required|string|max:10|unique:reg_provinces,id';
        } elseif ($level == 'kota_kab') {
            $rules['id'] = 'required|string|max:10|unique:reg_regencies,id';
            $rules['parent_id'] = 'required|exists:reg_provinces,id';
        } elseif ($level == 'kecamatan') {
            $rules['id'] = 'required|string|max:10|unique:reg_districts,id';
            $rules['parent_id'] = 'required|exists:reg_regencies,id';
        } elseif ($level == 'desa') {
            $rules['id'] = 'required|string|max:20|unique:reg_villages,id';
            $rules['parent_id'] = 'required|exists:reg_districts,id';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            if ($level == 'provinsi') {
                RegProvince::create(['id' => $request->id, 'name' => $request->name]);
            } elseif ($level == 'kota_kab') {
                RegRegency::create(['id' => $request->id, 'province_id' => $request->parent_id, 'name' => $request->name]);
            } elseif ($level == 'kecamatan') {
                RegDistrict::create(['id' => $request->id, 'regency_id' => $request->parent_id, 'name' => $request->name]);
            } elseif ($level == 'desa') {
                RegVillage::create(['id' => $request->id, 'district_id' => $request->parent_id, 'name' => $request->name]);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $level = $request->level;

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            if ($level == 'provinsi') {
                $item = RegProvince::findOrFail($id);
                $item->update(['name' => $request->name]);
            } elseif ($level == 'kota_kab') {
                $item = RegRegency::findOrFail($id);
                $item->update(['name' => $request->name]);
            } elseif ($level == 'kecamatan') {
                $item = RegDistrict::findOrFail($id);
                $item->update(['name' => $request->name]);
            } elseif ($level == 'desa') {
                $item = RegVillage::findOrFail($id);
                $item->update(['name' => $request->name]);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $th->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $level = $request->level;

        try {
            DB::beginTransaction();

            if ($level == 'provinsi') {
                $item = RegProvince::findOrFail($id);
                $item->delete();
            } elseif ($level == 'kota_kab') {
                $item = RegRegency::findOrFail($id);
                $item->delete();
            } elseif ($level == 'kecamatan') {
                $item = RegDistrict::findOrFail($id);
                $item->delete();
            } elseif ($level == 'desa') {
                $item = RegVillage::findOrFail($id);
                $item->delete();
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $th->getMessage()], 500);
        }
    }
}
