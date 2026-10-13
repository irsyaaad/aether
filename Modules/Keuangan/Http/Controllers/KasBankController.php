<?php

namespace Modules\Keuangan\Http\Controllers;

use App\Traits\HasAccessControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Keuangan\Entities\TransaksiKasBank;
use Modules\Master\Entities\MasterCoa;
use Yajra\DataTables\Facades\DataTables;

class KasBankController extends Controller
{
    use HasAccessControl;

    /**
     * Tampilan Menu Transaksi Kas / Bank Masuk
     */
    public function indexMasuk(Request $request)
    {
        if ($denied = $this->requireAccess('kas-bank-masuk.index')) {
            return $denied;
        }

        $now = Carbon::now();

        // Statistik Kas Masuk
        $stats = [
            'total_bulan_ini' => TransaksiKasBank::masuk()
                ->whereYear('tanggal', $now->year)
                ->whereMonth('tanggal', $now->month)
                ->sum('nominal'),
            'total_hari_ini' => TransaksiKasBank::masuk()
                ->whereDate('tanggal', $now->toDateString())
                ->sum('nominal'),
            'count_bulan_ini' => TransaksiKasBank::masuk()
                ->whereYear('tanggal', $now->year)
                ->whereMonth('tanggal', $now->month)
                ->count(),
            'total_akumulasi' => TransaksiKasBank::masuk()->sum('nominal'),
        ];

        // Pilihan Akun Kas / Bank (Ambil dari CoA yang kas bank)
        $kasBankList = MasterCoa::kasBank()->orderBy('kode_akun')->get();

        // Pilihan Akun Lawan (Ambil dari CoA yang beban dan pendapatan)
        $bebanList = MasterCoa::beban()->orderBy('kode_akun')->get();
        $pendapatanList = MasterCoa::pendapatan()->orderBy('kode_akun')->get();

        // Rekap Saldo Kas & Bank Terkini
        $saldoKasBank = $this->calculateSaldoKasBank($kasBankList);

        $defaultNoTransaksi = TransaksiKasBank::generateNomorTransaksi('masuk', $now->toDateString());

        return view('keuangan::kas-bank.masuk', compact(
            'stats',
            'kasBankList',
            'bebanList',
            'pendapatanList',
            'saldoKasBank',
            'defaultNoTransaksi'
        ));
    }

