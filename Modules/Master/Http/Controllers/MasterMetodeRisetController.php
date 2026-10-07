<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Master\Entities\MasterMetodeRiset;
use Yajra\DataTables\Facades\DataTables;

class MasterMetodeRisetController extends Controller
{
    use HasAccessControl;

    public function __construct()
    {
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        if (!Schema::hasTable('master_metode_risets')) {
            Schema::create('master_metode_risets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_metode', 50)->unique();
                $table->string('nama_metode', 150);
                $table->string('kategori', 50)->default('Kuantitatif'); // Kuantitatif, Kualitatif, Ekonometrika, Mixed Method
                $table->string('software_rekomendasi', 100)->nullable(); // SmartPLS, SPSS, EViews, Stata, NVivo, AMOS
                $table->text('deskripsi')->nullable();
                $table->text('uji_statistik_terkait')->nullable();
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            MasterMetodeRiset::insert([
                [
                    'kode_metode' => 'MET-PLS',
                    'nama_metode' => 'Partial Least Squares Structural Equation Modeling (PLS-SEM)',
                    'kategori' => 'Kuantitatif',
                    'software_rekomendasi' => 'SmartPLS 3 / 4, WarpPLS',
                    'deskripsi' => 'Pendekatan SEM berbasis varian untuk model prediktif, indikator formatif & reflektif, serta sampel kecil-sedang.',
                    'uji_statistik_terkait' => 'Convergent Validity (AVE), Discriminant Validity (HTMT), Path Coefficients, R-Square, Q-Square Predict.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_metode' => 'MET-CBSEM',
                    'nama_metode' => 'Covariance-Based SEM (CB-SEM)',
                    'kategori' => 'Kuantitatif',
                    'software_rekomendasi' => 'IBM SPSS AMOS, LISREL',
                    'deskripsi' => 'Pengujian konfirmasi teori (theory testing) dengan asumsi normalitas multivariate yang ketat.',
                    'uji_statistik_terkait' => 'Goodness of Fit (Chi-Square, RMSEA, GFI, CFI, TLI), Normalitas Multivariat, Modifikasi Model.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_metode' => 'MET-PANEL',
                    'nama_metode' => 'Regresi Data Panel Dinamis & Statis',
                    'kategori' => 'Ekonometrika',
                    'software_rekomendasi' => 'EViews 12, Stata 17',
                    'deskripsi' => 'Analisis data gabungan cross-section dan time-series untuk riset perbankan, keuangan, dan makroekonomi.',
                    'uji_statistik_terkait' => 'Uji Chow (CEM vs FEM), Uji Hausman (FEM vs REM), Uji Lagrange Multiplier, Uji Asumsi Heteroskedastisitas & Autokorelasi.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_metode' => 'MET-KUALI',
                    'nama_metode' => 'Analisis Tematik Kualitatif & Fenomenologi',
                    'kategori' => 'Kualitatif',
                    'software_rekomendasi' => 'NVivo, ATLAS.ti',
                    'deskripsi' => 'Kodifikasi wawancara mendalam, transkrip FGD, dan triangulasi data tematik.',
                    'uji_statistik_terkait' => 'Thematic Coding, Word Frequency Query, Matrix Coding, Credibility & Dependability.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function index()
    {
        return view('master::metode.index');
    }

    public function getData(Request $request)
    {
        $query = MasterMetodeRiset::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('metode_info', function ($row) {
                return '<div class="flex flex-col">
                            <span class="font-bold text-gray-900 text-sm">' . e($row->nama_metode) . '</span>
                            <span class="text-xs text-gray-500 font-mono text-[#1768B5] font-semibold mt-0.5">' . e($row->kode_metode) . '</span>
                        </div>';
            })
            ->addColumn('kategori_badge', function ($row) {
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-[#1768B5] border border-sky-200">' . e($row->kategori) . '</span>';
            })
            ->addColumn('software_badge', function ($row) {
                return '<span class="text-xs font-semibold text-gray-800">' . e($row->software_rekomendasi ?? '-') . '</span>';
            })
            ->addColumn('action', function ($row) {
                $dataJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                return '<div class="flex items-center justify-end gap-1.5">
                            <button type="button" onclick="editMetode(' . $dataJson . ')" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Metode">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button type="button" onclick="deleteMetode(' . $row->id . ', \'' . e(addslashes($row->nama_metode)) . '\')" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Metode">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['metode_info', 'kategori_badge', 'software_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_metode' => 'required|string|max:50|unique:master_metode_risets,kode_metode',
            'nama_metode' => 'required|string|max:150',
            'kategori' => 'required|string',
        ]);

        $item = MasterMetodeRiset::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Metode riset berhasil ditambahkan',
            'data' => $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = MasterMetodeRiset::findOrFail($id);

        $request->validate([
            'kode_metode' => 'required|string|max:50|unique:master_metode_risets,kode_metode,' . $id,
            'nama_metode' => 'required|string|max:150',
            'kategori' => 'required|string',
        ]);

        $item->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data metode riset berhasil diperbarui',
            'data' => $item
        ]);
    }

    public function destroy($id)
    {
        $item = MasterMetodeRiset::findOrFail($id);
        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Metode riset berhasil dihapus'
        ]);
    }
}
