<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Master\Entities\MasterProgram;
use Yajra\DataTables\Facades\DataTables;

class MasterProgramController extends Controller
{
    use HasAccessControl;

    public function index()
    {
        $showcasePrograms = MasterProgram::where('is_active', true)
            ->orderBy('is_populer', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        return view('master::programs.index', compact('showcasePrograms'));
    }

    public function create()
    {
        return view('master::programs.create');
    }

    public function edit($id)
    {
        $program = MasterProgram::findOrFail($id);
        return view('master::programs.edit', compact('program'));
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
                $badgeHtml = '';
                if ($row->is_populer) {
                    $badgeHtml = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 ml-1.5">⭐ Populer</span>';
                } elseif (!empty($row->badge)) {
                    $badgeHtml = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-[#1768B5] ml-1.5">' . e($row->badge) . '</span>';
                }

                $thumbnailHtml = '';
                if ($row->banner_url) {
                    $thumbnailHtml = '<img src="' . e($row->banner_url) . '" alt="Banner" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0 shadow-xs mr-3">';
                } else {
                    $thumbnailHtml = '<div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-100 to-indigo-100 text-[#1768B5] flex items-center justify-center border border-sky-200 shrink-0 shadow-xs mr-3"><i data-lucide="image" class="w-5 h-5 text-sky-500"></i></div>';
                }

                $targetBabHtml = $row->target_bab ? '<div class="text-[11px] text-gray-500 mt-0.5"><i data-lucide="bookmark" class="inline w-3 h-3 text-sky-500"></i> ' . e($row->target_bab) . '</div>' : '';

                return '<div class="flex items-center">
                            ' . $thumbnailHtml . '
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center flex-wrap gap-1">
                                    <span class="font-bold text-gray-900 text-sm leading-tight">' . e($row->nama_program) . '</span>
                                    ' . $badgeHtml . '
                                </div>
                                <span class="text-xs text-sky-600 font-mono mt-0.5">' . e($row->kode_program) . '</span>
                                ' . $targetBabHtml . '
                            </div>
                        </div>';
            })
            ->editColumn('kategori', function ($row) {
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-[#1768B5] border border-sky-200">' . e($row->kategori ?? 'Umum') . '</span>';
            })
            ->editColumn('biaya_standar', function ($row) {
                return '<div class="flex flex-col">
                            <span class="font-black text-gray-900 text-sm">Rp ' . number_format($row->biaya_standar, 0, ',', '.') . '</span>
                            <span class="text-[11px] text-gray-400">per paket</span>
                        </div>';
            })
            ->editColumn('durasi_sesi', function ($row) {
                $sesi = $row->jumlah_sesi ?: 1;
                $durasi = $row->durasi_sesi ?: 60;
                return '<div class="flex flex-col text-xs">
                            <span class="font-bold text-gray-800">' . $sesi . ' Pertemuan</span>
                            <span class="text-gray-500 text-[11px]">@' . $durasi . ' Menit/sesi</span>
                        </div>';
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
                $editUrl = route('master.programs.edit', $row->id);
                return '<div class="flex items-center justify-end gap-1.5">
                            <a href="' . $editUrl . '" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-sky-600 hover:bg-sky-50 transition" title="Edit Program">
                                <i data-lucide="edit-3" class="h-4 w-4"></i>
                            </a>
                            <button onclick="deleteProgram(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Program">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['nama_program', 'kategori', 'biaya_standar', 'durasi_sesi', 'is_active', 'actions'])
            ->make(true);
    }

    public function show($id)
    {
        $program = MasterProgram::findOrFail($id);
        $program->fasilitas_array = $program->fasilitas_array;
        $program->formatted_biaya = $program->formatted_biaya;
        return response()->json($program);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_program' => 'required|string|max:50|unique:master_programs,kode_program',
            'nama_program' => 'required|string|max:150',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'kategori' => 'required|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'target_bab' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            'biaya_standar' => 'required|numeric|min:0',
            'durasi_sesi' => 'nullable|integer|min:15',
            'jumlah_sesi' => 'nullable|integer|min:1',
            'is_populer' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['is_populer'] = $request->has('is_populer') ? true : false;
        $validated['durasi_sesi'] = $validated['durasi_sesi'] ?? 60;
        $validated['jumlah_sesi'] = $validated['jumlah_sesi'] ?? 1;

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = 'program_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('programs', $filename, 'public');
            $validated['banner'] = $path;
        }

        $program = MasterProgram::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Program akademik & bimbingan berhasil ditambahkan.',
                'data' => $program
            ]);
        }

        return redirect()->route('master.programs.index')->with('success', 'Program bimbingan skripsi baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $program = MasterProgram::findOrFail($id);

        $validated = $request->validate([
            'kode_program' => 'required|string|max:50|unique:master_programs,kode_program,' . $id,
            'nama_program' => 'required|string|max:150',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'kategori' => 'required|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'target_bab' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            'biaya_standar' => 'required|numeric|min:0',
            'durasi_sesi' => 'nullable|integer|min:15',
            'jumlah_sesi' => 'nullable|integer|min:1',
            'is_populer' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['is_populer'] = $request->has('is_populer') ? true : false;
        $validated['durasi_sesi'] = $validated['durasi_sesi'] ?? 60;
        $validated['jumlah_sesi'] = $validated['jumlah_sesi'] ?? 1;

        if ($request->has('hapus_banner') && $request->hapus_banner == 1) {
            if ($program->banner && Storage::disk('public')->exists($program->banner)) {
                Storage::disk('public')->delete($program->banner);
            }
            $validated['banner'] = null;
        }

        if ($request->hasFile('banner')) {
            if ($program->banner && Storage::disk('public')->exists($program->banner)) {
                Storage::disk('public')->delete($program->banner);
            }
            $file = $request->file('banner');
            $filename = 'program_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('programs', $filename, 'public');
            $validated['banner'] = $path;
        }

        $program->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Program akademik & bimbingan berhasil diperbarui.',
                'data' => $program
            ]);
        }

        return redirect()->route('master.programs.index')->with('success', 'Perubahan program bimbingan skripsi berhasil disimpan.');
    }

    public function destroy($id)
    {
        $program = MasterProgram::findOrFail($id);
        if ($program->banner && Storage::disk('public')->exists($program->banner)) {
            Storage::disk('public')->delete($program->banner);
        }
        $program->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Program akademik berhasil dihapus.'
        ]);
    }
}
