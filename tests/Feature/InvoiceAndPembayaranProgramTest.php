<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Bimbingan\Entities\TransaksiProgram;
use Modules\Master\Entities\MasterProgram;
use Modules\Olahdata\Entities\TransaksiOlahdata;
use Modules\Pelatihan\Entities\TransaksiPelatihan;
use Tests\TestCase;

class InvoiceAndPembayaranProgramTest extends TestCase
{
    use DatabaseTransactions;

    protected function createUserWithRole99(string $name): User
    {
        $user = User::create([
            'nm_user'  => $name,
            'username' => 'user_' . uniqid() . '@example.com',
            'email'    => 'user_' . uniqid() . '@example.com',
            'password' => Hash::make('secret123'),
            'is_aktif' => 1,
        ]);

        DB::table('role_user')->insert([
            'id_user' => $user->id_user,
            'id_role' => 99,
        ]);

        return $user;
    }

    protected function actingAsRole99(User $user)
    {
        return $this->actingAs($user)->withSession([
            'role' => [
                'id_role' => 99,
                'nm_role' => 'Karyawan',
            ],
            'user' => [
                'id_user' => $user->id_user,
                'nm_user' => $user->nm_user,
                'email'   => $user->email,
            ],
        ]);
    }

    protected function getAdminUser(): User
    {
        return User::first() ?? User::factory()->create([
            'username' => 'superadmin',
            'email'    => 'admin@aether.test',
            'password' => Hash::make('secret123'),
        ]);
    }

    protected function actingAsAdmin(User $admin)
    {
        return $this->actingAs($admin)->withSession([
            'role' => [
                'id_role' => 1,
                'nm_role' => 'Administrator',
            ],
            'user' => [
                'id_user' => $admin->id_user,
                'nm_user' => $admin->nm_user,
                'email'   => $admin->email,
            ],
        ]);
    }

    protected function getOrCreateProgram(): MasterProgram
    {
        return MasterProgram::firstOrCreate(
            ['kode_program' => 'PRG-TEST-INVOICE'],
            [
                'nama_program'  => 'Program Bimbingan Skripsi Unggulan',
                'kategori'      => 'Bimbingan Skripsi',
                'jenjang'       => 'S1 (Sarjana)',
                'biaya_standar' => 800000,
                'durasi_sesi'   => 90,
                'jumlah_sesi'   => 4,
                'is_active'     => true,
            ]
        );
    }

    public function test_admin_can_access_rekap_invoice_menu_and_see_all_transactions()
    {
        $admin = $this->getAdminUser();
        $program = $this->getOrCreateProgram();

        $trx1 = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-INV-TEST-001',
            'user_id'           => 1,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Budi Santoso',
            'email_pemesan'     => 'budi@example.com',
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        $response = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.index'));
        $response->assertStatus(200);
        $response->assertSee('Rekap Data Tagihan (Invoice) User');
        $response->assertSee('TRX-INV-TEST-001');
        $response->assertSee('Budi Santoso');
        $response->assertSee(number_format(805000, 0, ',', '.'));
    }

