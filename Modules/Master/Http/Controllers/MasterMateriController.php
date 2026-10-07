<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Master\Entities\MasterMateri;
use Modules\Master\Entities\MasterKelas;
use Yajra\DataTables\Facades\DataTables;

class MasterMateriController extends Controller
{
    use HasAccessControl;

    public function __construct()
    {
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        if (!Schema::hasTable('master_materis')) {
            Schema::create('master_materis', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('kelas_id');
                $table->integer('urutan')->default(1);
                $table->string('judul_materi', 255);
                $table->string('tipe_materi', 50)->default('Video'); // Video, Dokumen PDF, Dataset Praktik, Kuis/Tugas
                $table->integer('durasi_menit')->default(30);
                $table->string('link_video', 255)->nullable();
                $table->string('file_materi', 255)->nullable();
                $table->text('deskripsi')->nullable();
                $table->boolean('is_free_preview')->default(false);
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            // Initial seed
            $kelas = MasterKelas::first();
            $kelasId = $kelas ? $kelas->id : 1;

            MasterMateri::insert([
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 1,
                    'judul_materi' => 'Pengenalan SmartPLS 4 & Format Dataset yang Benar',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 45,
                    'link_video' => 'https://youtube.com/watch?v=sample1',
                    'file_materi' => null,
                    'deskripsi' => 'Pengenalan antarmuka SmartPLS 4, import file csv/sav, dan pengecekan missing value.',
                    'is_free_preview' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 2,
                    'judul_materi' => 'Evaluasi Measurement Model (Outer Model): Validitas & Reliabilitas',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 60,
                    'link_video' => 'https://youtube.com/watch?v=sample2',
                    'file_materi' => null,
                    'deskripsi' => 'Pengujian Cronbach Alpha, Composite Reliability, AVE, HTMT, dan Cross Loadings.',
                    'is_free_preview' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 3,
                    'judul_materi' => 'Template & Panduan Narasi Pembahasan Bab 4 Siap Sidang',
                    'tipe_materi' => 'Dokumen PDF',
                    'durasi_menit' => 0,
                    'link_video' => null,
                    'file_materi' => 'template_bab4_pls.docx',
                    'deskripsi' => 'Template laporan Word format standar universitas dengan tata letak tabel dan penafsiran hipotesis.',
                    'is_free_preview' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 4,
                    'judul_materi' => 'Master Dataset Praktik: 150 Responden Kuisioner Skala Likert',
                    'tipe_materi' => 'Dataset Praktik',
                    'durasi_menit' => 0,
                    'link_video' => null,
                    'file_materi' => 'dataset_latihan_150n.csv',
                    'deskripsi' => 'Dataset riil untuk latihan uji mediasi dan moderasi mandiri.',
                    'is_free_preview' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function index(Request $request)
    {
        $selectedKelasId = $request->query('kelas_id');
        $kelasList = MasterKelas::where('is_active', true)->orderBy('judul')->get();
        $selectedKelas = $selectedKelasId ? MasterKelas::find($selectedKelasId) : $kelasList->first();

        return view('master::materi.index', compact('kelasList', 'selectedKelas', 'selectedKelasId'));
    }

    public function getData(Request $request)
    {
        $query = MasterMateri::with('kelas');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('urutan_badge', function ($row) {
                return '<span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-sky-100 text-[#1768B5] font-bold text-xs">#' . $row->urutan . '</span>';
            })
            ->addColumn('materi_info', function ($row) {
                $previewBadge = $row->is_free_preview 
                    ? '<span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.5 rounded ml-1.5">Free Preview</span>' 
                    : '';
                $kelasJudul = $row->kelas ? e($row->kelas->judul) : '-';

                return '<div class="flex flex-col">
                            <div class="flex items-center">
                                <span class="font-bold text-gray-900 text-sm">' . e($row->judul_materi) . '</span>
                                ' . $previewBadge . '
                            </div>
                            <span class="text-xs text-gray-500 mt-0.5 truncate max-w-md">' . e($row->deskripsi ?? 'Kelas: ' . $kelasJudul) . '</span>
                        </div>';
            })
            ->addColumn('tipe_badge', function ($row) {
                $icons = [
                    'Video' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'Dokumen PDF' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'Dataset Praktik' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Kuis/Tugas' => 'bg-amber-50 text-amber-700 border-amber-200',
                ];
                $color = $icons[$row->tipe_materi] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border ' . $color . '">' . e($row->tipe_materi) . '</span>';
            })
            ->addColumn('durasi_info', function ($row) {
                return $row->durasi_menit > 0 
                    ? '<span class="text-xs font-medium text-gray-700 font-mono">' . $row->durasi_menit . ' Menit</span>'
                    : '<span class="text-xs text-gray-400">File/Modul</span>';
            })
            ->addColumn('action', function ($row) {
                $dataJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                return '<div class="flex items-center justify-end gap-1.5">
                            <button type="button" onclick="editMateri(' . $dataJson . ')" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Materi">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button type="button" onclick="deleteMateri(' . $row->id . ', \'' . e(addslashes($row->judul_materi)) . '\')" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Materi">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['urutan_badge', 'materi_info', 'tipe_badge', 'durasi_info', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:master_kelas,id',
            'judul_materi' => 'required|string|max:255',
            'tipe_materi' => 'required|string',
            'urutan' => 'required|integer|min:1',
        ]);

        $materi = MasterMateri::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Materi berhasil ditambahkan',
            'data' => $materi
        ]);
    }

    public function update(Request $request, $id)
    {
        $materi = MasterMateri::findOrFail($id);

        $request->validate([
            'kelas_id' => 'required|exists:master_kelas,id',
            'judul_materi' => 'required|string|max:255',
            'tipe_materi' => 'required|string',
            'urutan' => 'required|integer|min:1',
        ]);

        $materi->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Materi berhasil diperbarui',
            'data' => $materi
        ]);
    }

    public function destroy($id)
    {
        $materi = MasterMateri::findOrFail($id);
        $materi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Materi berhasil dihapus'
        ]);
    }
}
