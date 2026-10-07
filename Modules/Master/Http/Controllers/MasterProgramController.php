<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Master\Entities\MasterProgram;
use Yajra\DataTables\Facades\DataTables;

class MasterProgramController extends Controller
{
    use HasAccessControl;

    public function __construct()
    {
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        if (!Schema::hasTable('master_programs')) {
            Schema::create('master_programs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_program', 50)->unique();
                $table->string('nama_program', 150);
                $table->string('kategori', 50)->nullable(); // Skripsi, Tesis, Disertasi, Olahdata, Workshop
                $table->string('jenjang', 50)->nullable(); // S1, S2, S3, Umum
                $table->text('deskripsi')->nullable();
                $table->decimal('biaya_standar', 15, 2)->default(0);
                $table->integer('durasi_sesi')->default(60); // menit
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            // Seed initial program items
            MasterProgram::insert([
                [
                    'kode_program' => 'PRG-SKR-01',
                    'nama_program' => 'Pendampingan Skripsi S1 (Kuantitatif & Bab 4-5)',
                    'kategori' => 'Bimbingan Skripsi',
                    'jenjang' => 'S1 (Sarjana)',
                    'deskripsi' => 'Paket bimbingan intensif 1-on-1 analisis statistik skripsi sampai dinyatakan lulus sidang.',
                    'biaya_standar' => 650000,
                    'durasi_sesi' => 90,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_program' => 'PRG-TSS-01',
                    'nama_program' => 'Bimbingan Tesis S2 (SEM-PLS & Ekonometrika Lanjutan)',
                    'kategori' => 'Bimbingan Tesis',
                    'jenjang' => 'S2 (Magister)',
                    'deskripsi' => 'Pendampingan tesis model kompleks mediasi-moderasi, data panel, dan publikasi jurnal bereputasi.',
                    'biaya_standar' => 1250000,
                    'durasi_sesi' => 90,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_program' => 'PRG-OD-PLS',
                    'nama_program' => 'Olah Data SEM-PLS 4 Full Output & Interpretasi',
                    'kategori' => 'Olah Data',
                    'jenjang' => 'S1 / S2',
                    'deskripsi' => 'Pemrosesan data SmartPLS lengkap uji validitas konvergen, diskriminan, inner model, dan narasi pembahasan.',
                    'biaya_standar' => 450000,
                    'durasi_sesi' => 60,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_program' => 'PRG-WRK-01',
                    'nama_program' => 'Workshop Hands-on Analisis Kualitatif NVivo',
                    'kategori' => 'Pelatihan',
                    'jenjang' => 'Semua Jenjang',
                    'deskripsi' => 'Pelatihan thematic coding, node hierarchy, dan triangulasi data dengan sertifikat kelulusan.',
                    'biaya_standar' => 250000,
                    'durasi_sesi' => 120,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function index()
    {
        return view('master::programs.index');
    }

    public function getData(Request $request)
    {
        $query = MasterProgram::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1' || $request->status === 'active');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nama_program', function ($row) {
                return '<div class="flex flex-col">
                            <span class="font-bold text-gray-900 text-sm">' . e($row->nama_program) . '</span>
                            <span class="text-xs text-sky-600 font-mono mt-0.5">' . e($row->kode_program) . '</span>
                        </div>';
            })
            ->editColumn('kategori', function ($row) {
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-[#1768B5] border border-sky-200">' . e($row->kategori ?? 'Umum') . '</span>';
            })
            ->editColumn('biaya_standar', function ($row) {
                return '<span class="font-bold text-gray-900 text-sm">Rp ' . number_format($row->biaya_standar, 0, ',', '.') . '</span>';
            })
            ->editColumn('is_active', function ($row) {
                if ($row->is_active) {
                    return '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>';
                }
                return '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                            Nonaktif
                        </span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="flex items-center justify-end gap-1.5">
                            <button onclick="editProgram(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-sky-600 hover:bg-sky-50 transition" title="Edit Program">
                                <i data-lucide="edit-3" class="h-4 w-4"></i>
                            </button>
                            <button onclick="deleteProgram(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Program">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['nama_program', 'kategori', 'biaya_standar', 'is_active', 'actions'])
            ->make(true);
    }

    public function show($id)
    {
        $program = MasterProgram::findOrFail($id);
        return response()->json($program);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_program' => 'required|string|max:50|unique:master_programs,kode_program',
            'nama_program' => 'required|string|max:150',
            'kategori' => 'required|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'biaya_standar' => 'required|numeric|min:0',
            'durasi_sesi' => 'nullable|integer|min:15',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $program = MasterProgram::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Program akademik berhasil ditambahkan.',
            'data' => $program
        ]);
    }

    public function update(Request $request, $id)
    {
        $program = MasterProgram::findOrFail($id);

        $validated = $request->validate([
            'kode_program' => 'required|string|max:50|unique:master_programs,kode_program,' . $id,
            'nama_program' => 'required|string|max:150',
            'kategori' => 'required|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'biaya_standar' => 'required|numeric|min:0',
            'durasi_sesi' => 'nullable|integer|min:15',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $program->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Program akademik berhasil diperbarui.',
            'data' => $program
        ]);
    }

    public function destroy($id)
    {
        $program = MasterProgram::findOrFail($id);
        $program->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Program akademik berhasil dihapus.'
        ]);
    }
}
