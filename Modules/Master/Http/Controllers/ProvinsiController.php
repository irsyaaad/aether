<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Master\Entities\RegProvince;
use Yajra\DataTables\Facades\DataTables;

class ProvinsiController extends Controller
{
    use HasAccessControl;

    public function index()
    {
        if ($denied = $this->requireAccess('wilayah-provinsi.index')) {
            return $denied;
        }

        return view('master::provinsi.index');
    }

    public function create()
    {
        if ($denied = $this->requireAccess('wilayah-provinsi.create')) {
            return $denied;
        }

        return view('master::provinsi.create');
    }

    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('wilayah-provinsi.create')) {
            return $denied;
        }

        $request->validate([
            'id'   => 'required|string|max:10|unique:reg_provinces,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            RegProvince::create([
                'id'   => $request->id,
                'name' => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-provinsi.index')
                ->with('success', 'Data Provinsi berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        if ($denied = $this->requireAccess('wilayah-provinsi.update')) {
            return $denied;
        }

        $provinsi = RegProvince::findOrFail($id);
        return view('master::provinsi.create', compact('provinsi'));
    }

    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('wilayah-provinsi.update')) {
            return $denied;
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $provinsi = RegProvince::findOrFail($id);
            $provinsi->update([
                'name' => $request->name,
            ]);
            DB::commit();

            return redirect()->route('wilayah-provinsi.index')
                ->with('success', 'Data Provinsi berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($denied = $this->requireAccess('wilayah-provinsi.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            $provinsi = RegProvince::findOrFail($id);
            $provinsi->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Provinsi berhasil dihapus',
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
        $query = RegProvince::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('jumlah_kota_kab', function ($row) {
                return $row->regencies()->count();
            })
            ->addColumn('action', function ($row) {
                $edit = route('wilayah-provinsi.edit', $row->id);

                return '
                    <div class="flex items-center gap-2 justify-center">
                        <a href="' . $edit . '" class="kt-btn kt-btn-sm kt-btn-outline" title="Edit">
                            <i class="ki-filled ki-notepad-edit text-xs"></i>
                        </a>
                        <button class="kt-btn kt-btn-sm kt-btn-outline kt-btn-destructive" title="Hapus" onclick="deleteProvinsi(\'' . $row->id . '\')">
                            <i class="ki-filled ki-trash text-xs"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
