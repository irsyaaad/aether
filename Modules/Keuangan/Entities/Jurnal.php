<?php

namespace Modules\Keuangan\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Master\Entities\MasterCoa;

/**
 * Model Jurnal (General Ledger / Jurnal Umum)
 *
 * Catatan Arsitektur & Edukatif:
 * Model ini menggunakan RAW SQL QUERY murni (DB::select) sesuai permintaan agar alur data,
 * logika akuntansi pembukuan berpasangan (double-entry bookkeeping), dan query JOIN antar tabel
 * sangat transparan, mudah dipelajari, serta terbebas 100% dari masalah N+1 Query.
 *
 * Seluruh laporan keuangan (Neraca, Laba Rugi, Cash Flow) mengambil data langsung dari Model Jurnal
 * sebagai Single Source of Truth ("Satu Pintu").
 */
class Jurnal extends Model
{
    /**
     * Jalankan Raw SQL Query untuk mengambil seluruh baris Jurnal Umum berdasarkan filter.
     *
     * @param array $filters ['start_date', 'end_date', 'jenis', 'coa_id', 'search']
     * @return array
     */
    public static function getRawJurnalEntries(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? $filters['dr_tgl'] ?? null;
        $endDate   = $filters['end_date'] ?? $filters['sp_tgl'] ?? null;
        $jenis     = $filters['jenis'] ?? null;
        $coaId     = $filters['coa_id'] ?? null;
        $search    = $filters['search'] ?? null;

        // Siapkan klausa WHERE dinamis untuk kedua sisi UNION
        $whereClauses = ["tkb.deleted_at IS NULL"];
        $bindingsDebit = [];
        $bindingsKredit = [];

        if (!empty($startDate)) {
            $whereClauses[] = "tkb.tanggal >= ?";
            $bindingsDebit[] = $startDate;
            $bindingsKredit[] = $startDate;
        }

        if (!empty($endDate)) {
            $whereClauses[] = "tkb.tanggal <= ?";
            $bindingsDebit[] = $endDate;
            $bindingsKredit[] = $endDate;
        }

        if (!empty($jenis) && in_array($jenis, ['masuk', 'keluar'])) {
            $whereClauses[] = "tkb.jenis = ?";
            $bindingsDebit[] = $jenis;
            $bindingsKredit[] = $jenis;
        }

        if (!empty($coaId)) {
            $whereClauses[] = "(tkb.kas_bank_id = ? OR tkb.lawan_coa_id = ?)";
            $bindingsDebit[] = $coaId;
            $bindingsDebit[] = $coaId;
            $bindingsKredit[] = $coaId;
            $bindingsKredit[] = $coaId;
        }

        if (!empty($search)) {
            $searchPattern = '%' . $search . '%';
            $whereClauses[] = "(
                tkb.nomor_transaksi LIKE ? OR 
                tkb.penerima_pembayar LIKE ? OR 
                tkb.keterangan LIKE ? OR 
                tkb.nomor_referensi LIKE ? OR 
                c_kb.nama_akun LIKE ? OR 
                c_lw.nama_akun LIKE ? OR 
                c_kb.kode_akun LIKE ? OR 
                c_lw.kode_akun LIKE ?
            )";
            for ($i = 0; $i < 8; $i++) {
                $bindingsDebit[] = $searchPattern;
                $bindingsKredit[] = $searchPattern;
            }
        }

        $whereSql = implode(" AND ", $whereClauses);

        /**
         * Raw SQL Query Double-Entry Bookkeeping:
         * 1. Blok SELECT Pertama (DEBIT):
         *    - Jika jenis = 'masuk', debit adalah akun kas/bank (c_kb)
         *    - Jika jenis = 'keluar', debit adalah akun lawan/beban (c_lw)
         *
         * 2. Blok SELECT Kedua (KREDIT):
         *    - Jika jenis = 'masuk', kredit adalah akun lawan/pendapatan (c_lw)
         *    - Jika jenis = 'keluar', kredit adalah akun kas/bank (c_kb)
         */
        $rawSql = "
            /* =======================================================
               BAGIAN 1: POSISI DEBIT DARI SETIAP TRANSAKSI KEUANGAN
               ======================================================= */
            SELECT 
                tkb.id AS transaksi_id,
                tkb.nomor_transaksi,
                tkb.tanggal,
                tkb.jenis,
                tkb.penerima_pembayar,
                tkb.nomor_referensi,
                tkb.metode_pembayaran,
                tkb.keterangan,
                tkb.status,
                tkb.user_id,
                COALESCE(u.nm_user, u.username) AS nama_user,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.id 
                    ELSE c_lw.id 
                END AS coa_id,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.kode_akun 
                    ELSE c_lw.kode_akun 
                END AS kode_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.nama_akun 
                    ELSE c_lw.nama_akun 
                END AS nama_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.kategori 
                    ELSE c_lw.kategori 
                END AS kategori_coa,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.tipe 
                    ELSE c_lw.tipe 
                END AS tipe_coa,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.laporan 
                    ELSE c_lw.laporan 
                END AS laporan_coa,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.posisi_normal 
                    ELSE c_lw.posisi_normal 
                END AS posisi_normal_coa,
                -- Info Lawan Akun
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.id 
                    ELSE c_kb.id 
                END AS lawan_coa_id,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.kode_akun 
                    ELSE c_kb.kode_akun 
                END AS lawan_kode_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.nama_akun 
                    ELSE c_kb.nama_akun 
                END AS lawan_nama_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.kategori 
                    ELSE c_kb.kategori 
                END AS lawan_kategori_coa,
                'Debit' AS posisi,
                CAST(tkb.nominal AS DECIMAL(15,2)) AS debit,
                CAST(0.00 AS DECIMAL(15,2)) AS kredit,
                1 AS urutan_baris
            FROM transaksi_kas_banks tkb
            LEFT JOIN master_coas c_kb ON tkb.kas_bank_id = c_kb.id
            LEFT JOIN master_coas c_lw ON tkb.lawan_coa_id = c_lw.id
            LEFT JOIN users u ON tkb.user_id = u.id_user
            WHERE {$whereSql}

