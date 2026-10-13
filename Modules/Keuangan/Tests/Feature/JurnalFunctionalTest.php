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

class JurnalFunctionalTest extends TestCase
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
        $this->kasBankCoa = MasterCoa::kasBank()->first() ?? MasterCoa::create([
            'kode_akun'     => '1101',
            'nama_akun'     => 'Kas Operasional',
            'kategori'      => 'ASET LANCAR',
            'tipe'          => 'Aset',
            'laporan'       => 'Neraca',
            'posisi_normal' => 'Debit',
            'saldo_awal'    => 5000000,
            'is_active'     => true,
        ]);

        $this->pendapatanCoa = MasterCoa::pendapatan()->first() ?? MasterCoa::create([
            'kode_akun'     => '4101',
            'nama_akun'     => 'Pendapatan Jasa',
            'kategori'      => 'PENDAPATAN JASA',
            'tipe'          => 'Pendapatan',
            'laporan'       => 'Laba Rugi',
            'posisi_normal' => 'Kredit',
            'saldo_awal'    => 0,
            'is_active'     => true,
        ]);

        $this->bebanCoa = MasterCoa::beban()->first() ?? MasterCoa::create([
            'kode_akun'     => '5101',
            'nama_akun'     => 'Beban Operasional',
            'kategori'      => 'BEBAN OPERASIONAL',
            'tipe'          => 'Beban',
            'laporan'       => 'Laba Rugi',
            'posisi_normal' => 'Debit',
            'saldo_awal'    => 0,
            'is_active'     => true,
        ]);
    }

    /**
     * 1. Uji Registrasi Route Menu Jurnal
     */
    public function test_jurnal_routes_are_registered()
    {
        $this->assertTrue(Route::has('jurnal.index'));
        $this->assertTrue(Route::has('jurnal.data'));
        $this->assertTrue(Route::has('jurnal.stats'));
        $this->assertTrue(Route::has('jurnal.select2.coa'));
        $this->assertTrue(Route::has('jurnal.cetak'));
        $this->assertTrue(Route::has('jurnal.export-excel'));
    }

    /**
     * 2. Uji Menu Jurnal Dapat Diakses oleh Superadmin (Fast Initial Render)
     */
    public function test_jurnal_index_accessible_by_authorized_user()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('jurnal.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Jurnal Umum');
        $response->assertSee('jurnal-table');
        $response->assertSee('filter-coa');
    }

    /**
     * 3. Uji Model Raw Query Jurnal Menghasilkan Double-Entry Bookkeeping yang Seimbang (Balance)
     */
    public function test_jurnal_raw_query_double_entry_balance()
    {
        // Buat 1 transaksi Kas Masuk dummy
        $km = TransaksiKasBank::create([
            'nomor_transaksi'   => TransaksiKasBank::generateNomorTransaksi('masuk', '2026-10-15'),
            'jenis'             => 'masuk',
            'tanggal'           => '2026-10-15',
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->pendapatanCoa->id,
            'nominal'           => 1500000.00,
            'penerima_pembayar' => 'Klien Skripsi Testing',
            'metode_pembayaran' => 'Transfer Bank',
            'status'            => 'completed',
        ]);

        // Buat 1 transaksi Kas Keluar dummy
        $kk = TransaksiKasBank::create([
            'nomor_transaksi'   => TransaksiKasBank::generateNomorTransaksi('keluar', '2026-10-15'),
            'jenis'             => 'keluar',
            'tanggal'           => '2026-10-15',
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->bebanCoa->id,
            'nominal'           => 750000.00,
            'penerima_pembayar' => 'Vendor ATK Testing',
            'metode_pembayaran' => 'Tunai',
            'status'            => 'completed',
        ]);

        $entries = Jurnal::getRawJurnalEntries([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-15',
        ]);

        // Harus ada 4 baris entri: 2 dari KM (Debit Kas + Kredit Pendapatan) dan 2 dari KK (Debit Beban + Kredit Kas)
        $this->assertCount(4, $entries);

        // Periksa baris Kas Masuk
        $kmDebit = collect($entries)->first(fn($e) => $e->transaksi_id == $km->id && $e->posisi == 'Debit');
        $kmKredit = collect($entries)->first(fn($e) => $e->transaksi_id == $km->id && $e->posisi == 'Kredit');

        $this->assertNotNull($kmDebit);
        $this->assertEquals($this->kasBankCoa->kode_akun, $kmDebit->kode_akun);
        $this->assertEquals(1500000.00, (float) $kmDebit->debit);
        $this->assertEquals(0.00, (float) $kmDebit->kredit);

        $this->assertNotNull($kmKredit);
        $this->assertEquals($this->pendapatanCoa->kode_akun, $kmKredit->kode_akun);
        $this->assertEquals(0.00, (float) $kmKredit->debit);
        $this->assertEquals(1500000.00, (float) $kmKredit->kredit);

        // Periksa baris Kas Keluar
        $kkDebit = collect($entries)->first(fn($e) => $e->transaksi_id == $kk->id && $e->posisi == 'Debit');
        $kkKredit = collect($entries)->first(fn($e) => $e->transaksi_id == $kk->id && $e->posisi == 'Kredit');

        $this->assertNotNull($kkDebit);
        $this->assertEquals($this->bebanCoa->kode_akun, $kkDebit->kode_akun);
        $this->assertEquals(750000.00, (float) $kkDebit->debit);
        $this->assertEquals(0.00, (float) $kkDebit->kredit);

        $this->assertNotNull($kkKredit);
        $this->assertEquals($this->kasBankCoa->kode_akun, $kkKredit->kode_akun);
        $this->assertEquals(0.00, (float) $kkKredit->debit);
        $this->assertEquals(750000.00, (float) $kkKredit->kredit);

        // Periksa Rekapitulasi Summary Keseimbangan (Balance)
        $summary = Jurnal::getJurnalSummary([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-15',
        ]);

        $this->assertEquals(2, $summary->total_transaksi);
        $this->assertEquals(4, $summary->total_baris);
        $this->assertEquals(2250000.00, (float) $summary->total_debit);
        $this->assertEquals(2250000.00, (float) $summary->total_kredit);
        $this->assertEquals(0.00, (float) $summary->selisih);
        $this->assertTrue($summary->is_balanced);

        // Cleanup
        $km->forceDelete();
        $kk->forceDelete();
    }

    /**
     * 4. Uji Verifikasi ZERO N+1 Query pada Model Jurnal
     */
    public function test_jurnal_query_has_zero_n_plus_one()
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        // Jalankan pemanggilan getRawJurnalEntries
        $entries = Jurnal::getRawJurnalEntries();

        $log = DB::getQueryLog();
        DB::disableQueryLog();

        // Harus HANYA 1 query eksekusi tunggal (O(1)), tidak ada query N+1 berulang
        $this->assertCount(1, $log, 'Pengambilan data Jurnal wajib hanya 1 query SQL tanpa N+1!');
    }

    /**
     * 5. Uji DataTables Server-Side AJAX Endpoint (1 Kolom Kombinasi Transaksi & Akun)
     */
    public function test_jurnal_datatables_server_side_endpoint()
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson(route('jurnal.data'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'transaksi_info',
                    'akun',
                    'posisi',
                    'debit',
                    'kredit',
                ]
            ]
        ]);
    }

    /**
     * 6. Uji AJAX KPI Stats Server-side
     */
    public function test_jurnal_stats_ajax_server_side()
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson(route('jurnal.stats'));
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'total_transaksi',
                'total_transaksi_formatted',
                'total_baris',
                'total_baris_formatted',
                'total_debit',
                'total_debit_formatted',
                'total_kredit',
                'total_kredit_formatted',
                'selisih',
                'selisih_formatted',
                'is_balanced',
                'status_label',
            ]
        ]);
    }

    /**
     * 7. Uji Select2 AJAX Endpoint Akun CoA
     */
    public function test_jurnal_select2_coa_ajax_endpoint()
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson(route('jurnal.select2.coa', ['q' => 'Kas']));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'results' => [
                '*' => [
                    'id',
                    'text',
                    'kode_akun',
                    'nama_akun',
                    'kategori',
                ]
            ],
            'pagination' => [
                'more',
            ]
        ]);
    }

    /**
     * 8. Uji Endpoint Cetak HTML Jurnal
     */
    public function test_jurnal_cetak_html_action()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('jurnal.cetak'));
        $response->assertStatus(200);
        $response->assertSee('images/logo_aether.jpeg');
        $response->assertSee('LAPORAN JURNAL UMUM');
        $response->assertSee('AETHER ACADEMIC & RESEARCH', false);
        $response->assertSee('TOTAL JURNAL UMUM');
        $response->assertSee('Dibuat Oleh');
        $response->assertSee('Supervisor Keuangan');
    }

    /**
     * 9. Uji Endpoint Export Excel Jurnal
     */
    public function test_jurnal_export_excel_action()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('jurnal.export-excel'));
        $response->assertStatus(200);

        // Header response download excel
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
        $this->assertStringContainsString('Jurnal_Umum_', $response->headers->get('content-disposition'));
    }

    /**
     * 10. Uji Menu Jurnal Ditampilkan di Sidebar Layout
     */
    public function test_sidebar_renders_jurnal_menu()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Jurnal');
    }
}
