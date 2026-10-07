<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Master\Entities\MasterMentor;
use Yajra\DataTables\Facades\DataTables;

class MasterMentorController extends Controller
{
    use HasAccessControl;

    public function __construct()
    {
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        if (!Schema::hasTable('master_mentors')) {
            Schema::create('master_mentors', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama_lengkap', 150);
                $table->string('gelar', 50)->nullable();
                $table->string('spesialisasi', 100);
                $table->string('email', 100)->nullable();
                $table->string('no_hp', 25)->nullable();
                $table->string('pendidikan_terakhir', 50)->nullable();
                $table->string('institusi', 150)->nullable();
                $table->string('keahlian_software', 150)->nullable();
                $table->decimal('tarif_per_sesi', 15, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            // Seed initial mentors
            MasterMentor::insert([
                [
                    'nama_lengkap' => 'Nur Atika Rochmah',
                    'gelar' => 'S.Stat., M.Si.',
                    'spesialisasi' => 'Metodologi Kuantitatif & Structural Equation Modeling',
                    'email' => 'nur.atika@aether.test',
                    'no_hp' => '081234567890',
                    'pendidikan_terakhir' => 'S2 Statistika Terapan',
                    'institusi' => 'Institut Teknologi Sepuluh Nopember',
                    'keahlian_software' => 'SmartPLS, AMOS, SPSS, LISREL',
                    'tarif_per_sesi' => 150000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_lengkap' => 'Deny Aditya Pratama',
                    'gelar' => 'S.Stat.',
                    'spesialisasi' => 'Ekonometrika, Time Series & Data Panel',
                    'email' => 'deny.aditya@aether.test',
                    'no_hp' => '081234567891',
                    'pendidikan_terakhir' => 'S1 Statistika',
                    'institusi' => 'Universitas Brawijaya',
                    'keahlian_software' => 'EViews, Stata, R Studio, Python',
                    'tarif_per_sesi' => 125000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_lengkap' => 'Alessandra Natasya Panie',
                    'gelar' => 'S.Psi., M.Psi.',
                    'spesialisasi' => 'Riset Kualitatif, Psikometri & Studi Kasus',
                    'email' => 'alessandra@aether.test',
                    'no_hp' => '081234567892',
                    'pendidikan_terakhir' => 'S2 Psikologi Riset',
                    'institusi' => 'Universitas Gadjah Mada',
                    'keahlian_software' => 'NVivo, ATLAS.ti, MAXQDA',
                    'tarif_per_sesi' => 135000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_lengkap' => 'Arrum Rochmani Hidayah',
                    'gelar' => 'M.Kom.',
                    'spesialisasi' => 'Data Science, Text Mining & Machine Learning',
                    'email' => 'arrum.rochmani@aether.test',
                    'no_hp' => '081234567893',
                    'pendidikan_terakhir' => 'S2 Ilmu Komputer',
                    'institusi' => 'Universitas Indonesia',
                    'keahlian_software' => 'Python, Orange Data Mining, R, RapidMiner',
                    'tarif_per_sesi' => 150000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    public function index()
    {
        return view('master::mentors.index');
    }

    public function getData(Request $request)
    {
        $query = MasterMentor::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nama_lengkap', function ($row) {
                return '<div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-sky-100 text-[#1768B5] font-black flex items-center justify-center text-xs shrink-0">
                                ' . strtoupper(substr($row->nama_lengkap, 0, 2)) . '
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 text-sm">' . e($row->nama_lengkap) . ($row->gelar ? ', ' . e($row->gelar) : '') . '</span>
                                <span class="text-xs text-gray-500">' . e($row->spesialisasi) . '</span>
                            </div>
                        </div>';
            })
            ->editColumn('keahlian_software', function ($row) {
                $tools = explode(',', $row->keahlian_software ?? '');
                $html = '<div class="flex flex-wrap gap-1">';
                foreach ($tools as $t) {
                    $t = trim($t);
                    if ($t) {
                        $html .= '<span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[11px] font-semibold">' . e($t) . '</span>';
                    }
                }
                $html .= '</div>';
                return $html;
            })
            ->editColumn('tarif_per_sesi', function ($row) {
                return '<span class="font-bold text-gray-900 text-sm">Rp ' . number_format($row->tarif_per_sesi, 0, ',', '.') . '</span><span class="text-[10px] text-gray-400">/sesi</span>';
            })
            ->editColumn('is_active', function ($row) {
                if ($row->is_active) {
                    return '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Siap Membimbing
                            </span>';
                }
                return '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                            Cuti / Penuh
                        </span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="flex items-center justify-end gap-1.5">
                            <button onclick="editMentor(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-sky-600 hover:bg-sky-50 transition" title="Edit Mentor">
                                <i data-lucide="edit-3" class="h-4 w-4"></i>
                            </button>
                            <button onclick="deleteMentor(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Mentor">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['nama_lengkap', 'keahlian_software', 'tarif_per_sesi', 'is_active', 'actions'])
            ->make(true);
    }

    public function show($id)
    {
        return response()->json(MasterMentor::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'gelar' => 'nullable|string|max:50',
            'spesialisasi' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'no_hp' => 'nullable|string|max:25',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'institusi' => 'nullable|string|max:150',
            'keahlian_software' => 'nullable|string|max:150',
            'tarif_per_sesi' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $mentor = MasterMentor::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data mentor akademik berhasil ditambahkan.',
            'data' => $mentor
        ]);
    }

    public function update(Request $request, $id)
    {
        $mentor = MasterMentor::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'gelar' => 'nullable|string|max:50',
            'spesialisasi' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'no_hp' => 'nullable|string|max:25',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'institusi' => 'nullable|string|max:150',
            'keahlian_software' => 'nullable|string|max:150',
            'tarif_per_sesi' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $mentor->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data mentor akademik berhasil diperbarui.',
            'data' => $mentor
        ]);
    }

    public function destroy($id)
    {
        $mentor = MasterMentor::findOrFail($id);
        $mentor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data mentor berhasil dihapus.'
        ]);
    }
}
