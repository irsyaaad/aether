<?php

namespace Modules\Keuangan\Http\Controllers;

use App\Traits\HasAccessControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Keuangan\Entities\Jurnal;
use Modules\Keuangan\Exports\JurnalExport;
use Modules\Master\Entities\MasterCoa;
use Yajra\DataTables\Facades\DataTables;

class JurnalController extends Controller
{
    use HasAccessControl;

    /**
     * Tampilan Menu Utama Jurnal Umum (Fast Initial Page Load)
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('jurnal.index')) {
            return $denied;
        }

        return view('keuangan::jurnal.index');
    }

    /**
     * DataTables Server-Side AJAX Endpoint (Zero N+1)
     */
    public function getData(Request $request)
    {
        if (!$this->hasAccess('jurnal.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->extractFilters($request);
        $entries = Jurnal::getRawJurnalEntries($filters);

        return DataTables::of(collect($entries))
            ->addIndexColumn()
            ->addColumn('transaksi_info', function ($row) {
                $formattedDate = Carbon::parse($row->tanggal)->translatedFormat('d M Y');
                $isMasuk = ($row->jenis === 'masuk');
                $badgeJenis = $isMasuk
                    ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">' . e($row->nomor_transaksi) . '</span>'
                    : '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200">' . e($row->nomor_transaksi) . '</span>';

                $pihakHtml = $row->penerima_pembayar
                    ? '<span class="text-[11px] text-gray-500">Pihak: <strong class="text-gray-700">' . e($row->penerima_pembayar) . '</strong></span>'
                    : '';
                $refHtml = $row->nomor_referensi
                    ? '<span class="text-[11px] text-gray-400">Ref: ' . e($row->nomor_referensi) . '</span>'
                    : '';

                $metaItems = array_filter([$pihakHtml, $refHtml]);
                $metaRow = !empty($metaItems)
                    ? '<div class="flex flex-wrap items-center gap-1.5 mt-0.5 text-[11px]">' . implode(' <span class="text-gray-300">&bull;</span> ', $metaItems) . '</div>'
                    : '';

                return '<div class="flex flex-col gap-1 text-xs py-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 whitespace-nowrap">' . $formattedDate . '</span>
                                ' . $badgeJenis . '
                            </div>
                            <div class="font-semibold text-gray-800 leading-snug">' . e($row->keterangan ?: '-') . '</div>
                            ' . $metaRow . '
                        </div>';
            })
            ->addColumn('akun', function ($row) {
                $isDebit = ($row->posisi === 'Debit');
                if ($isDebit) {
                    return '<div class="flex flex-col">
                                <span class="font-bold text-slate-900 text-xs">' . e($row->nama_akun) . '</span>
                                <span class="font-mono text-[11px] text-gray-500 mt-0.5">' . e($row->kode_akun ?: '-') . ' &bull; ' . e($row->kategori_coa ?: '') . '</span>
                            </div>';
                }

                return '<div class="flex flex-col pl-4 border-l-2 border-indigo-200">
                            <span class="italic font-medium text-slate-700 text-xs">&bull; ' . e($row->nama_akun) . '</span>
                            <span class="font-mono text-[11px] text-gray-400 mt-0.5">' . e($row->kode_akun ?: '-') . ' &bull; ' . e($row->kategori_coa ?: '') . '</span>
                        </div>';
            })
            ->editColumn('posisi', function ($row) {
                if ($row->posisi === 'Debit') {
                    return '<span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">Debit</span>';
                }
                return '<span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800 border border-indigo-200">Kredit</span>';
            })
            ->editColumn('debit', function ($row) {
                return (float) $row->debit > 0
                    ? '<span class="font-mono font-bold text-blue-900">Rp ' . number_format($row->debit, 0, ',', '.') . '</span>'
                    : '<span class="text-gray-300 font-mono">-</span>';
            })
            ->editColumn('kredit', function ($row) {
                return (float) $row->kredit > 0
                    ? '<span class="font-mono font-bold text-indigo-900">Rp ' . number_format($row->kredit, 0, ',', '.') . '</span>'
                    : '<span class="text-gray-300 font-mono">-</span>';
            })
            ->rawColumns(['transaksi_info', 'akun', 'posisi', 'debit', 'kredit'])
            ->make(true);
    }

    /**
     * AJAX Endpoint: KPI Summary Cards (Total Transaksi, Total Debit, Kredit, Balance)
     */
    public function getStats(Request $request)
    {
        if (!$this->hasAccess('jurnal.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->extractFilters($request);
        $summary = Jurnal::getJurnalSummary($filters);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_transaksi'           => $summary->total_transaksi,
                'total_transaksi_formatted' => number_format($summary->total_transaksi, 0, ',', '.'),
                'total_baris'               => $summary->total_baris,
                'total_baris_formatted'     => number_format($summary->total_baris, 0, ',', '.'),
                'total_debit'               => $summary->total_debit,
                'total_debit_formatted'     => number_format($summary->total_debit, 0, ',', '.'),
                'total_kredit'              => $summary->total_kredit,
                'total_kredit_formatted'    => number_format($summary->total_kredit, 0, ',', '.'),
                'selisih'                   => $summary->selisih,
                'selisih_formatted'         => number_format($summary->selisih, 0, ',', '.'),
                'is_balanced'               => $summary->is_balanced,
                'status_label'              => $summary->status_label,
            ],
        ]);
    }

    /**
     * Select2 AJAX Endpoint: Pencarian Akun CoA Dinamis
     */
    public function select2Coa(Request $request)
    {
        $search  = trim($request->get('q', ''));
        $page    = (int) $request->get('page', 1);
        $perPage = 20;

        $query = MasterCoa::where('is_active', true);

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
                'id'        => $item->id,
                'text'      => $item->kode_akun . ' - ' . $item->nama_akun . ' (' . $item->kategori . ')',
                'kode_akun' => $item->kode_akun,
                'nama_akun' => $item->nama_akun,
                'kategori'  => $item->kategori,
            ];
        });

        return response()->json([
            'results'    => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $totalCount,
            ],
        ]);
    }

    /**
     * Cetak Lembar Laporan Jurnal Umum (HTML Printable)
     */
    public function cetak(Request $request)
    {
        if ($denied = $this->requireAccess('jurnal.index')) {
            return $denied;
        }

        $filters  = $this->extractFilters($request);
        $entries  = Jurnal::getRawJurnalEntries($filters);
        $summary  = Jurnal::getJurnalSummary($filters);
        $meta     = $this->getFilterMeta($filters);
        $excelUrl = route('jurnal.export-excel', $request->query());

        return view('keuangan::jurnal.cetak', compact(
            'entries',
            'summary',
            'meta',
            'excelUrl'
        ));
    }

    /**
     * Export Jurnal Umum ke File Excel
     */
    public function exportExcel(Request $request)
    {
        if ($denied = $this->requireAccess('jurnal.index')) {
            return $denied;
        }

        $filters  = $this->extractFilters($request);
        $entries  = Jurnal::getRawJurnalEntries($filters);
        $summary  = Jurnal::getJurnalSummary($filters);
        $meta     = $this->getFilterMeta($filters);
        $filename = 'Jurnal_Umum_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new JurnalExport($entries, $meta, $summary), $filename);
    }

    /**
     * Ekstrak parameter filter dari request
     */
    protected function extractFilters(Request $request): array
    {
        $dtSearch = $request->input('search.value');
        $search = $request->filled('search') && is_string($request->get('search'))
            ? $request->get('search')
            : $dtSearch;

        return [
            'start_date' => $request->get('start_date', $request->get('dr_tgl')),
            'end_date'   => $request->get('end_date', $request->get('sp_tgl')),
            'jenis'      => $request->get('jenis'),
            'coa_id'     => $request->get('coa_id'),
            'search'     => $search,
        ];
    }

    /**
     * Format deskripsi teks filter untuk header cetak & excel
     */
    protected function getFilterMeta(array $filters): array
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;

        if ($startDate && $endDate) {
            $periode = Carbon::parse($startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($endDate)->format('d/m/Y');
        } elseif ($startDate) {
            $periode = 'Mulai ' . Carbon::parse($startDate)->format('d/m/Y');
        } elseif ($endDate) {
            $periode = 'Sampai ' . Carbon::parse($endDate)->format('d/m/Y');
        } else {
            $periode = 'Semua Periode';
        }

        return [
            'periode' => $periode,
            'filters' => $filters,
        ];
    }
}