    public function test_rekap_invoice_shows_bimbingan_olahdata_and_pelatihan_with_labels()
    {
        $admin = $this->getAdminUser();
        $program = $this->getOrCreateProgram();

        // 1. Bimbingan
        $bimbingan = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-BIMB-MULTI-' . uniqid(),
            'user_id'           => 1,
            'program_id'        => $program->id,
            'nama_program'      => 'Mentoring Skripsi 1-on-1',
            'nama_pemesan'      => 'Mahasiswa Bimbingan',
            'email_pemesan'     => 'bimbingan@test.com',
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        // 2. Olah Data
        $olahdata = TransaksiOlahdata::create([
            'nomor_order'        => 'OD-MULTI-' . uniqid(),
            'user_id'            => 1,
            'nama_mahasiswa'     => 'Mahasiswa Olah Data',
            'email'              => 'olahdata@test.com',
            'no_whatsapp'        => '081234567890',
            'judul_penelitian'   => 'Analisis SEM PLS Kinerja',
            'software_digunakan' => 'SmartPLS',
            'metode_analisis'    => 'SEM-PLS',
            'status_pengerjaan'  => 'Proses Running Data',
            'total_biaya'        => 450000,
            'admin_fee'          => 5000,
            'total_bayar'        => 455000,
            'status_pembayaran'  => 'Lunas',
            'tanggal_pembelian'  => now(),
        ]);

        // 3. Pelatihan
        $pelatihan = TransaksiPelatihan::create([
            'nomor_registrasi'  => 'REG-MULTI-' . uniqid(),
            'user_id'           => 1,
            'judul_kelas'       => 'Bootcamp Mahir SPSS',
            'instruktur'        => 'Arrum Rochmani Hidayah, M.Kom.',
            'status'            => 'Aktif Berjalan',
            'nama_pemesan'      => 'Peserta Pelatihan',
            'email_pemesan'     => 'pelatihan@test.com',
            'telepon_pemesan'   => '089988776655',
            'biaya'             => 175000,
            'admin_fee'         => 5000,
            'total_bayar'       => 180000,
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        $response = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.index'));
        $response->assertStatus(200);

        // Verify all 3 transactions appear
        $response->assertSee($bimbingan->nomor_transaksi);
        $response->assertSee($olahdata->nomor_order);
        $response->assertSee($pelatihan->nomor_registrasi);

        // Verify explicit labels exist in output
        $response->assertSee('Bimbingan');
        $response->assertSee('Olah Data');
        $response->assertSee('Kelas &amp; Workshop', false);
    }

    public function test_filter_by_jenis_works_on_invoices_and_pembayaran()
    {
        $admin = $this->getAdminUser();
        $program = $this->getOrCreateProgram();

        $bimbinganTrx = 'TRX-FILTER-BIMB-' . uniqid();
        TransaksiProgram::create([
            'nomor_transaksi'   => $bimbinganTrx,
            'user_id'           => 1,
            'program_id'        => $program->id,
            'nama_program'      => 'Program Skripsi',
            'nama_pemesan'      => 'Budi Filter',
            'email_pemesan'     => 'budi_flt@test.com',
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'bukti_pembayaran'  => 'bukti_pembayaran/bimb_filter.jpg',
            'tanggal_pembelian' => now(),
        ]);

        $olahdataTrx = 'OD-FILTER-OD-' . uniqid();
        TransaksiOlahdata::create([
            'nomor_order'        => $olahdataTrx,
            'user_id'            => 1,
            'nama_mahasiswa'     => 'Citra Filter',
            'email'              => 'citra_flt@test.com',
            'judul_penelitian'   => 'Riset Filter Olahdata',
            'software_digunakan' => 'EViews',
            'status_pengerjaan'  => 'Proses Running Data',
            'total_biaya'        => 450000,
            'admin_fee'          => 5000,
            'total_bayar'        => 455000,
            'status_pembayaran'  => 'Lunas',
            'bukti_pembayaran'  => 'bukti_pembayaran/od_filter.jpg',
            'tanggal_pembelian'  => now(),
        ]);

        // Filter invoices by 'olahdata' only
        $respOdOnly = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.index', ['jenis' => 'olahdata']));
        $respOdOnly->assertStatus(200);
        $respOdOnly->assertSee($olahdataTrx);
        $respOdOnly->assertDontSee($bimbinganTrx);

        // Filter pembayaran by 'bimbingan' only
        $respBimbOnly = $this->actingAsAdmin($admin)->get(route('bimbingan.pembayaran.index', ['jenis' => 'bimbingan']));
        $respBimbOnly->assertStatus(200);
        $respBimbOnly->assertSee($bimbinganTrx);
        $respBimbOnly->assertDontSee($olahdataTrx);
    }

    public function test_pembayaran_menu_shows_all_transactions_and_supports_has_bukti_filter()
    {
        $admin = $this->getAdminUser();
        $program = $this->getOrCreateProgram();

        // 1. Tagihan baru belum upload bukti
        $trxWithoutProof = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-NO-PROOF-' . uniqid(),
            'user_id'           => 1,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'User Belum Upload',
            'email_pemesan'     => 'tanpabukti@example.com',
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'status'            => 'Menunggu Pembayaran',
            'status_pembayaran' => 'Menunggu Pembayaran',
            'bukti_pembayaran'  => null,
            'tanggal_pembelian' => now(),
        ]);

        // 2. Tagihan yang sudah upload bukti transfer
        $trxWithProof = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-WITH-PROOF-' . uniqid(),
            'user_id'           => 1,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'User Sudah Upload',
            'email_pemesan'     => 'adabukti@example.com',
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'metode_pembayaran' => 'BCA Virtual Account',
            'status'            => 'Menunggu Verifikasi',
            'status_pembayaran' => 'Menunggu Verifikasi',
            'bukti_pembayaran'  => 'bukti_pembayaran/sample_bukti.jpg',
            'tanggal_pembelian' => now(),
        ]);

        // 3. Olah Data (belum upload bukti)
        $odWithoutProof = TransaksiOlahdata::create([
            'nomor_order'        => 'OD-PAY-ALL-' . uniqid(),
            'user_id'            => 1,
            'nama_mahasiswa'     => 'Mahasiswa Olahdata Pembayaran',
            'email'              => 'od_pay@test.com',
            'judul_penelitian'   => 'Analisis Kepuasan Layanan',
            'software_digunakan' => 'SPSS',
            'total_biaya'        => 450000,
            'admin_fee'          => 5000,
            'total_bayar'        => 455000,
            'status_pengerjaan'  => 'Menunggu Pembayaran',
            'status_pembayaran'  => 'Menunggu Pembayaran',
            'bukti_pembayaran'   => null,
            'tanggal_pembelian'  => now(),
        ]);

        // 4. Pelatihan (sudah upload bukti)
        $pltWithProof = TransaksiPelatihan::create([
            'nomor_registrasi'  => 'REG-PAY-ALL-' . uniqid(),
            'user_id'           => 1,
            'judul_kelas'       => 'Workshop SEM-PLS',
            'nama_pemesan'      => 'Peserta Workshop Pembayaran',
            'email_pemesan'     => 'plt_pay@test.com',
            'biaya'             => 250000,
            'admin_fee'         => 5000,
            'total_bayar'       => 255000,
            'status'            => 'Menunggu Verifikasi',
            'status_pembayaran' => 'Menunggu Verifikasi',
            'bukti_pembayaran'  => 'bukti_pembayaran/plt_sample.jpg',
            'tanggal_pembelian' => now(),
        ]);

        // Pembayaran menu shows all transactions across all programs
        $respPay = $this->actingAsAdmin($admin)->get(route('bimbingan.pembayaran.index'));
        $respPay->assertStatus(200);
        $respPay->assertSee($trxWithProof->nomor_transaksi);
        $respPay->assertSee($trxWithoutProof->nomor_transaksi);
        $respPay->assertSee($odWithoutProof->nomor_order);
        $respPay->assertSee($pltWithProof->nomor_registrasi);

        // Category labels appear
        $respPay->assertSee('Bimbingan');
        $respPay->assertSee('Olah Data');
        $respPay->assertSee('Kelas &amp; Workshop', false);

        // Filter has_bukti = 1
        $respWithProofOnly = $this->actingAsAdmin($admin)->get(route('bimbingan.pembayaran.index', ['has_bukti' => '1']));
        $respWithProofOnly->assertStatus(200);
        $respWithProofOnly->assertSee($trxWithProof->nomor_transaksi);
        $respWithProofOnly->assertSee($pltWithProof->nomor_registrasi);
        $respWithProofOnly->assertDontSee($trxWithoutProof->nomor_transaksi);
        $respWithProofOnly->assertDontSee($odWithoutProof->nomor_order);

        // Filter has_bukti = 0
        $respWithoutProofOnly = $this->actingAsAdmin($admin)->get(route('bimbingan.pembayaran.index', ['has_bukti' => '0']));
        $respWithoutProofOnly->assertStatus(200);
        $respWithoutProofOnly->assertSee($trxWithoutProof->nomor_transaksi);
        $respWithoutProofOnly->assertSee($odWithoutProof->nomor_order);
        $respWithoutProofOnly->assertDontSee($trxWithProof->nomor_transaksi);
        $respWithoutProofOnly->assertDontSee($pltWithProof->nomor_registrasi);
    }

    public function test_admin_can_verify_olahdata_and_pelatihan_payment()
    {
        $admin = $this->getAdminUser();

        // 1. Olah Data payment verification
        $od = TransaksiOlahdata::create([
            'nomor_order'        => 'OD-VERIF-' . uniqid(),
            'user_id'            => 1,
            'nama_mahasiswa'     => 'Verif Olah Data',
            'email'              => 'verif_od@test.com',
            'judul_penelitian'   => 'Pengaruh Inflasi Terhadap Saham',
            'software_digunakan' => 'EViews',
            'total_biaya'        => 450000,
            'admin_fee'          => 5000,
            'total_bayar'        => 455000,
            'status_pengerjaan'  => 'Menunggu Verifikasi',
            'status_pembayaran'  => 'Menunggu Verifikasi',
            'bukti_pembayaran'  => 'bukti_pembayaran/od_verif.jpg',
        ]);

        $respVerifyOd = $this->actingAsAdmin($admin)->post(route('bimbingan.pembayaran.update-status', $od->id), [
            'status_pembayaran' => 'Lunas',
            'jenis'             => 'olahdata',
            'catatan'           => 'Transfer Olah Data valid terverifikasi.',
        ]);
        $respVerifyOd->assertRedirect();

        $od->refresh();
        $this->assertEquals('Lunas', $od->status_pembayaran);
        $this->assertEquals('Proses Running Data', $od->status_pengerjaan);
        $this->assertEquals('Transfer Olah Data valid terverifikasi.', $od->catatan_pembayaran);

        // 2. Pelatihan payment verification
        $pelatihan = TransaksiPelatihan::create([
            'nomor_registrasi'  => 'REG-VERIF-' . uniqid(),
            'user_id'           => 1,
            'judul_kelas'       => 'Workshop Regresi Data Panel',
            'instruktur'        => 'Deny Aditya Pratama, S.Stat.',
            'status'            => 'Menunggu Verifikasi',
            'nama_pemesan'      => 'Verif Pelatihan',
            'email_pemesan'     => 'verif_plt@test.com',
            'biaya'             => 350000,
            'admin_fee'         => 5000,
            'total_bayar'       => 355000,
            'status_pembayaran' => 'Menunggu Verifikasi',
            'bukti_pembayaran'  => 'bukti_pembayaran/plt_verif.jpg',
        ]);

        $respVerifyPlt = $this->actingAsAdmin($admin)->post(route('bimbingan.pembayaran.update-status', $pelatihan->id), [
            'status_pembayaran' => 'Lunas',
            'jenis'             => 'pelatihan',
            'catatan'           => 'Peserta kelas workshop terverifikasi aktif.',
        ]);
        $respVerifyPlt->assertRedirect();

        $pelatihan->refresh();
        $this->assertEquals('Lunas', $pelatihan->status_pembayaran);
        $this->assertEquals('Aktif Berjalan', $pelatihan->status);
        $this->assertEquals('Peserta kelas workshop terverifikasi aktif.', $pelatihan->catatan);
    }

    public function test_admin_can_view_invoice_detail_and_print()
    {
        $admin = $this->getAdminUser();
        $program = $this->getOrCreateProgram();

        $trx = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-INV-DETAIL-002',
            'user_id'           => 1,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Siti Rahma',
            'email_pemesan'     => 'siti@example.com',
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        // View detail
        $showResponse = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.show', $trx->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('TRX-INV-DETAIL-002');
        $showResponse->assertSee('Siti Rahma');

        // View print layout
        $printResponse = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.print', $trx->id));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('OFFICIAL INVOICE');
        $printResponse->assertSee('TRX-INV-DETAIL-002');
    }

    public function test_admin_can_view_list_pembayaran_and_update_status()
    {
        $admin = $this->getAdminUser();
        $program = $this->getOrCreateProgram();

        $trx = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-PAY-TEST-003',
            'user_id'           => 1,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Ahmad Fauzi',
            'email_pemesan'     => 'ahmad@example.com',
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'metode_pembayaran' => 'BCA Virtual Account',
            'kode_pembayaran'   => '8077712345678',
            'bukti_pembayaran'  => 'bukti_pembayaran/bukti_ahmad.jpg',
            'status'            => 'Menunggu Verifikasi',
            'status_pembayaran' => 'Menunggu Verifikasi',
        ]);

        // Access list pembayaran
        $response = $this->actingAsAdmin($admin)->get(route('bimbingan.pembayaran.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Bukti Bayar User');
        $response->assertSee('TRX-PAY-TEST-003');
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('BCA Virtual Account');

        // Admin confirms payment as Lunas
        $updateResponse = $this->actingAsAdmin($admin)->post(route('bimbingan.pembayaran.update-status', $trx->id), [
            'status_pembayaran' => 'Lunas',
            'catatan'           => 'Pembayaran telah dikonfirmasi valid oleh Admin Keuangan.',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $trx->refresh();
        $this->assertEquals('Lunas', $trx->status_pembayaran);
        $this->assertEquals('Aktif', $trx->status);
        $this->assertEquals('Pembayaran telah dikonfirmasi valid oleh Admin Keuangan.', $trx->catatan);
    }

    public function test_role_99_data_isolation_on_invoice_and_pembayaran()
    {
        $userA = $this->createUserWithRole99('Mahasiswa A');
        $userB = $this->createUserWithRole99('Mahasiswa B');
        $program = $this->getOrCreateProgram();

        $trxA = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-ISOLATION-USER-A',
            'user_id'           => $userA->id_user,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Mahasiswa A',
            'email_pemesan'     => $userA->email,
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'bukti_pembayaran'  => 'bukti_pembayaran/bukti_user_a.jpg',
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        $trxB = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-ISOLATION-USER-B',
            'user_id'           => $userB->id_user,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Mahasiswa B',
            'email_pemesan'     => $userB->email,
            'biaya'             => 800000,
            'admin_fee'         => 5000,
            'total_bayar'       => 805000,
            'bukti_pembayaran'  => 'bukti_pembayaran/bukti_user_b.jpg',
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        // User A sees TRX A but not TRX B on Invoice list
        $respA_inv = $this->actingAsRole99($userA)->get(route('bimbingan.invoices.index'));
        $respA_inv->assertStatus(200);
        $respA_inv->assertSee('TRX-ISOLATION-USER-A');
        $respA_inv->assertDontSee('TRX-ISOLATION-USER-B');

        // User A sees TRX A but not TRX B on Pembayaran list
        $respA_pay = $this->actingAsRole99($userA)->get(route('bimbingan.pembayaran.index'));
        $respA_pay->assertStatus(200);
        $respA_pay->assertSee('TRX-ISOLATION-USER-A');
        $respA_pay->assertDontSee('TRX-ISOLATION-USER-B');

        // User B cannot access User A's invoice detail (403 Forbidden)
        $forbiddenInv = $this->actingAsRole99($userB)->get(route('bimbingan.invoices.show', $trxA->id));
        $forbiddenInv->assertStatus(403);

        // User B cannot access User A's invoice print (403 Forbidden)
        $forbiddenPrint = $this->actingAsRole99($userB)->get(route('bimbingan.invoices.print', $trxA->id));
        $forbiddenPrint->assertStatus(403);

        // Role 99 cannot update payment status (403 Forbidden)
        $forbiddenUpdate = $this->actingAsRole99($userA)->post(route('bimbingan.pembayaran.update-status', $trxA->id), [
            'status_pembayaran' => 'Lunas',
        ]);
        $forbiddenUpdate->assertStatus(403);
    }

    public function test_per_page_filter_and_selector_works_on_invoices_and_pembayaran()
    {
        $admin = $this->getAdminUser();

        // 1. Invoices per_page 25
        $respInv = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.index', ['per_page' => 25]));
        $respInv->assertStatus(200);
        $this->assertEquals(25, $respInv->viewData('invoices')->perPage());
        $respInv->assertSee('Baris per halaman:');
        $respInv->assertSee('changePerPage');

        // 2. Pembayaran per_page 50
        $respPay = $this->actingAsAdmin($admin)->get(route('bimbingan.pembayaran.index', ['per_page' => 50]));
        $respPay->assertStatus(200);
        $this->assertEquals(50, $respPay->viewData('pembayarans')->perPage());
        $respPay->assertSee('Baris per halaman:');
        $respPay->assertSee('changePerPage');

        // 3. Fallback on invalid per_page defaults to 10
        $respFallback = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.index', ['per_page' => 999]));
        $respFallback->assertStatus(200);
        $this->assertEquals(10, $respFallback->viewData('invoices')->perPage());
    }
}

