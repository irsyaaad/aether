<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;
use Modules\Master\Entities\MasterKelas;
use Modules\Master\Entities\MasterProgram;
use Modules\Master\Entities\MasterMentor;
use Yajra\DataTables\Facades\DataTables;

class MasterKelasController extends Controller
{
    use HasAccessControl;

    public function __construct()
    {
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        if (!Schema::hasTable('master_kelas')) {
            Schema::create('master_kelas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_kelas', 50)->unique();
                $table->string('judul', 255);
                $table->string('thumbnail', 255)->nullable();
                $table->unsignedBigInteger('program_id')->nullable();
                $table->unsignedBigInteger('mentor_id')->nullable();
                $table->string('level', 50)->default('Semua Level');
                $table->string('kategori', 100)->nullable();
                $table->text('deskripsi')->nullable();
                $table->text('silabus_singkat')->nullable();
                $table->decimal('harga', 15, 2)->default(0);
                $table->integer('kuota_maksimal')->default(50);
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->string('tipe_kelas', 50)->default('Online Webinar');
                $table->string('link_meeting', 255)->nullable();
                $table->string('badge', 50)->nullable();
                $table->string('status', 50)->default('Dibuka');
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            // Initial seed
            $mentor = MasterMentor::first();
            $mentorId = $mentor ? $mentor->id : null;
            $program = MasterProgram::first();
            $programId = $program ? $program->id : null;

            MasterKelas::insert([
                [
                    'kode_kelas' => 'KLS-PLS-01',
                    'judul' => 'Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4',
                    'program_id' => $programId,
                    'mentor_id' => $mentorId,
                    'level' => 'Intermediate',
                    'kategori' => 'Kuantitatif & SEM',
                    'deskripsi' => 'Panduan lengkap uji validitas, reliabilitas, inner model, f-square, R-square, hingga mediasi moderasi siap pakai sidang.',
                    'silabus_singkat' => '8 Sesi Video Praktik + Template Word Bab 4 + Live Q&A',
                    'harga' => 250000,
                    'kuota_maksimal' => 100,
                    'tanggal_mulai' => now()->addDays(5)->toDateString(),
                    'tanggal_selesai' => now()->addDays(8)->toDateString(),
                    'tipe_kelas' => 'Bootcamp Intensif',
                    'link_meeting' => 'https://meet.google.com/aether-pls',
                    'badge' => 'Terpopuler',
                    'status' => 'Dibuka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_kelas' => 'KLS-EVW-02',
                    'judul' => 'Workshop Regresi Data Panel Menggunakan EViews & Stata untuk Tesis Magister',
                    'program_id' => $programId,
                    'mentor_id' => $mentorId,
                    'level' => 'Advanced',
                    'kategori' => 'Ekonometrika',
                    'deskripsi' => 'Bedah tuntas uji Chow, Hausman, Lagrange Multiplier (LM), estimasi FEM, REM, CEM, dan teknik penanganan heteroskedastisitas.',
                    'silabus_singkat' => '6 Sesi Praktik Data Panel + Do-file Stata & Workfile EViews',
                    'harga' => 350000,
                    'kuota_maksimal' => 50,
                    'tanggal_mulai' => now()->addDays(12)->toDateString(),
                    'tanggal_selesai' => now()->addDays(15)->toDateString(),
                    'tipe_kelas' => 'Online Webinar',
                    'link_meeting' => 'https://zoom.us/j/aether-eviews',
                    'badge' => 'Intensif',
                    'status' => 'Dibuka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_kelas' => 'KLS-SPS-03',
                    'judul' => 'Bootcamp Mahir SPSS: Uji Validitas, Reliabilitas, & Asumsi Klasik Tanpa Pusing',
                    'program_id' => $programId,
                    'mentor_id' => $mentorId,
                    'level' => 'Pemula',
                    'kategori' => 'Statistik Dasar',
                    'deskripsi' => 'Pelatihan komprehensif mengolah data kuisioner skala likert, uji normalitas, multikolinearitas, heteroskedastisitas, dan regresi linier.',
                    'silabus_singkat' => '10 Sesi Interaktif + Master Dataset .SAV Latihan',
                    'harga' => 175000,
                    'kuota_maksimal' => 150,
                    'tanggal_mulai' => now()->addDays(20)->toDateString(),
                    'tanggal_selesai' => now()->addDays(25)->toDateString(),
                    'tipe_kelas' => 'Online Webinar',
                    'link_meeting' => 'https://meet.google.com/aether-spss',
                    'badge' => 'Best Seller',
                    'status' => 'Dibuka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    public function index()
    {
        $programs = MasterProgram::where('is_active', true)->orderBy('nama_program')->get();
        $mentors = MasterMentor::where('is_active', true)->orderBy('nama_lengkap')->get();

        $stats = [
            'total' => MasterKelas::count(),
            'dibuka' => MasterKelas::where('status', 'Dibuka')->count(),
            'berlangsung' => MasterKelas::where('status', 'Berlangsung')->count(),
            'selesai' => MasterKelas::where('status', 'Selesai')->count(),
            'ditutup' => MasterKelas::where('status', 'Ditutup')->count(),
        ];

        $showcaseKelas = MasterKelas::with(['mentor', 'program'])
            ->latest('id')
            ->get();

        return view('master::kelas.index', compact('programs', 'mentors', 'stats', 'showcaseKelas'));
    }

    public function create()
    {
        $programs = MasterProgram::where('is_active', true)->orderBy('nama_program')->get();
        $mentors = MasterMentor::where('is_active', true)->orderBy('nama_lengkap')->get();

        return view('master::kelas.create', compact('programs', 'mentors'));
    }

    public function edit($id)
    {
        $kelas = MasterKelas::findOrFail($id);
        $programs = MasterProgram::where('is_active', true)->orderBy('nama_program')->get();
        $mentors = MasterMentor::where('is_active', true)->orderBy('nama_lengkap')->get();

        return view('master::kelas.edit', compact('kelas', 'programs', 'mentors'));
    }

    public function getData(Request $request)
    {
        $query = MasterKelas::with(['program', 'mentor']);

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('judul_info', function ($row) {
                $badgeHtml = $row->badge 
                    ? '<span class="inline-flex items-center text-[10px] bg-amber-50 text-amber-800 border border-amber-200 font-extrabold px-2 py-0.5 rounded-full ml-1.5 shadow-xs leading-none">⭐ ' . e($row->badge) . '</span>' 
                    : '';
                $mentorName = $row->mentor ? e($row->mentor->nama_lengkap . ($row->mentor->gelar ? ', ' . $row->mentor->gelar : '')) : '<span class="text-gray-400 italic">Belum ditentukan</span>';
                
                $thumbnailHtml = '';
                if ($row->thumbnail_url) {
                    $thumbnailHtml = '<img src="' . e($row->thumbnail_url) . '" alt="Thumbnail" class="w-16 h-11 rounded-xl object-cover border border-gray-200 shadow-xs mr-3 shrink-0">';
                } else {
                    $thumbnailHtml = '<div class="w-16 h-11 rounded-xl bg-gradient-to-br from-indigo-50 via-sky-50 to-purple-50 text-[#1768B5] flex items-center justify-center border border-sky-100 shadow-xs mr-3 shrink-0"><i data-lucide="award" class="w-5 h-5 text-[#1768B5]"></i></div>';
                }

                $programTag = $row->program ? '<span class="text-gray-300">&bull;</span><span class="inline-flex items-center gap-1 text-purple-600 font-medium text-[11px] truncate max-w-[140px]"><i data-lucide="bookmark" class="w-3 h-3 shrink-0"></i>' . e($row->program->nama_program) . '</span>' : '';

                return '<div class="flex items-center">
                            ' . $thumbnailHtml . '
                            <div class="flex flex-col min-w-0 max-w-[340px]">
                                <div class="flex items-center flex-wrap gap-1">
                                    <a href="' . route('master.kelas.show', $row->id) . '" class="font-bold text-gray-900 text-sm hover:text-[#1768B5] transition line-clamp-1" title="' . e($row->judul) . '">' . e($row->judul) . '</a>
                                    ' . $badgeHtml . '
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-1 flex-wrap">
                                    <span class="font-mono text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-100 px-1.5 py-0.5 rounded leading-none">' . e($row->kode_kelas) . '</span>
                                    <span class="text-gray-300">&bull;</span>
                                    <span class="inline-flex items-center gap-1 text-[11px] text-gray-600 font-medium"><i data-lucide="user" class="w-3 h-3 text-gray-400"></i>' . $mentorName . '</span>
                                    ' . $programTag . '
                                </div>
                            </div>
                        </div>';
            })
            ->addColumn('level_kategori', function ($row) {
                $levelBadges = [
                    'Pemula' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Intermediate' => 'bg-sky-50 text-sky-700 border-sky-200',
                    'Advanced' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'Semua Level' => 'bg-slate-50 text-slate-700 border-slate-200',
                ];
                $lvlColor = $levelBadges[$row->level] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                return '<div class="flex flex-col gap-1 items-start">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border ' . $lvlColor . '">' . e($row->level ?? 'Semua Level') . '</span>
                            <span class="text-[11px] text-gray-500 font-medium flex items-center gap-1"><i data-lucide="tag" class="w-3 h-3 text-gray-400"></i>' . e($row->kategori ?? 'Umum') . '</span>
                        </div>';
            })
            ->addColumn('harga_formatted', function ($row) {
                return '<div class="flex flex-col">
                            <span class="text-xs font-black font-mono text-emerald-700">Rp ' . number_format($row->harga, 0, ',', '.') . '</span>
                            <span class="text-[11px] text-gray-500 flex items-center gap-1 mt-0.5">
                                <i data-lucide="users" class="w-3 h-3 text-gray-400"></i>
                                ' . $row->kuota_maksimal . ' kuota
                            </span>
                        </div>';
            })
            ->addColumn('jadwal_tipe', function ($row) {
                $mulai = $row->tanggal_mulai ? date('d M Y', strtotime($row->tanggal_mulai)) : '-';
                $selesai = $row->tanggal_selesai ? date('d M Y', strtotime($row->tanggal_selesai)) : '-';
                $tipeBadge = '<span class="inline-flex items-center text-[10px] font-bold text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-100">' . e($row->tipe_kelas) . '</span>';
                return '<div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold text-gray-800 flex items-center gap-1.5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                ' . $mulai . ' s/d ' . $selesai . '
                            </span>
                            <div>' . $tipeBadge . '</div>
                        </div>';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'Dibuka') {
                    return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Dibuka
                            </span>';
                } elseif ($row->status === 'Berlangsung') {
                    return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span> Berlangsung
                            </span>';
                } elseif ($row->status === 'Selesai') {
                    return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-gray-500"></i> Selesai
                            </span>';
                } else {
                    return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                <i data-lucide="ban" class="w-3.5 h-3.5 text-rose-500"></i> Ditutup
                            </span>';
                }
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('master.kelas.show', $row->id);
                $editUrl = route('master.kelas.edit', $row->id);
                $escapedJudul = e(addslashes($row->judul));

                $statusButtons = '';
                if ($row->status !== 'Ditutup') {
                    $statusButtons .= '<button type="button" onclick="ubahStatusKelas(' . $row->id . ', \'Ditutup\', \'' . $escapedJudul . '\')" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg border border-gray-200 transition" title="Tutup Kelas (Kunci Pendaftaran)">
                                            <i data-lucide="ban" class="w-4 h-4"></i>
                                        </button>';
                }
                if ($row->status !== 'Selesai') {
                    $statusButtons .= '<button type="button" onclick="ubahStatusKelas(' . $row->id . ', \'Selesai\', \'' . $escapedJudul . '\')" class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg border border-gray-200 transition" title="Tandai Kelas Selesai">
                                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                        </button>';
                }
                if ($row->status === 'Ditutup' || $row->status === 'Selesai') {
                    $statusButtons .= '<button type="button" onclick="ubahStatusKelas(' . $row->id . ', \'Dibuka\', \'' . $escapedJudul . '\')" class="p-1.5 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-lg border border-gray-200 transition" title="Buka Kembali Pendaftaran">
                                            <i data-lucide="play-circle" class="w-4 h-4"></i>
                                        </button>';
                }

                return '<div class="flex items-center justify-end gap-1.5">
                            <a href="' . $detailUrl . '" class="p-1.5 text-[#1768B5] hover:bg-sky-50 rounded-lg border border-gray-200 transition" title="Lihat Detail Kelas & Daftar Pembeli">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="' . route('master.materi.index', ['kelas_id' => $row->id]) . '" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg border border-gray-200 transition" title="Kelola Materi & Silabus">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                            </a>
                            <a href="' . $editUrl . '" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg border border-gray-200 transition" title="Edit Kelas">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            ' . $statusButtons . '
                            <button type="button" onclick="deleteKelas(' . $row->id . ', \'' . $escapedJudul . '\')" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-gray-200 transition" title="Hapus Kelas">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['judul_info', 'level_kategori', 'harga_formatted', 'jadwal_tipe', 'status_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kelas' => 'required|string|max:50|unique:master_kelas,kode_kelas',
            'judul' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'mentor_id' => 'nullable|exists:master_mentors,id',
            'program_id' => 'nullable|exists:master_programs,id',
            'level' => 'required|string|max:50',
            'kategori' => 'nullable|string|max:100',
            'tipe_kelas' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'kuota_maksimal' => 'required|integer|min:1',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'link_meeting' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:50',
            'status' => 'required|string',
            'deskripsi' => 'nullable|string',
            'silabus_singkat' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        if (!$request->has('is_active') && !$request->has('_form_page')) {
            $validated['is_active'] = true;
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = 'kelas_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('kelas', $filename, 'public');
            $validated['thumbnail'] = $path;
        }

        $kelas = MasterKelas::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kelas berhasil ditambahkan',
                'data' => $kelas
            ]);
        }

        return redirect()->route('master.kelas.index')->with('success', 'Kelas & Workshop baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kelas = MasterKelas::findOrFail($id);

        $validated = $request->validate([
            'kode_kelas' => 'required|string|max:50|unique:master_kelas,kode_kelas,' . $id,
            'judul' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'mentor_id' => 'nullable|exists:master_mentors,id',
            'program_id' => 'nullable|exists:master_programs,id',
            'level' => 'required|string|max:50',
            'kategori' => 'nullable|string|max:100',
            'tipe_kelas' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'kuota_maksimal' => 'required|integer|min:1',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'link_meeting' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:50',
            'status' => 'required|string',
            'deskripsi' => 'nullable|string',
            'silabus_singkat' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->has('is_active')) {
            $validated['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('hapus_thumbnail') && $request->hapus_thumbnail == 1) {
            if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
                Storage::disk('public')->delete($kelas->thumbnail);
            }
            $validated['thumbnail'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
                Storage::disk('public')->delete($kelas->thumbnail);
            }
            $file = $request->file('thumbnail');
            $filename = 'kelas_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('kelas', $filename, 'public');
            $validated['thumbnail'] = $path;
        }

        $kelas->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data kelas berhasil diperbarui',
                'data' => $kelas
            ]);
        }

        return redirect()->route('master.kelas.index')->with('success', 'Perubahan kelas & workshop berhasil disimpan.');
    }

    public function destroy($id)
    {
        $kelas = MasterKelas::findOrFail($id);
        if ($kelas->thumbnail && Storage::disk('public')->exists($kelas->thumbnail)) {
            Storage::disk('public')->delete($kelas->thumbnail);
        }
        $kelas->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kelas berhasil dihapus'
        ]);
    }

