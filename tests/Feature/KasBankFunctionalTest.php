<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Entities\TransaksiKasBank;
use Modules\Master\Entities\MasterCoa;
use Tests\TestCase;

class KasBankFunctionalTest extends TestCase
{
    protected $adminUser;
    protected $kasBankCoa;
    protected $bebanCoa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::first() ?? User::factory()->create();
        $this->kasBankCoa = MasterCoa::kasBank()->first();
        $this->bebanCoa = MasterCoa::beban()->first();
    }

    /**
     * 1. Uji Registrasi Route Menu Kas/Bank Masuk & Keluar
     */
    public function test_kas_bank_routes_are_registered()
    {
        $this->assertTrue(Route::has('kas-bank-masuk.index'));
        $this->assertTrue(Route::has('kas-bank-masuk.stats'));
        $this->assertTrue(Route::has('kas-bank-masuk.data'));
        $this->assertTrue(Route::has('kas-bank-masuk.store'));
        $this->assertTrue(Route::has('kas-bank-masuk.show'));
        $this->assertTrue(Route::has('kas-bank-masuk.update'));
        $this->assertTrue(Route::has('kas-bank-masuk.destroy'));

        $this->assertTrue(Route::has('kas-bank-keluar.index'));
        $this->assertTrue(Route::has('kas-bank-keluar.stats'));
        $this->assertTrue(Route::has('kas-bank-keluar.data'));
        $this->assertTrue(Route::has('kas-bank-keluar.store'));
        $this->assertTrue(Route::has('kas-bank-keluar.show'));
        $this->assertTrue(Route::has('kas-bank-keluar.update'));
        $this->assertTrue(Route::has('kas-bank-keluar.destroy'));

        $this->assertTrue(Route::has('kas-bank.next-nomor'));
        $this->assertTrue(Route::has('kas-bank.voucher'));
    }

    /**
     * 2. Uji Fungsional CRUD Kas Masuk (Debet = Kas/Bank, Kredit = Akun Lawan)
     */
    public function test_kas_masuk_crud_and_debet_kredit_persistence()
    {
        $this->actingAs($this->adminUser);
        session(['role' => ['id_role' => 1]]);

        // A. CREATE: Simpan Transaksi Kas Masuk
        $payload = [
            'tanggal'           => date('Y-m-d'),
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->bebanCoa->id,
            'nominal'           => '1.750.000',
            'metode_pembayaran' => 'Transfer Bank',
            'penerima_pembayar' => 'Klien Konsultasi Skripsi',
            'nomor_referensi'   => 'REF-KM-TEST-001',
            'keterangan'        => 'Pengembalian / Penerimaan Uji Coba',
        ];

        $response = $this->post(route('kas-bank-masuk.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $transaksiId = $response->json('data.id');
        $this->assertNotNull($transaksiId);

        // Verifikasi Akun Debet & Kredit tersimpan di Database
        $transaksi = TransaksiKasBank::with(['kasBank', 'lawanCoa'])->find($transaksiId);
        $this->assertNotNull($transaksi);
        $this->assertEquals('masuk', $transaksi->jenis);
        $this->assertEquals($this->kasBankCoa->id, $transaksi->kas_bank_id);
        $this->assertEquals($this->bebanCoa->id, $transaksi->lawan_coa_id);
        $this->assertEquals(1750000, (float)$transaksi->nominal);

        // Debet: Kas/Bank (Aset Lancar bertambah)
        $this->assertEquals($this->kasBankCoa->nama_akun, $transaksi->kasBank->nama_akun);
        $this->assertEquals('Aset', $transaksi->kasBank->tipe);

        // Kredit: Lawan CoA (Beban Operasional)
        $this->assertEquals($this->bebanCoa->nama_akun, $transaksi->lawanCoa->nama_akun);
        $this->assertEquals('Beban', $transaksi->lawanCoa->tipe);

        // B. READ: Tampil via Show API, DataTables, dan Voucher
        $showResponse = $this->get(route('kas-bank-masuk.show', $transaksiId));
        $showResponse->assertStatus(200);
        $showResponse->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $transaksiId,
                'kas_bank_id' => $this->kasBankCoa->id,
                'lawan_coa_id' => $this->bebanCoa->id,
            ]
        ]);

        $dataTableResponse = $this->get(route('kas-bank-masuk.data'));
        $dataTableResponse->assertStatus(200);
        $dataTableResponse->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);

        $voucherResponse = $this->get(route('kas-bank.voucher', $transaksiId));
        $voucherResponse->assertStatus(200);
        $voucherResponse->assertSee('BUKTI KAS / BANK MASUK');
        $voucherResponse->assertSee($transaksi->nomor_transaksi);
        $voucherResponse->assertSee($this->kasBankCoa->nama_akun);
        $voucherResponse->assertSee($this->bebanCoa->nama_akun);

        // C. UPDATE: Perbarui Transaksi
        $updatePayload = [
            'tanggal'           => date('Y-m-d'),
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->bebanCoa->id,
            'nominal'           => '2.000.000',
            'metode_pembayaran' => 'Tunai',
            'penerima_pembayar' => 'Klien Konsultasi Revisi',
            'nomor_referensi'   => 'REF-KM-TEST-UPDATED',
            'keterangan'        => 'Keterangan diperbarui melalui unit test',
        ];

        $updateResponse = $this->put(route('kas-bank-masuk.update', $transaksiId), $updatePayload);
        $updateResponse->assertStatus(200);
        $updateResponse->assertJson(['status' => 'success']);

        $transaksiUpdated = TransaksiKasBank::find($transaksiId);
        $this->assertEquals(2000000, (float)$transaksiUpdated->nominal);
        $this->assertEquals('Klien Konsultasi Revisi', $transaksiUpdated->penerima_pembayar);

        // D. DELETE: Soft Delete Transaksi
        $deleteResponse = $this->delete(route('kas-bank-masuk.destroy', $transaksiId));
        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['status' => 'success']);

        $this->assertSoftDeleted('transaksi_kas_banks', ['id' => $transaksiId]);
    }

    /**
     * 3. Uji Fungsional CRUD Kas Keluar (Debet = Akun Beban, Kredit = Kas/Bank)
     */
    public function test_kas_keluar_crud_and_debet_kredit_persistence()
    {
        $this->actingAs($this->adminUser);
        session(['role' => ['id_role' => 1]]);

        // A. CREATE: Simpan Transaksi Kas Keluar
        $payload = [
            'tanggal'           => date('Y-m-d'),
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->bebanCoa->id,
            'nominal'           => '850.000',
            'metode_pembayaran' => 'Transfer Bank',
            'penerima_pembayar' => 'Vendor Lisensi Software Riset',
            'nomor_referensi'   => 'INV-KK-TEST-001',
            'keterangan'        => 'Pembayaran Beban Operasional Uji Coba',
        ];

        $response = $this->post(route('kas-bank-keluar.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $transaksiId = $response->json('data.id');
        $this->assertNotNull($transaksiId);

        // Verifikasi Akun Debet & Kredit tersimpan di Database
        $transaksi = TransaksiKasBank::with(['kasBank', 'lawanCoa'])->find($transaksiId);
        $this->assertNotNull($transaksi);
        $this->assertEquals('keluar', $transaksi->jenis);
        $this->assertEquals($this->kasBankCoa->id, $transaksi->kas_bank_id);
        $this->assertEquals($this->bebanCoa->id, $transaksi->lawan_coa_id);
        $this->assertEquals(850000, (float)$transaksi->nominal);

        // Debet: Akun Beban Operasional (Beban bertambah)
        $this->assertEquals($this->bebanCoa->nama_akun, $transaksi->lawanCoa->nama_akun);
        $this->assertEquals('Beban', $transaksi->lawanCoa->tipe);

        // Kredit: Kas/Bank (Aset berkurang)
        $this->assertEquals($this->kasBankCoa->nama_akun, $transaksi->kasBank->nama_akun);
        $this->assertEquals('Aset', $transaksi->kasBank->tipe);

        // B. READ: Tampil via Show API, DataTables, dan Voucher
        $showResponse = $this->get(route('kas-bank-keluar.show', $transaksiId));
        $showResponse->assertStatus(200);
        $showResponse->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $transaksiId,
                'kas_bank_id' => $this->kasBankCoa->id,
                'lawan_coa_id' => $this->bebanCoa->id,
            ]
        ]);

        $dataTableResponse = $this->get(route('kas-bank-keluar.data'));
        $dataTableResponse->assertStatus(200);
        $dataTableResponse->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);

        $voucherResponse = $this->get(route('kas-bank.voucher', $transaksiId));
        $voucherResponse->assertStatus(200);
        $voucherResponse->assertSee('BUKTI KAS / BANK KELUAR');
        $voucherResponse->assertSee($transaksi->nomor_transaksi);
        $voucherResponse->assertSee($this->kasBankCoa->nama_akun);
        $voucherResponse->assertSee($this->bebanCoa->nama_akun);

        // C. UPDATE: Perbarui Transaksi
        $updatePayload = [
            'tanggal'           => date('Y-m-d'),
            'kas_bank_id'       => $this->kasBankCoa->id,
            'lawan_coa_id'      => $this->bebanCoa->id,
            'nominal'           => '900.000',
            'metode_pembayaran' => 'Transfer Bank',
            'penerima_pembayar' => 'Vendor Lisensi Software Diperbarui',
            'nomor_referensi'   => 'INV-KK-TEST-UPDATED',
            'keterangan'        => 'Pembayaran beban diperbarui',
        ];

        $updateResponse = $this->put(route('kas-bank-keluar.update', $transaksiId), $updatePayload);
        $updateResponse->assertStatus(200);
        $updateResponse->assertJson(['status' => 'success']);

        $transaksiUpdated = TransaksiKasBank::find($transaksiId);
        $this->assertEquals(900000, (float)$transaksiUpdated->nominal);
        $this->assertEquals('Vendor Lisensi Software Diperbarui', $transaksiUpdated->penerima_pembayar);

        // D. DELETE: Soft Delete Transaksi
        $deleteResponse = $this->delete(route('kas-bank-keluar.destroy', $transaksiId));
        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['status' => 'success']);

        $this->assertSoftDeleted('transaksi_kas_banks', ['id' => $transaksiId]);
    }

    /**
     * 4. Uji Definisi Hak Akses di config/constant.php
     */
    public function test_constant_permissions_define_kas_bank()
    {
        $permissions = config('constant.permissions.KEUANGAN');
        $this->assertIsArray($permissions);
        
        $this->assertArrayHasKey('kas-bank-masuk', $permissions);
        $this->assertEquals('Kas Bank Masuk', $permissions['kas-bank-masuk']['label']);
        $this->assertArrayHasKey('index', $permissions['kas-bank-masuk']['actions']);
        $this->assertArrayHasKey('create', $permissions['kas-bank-masuk']['actions']);
        $this->assertArrayHasKey('update', $permissions['kas-bank-masuk']['actions']);
        $this->assertArrayHasKey('delete', $permissions['kas-bank-masuk']['actions']);

        $this->assertArrayHasKey('kas-bank-keluar', $permissions);
        $this->assertEquals('Kas Bank Keluar', $permissions['kas-bank-keluar']['label']);
        $this->assertArrayHasKey('index', $permissions['kas-bank-keluar']['actions']);
        $this->assertArrayHasKey('create', $permissions['kas-bank-keluar']['actions']);
        $this->assertArrayHasKey('update', $permissions['kas-bank-keluar']['actions']);
        $this->assertArrayHasKey('delete', $permissions['kas-bank-keluar']['actions']);
    }

    /**
     * 5. Uji Kehadiran Menu Kas Bank di Sidebar Layout
     */
    public function test_sidebar_renders_kas_bank_menus_with_permission()
    {
        $this->actingAs($this->adminUser);
        session(['role' => ['id_role' => 1]]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee(route('kas-bank-masuk.index'));
        $response->assertSee(route('kas-bank-keluar.index'));
        $response->assertSee('Kas / Bank Masuk');
        $response->assertSee('Kas / Bank Keluar');
    }

    /**
     * 6. Uji Proteksi Izin (Permission Enforcement) Menu & Aksi Kas Bank
     */
    public function test_permission_denied_for_unauthorized_role()
    {
        $this->actingAs($this->adminUser);
        // Role 9999 tanpa permission kas-bank
        session(['role' => ['id_role' => 9999]]);

        // Akses menu masuk ditolak
        $responseMasuk = $this->get(route('kas-bank-masuk.index'));
        $responseMasuk->assertRedirect();

        // Akses menu keluar ditolak
        $responseKeluar = $this->get(route('kas-bank-keluar.index'));
        $responseKeluar->assertRedirect();

        // Akses simpan masuk ditolak 403
        $storeMasuk = $this->post(route('kas-bank-masuk.store'), [
            'tanggal' => date('Y-m-d'),
            'kas_bank_id' => $this->kasBankCoa->id,
            'lawan_coa_id' => $this->bebanCoa->id,
            'nominal' => '100.000',
        ]);
        $storeMasuk->assertStatus(403);

        // Akses simpan keluar ditolak 403
        $storeKeluar = $this->post(route('kas-bank-keluar.store'), [
            'tanggal' => date('Y-m-d'),
            'kas_bank_id' => $this->kasBankCoa->id,
            'lawan_coa_id' => $this->bebanCoa->id,
            'nominal' => '100.000',
        ]);
        $storeKeluar->assertStatus(403);
    }

    /**
     * 7. Uji Endpoint Select2 Ajax untuk Kas Bank & Akun Lawan
     */
    public function test_select2_ajax_endpoints_return_expected_results()
    {
        $this->actingAs($this->adminUser);
        session(['role' => ['id_role' => 1]]);

        // A. Test Select2 Kas Bank
        $resKasBank = $this->getJson(route('kas-bank.select2.kas-bank', ['q' => 'Bank Mandiri']));
        $resKasBank->assertStatus(200);
        $resKasBank->assertJsonStructure([
            'results' => [
                '*' => ['id', 'text', 'kode_akun', 'nama_akun', 'kategori', 'tipe']
            ],
            'pagination' => ['more']
        ]);
        $this->assertNotEmpty($resKasBank->json('results'));

        // B. Test Select2 Lawan (Masuk)
        $resLawanMasuk = $this->getJson(route('kas-bank.select2.lawan', ['jenis' => 'masuk', 'q' => 'Beban']));
        $resLawanMasuk->assertStatus(200);
        $resLawanMasuk->assertJsonStructure([
            'results',
            'pagination' => ['more']
        ]);
        $this->assertNotEmpty($resLawanMasuk->json('results'));

        // C. Test Select2 Lawan (Keluar)
        $resLawanKeluar = $this->getJson(route('kas-bank.select2.lawan', ['jenis' => 'keluar', 'q' => 'Operasional']));
        $resLawanKeluar->assertStatus(200);
        $this->assertNotEmpty($resLawanKeluar->json('results'));
    }

    /**
     * 8. Uji Endpoint AJAX Server-Side Stats & Saldo Kas/Bank (Tanpa N+1)
     */
    public function test_stats_ajax_endpoints_return_aggregated_metrics_and_balances()
    {
        $this->actingAs($this->adminUser);
        session(['role' => ['id_role' => 1]]);

        // A. Test Stats Kas Masuk
        $resMasuk = $this->getJson(route('kas-bank-masuk.stats'));
        $resMasuk->assertStatus(200);
        $resMasuk->assertJsonStructure([
            'status',
            'data' => [
                'total_bulan_ini',
                'total_bulan_ini_formatted',
                'count_bulan_ini',
                'total_hari_ini',
                'total_hari_ini_formatted',
                'total_akumulasi',
                'total_akumulasi_formatted',
                'total_akun',
                'saldo_kas_bank' => [
                    '*' => [
                        'id',
                        'kode_akun',
                        'nama_akun',
                        'saldo_awal',
                        'total_masuk',
                        'total_masuk_formatted',
                        'total_keluar',
                        'total_keluar_formatted',
                        'saldo_akhir',
                        'saldo_akhir_formatted',
                    ]
                ]
            ]
        ]);

        // B. Test Stats Kas Keluar
        $resKeluar = $this->getJson(route('kas-bank-keluar.stats'));
        $resKeluar->assertStatus(200);
        $resKeluar->assertJsonStructure([
            'status',
            'data' => [
                'total_bulan_ini',
                'total_bulan_ini_formatted',
                'count_bulan_ini',
                'total_hari_ini',
                'total_hari_ini_formatted',
                'total_akumulasi',
                'total_akumulasi_formatted',
                'total_akun',
                'saldo_kas_bank'
            ]
        ]);
    }
}