            UNION ALL

            /* =======================================================
               BAGIAN 2: POSISI KREDIT DARI SETIAP TRANSAKSI KEUANGAN
               ======================================================= */
            SELECT 
                tkb.id AS transaksi_id,
                tkb.nomor_transaksi,
                tkb.tanggal,
                tkb.jenis,
                tkb.penerima_pembayar,
                tkb.nomor_referensi,
                tkb.metode_pembayaran,
                tkb.keterangan,
                tkb.status,
                tkb.user_id,
                COALESCE(u.nm_user, u.username) AS nama_user,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.id 
                    ELSE c_kb.id 
                END AS coa_id,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.kode_akun 
                    ELSE c_kb.kode_akun 
                END AS kode_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.nama_akun 
                    ELSE c_kb.nama_akun 
                END AS nama_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.kategori 
                    ELSE c_kb.kategori 
                END AS kategori_coa,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.tipe 
                    ELSE c_kb.tipe 
                END AS tipe_coa,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.laporan 
                    ELSE c_kb.laporan 
                END AS laporan_coa,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_lw.posisi_normal 
                    ELSE c_kb.posisi_normal 
                END AS posisi_normal_coa,
                -- Info Lawan Akun
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.id 
                    ELSE c_lw.id 
                END AS lawan_coa_id,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.kode_akun 
                    ELSE c_lw.kode_akun 
                END AS lawan_kode_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.nama_akun 
                    ELSE c_lw.nama_akun 
                END AS lawan_nama_akun,
                CASE 
                    WHEN tkb.jenis = 'masuk' THEN c_kb.kategori 
                    ELSE c_lw.kategori 
                END AS lawan_kategori_coa,
                'Kredit' AS posisi,
                CAST(0.00 AS DECIMAL(15,2)) AS debit,
                CAST(tkb.nominal AS DECIMAL(15,2)) AS kredit,
                2 AS urutan_baris
            FROM transaksi_kas_banks tkb
            LEFT JOIN master_coas c_kb ON tkb.kas_bank_id = c_kb.id
            LEFT JOIN master_coas c_lw ON tkb.lawan_coa_id = c_lw.id
            LEFT JOIN users u ON tkb.user_id = u.id_user
            WHERE {$whereSql}

            ORDER BY tanggal ASC, transaksi_id ASC, urutan_baris ASC
        ";

        $allBindings = array_merge($bindingsDebit, $bindingsKredit);

