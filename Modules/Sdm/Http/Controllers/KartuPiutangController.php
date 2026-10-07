<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Sdm\Entities\KartuPiutang;
use Modules\Sdm\Entities\Karyawan;
use Modules\Sdm\Exports\KartuPiutangExport;
use Yajra\DataTables\Facades\DataTables;

class KartuPiutangController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($denied = $this->requireAccess('kartu-piutang.index')) {
            return $denied;
        }

        return view('sdm::kartu-piutang.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        if ($denied = $this->requireAccess('kartu-piutang.index')) {
            return $denied;
        }
        $query = KartuPiutang::getAggregatedData();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('total_piutang', function ($row) {
                return 'Rp ' . number_format($row->total_piutang, 0, ',', '.');
            })
            ->editColumn('total_bayar', function ($row) {
                return 'Rp ' . number_format($row->total_bayar, 0, ',', '.');
            })
            ->editColumn('sisa_piutang', function ($row) {
                return 'Rp ' . number_format($row->sisa_piutang, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . url('kartu-piutang/' . $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="fa fa-eye"></i> Detail
                    </a>
                    <a href="' . url('kartu-piutang/' . $row->id . '/cetak') . '" target="_blank" class="btn btn-sm btn-info text-white">
                        <i class="fa fa-print"></i> Cetak
                    </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show transaction history for a specific employee.
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('kartu-piutang.index')) {
            return $denied;
        }

        $data['karyawan'] = Karyawan::findOrFail($id);
        $data['history']  = KartuPiutang::getHistory($id);
        return view('sdm::kartu-piutang.show', $data);
    }

    /**
     * Print report.
     */
    public function cetak($id)
    {
        if ($denied = $this->requireAccess('kartu-piutang.index')) {
            return $denied;
        }

        $data['karyawan'] = Karyawan::findOrFail($id);
        $data['history']  = KartuPiutang::getHistory($id);

        $pdf = Pdf::loadView('sdm::kartu-piutang.cetak', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('Kartu_Piutang_' . $data['karyawan']->nama_lengkap . '.pdf');
    }

    /**
     * Export transaction history to Excel.
     */
    public function exportExcel($id)
    {
        if ($denied = $this->requireAccess('kartu-piutang.index')) {
            return $denied;
        }

        $karyawan = Karyawan::findOrFail($id);
        $history  = KartuPiutang::getHistory($id);

        return Excel::download(new KartuPiutangExport($history, $karyawan), 'Kartu_Piutang_' . $karyawan->nama_lengkap . '.xlsx');
    }

    /*
    prompt untuk membuat menu ini, KERENNN !!!
    aku ingin membuat menu Kartu Piutang (<li><a href="{{ url('kartu-piutang') }}">Kartu Piutang</a></li>), 
    buatkan Controller dan model yang menampilkan custom queryku, di controller cukup buat function index, get_data (datatable), show, dan cetak.
    di model siapkan saja untuk aku menyimpan queryku ini
    SELECT
        A.id,
        A.nama_lengkap,
        A.jabatan,
        COALESCE(SUM( B.jumlah_piutang ),0) AS jumlah_piutang,
        COALESCE(SUM( B.jumlah_bayar ),0) AS jumlah_bayar
    FROM
        karyawan AS A
        LEFT JOIN (
        SELECT
            karyawan_id,
            tanggal_piutang,
            nomor_piutang,
            jumlah_piutang,
            0 AS jumlah_bayar,
            created_at 
        FROM
            piutang_karyawan UNION ALL
        SELECT
            B.karyawan_id,
            A.tanggal_bayar,
            A.nomor_pembayaran,
            0 AS jumlah_piutang,
            A.jumlah_bayar,
            A.created_at 
        FROM
            piutang_karyawan_pembayaran AS A
            JOIN piutang_karyawan AS B ON A.piutang_karyawan_id = B.id 
        ) AS B ON A.id = B.karyawan_id 
    GROUP BY
        A.id

    buatkan eloquent untuk queryku di Model
    */
}
