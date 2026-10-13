<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Master\Entities\MasterCoa;
use Yajra\DataTables\Facades\DataTables;

class MasterCoaController extends Controller
{
    use HasAccessControl;

    public function index()
    {
        $stats = [
            'total' => MasterCoa::count(),
            'neraca' => MasterCoa::where('laporan', 'Neraca')->count(),
            'labarugi' => MasterCoa::where('laporan', 'Laba Rugi')->count(),
            'aset' => MasterCoa::where('tipe', 'Aset')->count(),
            'kewajiban' => MasterCoa::where('tipe', 'Kewajiban')->count(),
            'ekuitas' => MasterCoa::where('tipe', 'Ekuitas')->count(),
            'pendapatan' => MasterCoa::where('tipe', 'Pendapatan')->count(),
            'beban' => MasterCoa::where('tipe', 'Beban')->count(),
            'aktif' => MasterCoa::where('is_active', true)->count(),
        ];

        $kategoriList = MasterCoa::select('kategori')->distinct()->pluck('kategori');

        return view('master::coa.index', compact('stats', 'kategoriList'));
    }

    public function getData(Request $request)
    {
        $query = MasterCoa::query();

        if ($request->filled('laporan')) {
            $query->where('laporan', $request->laporan);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('posisi')) {
            $query->where('posisi_normal', $request->posisi);
        }

        if ($request->filled('status')) {
            if ($request->status === '1' || $request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === '0' || $request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('kode_akun', function ($row) {
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">' . e($row->kode_akun) . '</span>';
            })
            ->editColumn('nama_akun', function ($row) {
                return '<div class="flex flex-col">
                            <span class="font-bold text-gray-900 text-sm">' . e($row->nama_akun) . '</span>
                            ' . ($row->deskripsi ? '<span class="text-xs text-gray-500 line-clamp-1 mt-0.5">' . e($row->deskripsi) . '</span>' : '') . '
                        </div>';
            })
            ->editColumn('laporan', function ($row) {
                if ($row->laporan === 'Neraca') {
                    return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Neraca
                            </span>';
                }
                return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>Laba Rugi
                        </span>';
            })
            ->editColumn('kategori', function ($row) {
                $kat = strtoupper($row->kategori);
                if ($kat === 'ASET LANCAR') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aset Lancar</span>';
                } elseif ($kat === 'ASET TETAP') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">Aset Tetap</span>';
                } elseif ($kat === 'KEWAJIBAN LANCAR') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Kewajiban Lancar</span>';
                } elseif ($kat === 'KEWAJIBAN JANGKA PANJANG') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">Kewajiban Jk. Panjang</span>';
                } elseif ($kat === 'EKUITAS') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">Ekuitas / Modal</span>';
                } elseif ($kat === 'PENDAPATAN JASA') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Pendapatan Jasa</span>';
                } elseif ($kat === 'BEBAN OPERASIONAL') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Beban Operasional</span>';
                }
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">' . e($row->kategori) . '</span>';
            })
            ->editColumn('tipe', function ($row) {
                return '<span class="text-xs font-medium text-gray-700">' . e($row->tipe ?? '-') . '</span>';
            })
            ->editColumn('posisi_normal', function ($row) {
                $badgeClass = $row->posisi_normal === 'Debit' 
                    ? 'bg-blue-50 text-blue-700 border-blue-200' 
                    : 'bg-purple-50 text-purple-700 border-purple-200';
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border ' . $badgeClass . '">' . e($row->posisi_normal) . '</span>';
            })
            ->editColumn('saldo_awal', function ($row) {
                return '<span class="text-xs font-mono font-bold text-gray-800">Rp ' . number_format($row->saldo_awal ?? 0, 0, ',', '.') . '</span>';
            })
            ->editColumn('is_active', function ($row) {
                if ($row->is_active) {
                    return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>';
                }
                return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                            Nonaktif
                        </span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="flex items-center justify-end gap-1.5">
                            <button type="button" onclick="editCoa(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-sky-600 hover:bg-sky-50 transition" title="Edit Akun CoA">
                                <i data-lucide="edit-3" class="h-4 w-4"></i>
                            </button>
                            <button type="button" onclick="deleteCoa(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Akun CoA">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['kode_akun', 'nama_akun', 'laporan', 'kategori', 'tipe', 'posisi_normal', 'saldo_awal', 'is_active', 'actions'])
            ->make(true);
    }

    public function show($id)
    {
        $coa = MasterCoa::findOrFail($id);
        return response()->json($coa);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_akun' => 'required|string|max:50|unique:master_coas,kode_akun,NULL,id,deleted_at,NULL',
            'nama_akun' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'tipe' => 'required|string|max:50',
            'laporan' => 'nullable|string|in:Neraca,Laba Rugi',
            'posisi_normal' => 'required|in:Debit,Kredit',
            'saldo_awal' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['saldo_awal'] = $validated['saldo_awal'] ?? 0;

        if (empty($validated['laporan'])) {
            $validated['laporan'] = in_array($validated['tipe'], ['Pendapatan', 'Beban']) ? 'Laba Rugi' : 'Neraca';
        }

        $coa = MasterCoa::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Akun CoA berhasil ditambahkan.',
            'data' => $coa,
        ]);
    }

    public function update(Request $request, $id)
    {
        $coa = MasterCoa::findOrFail($id);

        $validated = $request->validate([
            'kode_akun' => 'required|string|max:50|unique:master_coas,kode_akun,' . $id . ',id,deleted_at,NULL',
            'nama_akun' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'tipe' => 'required|string|max:50',
            'laporan' => 'nullable|string|in:Neraca,Laba Rugi',
            'posisi_normal' => 'required|in:Debit,Kredit',
            'saldo_awal' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['saldo_awal'] = $validated['saldo_awal'] ?? 0;

        if (empty($validated['laporan'])) {
            $validated['laporan'] = in_array($validated['tipe'], ['Pendapatan', 'Beban']) ? 'Laba Rugi' : 'Neraca';
        }

        $coa->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Akun CoA berhasil diperbarui.',
            'data' => $coa,
        ]);
    }

    public function destroy($id)
    {
        $coa = MasterCoa::findOrFail($id);
        $coa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Akun CoA berhasil dihapus.',
        ]);
    }
}