        return DB::select($rawSql, $allBindings);
    }

    /**
     * Hitung Rekapitulasi & Keseimbangan Jurnal (Total Debit, Total Kredit, Balance)
     *
     * @param array $filters
     * @return object
     */
    public static function getJurnalSummary(array $filters = []): object
    {
        $entries = self::getRawJurnalEntries($filters);

        $totalDebit = 0.0;
        $totalKredit = 0.0;
        $transaksiIds = [];

        foreach ($entries as $row) {
            $totalDebit += (float) $row->debit;
            $totalKredit += (float) $row->kredit;
            $transaksiIds[$row->transaksi_id] = true;
        }

        $totalTransaksi = count($transaksiIds);
        $totalBaris = count($entries);
        $selisih = abs($totalDebit - $totalKredit);
        $isBalanced = ($selisih < 0.01);

        return (object) [
            'total_transaksi' => $totalTransaksi,
            'total_baris'     => $totalBaris,
            'total_debit'     => $totalDebit,
            'total_kredit'    => $totalKredit,
            'selisih'         => $selisih,
            'is_balanced'     => $isBalanced,
            'status_label'    => $isBalanced ? 'Seimbang (Balance)' : 'Tidak Seimbang (Unbalanced)',
        ];
    }

    /**
     * Ambil data jurnal yang telah dikelompokkan per transaksi (Voucher Grouping).
     *
     * @param array $filters
     * @return array
     */
    public static function getJurnalGrouped(array $filters = []): array
    {
        $entries = self::getRawJurnalEntries($filters);
        $grouped = [];

        foreach ($entries as $row) {
            $txId = $row->transaksi_id;
            if (!isset($grouped[$txId])) {
                $grouped[$txId] = [
                    'transaksi_id'      => $row->transaksi_id,
                    'nomor_transaksi'   => $row->nomor_transaksi,
                    'tanggal'           => $row->tanggal,
                    'jenis'             => $row->jenis,
                    'penerima_pembayar' => $row->penerima_pembayar,
                    'nomor_referensi'   => $row->nomor_referensi,
                    'metode_pembayaran' => $row->metode_pembayaran,
                    'keterangan'        => $row->keterangan,
                    'nama_user'         => $row->nama_user,
                    'total_nominal'     => max((float) $row->debit, (float) $row->kredit),
                    'entries'           => [],
                ];
            }

            $grouped[$txId]['entries'][] = $row;
        }

        return array_values($grouped);
    }

    /**
     * =========================================================================
     * LAPORAN LABA RUGI (INCOME STATEMENT)
     * =========================================================================
     * Data diambil satu pintu dari Jurnal Umum. Zero N+1 Query.
     * Formula Akuntansi:
     * - Pendapatan: Kredit - Debit
     * - Beban     : Debit - Kredit
     * - Laba Bersih = Total Pendapatan - Total Beban
     *
     * @param array $filters
     * @return array
     */
    public static function getRugiLabaReport(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? $filters['dr_tgl'] ?? null;
        $endDate   = $filters['end_date'] ?? $filters['sp_tgl'] ?? null;

        // 1. Ambil seluruh entri jurnal pada periode terpilih (1 query)
        $entries = self::getRawJurnalEntries([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        // 2. Ambil master akun pendapatan dan beban (1 query)
        $coas = MasterCoa::where('is_active', true)
            ->where(function ($q) {
                $q->where('laporan', 'Laba Rugi')
                  ->orWhereIn('tipe', ['Pendapatan', 'Beban'])
                  ->orWhere('kode_akun', 'like', '4%')
                  ->orWhere('kode_akun', 'like', '5%');
            })
            ->orderBy('kode_akun', 'asc')
            ->get();

        // 3. Agregasikan mutasi per akun
        $entriesByCoa = [];
        foreach ($entries as $row) {
            $coaId = (int) $row->coa_id;
            if (!isset($entriesByCoa[$coaId])) {
                $entriesByCoa[$coaId] = [
                    'debit'  => 0.0,
                    'kredit' => 0.0,
                    'count'  => 0,
                ];
            }
            $entriesByCoa[$coaId]['debit'] += (float) $row->debit;
            $entriesByCoa[$coaId]['kredit'] += (float) $row->kredit;
            $entriesByCoa[$coaId]['count'] += 1;
        }

        $pendapatanCategories = [];
        $bebanCategories = [];
        $totalPendapatan = 0.0;
        $totalBeban = 0.0;

        foreach ($coas as $coa) {
            $entryData = $entriesByCoa[$coa->id] ?? ['debit' => 0.0, 'kredit' => 0.0, 'count' => 0];
            $isPendapatan = ($coa->tipe === 'Pendapatan' || str_starts_with($coa->kode_akun, '4') || str_contains(strtoupper($coa->kategori), 'PENDAPATAN'));

            if ($isPendapatan) {
                // Pendapatan bertambah di Kredit
                $nominal = $entryData['kredit'] - $entryData['debit'];
                $kategori = $coa->kategori ?: 'PENDAPATAN JASA';

                if (!isset($pendapatanCategories[$kategori])) {
                    $pendapatanCategories[$kategori] = [
                        'kategori' => $kategori,
                        'accounts' => [],
                        'subtotal' => 0.0,
                    ];
                }

                $pendapatanCategories[$kategori]['accounts'][] = (object) [
                    'coa_id'          => $coa->id,
                    'kode_akun'       => $coa->kode_akun,
                    'nama_akun'       => $coa->nama_akun,
                    'kategori'        => $kategori,
                    'mutasi_debit'    => $entryData['debit'],
                    'mutasi_kredit'   => $entryData['kredit'],
                    'nominal'         => $nominal,
                    'transaksi_count' => $entryData['count'],
                ];

                $pendapatanCategories[$kategori]['subtotal'] += $nominal;
                $totalPendapatan += $nominal;
            } else {
                // Beban bertambah di Debit
                $nominal = $entryData['debit'] - $entryData['kredit'];
                $kategori = $coa->kategori ?: 'BEBAN OPERASIONAL';

                if (!isset($bebanCategories[$kategori])) {
                    $bebanCategories[$kategori] = [
                        'kategori' => $kategori,
                        'accounts' => [],
                        'subtotal' => 0.0,
                    ];
                }

                $bebanCategories[$kategori]['accounts'][] = (object) [
                    'coa_id'          => $coa->id,
                    'kode_akun'       => $coa->kode_akun,
                    'nama_akun'       => $coa->nama_akun,
                    'kategori'        => $kategori,
                    'mutasi_debit'    => $entryData['debit'],
                    'mutasi_kredit'   => $entryData['kredit'],
                    'nominal'         => $nominal,
                    'transaksi_count' => $entryData['count'],
                ];

                $bebanCategories[$kategori]['subtotal'] += $nominal;
                $totalBeban += $nominal;
            }
        }

        $labaBersih = $totalPendapatan - $totalBeban;
        $isLaba = ($labaBersih >= 0);

        return [
            'pendapatan' => [
                'categories' => array_values($pendapatanCategories),
                'total'      => $totalPendapatan,
            ],
            'beban' => [
                'categories' => array_values($bebanCategories),
                'total'      => $totalBeban,
            ],
            'total_pendapatan'   => $totalPendapatan,
            'total_beban'        => $totalBeban,
            'is_beban_kredit'    => ($totalBeban < 0),
            'laba_bersih'        => $labaBersih,
            'is_laba'            => $isLaba,
            'status_label'       => $isLaba ? 'Laba Bersih' : 'Rugi Bersih',
            'formula_penjelasan' => ($totalBeban < 0)
                ? 'Rp ' . number_format($totalPendapatan, 0, ',', '.') . ' - (-Rp ' . number_format(abs($totalBeban), 0, ',', '.') . ') = Rp ' . number_format($labaBersih, 0, ',', '.') . ' (Pendapatan + Pemulihan/Pengembalian Beban Bersih)'
                : 'Rp ' . number_format($totalPendapatan, 0, ',', '.') . ' - Rp ' . number_format($totalBeban, 0, ',', '.') . ' = Rp ' . number_format($labaBersih, 0, ',', '.'),
            'filters'            => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ],
        ];
    }

    /**
     * =========================================================================
     * LAPORAN NERACA KEUANGAN (BALANCE SHEET)
     * =========================================================================
     * Mengambil data mutasi dari Model Jurnal secara kumulatif up to end_date. Zero N+1 Query.
     * Persamaan Neraca:
     * Total Aset (Aktiva) = Total Kewajiban + Total Ekuitas (Pasiva)
     *
     * @param array $filters
     * @return array
     */
    public static function getNeracaReport(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? $filters['dr_tgl'] ?? null;
        $endDate   = $filters['end_date'] ?? $filters['sp_tgl'] ?? Carbon::now()->toDateString();

        // 1. Ambil akun-akun Neraca dari MasterCoa (1 query)
        $coas = MasterCoa::where('is_active', true)
            ->where(function ($q) {
                $q->where('laporan', 'Neraca')
                  ->orWhereIn('tipe', ['Aset', 'Kewajiban', 'Ekuitas'])
                  ->orWhereIn(DB::raw('SUBSTRING(kode_akun, 1, 1)'), ['1', '2', '3']);
            })
            ->orderBy('kode_akun', 'asc')
            ->get();

        // 2. Ambil seluruh baris jurnal up to $endDate dari Model Jurnal (1 query)
        $allEntriesUpToEnd = self::getRawJurnalEntries([
            'end_date' => $endDate,
        ]);

        $priorMutasi = [];
        $periodMutasi = [];
        $cumulativeMutasi = [];

        $totalPendapatanUpToEnd = 0.0;
        $totalBebanUpToEnd = 0.0;

        foreach ($allEntriesUpToEnd as $row) {
            $coaId = (int) $row->coa_id;
            $rowDate = $row->tanggal;
            $isBeforeStart = ($startDate && $rowDate < $startDate);
            $debit = (float) $row->debit;
            $kredit = (float) $row->kredit;

            if (!isset($cumulativeMutasi[$coaId])) {
                $cumulativeMutasi[$coaId] = ['debit' => 0.0, 'kredit' => 0.0, 'count' => 0];
            }
            $cumulativeMutasi[$coaId]['debit'] += $debit;
            $cumulativeMutasi[$coaId]['kredit'] += $kredit;
            $cumulativeMutasi[$coaId]['count'] += 1;

            if ($isBeforeStart) {
                if (!isset($priorMutasi[$coaId])) {
                    $priorMutasi[$coaId] = ['debit' => 0.0, 'kredit' => 0.0];
                }
                $priorMutasi[$coaId]['debit'] += $debit;
                $priorMutasi[$coaId]['kredit'] += $kredit;
            } else {
                if (!isset($periodMutasi[$coaId])) {
                    $periodMutasi[$coaId] = ['debit' => 0.0, 'kredit' => 0.0, 'count' => 0];
                }
                $periodMutasi[$coaId]['debit'] += $debit;
                $periodMutasi[$coaId]['kredit'] += $kredit;
                $periodMutasi[$coaId]['count'] += 1;
            }

            // Hitung pendapatan & beban up to end_date untuk Laba Periode Berjalan
            $isPendapatan = ($row->tipe_coa === 'Pendapatan' || str_starts_with($row->kode_akun, '4'));
            $isBeban = ($row->tipe_coa === 'Beban' || str_starts_with($row->kode_akun, '5'));
            if ($isPendapatan) {
                $totalPendapatanUpToEnd += ($kredit - $debit);
            } elseif ($isBeban) {
                $totalBebanUpToEnd += ($debit - $kredit);
            }
        }

        $labaBerjalanUpToEnd = $totalPendapatanUpToEnd - $totalBebanUpToEnd;

        $asetLancar = [];
        $asetTetap = [];
        $kewajibanLancar = [];
        $kewajibanJangkaPanjang = [];
        $ekuitas = [];

        $subtotalAsetLancar = 0.0;
        $subtotalAsetTetap = 0.0;
        $subtotalKewajibanLancar = 0.0;
        $subtotalKewajibanJangkaPanjang = 0.0;
        $subtotalEkuitas = 0.0;

        foreach ($coas as $coa) {
            // Akun Laba Periode Berjalan (3202) dihitung dinamis dari hasil operasional
            if ($coa->kode_akun === '3202') {
                continue;
            }

            $saldoAwalMaster = (float) $coa->saldo_awal;
            $prior = $priorMutasi[$coa->id] ?? ['debit' => 0.0, 'kredit' => 0.0];
            $period = $periodMutasi[$coa->id] ?? ['debit' => 0.0, 'kredit' => 0.0, 'count' => 0];
            $cumul = $cumulativeMutasi[$coa->id] ?? ['debit' => 0.0, 'kredit' => 0.0, 'count' => 0];

            $posisiNormal = $coa->posisi_normal ?: ($coa->tipe === 'Aset' ? 'Debit' : 'Kredit');
            $isKontraAset = ($coa->tipe === 'Aset' && $posisiNormal === 'Kredit');
            $isKontraEkuitas = ($coa->tipe === 'Ekuitas' && $posisiNormal === 'Debit');

            if ($posisiNormal === 'Debit') {
                $saldoAwalPeriode = $saldoAwalMaster + ($prior['debit'] - $prior['kredit']);
                $saldoAkhir = $saldoAwalMaster + ($cumul['debit'] - $cumul['kredit']);
            } else {
                $saldoAwalPeriode = $saldoAwalMaster + ($prior['kredit'] - $prior['debit']);
                $saldoAkhir = $saldoAwalMaster + ($cumul['kredit'] - $cumul['debit']);
            }

            $accountObj = (object) [
                'coa_id'          => $coa->id,
                'kode_akun'       => $coa->kode_akun,
                'nama_akun'       => $coa->nama_akun,
                'kategori'        => $coa->kategori,
                'tipe'            => $coa->tipe,
                'posisi_normal'   => $posisiNormal,
                'saldo_awal'      => $saldoAwalPeriode,
                'mutasi_debit'    => $period['debit'],
                'mutasi_kredit'   => $period['kredit'],
                'saldo_akhir'     => $saldoAkhir,
                'transaksi_count' => $cumul['count'],
                'is_kontra'       => $isKontraAset || $isKontraEkuitas,
            ];

            if ($coa->kategori === 'ASET LANCAR') {
                $asetLancar[] = $accountObj;
                $subtotalAsetLancar += $saldoAkhir;
            } elseif ($coa->kategori === 'ASET TETAP') {
                $asetTetap[] = $accountObj;
                if ($isKontraAset) {
                    $subtotalAsetTetap -= $saldoAkhir;
                } else {
                    $subtotalAsetTetap += $saldoAkhir;
                }
            } elseif ($coa->kategori === 'KEWAJIBAN LANCAR') {
                $kewajibanLancar[] = $accountObj;
                $subtotalKewajibanLancar += $saldoAkhir;
            } elseif ($coa->kategori === 'KEWAJIBAN JANGKA PANJANG') {
                $kewajibanJangkaPanjang[] = $accountObj;
                $subtotalKewajibanJangkaPanjang += $saldoAkhir;
            } elseif ($coa->tipe === 'Ekuitas' || $coa->kategori === 'EKUITAS') {
                $ekuitas[] = $accountObj;
                if ($isKontraEkuitas) {
                    $subtotalEkuitas -= $saldoAkhir;
                } else {
                    $subtotalEkuitas += $saldoAkhir;
                }
            }
        }

        // Tambahkan baris Laba Periode Berjalan ke Ekuitas
        $ekuitas[] = (object) [
            'coa_id'          => 0,
            'kode_akun'       => '3202',
            'nama_akun'       => 'Laba Periode Berjalan',
            'kategori'        => 'EKUITAS',
            'tipe'            => 'Ekuitas',
            'posisi_normal'   => 'Kredit',
            'saldo_awal'      => 0.0,
            'mutasi_debit'    => $totalBebanUpToEnd,
            'mutasi_kredit'   => $totalPendapatanUpToEnd,
            'saldo_akhir'     => $labaBerjalanUpToEnd,
            'transaksi_count' => count($allEntriesUpToEnd),
            'is_kontra'       => false,
            'is_calculated'   => true,
        ];
        $subtotalEkuitas += $labaBerjalanUpToEnd;

        $totalAset = $subtotalAsetLancar + $subtotalAsetTetap;
        $totalKewajiban = $subtotalKewajibanLancar + $subtotalKewajibanJangkaPanjang;
        $totalPasiva = $totalKewajiban + $subtotalEkuitas;
        $selisih = abs($totalAset - $totalPasiva);
        $isBalanced = ($selisih < 0.01);

        return [
            'aset' => [
                'aset_lancar' => [
                    'accounts' => $asetLancar,
                    'subtotal' => $subtotalAsetLancar,
                ],
                'aset_tetap' => [
                    'accounts' => $asetTetap,
                    'subtotal' => $subtotalAsetTetap,
                ],
                'total' => $totalAset,
            ],
            'kewajiban' => [
                'kewajiban_lancar' => [
                    'accounts' => $kewajibanLancar,
                    'subtotal' => $subtotalKewajibanLancar,
                ],
                'kewajiban_jangka_panjang' => [
                    'accounts' => $kewajibanJangkaPanjang,
                    'subtotal' => $subtotalKewajibanJangkaPanjang,
                ],
                'total' => $totalKewajiban,
            ],
            'ekuitas' => [
                'accounts' => $ekuitas,
                'total'    => $subtotalEkuitas,
            ],
            'total_aset'   => $totalAset,
            'total_pasiva' => $totalPasiva,
            'selisih'      => $selisih,
            'is_balanced'  => $isBalanced,
            'status_label' => $isBalanced ? 'Seimbang (Balance)' : 'Tidak Seimbang',
            'filters'      => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ],
        ];
    }

    /**
     * =========================================================================
     * LAPORAN ARUS KAS (CASH FLOW STATEMENT)
     * =========================================================================
     * Mengelompokkan mutasi Kas & Bank ke dalam 3 Aktivitas Utama (Metode Langsung):
     * 1. Aktivitas Operasional
     * 2. Aktivitas Investasi
     * 3. Aktivitas Pendanaan
     * Mengambil data satu pintu dari Model Jurnal. Zero N+1 Query.
     *
     * @param array $filters
     * @return array
     */
    public static function getCashFlowReport(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? $filters['dr_tgl'] ?? null;
        $endDate   = $filters['end_date'] ?? $filters['sp_tgl'] ?? null;

        // 1. Ambil daftar akun Kas & Bank
        $kasBankCoas = MasterCoa::kasBank()->get();
        $kasBankIds = $kasBankCoas->pluck('id')->toArray();

        // 2. Hitung Saldo Awal Kas & Bank sebelum startDate
        $totalSaldoAwalMasterKas = $kasBankCoas->sum('saldo_awal');
        $priorKasMutasi = 0.0;
        if ($startDate) {
            $priorEntries = self::getRawJurnalEntries([
                'end_date' => Carbon::parse($startDate)->subDay()->toDateString(),
            ]);
            foreach ($priorEntries as $entry) {
                if (in_array((int) $entry->coa_id, $kasBankIds)) {
                    $priorKasMutasi += ((float) $entry->debit - (float) $entry->kredit);
                }
            }
        }
        $saldoAwalKas = $totalSaldoAwalMasterKas + $priorKasMutasi;

        // 3. Ambil entri periode berjalan dari Model Jurnal (1 query)
        $entries = self::getRawJurnalEntries([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $operasionalInflow = [];
        $operasionalOutflow = [];
        $investasiInflow = [];
        $investasiOutflow = [];
        $pendanaanInflow = [];
        $pendanaanOutflow = [];

        $totalKasMasuk = 0.0;
        $totalKasKeluar = 0.0;

        foreach ($entries as $row) {
            // Hanya proses dari sisi akun Kas & Bank
            if (!in_array((int) $row->coa_id, $kasBankIds)) {
                continue;
            }

            $nominalDebit = (float) $row->debit;
            $nominalKredit = (float) $row->kredit;
            $lawanCoaId = (int) $row->lawan_coa_id;
            $lawanNama = $row->lawan_nama_akun ?: 'Tanpa Keterangan Akun';
            $lawanKode = $row->lawan_kode_akun ?: '-';
            $lawanKategori = $row->lawan_kategori_coa ?: '';

            // Kas Masuk (Debit pada Kas/Bank)
            if ($nominalDebit > 0) {
                $totalKasMasuk += $nominalDebit;

                if (str_starts_with($lawanKode, '12') || str_contains(strtoupper($lawanKategori), 'ASET TETAP')) {
                    self::accumulateCashFlow($investasiInflow, $lawanCoaId, $lawanKode, $lawanNama, $lawanKategori, $nominalDebit);
                } elseif (str_starts_with($lawanKode, '3') || str_starts_with($lawanKode, '22') || str_contains(strtoupper($lawanKategori), 'EKUITAS') || str_contains(strtoupper($lawanKategori), 'JANGKA PANJANG')) {
                    self::accumulateCashFlow($pendanaanInflow, $lawanCoaId, $lawanKode, $lawanNama, $lawanKategori, $nominalDebit);
                } else {
                    self::accumulateCashFlow($operasionalInflow, $lawanCoaId, $lawanKode, $lawanNama, $lawanKategori, $nominalDebit);
                }
            }

            // Kas Keluar (Kredit pada Kas/Bank)
            if ($nominalKredit > 0) {
                $totalKasKeluar += $nominalKredit;

                if (str_starts_with($lawanKode, '12') || str_contains(strtoupper($lawanKategori), 'ASET TETAP')) {
                    self::accumulateCashFlow($investasiOutflow, $lawanCoaId, $lawanKode, $lawanNama, $lawanKategori, $nominalKredit);
                } elseif (str_starts_with($lawanKode, '3') || str_starts_with($lawanKode, '22') || str_contains(strtoupper($lawanKategori), 'EKUITAS') || str_contains(strtoupper($lawanKategori), 'JANGKA PANJANG')) {
                    self::accumulateCashFlow($pendanaanOutflow, $lawanCoaId, $lawanKode, $lawanNama, $lawanKategori, $nominalKredit);
                } else {
                    self::accumulateCashFlow($operasionalOutflow, $lawanCoaId, $lawanKode, $lawanNama, $lawanKategori, $nominalKredit);
                }
            }
        }

        $subtotalOpIn = array_sum(array_column($operasionalInflow, 'total_nominal'));
        $subtotalOpOut = array_sum(array_column($operasionalOutflow, 'total_nominal'));
        $netOperasional = $subtotalOpIn - $subtotalOpOut;

        $subtotalInvIn = array_sum(array_column($investasiInflow, 'total_nominal'));
        $subtotalInvOut = array_sum(array_column($investasiOutflow, 'total_nominal'));
        $netInvestasi = $subtotalInvIn - $subtotalInvOut;

        $subtotalPenIn = array_sum(array_column($pendanaanInflow, 'total_nominal'));
        $subtotalPenOut = array_sum(array_column($pendanaanOutflow, 'total_nominal'));
        $netPendanaan = $subtotalPenIn - $subtotalPenOut;

        $kenaikanBersih = $netOperasional + $netInvestasi + $netPendanaan;
        $saldoAkhirKas = $saldoAwalKas + $kenaikanBersih;

        return [
            'operasional' => [
                'inflows'        => array_values($operasionalInflow),
                'outflows'       => array_values($operasionalOutflow),
                'total_inflow'   => $subtotalOpIn,
                'total_outflow'  => $subtotalOpOut,
                'net_operasional'=> $netOperasional,
            ],
            'investasi' => [
                'inflows'        => array_values($investasiInflow),
                'outflows'       => array_values($investasiOutflow),
                'total_inflow'   => $subtotalInvIn,
                'total_outflow'  => $subtotalInvOut,
                'net_investasi'  => $netInvestasi,
            ],
            'pendanaan' => [
                'inflows'        => array_values($pendanaanInflow),
                'outflows'       => array_values($pendanaanOutflow),
                'total_inflow'   => $subtotalPenIn,
                'total_outflow'  => $subtotalPenOut,
                'net_pendanaan'  => $netPendanaan,
            ],
            'rekapitulasi' => [
                'total_kas_masuk'  => $totalKasMasuk,
                'total_kas_keluar' => $totalKasKeluar,
                'kenaikan_bersih'  => $kenaikanBersih,
                'saldo_awal_kas'   => $saldoAwalKas,
                'saldo_akhir_kas'  => $saldoAkhirKas,
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ],
        ];
    }

    /**
     * Helper akumulasi baris arus kas
     */
    private static function accumulateCashFlow(array &$target, int $lawanCoaId, string $lawanKode, string $lawanNama, string $lawanKategori, float $nominal): void
    {
        if (!isset($target[$lawanCoaId])) {
            $target[$lawanCoaId] = [
                'coa_id'          => $lawanCoaId,
                'kode_akun'       => $lawanKode,
                'nama_akun'       => $lawanNama,
                'kategori'        => $lawanKategori,
                'total_nominal'   => 0.0,
                'transaksi_count' => 0,
            ];
        }
        $target[$lawanCoaId]['total_nominal'] += $nominal;
        $target[$lawanCoaId]['transaksi_count'] += 1;
    }

    /**
     * =========================================================================
     * DRILL-DOWN: RINCIAN MUTASI TRANSAKSI AKUN (BUKU BESAR / DETAIL JURNAL)
     * =========================================================================
     * Menampilkan seluruh transaksi yang berkontribusi pada suatu akun dalam periode.
     * Dilengkapi saldo berjalan baris per baris. Zero N+1 Query.
     *
     * @param int $coaId
     * @param array $filters
     * @return array
     */
    public static function getDetailAkunEntries(int $coaId, array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? $filters['dr_tgl'] ?? null;
        $endDate   = $filters['end_date'] ?? $filters['sp_tgl'] ?? null;

        // Kasus khusus: coa_id = 0 (Laba Periode Berjalan)
        if ($coaId === 0) {
            $account = (object) [
                'id'            => 0,
                'kode_akun'     => '3202',
                'nama_akun'     => 'Laba Periode Berjalan',
                'kategori'      => 'EKUITAS',
                'tipe'          => 'Ekuitas',
                'posisi_normal' => 'Kredit',
                'saldo_awal'    => 0,
            ];
            $posisiNormal = 'Kredit';
            $saldoAwalPeriode = 0.0;

            $entries = self::getRawJurnalEntries([
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ]);

            // Ambil hanya baris akun nominal (Pendapatan & Beban)
            $accountEntries = array_filter($entries, function ($e) {
                return $e->tipe_coa === 'Pendapatan' || $e->tipe_coa === 'Beban'
                    || str_starts_with($e->kode_akun, '4') || str_starts_with($e->kode_akun, '5');
            });
        } else {
            $account = MasterCoa::findOrFail($coaId);
            $posisiNormal = $account->posisi_normal ?: ($account->tipe === 'Aset' || $account->tipe === 'Beban' ? 'Debit' : 'Kredit');
            $saldoAwalMaster = (float) $account->saldo_awal;

            $priorDebit = 0.0;
            $priorKredit = 0.0;
            if ($startDate) {
                $priorEntries = self::getRawJurnalEntries([
                    'coa_id'   => $coaId,
                    'end_date' => Carbon::parse($startDate)->subDay()->toDateString(),
                ]);
                foreach ($priorEntries as $pe) {
                    if ((int) $pe->coa_id === $coaId) {
                        $priorDebit += (float) $pe->debit;
                        $priorKredit += (float) $pe->kredit;
                    }
                }
            }

            if ($posisiNormal === 'Debit') {
                $saldoAwalPeriode = $saldoAwalMaster + ($priorDebit - $priorKredit);
            } else {
                $saldoAwalPeriode = $saldoAwalMaster + ($priorKredit - $priorDebit);
            }

            $entries = self::getRawJurnalEntries([
                'coa_id'     => $coaId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ]);

            $accountEntries = array_filter($entries, fn($e) => (int) $e->coa_id === $coaId);
        }

        $runningBalance = $saldoAwalPeriode;
        $totalDebit = 0.0;
        $totalKredit = 0.0;
        $detailedRows = [];

        foreach ($accountEntries as $entry) {
            $debit = (float) $entry->debit;
            $kredit = (float) $entry->kredit;
            $totalDebit += $debit;
            $totalKredit += $kredit;

            if ($posisiNormal === 'Debit') {
                $runningBalance += ($debit - $kredit);
            } else {
                $runningBalance += ($kredit - $debit);
            }

            $detailedRows[] = (object) [
                'transaksi_id'      => $entry->transaksi_id,
                'nomor_transaksi'   => $entry->nomor_transaksi,
                'tanggal'           => $entry->tanggal,
                'jenis'             => $entry->jenis,
                'penerima_pembayar' => $entry->penerima_pembayar,
                'nomor_referensi'   => $entry->nomor_referensi,
                'metode_pembayaran' => $entry->metode_pembayaran,
                'keterangan'        => $entry->keterangan,
                'coa_id'            => $entry->coa_id,
                'kode_akun'         => $entry->kode_akun,
                'nama_akun'         => $entry->nama_akun,
                'lawan_coa_id'      => $entry->lawan_coa_id,
                'lawan_kode_akun'   => $entry->lawan_kode_akun,
                'lawan_nama_akun'   => $entry->lawan_nama_akun,
                'posisi'            => $entry->posisi,
                'debit'             => $debit,
                'kredit'            => $kredit,
                'saldo_berjalan'    => $runningBalance,
                'nama_user'         => $entry->nama_user,
            ];
        }

        return [
            'account'          => $account,
            'posisi_normal'    => $posisiNormal,
            'saldo_awal'       => $saldoAwalPeriode,
            'total_debit'      => $totalDebit,
            'total_kredit'     => $totalKredit,
            'saldo_akhir'      => $runningBalance,
            'total_transaksi'  => count($detailedRows),
            'entries'          => $detailedRows,
            'filters'          => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ],
        ];
    }
}
