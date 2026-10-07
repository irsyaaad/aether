<?php
namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KartuPiutang extends Model
{
    /**
     * Get the aggregated piutang data per employee based on user's custom query.
     */
    public static function getAggregatedData()
    {
        $subPiutang = DB::table('piutang_karyawan')
            ->select(
                'karyawan_id',
                'tanggal_piutang as tanggal',
                'nomor_piutang as nomor',
                'jumlah_piutang',
                DB::raw('0 AS jumlah_bayar'),
                'created_at'
            );

        $subBayar = DB::table('piutang_karyawan_pembayaran as A')
            ->join('piutang_karyawan as B', 'A.piutang_karyawan_id', '=', 'B.id')
            ->select(
                'B.karyawan_id',
                'A.tanggal_bayar as tanggal',
                'A.nomor_pembayaran as nomor',
                DB::raw('0 AS jumlah_piutang'),
                'A.jumlah_bayar',
                'A.created_at'
            );

        $union = $subPiutang->unionAll($subBayar);

        return DB::table('karyawan as A')
            ->leftJoinSub($union, 'B', function ($join) {
                $join->on('A.id', '=', 'B.karyawan_id');
            })
            ->select(
                'A.id',
                'A.nama_lengkap',
                'A.jabatan',
                DB::raw('COALESCE(SUM(B.jumlah_piutang), 0) AS total_piutang'),
                DB::raw('COALESCE(SUM(B.jumlah_bayar), 0) AS total_bayar'),
                DB::raw('COALESCE(SUM(B.jumlah_piutang), 0) - COALESCE(SUM(B.jumlah_bayar), 0) AS sisa_piutang')
            )
            ->groupBy('A.id', 'A.nama_lengkap', 'A.jabatan');
    }

    /**
     * Get transaction history for a specific employee.
     */
    public static function getHistory($karyawanId)
    {
        $subPiutang = DB::table('piutang_karyawan')
            ->select(
                'tanggal_piutang as tanggal',
                'nomor_piutang as nomor',
                'keterangan',
                'jumlah_piutang as debet',
                DB::raw('0 AS kredit'),
                'created_at',
                DB::raw("'piutang' as type")
            )
            ->where('karyawan_id', $karyawanId)
            ->whereNull('deleted_at');

        $subBayar = DB::table('piutang_karyawan_pembayaran as A')
            ->join('piutang_karyawan as B', 'A.piutang_karyawan_id', '=', 'B.id')
            ->select(
                'A.tanggal_bayar as tanggal',
                'A.nomor_pembayaran as nomor',
                'A.keterangan',
                DB::raw('0 AS debet'),
                'A.jumlah_bayar as kredit',
                'A.created_at',
                DB::raw("'pembayaran' as type")
            )
            ->where('B.karyawan_id', $karyawanId)
            ->whereNull('A.deleted_at');

        return $subPiutang->unionAll($subBayar)
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
