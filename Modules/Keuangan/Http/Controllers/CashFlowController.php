<?php

namespace Modules\Keuangan\Http\Controllers;

use App\Traits\HasAccessControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Keuangan\Entities\Jurnal;
use Modules\Keuangan\Exports\CashFlowExport;

class CashFlowController extends Controller
{
    use HasAccessControl;

    /**
     * Tampilan Menu Utama Laporan Arus Kas (Cash Flow)
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('cashflow.index')) {
            return $denied;
        }

        $filters = $this->extractFilters($request);
        $report  = Jurnal::getCashFlowReport($filters);
        $meta    = $this->getFilterMeta($filters);

        return view('keuangan::cashflow.index', compact('report', 'meta'));
    }

    /**
     * Endpoint AJAX JSON untuk update filter Cash Flow
     */
    public function getData(Request $request)
    {
        if (!$this->hasAccess('cashflow.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->extractFilters($request);
        $report  = Jurnal::getCashFlowReport($filters);

        return response()->json([
            'status' => 'success',
            'data'   => $report,
        ]);
    }

    /**
     * Endpoint Drill-Down: Rincian Mutasi Transaksi Suatu Akun
     */
    public function rincian(Request $request, $coa_id)
    {
        if ($denied = $this->requireAccess('cashflow.index')) {
            return $request->expectsJson() || $request->ajax()
                ? response()->json(['error' => 'Unauthorized'], 403)
                : $denied;
        }

        $filters = $this->extractFilters($request);
        $detail  = Jurnal::getDetailAkunEntries((int) $coa_id, $filters);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data'   => $detail,
            ]);
        }

        $meta = $this->getFilterMeta($filters);
        $backUrl = route('cashflow.index', array_filter(['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]));
        $sourceReport = 'Laporan Arus Kas';
        $reportType = 'cashflow';

        return view('keuangan::rincian.index', compact('detail', 'meta', 'filters', 'backUrl', 'sourceReport', 'reportType', 'coa_id'));
    }

    /**
     * Cetak Lembar Laporan Arus Kas (HTML Printable)
     */
    public function cetak(Request $request)
    {
        if ($denied = $this->requireAccess('cashflow.index')) {
            return $denied;
        }

        $filters  = $this->extractFilters($request);
        $report   = Jurnal::getCashFlowReport($filters);
        $meta     = $this->getFilterMeta($filters);
        $excelUrl = route('cashflow.export-excel', $request->query());

        return view('keuangan::cashflow.cetak', compact('report', 'meta', 'excelUrl'));
    }

    /**
     * Export Arus Kas ke File Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        if ($denied = $this->requireAccess('cashflow.index')) {
            return $denied;
        }

        $filters  = $this->extractFilters($request);
        $report   = Jurnal::getCashFlowReport($filters);
        $meta     = $this->getFilterMeta($filters);
        $filename = 'Arus_Kas_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new CashFlowExport($report, $meta), $filename);
    }

    /**
     * Ekstrak parameter filter
     */
    protected function extractFilters(Request $request): array
    {
        return [
            'start_date' => $request->get('start_date', $request->get('dr_tgl')),
            'end_date'   => $request->get('end_date', $request->get('sp_tgl')),
        ];
    }

    /**
     * Format deskripsi teks filter
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
            'periode'    => $periode,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'filters'    => $filters,
        ];
    }
}
