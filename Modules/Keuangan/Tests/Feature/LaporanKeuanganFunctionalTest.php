<?php

namespace Modules\Keuangan\Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Entities\Jurnal;
use Modules\Keuangan\Entities\TransaksiKasBank;
use Modules\Master\Entities\MasterCoa;
use Tests\TestCase;

class LaporanKeuanganFunctionalTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;
    protected $kasBankCoa;
    protected $pendapatanCoa;
    protected $bebanCoa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::first() ?? User::factory()->create();
        $this->kasBankCoa = MasterCoa::kasBank()->first();
        $this->pendapatanCoa = MasterCoa::pendapatan()->first();
        $this->bebanCoa = MasterCoa::beban()->first();
    }

    /**
     * 1. Uji Registrasi Seluruh Route Menu Laporan Keuangan
     */
    public function test_laporan_keuangan_routes_are_registered()
    {
        // Neraca
        $this->assertTrue(Route::has('neraca.index'));
        $this->assertTrue(Route::has('neraca.data'));
        $this->assertTrue(Route::has('neraca.rincian'));
        $this->assertTrue(Route::has('neraca.cetak'));
        $this->assertTrue(Route::has('neraca.export-excel'));

        // Laba Rugi
        $this->assertTrue(Route::has('rugi-laba.index'));
        $this->assertTrue(Route::has('rugi-laba.data'));
        $this->assertTrue(Route::has('rugi-laba.rincian'));
        $this->assertTrue(Route::has('rugi-laba.cetak'));
        $this->assertTrue(Route::has('rugi-laba.export-excel'));

        // Cash Flow
        $this->assertTrue(Route::has('cashflow.index'));
        $this->assertTrue(Route::has('cashflow.data'));
        $this->assertTrue(Route::has('cashflow.rincian'));
        $this->assertTrue(Route::has('cashflow.cetak'));
        $this->assertTrue(Route::has('cashflow.export-excel'));
    }

    /**
     * 2. Uji Halaman Utama Neraca Dapat Diakses dan Menampilkan Komponen Lengkap
     */
    public function test_neraca_index_accessible_and_renders_components()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('neraca.index'));
        $response->assertStatus(200);
        $response->assertSee('Neraca Keuangan');
        $response->assertSee('Aset (Aktiva)');
        $response->assertSee('Kewajiban &amp; Ekuitas (Pasiva)', false);
        $response->assertSee('Cetak Lembar HTML');
        $response->assertSee('Export Excel');
        $response->assertSee('clickable-account-row');
    }

    /**
     * 3. Uji Halaman Utama Laba Rugi Dapat Diakses dan Menampilkan Komponen Lengkap
     */
    public function test_rugi_laba_index_accessible_and_renders_components()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('rugi-laba.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Laba Rugi');
        $response->assertSee('PENDAPATAN (REVENUE)');
        $response->assertSee('BEBAN OPERASIONAL (EXPENSES)');
        $response->assertSee('HASIL USAHA PERIODE');
        $response->assertSee('Cetak Lembar HTML');
        $response->assertSee('Export Excel');
    }

    /**
     * 4. Uji Halaman Utama Cash Flow Dapat Diakses dan Menampilkan Komponen Lengkap
     */
    public function test_cashflow_index_accessible_and_renders_components()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('cashflow.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Arus Kas');
        $response->assertSee('ARUS KAS DARI AKTIVITAS OPERASIONAL');
        $response->assertSee('ARUS KAS DARI AKTIVITAS INVESTASI');
        $response->assertSee('ARUS KAS DARI AKTIVITAS PENDANAAN');
        $response->assertSee('SALDO AKHIR KAS & BANK');
    }

    /**
     * 5. Uji Model Jurnal: Laba Rugi Dihitung dari Satu Pintu Jurnal Umum
     */
    public function test_rugi_laba_calculation_from_model_jurnal()
    {
        // Buat transaksi pemasukan & pengeluaran baru
        $km = TransaksiKasBank::create([
            'nomor_transaksi'   => TransaksiKasBank::generateNomorTransaksi('masuk', '2026-10-18'),
            'jenis'             => 'masuk',
            'tanggal'           => '2026-10-18',
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->pendapatanCoa->id,
            'nominal'           => 3000000.00,
            'penerima_pembayar' => 'Mahasiswa Konsultasi Skripsi',
            'metode_pembayaran' => 'Transfer Bank',
            'status'            => 'completed',
        ]);

        $kk = TransaksiKasBank::create([
            'nomor_transaksi'   => TransaksiKasBank::generateNomorTransaksi('keluar', '2026-10-18'),
            'jenis'             => 'keluar',
            'tanggal'           => '2026-10-18',
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->bebanCoa->id,
            'nominal'           => 1000000.00,
            'penerima_pembayar' => 'Vendor Software Riset',
            'metode_pembayaran' => 'Transfer Bank',
            'status'            => 'completed',
        ]);

        $report = Jurnal::getRugiLabaReport([
            'start_date' => '2026-10-18',
            'end_date'   => '2026-10-18',
        ]);

        $this->assertGreaterThanOrEqual(3000000.00, $report['total_pendapatan']);
        $this->assertGreaterThanOrEqual(1000000.00, $report['total_beban']);
        $this->assertEquals(2000000.00, $report['laba_bersih']);
        $this->assertTrue($report['is_laba']);
        $this->assertEquals('Laba Bersih', $report['status_label']);

        // Cleanup
        $km->forceDelete();
        $kk->forceDelete();
    }

    /**
     * 6. Uji Model Jurnal: Neraca Keseimbangan (Total Aset = Total Pasiva)
     */
    public function test_neraca_balance_equation()
    {
        $neraca = Jurnal::getNeracaReport();

        $this->assertArrayHasKey('aset', $neraca);
        $this->assertArrayHasKey('kewajiban', $neraca);
        $this->assertArrayHasKey('ekuitas', $neraca);
        $this->assertArrayHasKey('total_aset', $neraca);
        $this->assertArrayHasKey('total_pasiva', $neraca);
        $this->assertArrayHasKey('is_balanced', $neraca);

        // Nilai aset dan pasiva harus seimbang dengan toleransi desimal
        $this->assertTrue($neraca['is_balanced'], 'Neraca harus seimbang (Total Aset = Total Pasiva)');
        $this->assertLessThan(0.01, $neraca['selisih']);
    }

    /**
     * 7. Uji Model Jurnal: Arus Kas Mengalir Sesuai Rekapitulasi Jurnal
     */
    public function test_cashflow_calculation_from_model_jurnal()
    {
        $cf = Jurnal::getCashFlowReport();

        $this->assertArrayHasKey('operasional', $cf);
        $this->assertArrayHasKey('investasi', $cf);
        $this->assertArrayHasKey('pendanaan', $cf);
        $this->assertArrayHasKey('rekapitulasi', $cf);

        $rekap = $cf['rekapitulasi'];
        $expectedNet = $cf['operasional']['net_operasional'] + $cf['investasi']['net_investasi'] + $cf['pendanaan']['net_pendanaan'];
        $this->assertEquals($expectedNet, $rekap['kenaikan_bersih']);
        $this->assertEquals($rekap['saldo_awal_kas'] + $rekap['kenaikan_bersih'], $rekap['saldo_akhir_kas']);
    }

    /**
     * 8. Uji Drill-Down: Rincian Transaksi Akun (Klik-able)
     */
    public function test_drill_down_rincian_akun_endpoint()
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson(route('neraca.rincian', ['coa_id' => $this->kasBankCoa->id]));
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'account' => [
                    'id',
                    'kode_akun',
                    'nama_akun',
                ],
                'posisi_normal',
                'saldo_awal',
                'total_debit',
                'total_kredit',
                'saldo_akhir',
                'total_transaksi',
                'entries' => [
                    '*' => [
                        'transaksi_id',
                        'nomor_transaksi',
                        'tanggal',
                        'keterangan',
                        'debit',
                        'kredit',
                        'saldo_berjalan',
                    ]
                ]
            ]
        ]);

        // Uji Halaman Penuh Rincian (Bukan Modal, Langsung ke Halaman Rincian)
        $htmlResponse = $this->get(route('neraca.rincian', ['coa_id' => $this->kasBankCoa->id]));
        $htmlResponse->assertStatus(200);
        $htmlResponse->assertSee('Rincian Buku Besar Akun');
        $htmlResponse->assertSee($this->kasBankCoa->nama_akun);
        $htmlResponse->assertSee($this->kasBankCoa->kode_akun);
        $htmlResponse->assertSee('Kembali ke Neraca Keuangan');
    }

    /**
     * 9. Uji Verifikasi ZERO N+1 Query pada Seluruh Laporan Keuangan
     */
    public function test_reports_have_zero_n_plus_one()
    {
        // A. Neraca
        DB::flushQueryLog();
        DB::enableQueryLog();
        Jurnal::getNeracaReport();
        $neracaLog = DB::getQueryLog();
        DB::disableQueryLog();

        // Maksimal 2 query: 1 query MasterCoa + 1 query Jurnal double-entry. Sama sekali tidak ada query per-akun di loop!
        $this->assertLessThanOrEqual(2, count($neracaLog), 'Neraca report harus bebas dari query N+1!');

        // B. Laba Rugi
        DB::flushQueryLog();
        DB::enableQueryLog();
        Jurnal::getRugiLabaReport();
        $rlLog = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertLessThanOrEqual(2, count($rlLog), 'Rugi Laba report harus bebas dari query N+1!');

        // C. Arus Kas
        DB::flushQueryLog();
        DB::enableQueryLog();
        Jurnal::getCashFlowReport();
        $cfLog = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertLessThanOrEqual(3, count($cfLog), 'Cash Flow report harus bebas dari query N+1!');
    }

    /**
     * 10. Uji Cetak HTML untuk Neraca, Laba Rugi, dan Arus Kas
     */
    public function test_cetak_html_for_all_financial_reports()
    {
        $this->actingAs($this->adminUser);

        // Cetak Neraca
        $resNeraca = $this->get(route('neraca.cetak'));
        $resNeraca->assertStatus(200);
        $resNeraca->assertSee('LAPORAN NERACA KEUANGAN');
        $resNeraca->assertSee('AETHER ACADEMIC');

        // Cetak Laba Rugi
        $resRL = $this->get(route('rugi-laba.cetak'));
        $resRL->assertStatus(200);
        $resRL->assertSee('LAPORAN LABA RUGI');

        // Cetak Cash Flow
        $resCF = $this->get(route('cashflow.cetak'));
        $resCF->assertStatus(200);
        $resCF->assertSee('LAPORAN ARUS KAS');
    }

    /**
     * 11. Uji Export Excel untuk Neraca, Laba Rugi, dan Arus Kas
     */
    public function test_export_excel_for_all_financial_reports()
    {
        $this->actingAs($this->adminUser);

        // Excel Neraca
        $resNeraca = $this->get(route('neraca.export-excel'));
        $resNeraca->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $resNeraca->headers->get('content-type'));
        $this->assertStringContainsString('Neraca_Keuangan_', $resNeraca->headers->get('content-disposition'));

        // Excel Laba Rugi
        $resRL = $this->get(route('rugi-laba.export-excel'));
        $resRL->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $resRL->headers->get('content-type'));
        $this->assertStringContainsString('Laba_Rugi_', $resRL->headers->get('content-disposition'));

        // Excel Cash Flow
        $resCF = $this->get(route('cashflow.export-excel'));
        $resCF->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $resCF->headers->get('content-type'));
        $this->assertStringContainsString('Arus_Kas_', $resCF->headers->get('content-disposition'));
    }

    /**
     * 12. Uji Sidebar Layout Menampilkan Menu Neraca, Laba Rugi, dan Cash Flow
     */
    public function test_sidebar_renders_all_three_financial_report_menus()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Neraca Keuangan');
        $response->assertSee('Laba Rugi');
        $response->assertSee('Arus Kas (Cash Flow)');
    }
}