    /**
     * DataTables Data untuk Kas Masuk
     */
    public function getDataMasuk(Request $request)
    {
        if (!$this->hasAccess('kas-bank-masuk.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = TransaksiKasBank::with(['kasBank', 'lawanCoa', 'user'])
            ->masuk();

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('kas_bank_id')) {
            $query->where('kas_bank_id', $request->kas_bank_id);
        }

        if ($request->filled('lawan_coa_id')) {
            $query->where('lawan_coa_id', $request->lawan_coa_id);
        }

        $canUpdate = $this->hasAccess('kas-bank-masuk.update');
        $canDelete = $this->hasAccess('kas-bank-masuk.delete') || $this->hasAccess('kas-bank-masuk.destroy');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nomor_transaksi', function ($row) {
                return '<div class="flex flex-col gap-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 w-fit">' . e($row->nomor_transaksi) . '</span>
                            <span class="text-[11px] font-medium text-gray-500 mt-0.5">' . Carbon::parse($row->tanggal)->translatedFormat('d M Y') . '</span>
                        </div>';
            })
            ->filterColumn('nomor_transaksi', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nomor_transaksi', 'like', "%{$keyword}%")
                      ->orWhere('tanggal', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('nomor_transaksi', function ($query, $order) {
                $query->orderBy('tanggal', $order)->orderBy('id', $order);
            })
            ->editColumn('tanggal', function ($row) {
                return '<span class="text-xs font-medium text-gray-700">' . Carbon::parse($row->tanggal)->translatedFormat('d M Y') . '</span>';
            })
            ->addColumn('akun', function ($row) {
                $kasText = $row->kasBank ? e($row->kasBank->nama_akun) . ' <span class="text-[11px] font-mono text-gray-400">(' . e($row->kasBank->kode_akun) . ')</span>' : '<span class="text-xs text-gray-400">-</span>';
                $lawanText = $row->lawanCoa ? e($row->lawanCoa->nama_akun) . ' <span class="text-[11px] font-mono text-gray-400">(' . e($row->lawanCoa->kode_akun) . ')</span>' : '<span class="text-xs text-gray-400">-</span>';

                return '<div class="flex flex-col gap-1 text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Debet">D</span>
                                <span class="font-bold text-gray-900">' . $kasText . '</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200" title="Kredit">K</span>
                                <span class="text-gray-700">' . $lawanText . '</span>
                            </div>
                        </div>';
            })
            ->filterColumn('akun', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('kasBank', function ($qb) use ($keyword) {
                        $qb->where('nama_akun', 'like', "%{$keyword}%")
                           ->orWhere('kode_akun', 'like', "%{$keyword}%");
                    })->orWhereHas('lawanCoa', function ($qb) use ($keyword) {
                        $qb->where('nama_akun', 'like', "%{$keyword}%")
                           ->orWhere('kode_akun', 'like', "%{$keyword}%");
                    });
                });
            })
            ->editColumn('kas_bank', function ($row) {
                if (!$row->kasBank) return '<span class="text-xs text-gray-400">-</span>';
                return '<div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-900">' . e($row->kasBank->nama_akun) . '</span>
                            <span class="text-[11px] font-mono text-gray-500">' . e($row->kasBank->kode_akun) . '</span>
                        </div>';
            })
            ->editColumn('lawan_coa', function ($row) {
                if (!$row->lawanCoa) return '<span class="text-xs text-gray-400">-</span>';
                $badgeClass = $row->lawanCoa->tipe === 'Beban' ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-teal-700 bg-teal-50 border-teal-200';
                return '<div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-900">' . e($row->lawanCoa->nama_akun) . '</span>
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="text-[10px] font-mono font-bold px-1.5 py-0.2 rounded border ' . $badgeClass . '">' . e($row->lawanCoa->kode_akun) . '</span>
                                <span class="text-[11px] text-gray-500">' . e($row->lawanCoa->kategori) . '</span>
                            </div>
                        </div>';
            })
            ->editColumn('penerima_pembayar', function ($row) {
                return '<span class="text-xs text-gray-800 font-medium">' . e($row->penerima_pembayar ?: '-') . '</span>';
            })
            ->editColumn('nominal', function ($row) {
                return '<div class="text-right">
                            <span class="text-xs font-mono font-bold text-emerald-600">+ Rp ' . number_format($row->nominal, 0, ',', '.') . '</span>
                        </div>';
            })
            ->editColumn('keterangan', function ($row) {
                return '<span class="text-xs text-gray-600 line-clamp-1" title="' . e($row->keterangan) . '">' . e($row->keterangan ?: '-') . '</span>';
            })
            ->addColumn('actions', function ($row) use ($canUpdate, $canDelete) {
                $btnVoucher = '<a href="' . route('kas-bank.voucher', $row->id) . '" target="_blank" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Cetak Bukti Kas Masuk (Voucher)">
                                    <i data-lucide="printer" class="h-4 w-4"></i>
                                </a>';
                $btnDetail = '<button type="button" onclick="showDetail(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition" title="Lihat Rincian Transaksi">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>';
                $btnEdit = $canUpdate ? '<a href="' . route('kas-bank-masuk.edit', $row->id) . '" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-sky-600 hover:bg-sky-50 transition" title="Edit Transaksi">
                                    <i data-lucide="edit-3" class="h-4 w-4"></i>
                                </a>' : '';
                $btnDelete = $canDelete ? '<button type="button" onclick="deleteTransaksi(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Transaksi">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>' : '';

                return '<div class="flex items-center justify-end gap-1">' . $btnVoucher . $btnDetail . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['nomor_transaksi', 'tanggal', 'akun', 'kas_bank', 'lawan_coa', 'penerima_pembayar', 'nominal', 'keterangan', 'actions'])
            ->make(true);
    }

    /**
     * Halaman Buat Transaksi Kas / Bank Masuk (Dedicated Page)
     */
    public function createMasuk()
    {
        if ($denied = $this->requireAccess('kas-bank-masuk.create')) {
            return $denied;
        }

        $defaultNoTransaksi = TransaksiKasBank::generateNomorTransaksi('masuk', date('Y-m-d'));

        return view('keuangan::kas-bank.create-masuk', compact('defaultNoTransaksi'));
    }

    /**
     * Halaman Edit Transaksi Kas / Bank Masuk (Dedicated Page)
     */
    public function editMasuk($id)
    {
        if ($denied = $this->requireAccess('kas-bank-masuk.update')) {
            return $denied;
        }

        $transaksi = TransaksiKasBank::with(['kasBank', 'lawanCoa'])->findOrFail($id);

        return view('keuangan::kas-bank.edit-masuk', compact('transaksi'));
    }

    /**
     * Simpan Transaksi Kas / Bank Masuk
     */
    public function storeMasuk(Request $request)
    {
        if (!$this->hasAccess('kas-bank-masuk.create') && !$this->hasAccess('kas-bank-masuk.store')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk mencatat transaksi Kas Masuk.',
            ], 403);
        }

        $validated = $this->validateTransaksi($request, 'masuk');

        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('bukti_kas_bank', 'public');
            $validated['lampiran'] = $path;
        }

        $validated['jenis'] = 'masuk';
        $validated['user_id'] = Auth::user() ? Auth::user()->id_user : null;

        if (empty($validated['nomor_transaksi'])) {
            $validated['nomor_transaksi'] = TransaksiKasBank::generateNomorTransaksi('masuk', $validated['tanggal']);
        }

        $transaksi = TransaksiKasBank::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi Kas/Bank Masuk berhasil disimpan.',
            'redirect_url' => route('kas-bank-masuk.index'),
            'data' => $transaksi,
        ]);
    }

    /**
     * Tampilan Menu Transaksi Kas / Bank Keluar
     */
    public function indexKeluar(Request $request)
    {
        if ($denied = $this->requireAccess('kas-bank-keluar.index')) {
            return $denied;
        }

        $now = Carbon::now();

        // Statistik Kas Keluar
        $stats = [
            'total_bulan_ini' => TransaksiKasBank::keluar()
                ->whereYear('tanggal', $now->year)
                ->whereMonth('tanggal', $now->month)
                ->sum('nominal'),
            'total_hari_ini' => TransaksiKasBank::keluar()
                ->whereDate('tanggal', $now->toDateString())
                ->sum('nominal'),
            'count_bulan_ini' => TransaksiKasBank::keluar()
                ->whereYear('tanggal', $now->year)
                ->whereMonth('tanggal', $now->month)
                ->count(),
            'total_akumulasi' => TransaksiKasBank::keluar()->sum('nominal'),
        ];

        // Pilihan Akun Kas / Bank (Ambil dari CoA yang kas bank)
        $kasBankList = MasterCoa::kasBank()->orderBy('kode_akun')->get();

        // Pilihan Akun Lawan (Ambil dari CoA yang beban operasional)
        $bebanList = MasterCoa::beban()->orderBy('kode_akun')->get();

        // Rekap Saldo Kas & Bank Terkini
        $saldoKasBank = $this->calculateSaldoKasBank($kasBankList);

        $defaultNoTransaksi = TransaksiKasBank::generateNomorTransaksi('keluar', $now->toDateString());

        return view('keuangan::kas-bank.keluar', compact(
            'stats',
            'kasBankList',
            'bebanList',
            'saldoKasBank',
            'defaultNoTransaksi'
        ));
    }

    /**
     * DataTables Data untuk Kas Keluar
     */
    public function getDataKeluar(Request $request)
    {
        if (!$this->hasAccess('kas-bank-keluar.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = TransaksiKasBank::with(['kasBank', 'lawanCoa', 'user'])
            ->keluar();

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('kas_bank_id')) {
            $query->where('kas_bank_id', $request->kas_bank_id);
        }

        if ($request->filled('lawan_coa_id')) {
            $query->where('lawan_coa_id', $request->lawan_coa_id);
        }

        $canUpdate = $this->hasAccess('kas-bank-keluar.update');
        $canDelete = $this->hasAccess('kas-bank-keluar.delete') || $this->hasAccess('kas-bank-keluar.destroy');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nomor_transaksi', function ($row) {
                return '<div class="flex flex-col gap-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200 w-fit">' . e($row->nomor_transaksi) . '</span>
                            <span class="text-[11px] font-medium text-gray-500 mt-0.5">' . Carbon::parse($row->tanggal)->translatedFormat('d M Y') . '</span>
                        </div>';
            })
            ->filterColumn('nomor_transaksi', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nomor_transaksi', 'like', "%{$keyword}%")
                      ->orWhere('tanggal', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('nomor_transaksi', function ($query, $order) {
                $query->orderBy('tanggal', $order)->orderBy('id', $order);
            })
            ->editColumn('tanggal', function ($row) {
                return '<span class="text-xs font-medium text-gray-700">' . Carbon::parse($row->tanggal)->translatedFormat('d M Y') . '</span>';
            })
            ->addColumn('akun', function ($row) {
                $lawanText = $row->lawanCoa ? e($row->lawanCoa->nama_akun) . ' <span class="text-[11px] font-mono text-gray-400">(' . e($row->lawanCoa->kode_akun) . ')</span>' : '<span class="text-xs text-gray-400">-</span>';
                $kasText = $row->kasBank ? e($row->kasBank->nama_akun) . ' <span class="text-[11px] font-mono text-gray-400">(' . e($row->kasBank->kode_akun) . ')</span>' : '<span class="text-xs text-gray-400">-</span>';

                return '<div class="flex flex-col gap-1 text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Debet">D</span>
                                <span class="font-bold text-gray-900">' . $lawanText . '</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200" title="Kredit">K</span>
                                <span class="text-gray-700">' . $kasText . '</span>
                            </div>
                        </div>';
            })
            ->filterColumn('akun', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('lawanCoa', function ($qb) use ($keyword) {
                        $qb->where('nama_akun', 'like', "%{$keyword}%")
                           ->orWhere('kode_akun', 'like', "%{$keyword}%");
                    })->orWhereHas('kasBank', function ($qb) use ($keyword) {
                        $qb->where('nama_akun', 'like', "%{$keyword}%")
                           ->orWhere('kode_akun', 'like', "%{$keyword}%");
                    });
                });
            })
            ->editColumn('kas_bank', function ($row) {
                if (!$row->kasBank) return '<span class="text-xs text-gray-400">-</span>';
                return '<div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-900">' . e($row->kasBank->nama_akun) . '</span>
                            <span class="text-[11px] font-mono text-gray-500">' . e($row->kasBank->kode_akun) . '</span>
                        </div>';
            })
            ->editColumn('lawan_coa', function ($row) {
                if (!$row->lawanCoa) return '<span class="text-xs text-gray-400">-</span>';
                return '<div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-900">' . e($row->lawanCoa->nama_akun) . '</span>
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="text-[10px] font-mono font-bold px-1.5 py-0.2 rounded border bg-amber-50 text-amber-700 border-amber-200">' . e($row->lawanCoa->kode_akun) . '</span>
                                <span class="text-[11px] text-gray-500">' . e($row->lawanCoa->kategori) . '</span>
                            </div>
                        </div>';
            })
            ->editColumn('penerima_pembayar', function ($row) {
                return '<span class="text-xs text-gray-800 font-medium">' . e($row->penerima_pembayar ?: '-') . '</span>';
            })
            ->editColumn('nominal', function ($row) {
                return '<div class="text-right">
                            <span class="text-xs font-mono font-bold text-rose-600">- Rp ' . number_format($row->nominal, 0, ',', '.') . '</span>
                        </div>';
            })
            ->editColumn('keterangan', function ($row) {
                return '<span class="text-xs text-gray-600 line-clamp-1" title="' . e($row->keterangan) . '">' . e($row->keterangan ?: '-') . '</span>';
            })
            ->addColumn('actions', function ($row) use ($canUpdate, $canDelete) {
                $btnVoucher = '<a href="' . route('kas-bank.voucher', $row->id) . '" target="_blank" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-rose-600 hover:bg-rose-50 transition" title="Cetak Bukti Kas Keluar (Voucher)">
                                    <i data-lucide="printer" class="h-4 w-4"></i>
                                </a>';
                $btnDetail = '<button type="button" onclick="showDetail(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition" title="Lihat Rincian Transaksi">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>';
                $btnEdit = $canUpdate ? '<a href="' . route('kas-bank-keluar.edit', $row->id) . '" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-sky-600 hover:bg-sky-50 transition" title="Edit Transaksi">
                                    <i data-lucide="edit-3" class="h-4 w-4"></i>
                                </a>' : '';
                $btnDelete = $canDelete ? '<button type="button" onclick="deleteTransaksi(' . $row->id . ')" class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Transaksi">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>' : '';

                return '<div class="flex items-center justify-end gap-1">' . $btnVoucher . $btnDetail . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['nomor_transaksi', 'tanggal', 'akun', 'kas_bank', 'lawan_coa', 'penerima_pembayar', 'nominal', 'keterangan', 'actions'])
            ->make(true);
    }

    /**
     * Halaman Buat Transaksi Kas / Bank Keluar (Dedicated Page)
     */
    public function createKeluar()
    {
        if ($denied = $this->requireAccess('kas-bank-keluar.create')) {
            return $denied;
        }

        $defaultNoTransaksi = TransaksiKasBank::generateNomorTransaksi('keluar', date('Y-m-d'));

        return view('keuangan::kas-bank.create-keluar', compact('defaultNoTransaksi'));
    }

    /**
     * Halaman Edit Transaksi Kas / Bank Keluar (Dedicated Page)
     */
    public function editKeluar($id)
    {
        if ($denied = $this->requireAccess('kas-bank-keluar.update')) {
            return $denied;
        }

        $transaksi = TransaksiKasBank::with(['kasBank', 'lawanCoa'])->findOrFail($id);

        return view('keuangan::kas-bank.edit-keluar', compact('transaksi'));
    }

    /**
     * Simpan Transaksi Kas / Bank Keluar
     */
    public function storeKeluar(Request $request)
    {
        if (!$this->hasAccess('kas-bank-keluar.create') && !$this->hasAccess('kas-bank-keluar.store')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk mencatat transaksi Kas Keluar.',
            ], 403);
        }

        $validated = $this->validateTransaksi($request, 'keluar');

        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('bukti_kas_bank', 'public');
            $validated['lampiran'] = $path;
        }

        $validated['jenis'] = 'keluar';
        $validated['user_id'] = Auth::user() ? Auth::user()->id_user : null;

        if (empty($validated['nomor_transaksi'])) {
            $validated['nomor_transaksi'] = TransaksiKasBank::generateNomorTransaksi('keluar', $validated['tanggal']);
        }

        $transaksi = TransaksiKasBank::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi Kas/Bank Keluar berhasil disimpan.',
            'redirect_url' => route('kas-bank-keluar.index'),
            'data' => $transaksi,
        ]);
    }

    /**
     * Ambil Detail Transaksi untuk Modal / Edit
     */
    public function show($id)
    {
        $transaksi = TransaksiKasBank::with(['kasBank', 'lawanCoa', 'user'])->findOrFail($id);
        $perm = ($transaksi->jenis === 'masuk') ? 'kas-bank-masuk.index' : 'kas-bank-keluar.index';

        if (!$this->hasAccess($perm)) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk melihat rincian transaksi ini.'], 403);
        }
        
        $data = $transaksi->toArray();
        $data['formatted_nominal'] = number_format($transaksi->nominal, 0, ',', '.');
        $data['formatted_tanggal'] = Carbon::parse($transaksi->tanggal)->translatedFormat('d F Y');
        $data['lampiran_url'] = $transaksi->lampiran ? asset('storage/' . $transaksi->lampiran) : null;

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Perbarui Transaksi Kas / Bank
     */
    public function update(Request $request, $id)
    {
        $transaksi = TransaksiKasBank::findOrFail($id);
        $perm = ($transaksi->jenis === 'masuk') ? 'kas-bank-masuk.update' : 'kas-bank-keluar.update';

        if (!$this->hasAccess($perm)) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk mengubah transaksi ini.'], 403);
        }

        $validated = $this->validateTransaksi($request, $transaksi->jenis, $id);

        if ($request->hasFile('lampiran')) {
            if ($transaksi->lampiran && Storage::disk('public')->exists($transaksi->lampiran)) {
                Storage::disk('public')->delete($transaksi->lampiran);
            }
            $path = $request->file('lampiran')->store('bukti_kas_bank', 'public');
            $validated['lampiran'] = $path;
        }

        $transaksi->update($validated);

        $redirectRoute = ($transaksi->jenis === 'masuk') ? route('kas-bank-masuk.index') : route('kas-bank-keluar.index');

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi Kas/Bank berhasil diperbarui.',
            'redirect_url' => $redirectRoute,
            'data' => $transaksi,
        ]);
    }

    /**
     * Hapus Transaksi Kas / Bank (Soft Delete)
     */
    public function destroy($id)
    {
        $transaksi = TransaksiKasBank::findOrFail($id);
        $permDelete = ($transaksi->jenis === 'masuk') ? 'kas-bank-masuk.delete' : 'kas-bank-keluar.delete';
        $permDestroy = ($transaksi->jenis === 'masuk') ? 'kas-bank-masuk.destroy' : 'kas-bank-keluar.destroy';

        if (!$this->hasAccess($permDelete) && !$this->hasAccess($permDestroy)) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menghapus transaksi ini.'], 403);
        }

        $transaksi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi Kas/Bank berhasil dihapus.',
        ]);
    }

    /**
     * Cetak Lembar Bukti Kas Masuk / Keluar (Voucher)
     */
    public function voucher($id)
    {
        $transaksi = TransaksiKasBank::with(['kasBank', 'lawanCoa', 'user'])->findOrFail($id);
        $perm = ($transaksi->jenis === 'masuk') ? 'kas-bank-masuk.index' : 'kas-bank-keluar.index';

        if ($denied = $this->requireAccess($perm)) {
            return $denied;
        }
        
        $terbilangText = function_exists('terbilang') 
            ? ucwords(terbilang($transaksi->nominal)) . ' Rupiah' 
            : 'Rp ' . number_format($transaksi->nominal, 0, ',', '.');

        return view('keuangan::kas-bank.voucher', compact('transaksi', 'terbilangText'));
    }

    /**
     * API Get Next Nomor Transaksi
     */
    public function getNextNomor(Request $request)
    {
        $jenis = $request->get('jenis', 'masuk');
        $perm = ($jenis === 'masuk') ? 'kas-bank-masuk.create' : 'kas-bank-keluar.create';
        $permStore = ($jenis === 'masuk') ? 'kas-bank-masuk.store' : 'kas-bank-keluar.store';
        $permIndex = ($jenis === 'masuk') ? 'kas-bank-masuk.index' : 'kas-bank-keluar.index';

        if (!$this->hasAccess($perm) && !$this->hasAccess($permStore) && !$this->hasAccess($permIndex)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tanggal = $request->get('tanggal', date('Y-m-d'));

        $nomor = TransaksiKasBank::generateNomorTransaksi($jenis, $tanggal);

        return response()->json([
            'status' => 'success',
            'nomor_transaksi' => $nomor,
        ]);
    }

    /**
     * Validasi data transaksi
     */
    protected function validateTransaksi(Request $request, string $jenis, ?int $id = null): array
    {
        // Bersihkan nominal dari format rupiah (e.g. "Rp 1.500.000" -> 1500000)
        if ($request->has('nominal')) {
            $cleanedNominal = preg_replace('/[^0-9]/', '', (string)$request->nominal);
            $request->merge(['nominal' => $cleanedNominal]);
        }

        $uniqueNomor = 'unique:transaksi_kas_banks,nomor_transaksi';
        if ($id) {
            $uniqueNomor .= ',' . $id . ',id,deleted_at,NULL';
        } else {
            $uniqueNomor .= ',NULL,id,deleted_at,NULL';
        }

        return $request->validate([
            'nomor_transaksi'   => 'nullable|string|max:50|' . $uniqueNomor,
            'tanggal'           => 'required|date',
            'kas_bank_id'       => 'required|exists:master_coas,id',
            'lawan_coa_id'      => 'required|exists:master_coas,id',
            'nominal'           => 'required|numeric|min:1',
            'penerima_pembayar' => 'nullable|string|max:150',
            'nomor_referensi'   => 'nullable|string|max:100',
            'metode_pembayaran' => 'nullable|string|max:50',
            'keterangan'        => 'nullable|string',
            'lampiran'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
    }

    /**
     * Menghitung saldo berjalan masing-masing akun Kas / Bank
     */
    protected function calculateSaldoKasBank($kasBankList)
    {
        $balances = [];

        foreach ($kasBankList as $kb) {
            $totalMasuk = TransaksiKasBank::masuk()->where('kas_bank_id', $kb->id)->sum('nominal');
            $totalKeluar = TransaksiKasBank::keluar()->where('kas_bank_id', $kb->id)->sum('nominal');
            $saldoAwal = (float) $kb->saldo_awal;
            $saldoAkhir = $saldoAwal + (float) $totalMasuk - (float) $totalKeluar;

            $balances[] = [
                'id' => $kb->id,
                'kode_akun' => $kb->kode_akun,
                'nama_akun' => $kb->nama_akun,
                'saldo_awal' => $saldoAwal,
                'total_masuk' => (float) $totalMasuk,
                'total_keluar' => (float) $totalKeluar,
                'saldo_akhir' => $saldoAkhir,
            ];
        }

        return $balances;
    }

    /**
     * Select2 Ajax untuk Akun Kas / Bank
     */
    public function select2KasBank(Request $request)
    {
        $search = trim($request->get('q', ''));
        $page = (int) $request->get('page', 1);
        $perPage = 20;

        $query = MasterCoa::kasBank();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_akun', 'like', "%{$search}%")
                  ->orWhere('kode_akun', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $totalCount = $query->count();
        $items = $query->orderBy('kode_akun', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $results = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->kode_akun . ' - ' . $item->nama_akun,
                'kode_akun' => $item->kode_akun,
                'nama_akun' => $item->nama_akun,
                'kategori' => $item->kategori,
                'tipe' => $item->tipe,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $totalCount,
            ],
        ]);
    }

    /**
     * Select2 Ajax untuk Akun Lawan (Beban / Pendapatan)
     */
    public function select2Lawan(Request $request)
    {
        $search = trim($request->get('q', ''));
        $jenis = $request->get('jenis', 'masuk');
        $page = (int) $request->get('page', 1);
        $perPage = 20;

        $query = MasterCoa::where('is_active', true);

        if ($jenis === 'keluar') {
            $query->where(function ($q) {
                $q->where('tipe', 'Beban')
                  ->orWhere('kategori', 'like', '%BEBAN%')
                  ->orWhere('kode_akun', 'like', '5%');
            });
        } else {
            // Untuk Masuk: Beban (misal refund) atau Pendapatan
            $query->where(function ($q) {
                $q->where('tipe', 'Beban')
                  ->orWhere('tipe', 'Pendapatan')
                  ->orWhere('kategori', 'like', '%BEBAN%')
                  ->orWhere('kategori', 'like', '%PENDAPATAN%')
                  ->orWhere('kode_akun', 'like', '4%')
                  ->orWhere('kode_akun', 'like', '5%');
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_akun', 'like', "%{$search}%")
                  ->orWhere('kode_akun', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $totalCount = $query->count();
        $items = $query->orderBy('kode_akun', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $results = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->kode_akun . ' - ' . $item->nama_akun,
                'kode_akun' => $item->kode_akun,
                'nama_akun' => $item->nama_akun,
                'kategori' => $item->kategori,
                'tipe' => $item->tipe,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $totalCount,
            ],
        ]);
    }
}
