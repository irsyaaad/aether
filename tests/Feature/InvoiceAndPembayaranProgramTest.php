<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Bimbingan\Entities\TransaksiProgram;
use Modules\Master\Entities\MasterProgram;
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

    public function test_pembayaran_menu_only_shows_transactions_with_uploaded_proof()
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
        ]);

        // Invoice menu shows both tagihan
        $respInv = $this->actingAsAdmin($admin)->get(route('bimbingan.invoices.index'));
        $respInv->assertStatus(200);
        $respInv->assertSee($trxWithoutProof->nomor_transaksi);
        $respInv->assertSee($trxWithProof->nomor_transaksi);

        // Pembayaran menu ONLY shows transactions with uploaded proof!
        $respPay = $this->actingAsAdmin($admin)->get(route('bimbingan.pembayaran.index'));
        $respPay->assertStatus(200);
        $respPay->assertSee($trxWithProof->nomor_transaksi);
        $respPay->assertDontSee($trxWithoutProof->nomor_transaksi);
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
}