    public function show(Request $request, $id)
    {
        $kelas = MasterKelas::with(['mentor', 'program', 'materis'])->findOrFail($id);

        $transaksis = \Modules\Pelatihan\Entities\TransaksiPelatihan::with('user')
            ->where(function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
                if ($kelas->kode_kelas) {
                    $q->orWhere('kode_kelas', $kelas->kode_kelas);
                }
            })
            ->latest('id')
            ->get();

        $kelas->setRelation('transaksis', $transaksis);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($kelas);
        }

        $stats = [
            'total_pembeli'    => $transaksis->count(),
            'total_lunas'      => $transaksis->where('status_pembayaran', 'Lunas')->count(),
            'total_omset'      => $transaksis->where('status_pembayaran', 'Lunas')->sum('total_bayar'),
            'kuota_maksimal'   => $kelas->kuota_maksimal,
            'sisa_kuota'       => max(0, $kelas->kuota_maksimal - $transaksis->where('status_pembayaran', 'Lunas')->count()),
            'menunggu_bayar'   => $transaksis->whereIn('status_pembayaran', ['Menunggu Pembayaran', 'Menunggu Verifikasi'])->count(),
            'dibatalkan'       => $transaksis->where('status_pembayaran', 'Dibatalkan')->count(),
            'selesai'          => $transaksis->where('status', 'Selesai')->count(),
        ];

        return view('master::kelas.show', compact('kelas', 'transaksis', 'stats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $kelas = MasterKelas::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:Dibuka,Berlangsung,Selesai,Ditutup',
        ]);

        $status = $request->status;
        $kelas->status = $status;
        $kelas->save();

        $messages = [
            'Ditutup' => "Kelas \"{$kelas->judul}\" berhasil ditutup. Pendaftaran telah dinonaktifkan.",
            'Selesai' => "Kelas \"{$kelas->judul}\" berhasil ditandai selesai.",
            'Dibuka' => "Pendaftaran kelas \"{$kelas->judul}\" berhasil dibuka kembali.",
            'Berlangsung' => "Kelas \"{$kelas->judul}\" kini sedang berlangsung.",
        ];

        return response()->json([
            'status' => 'success',
            'message' => $messages[$status] ?? "Status kelas berhasil diubah menjadi {$status}.",
            'data' => [
                'id' => $kelas->id,
                'status' => $kelas->status,
            ]
        ]);
    }
}
