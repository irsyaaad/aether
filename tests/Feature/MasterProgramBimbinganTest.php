<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Bimbingan\Entities\TransaksiBimbingan;
use Modules\Master\Entities\MasterProgram;
use Tests\TestCase;

class MasterProgramBimbinganTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::factory()->create([
            'username' => 'testadmin',
            'email' => 'admin@test.com',
            'id_role' => 1,
        ]);
    }

    public function test_master_program_index_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get(route('master.programs.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Program');
        $response->assertSee('Tabel Kelola Program');
        $response->assertSee('Katalog Penawaran Klien');
        $response->assertSee(route('master.programs.create'));
    }

    public function test_create_program_page_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get(route('master.programs.create'));
        $response->assertStatus(200);
        $response->assertSee('Halaman Tambah Program');
        $response->assertSee('Formulir Program Bimbingan Baru');
        $response->assertSee('Foto / Banner Program Bimbingan');
        $response->assertSee('Kode Program');
    }

    public function test_master_program_datatable_returns_data()
    {
        $program = MasterProgram::firstOrCreate(
            ['kode_program' => 'PRG-TEST-01'],
            [
                'nama_program' => 'Paket Uji Skripsi Kuantitatif',
                'kategori' => 'Bimbingan Skripsi',
                'jenjang' => 'S1 (Sarjana)',
                'biaya_standar' => 500000,
                'durasi_sesi' => 90,
                'jumlah_sesi' => 4,
                'badge' => 'Populer',
                'is_active' => true,
            ]
        );

        $response = $this->actingAs($this->user)->get(route('master.programs.get-data'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_can_create_new_program_via_form_post()
    {
        $kode = 'PRG-PAGE-' . rand(1000, 9999);
        $payload = [
            'kode_program' => $kode,
            'nama_program' => 'Paket Bimbingan Skripsi Halaman Baru',
            'kategori' => 'Bimbingan Skripsi',
            'jenjang' => 'S1 (Sarjana)',
            'target_bab' => 'Bab 1 s/d Bab 5',
            'biaya_standar' => 1350000,
            'durasi_sesi' => 90,
            'jumlah_sesi' => 6,
            'badge' => 'Terbaru',
            'fasilitas' => "Konsultasi 1-on-1\nFree Cek Turnitin",
            'deskripsi' => 'Program pengujian via dedicated create page.',
            'is_populer' => 1,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->post(route('master.programs.store'), $payload);
        $response->assertRedirect(route('master.programs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('master_programs', [
            'kode_program' => $kode,
            'nama_program' => 'Paket Bimbingan Skripsi Halaman Baru',
            'biaya_standar' => 1350000,
        ]);
    }

    public function test_can_create_program_with_banner_upload()
    {
        Storage::fake('public');

        $kode = 'PRG-BANNER-' . rand(1000, 9999);
        $file = UploadedFile::fake()->image('banner_skripsi.jpg', 1200, 675);

        $payload = [
            'kode_program' => $kode,
            'nama_program' => 'Paket Bimbingan Skripsi Banner Visual',
            'banner' => $file,
            'kategori' => 'Bimbingan Skripsi',
            'jenjang' => 'S1 (Sarjana)',
            'biaya_standar' => 1400000,
            'durasi_sesi' => 90,
            'jumlah_sesi' => 8,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->post(route('master.programs.store'), $payload);
        $response->assertRedirect(route('master.programs.index'));

        $program = MasterProgram::where('kode_program', $kode)->first();
        $this->assertNotNull($program);
        $this->assertNotNull($program->banner);
        Storage::disk('public')->assertExists($program->banner);
        $this->assertNotEmpty($program->banner_url);
    }

    public function test_edit_program_page_can_be_rendered()
    {
        $program = MasterProgram::where('is_active', true)->first();

        $response = $this->actingAs($this->user)->get(route('master.programs.edit', $program->id));
        $response->assertStatus(200);
        $response->assertSee('Halaman Edit Program');
        $response->assertSee($program->kode_program);
    }

    public function test_can_update_program_via_form_put()
    {
        $program = MasterProgram::where('is_active', true)->first();
        $payload = [
            'kode_program' => $program->kode_program,
            'nama_program' => 'Nama Paket Telah Diperbarui',
            'kategori' => 'Bimbingan Skripsi',
            'jenjang' => 'S1 (Sarjana)',
            'biaya_standar' => 990000,
            'durasi_sesi' => 90,
            'jumlah_sesi' => 5,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->put(route('master.programs.update', $program->id), $payload);
        $response->assertRedirect(route('master.programs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('master_programs', [
            'id' => $program->id,
            'nama_program' => 'Nama Paket Telah Diperbarui',
            'biaya_standar' => 990000,
        ]);
    }

    public function test_user_can_view_program_catalog_offerings()
    {
        $response = $this->actingAs($this->user)->get(route('bimbingan.programs'));
        $response->assertStatus(200);
        $response->assertSee('Pilih Program Bimbingan Sesuai Kebutuhan Anda');
        $response->assertSee('Beli / Ikuti Pelatihan');
    }

    public function test_user_can_access_booking_form_with_preselected_program()
    {
        $program = MasterProgram::where('is_active', true)->first();

        $response = $this->actingAs($this->user)->get(route('bimbingan.create', ['program_id' => $program->id]));
        $response->assertStatus(200);
        $response->assertSee($program->nama_program);
        $response->assertSee('Form Pengajuan Sesi Bimbingan');
    }

    public function test_user_can_submit_bimbingan_application_with_selected_program()
    {
        $program = MasterProgram::where('is_active', true)->first();

        $payload = [
            'program_id' => $program->id,
            'judul_penelitian' => 'Analisis Pengaruh Kepemimpinan dan Budaya Organisasi Terhadap Kinerja Guru',
            'topik_bab' => 'Bab 4 - Hasil Olah Data & Pembahasan',
            'mentor' => 'Nur Atika Rochmah, M.Si.',
            'tanggal_preferensi' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'media' => 'Google Meet',
            'catatan' => 'Mohon bantuan analisis SEM-PLS.',
        ];

        $response = $this->actingAs($this->user)->post(route('bimbingan.store'), $payload);
        $response->assertRedirect(route('bimbingan.index'));

        $this->assertDatabaseHas('transaksi_bimbingans', [
            'judul_skripsi' => 'Analisis Pengaruh Kepemimpinan dan Budaya Organisasi Terhadap Kinerja Guru',
            'program_id' => $program->id,
            'paket_bimbingan' => $program->nama_program,
        ]);
    }

    public function test_program_handles_ckeditor_html_fasilitas_and_deskripsi()
    {
        $kode = 'PRG-CKED-' . rand(1000, 9999);
        $payload = [
            'kode_program' => $kode,
            'nama_program' => 'Paket Bimbingan Premium Rich Text CKEditor',
            'kategori' => 'Bimbingan Skripsi',
            'jenjang' => 'S1 (Sarjana)',
            'biaya_standar' => 2000000,
            'durasi_sesi' => 90,
            'jumlah_sesi' => 8,
            'fasilitas' => '<ul><li>8x Sesi Pendampingan 1-on-1</li><li>Review Metodologi &amp; Kuesioner</li><li>Simulasi Ujian Sidang</li></ul>',
            'deskripsi' => '<p>Program bimbingan intensif <strong>berkualitas tinggi</strong> dengan jaminan bimbingan sampai lulus.</p>',
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->post(route('master.programs.store'), $payload);
        $response->assertRedirect(route('master.programs.index'));

        $program = MasterProgram::where('kode_program', $kode)->firstOrFail();
        $this->assertCount(3, $program->fasilitas_array);
        $this->assertEquals('8x Sesi Pendampingan 1-on-1', $program->fasilitas_array[0]);
        $this->assertEquals('Review Metodologi & Kuesioner', $program->fasilitas_array[1]);
        $this->assertEquals('Simulasi Ujian Sidang', $program->fasilitas_array[2]);
        $this->assertStringContainsString('<ul><li>8x Sesi Pendampingan 1-on-1</li>', $program->fasilitas_html);

        // Test edit page contains CKEditor assets and existing values
        $editResponse = $this->actingAs($this->user)->get(route('master.programs.edit', $program->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('classic/ckeditor.js');
        $editResponse->assertSee('ckeditor');
    }

    public function test_user_can_access_payment_checkout_page()
    {
        $program = MasterProgram::where('is_active', true)->firstOrFail();

        $response = $this->actingAs($this->user)->get(route('bimbingan.payment', $program->id));
        $response->assertStatus(200);
        $response->assertSee('Kembali ke Katalog Program');
        $response->assertSee($program->nama_program);
        $response->assertSee('Lengkapi Data Diri Pemesan');
        $response->assertSee('Pilih Metode Pembayaran');
        $response->assertSee('Detail Pembayaran');
        $response->assertSee('Lanjut Pembayaran');
    }

    public function test_user_can_process_payment_and_view_instruction_for_va()
    {
        $program = MasterProgram::where('is_active', true)->firstOrFail();

        $payload = [
            'nama_pemesan'      => 'Ahmad Fulan',
            'email_pemesan'     => 'ahmad.fulan@example.com',
            'telepon_pemesan'   => '081298765432',
            'universitas'       => 'Universitas Indonesia',
            'judul_skripsi'     => 'Analisis Implementasi Laravel pada Aplikasi Enterprise',
            'metode_pembayaran' => 'BCA Virtual Account',
        ];

        $response = $this->actingAs($this->user)->post(route('bimbingan.payment.process', $program->id), $payload);
        $response->assertRedirect();

        $transaksi = \Modules\Bimbingan\Entities\TransaksiProgram::where('email_pemesan', 'ahmad.fulan@example.com')->latest()->first();
        $this->assertNotNull($transaksi);
        $this->assertEquals('BCA Virtual Account', $transaksi->metode_pembayaran);
        $this->assertEquals('Menunggu Pembayaran', $transaksi->status);
        $this->assertEquals('Menunggu Pembayaran', $transaksi->status_pembayaran);

        $response->assertRedirect(route('bimbingan.payment.instruction', $transaksi->id));

        // Test instruction view for VA
        $instResponse = $this->actingAs($this->user)->get(route('bimbingan.payment.instruction', $transaksi->id));
        $instResponse->assertStatus(200);
        $instResponse->assertSee('BCA Virtual Account');
        $instResponse->assertSee('Nomor Virtual Account');
        $instResponse->assertSee('Salin Nomor VA');
        $instResponse->assertSee('Upload Bukti Pembayaran');
    }

    public function test_user_can_process_payment_and_view_instruction_for_qris()
    {
        $program = MasterProgram::where('is_active', true)->firstOrFail();

        $payload = [
            'nama_pemesan'      => 'Siti Aisyah',
            'email_pemesan'     => 'siti.aisyah@example.com',
            'telepon_pemesan'   => '081299887766',
            'universitas'       => 'Universitas Gadjah Mada',
            'judul_skripsi'     => 'Studi SEM-PLS Persepsi Mahasiswa Terhadap Pembayaran Digital',
            'metode_pembayaran' => 'QRIS Instant',
        ];

        $response = $this->actingAs($this->user)->post(route('bimbingan.payment.process', $program->id), $payload);
        $transaksi = \Modules\Bimbingan\Entities\TransaksiProgram::where('email_pemesan', 'siti.aisyah@example.com')->latest()->first();
        $response->assertRedirect(route('bimbingan.payment.instruction', $transaksi->id));

        // Test instruction view for QRIS
        $instResponse = $this->actingAs($this->user)->get(route('bimbingan.payment.instruction', $transaksi->id));
        $instResponse->assertStatus(200);
        $instResponse->assertSee('QRIS Pembayaran Digital');
        $instResponse->assertSee('Scan QRIS');
        $instResponse->assertSee('AETHER ACADEMY INDONESIA');
        $instResponse->assertSee('Upload Bukti Pembayaran');
    }

    public function test_user_can_upload_payment_proof_and_activate_program()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $program = MasterProgram::where('is_active', true)->firstOrFail();
        $transaksi = \Modules\Bimbingan\Entities\TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-TEST-' . rand(1000, 9999),
            'user_id'           => $this->user->id_user ?? $this->user->id,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Budi Test',
            'email_pemesan'     => 'budi@test.com',
            'telepon_pemesan'   => '081234567890',
            'biaya'             => $program->biaya_standar,
            'metode_pembayaran' => 'BCA Virtual Account',
            'kode_pembayaran'   => '80777123456789',
            'status'            => 'Menunggu Pembayaran',
            'status_pembayaran' => 'Menunggu Pembayaran',
            'total_sesi'        => 4,
            'sisa_sesi'         => 4,
            'sesi_digunakan'    => 0,
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_transfer.jpg', 800, 1200);

        $uploadResponse = $this->actingAs($this->user)->post(route('bimbingan.payment.upload-bukti', $transaksi->id), [
            'bukti_pembayaran' => $file,
            'catatan'          => 'Transfer via BCA atas nama Budi',
        ]);

        $uploadResponse->assertRedirect(route('bimbingan.payment.invoice', $transaksi->id));

        $transaksi->refresh();
        $this->assertEquals('Menunggu Verifikasi', $transaksi->status_pembayaran);
        $this->assertEquals('Menunggu Verifikasi', $transaksi->status);
        $this->assertNotNull($transaksi->bukti_pembayaran);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($transaksi->bukti_pembayaran);

        // Test invoice view displays pending verification
        $invoiceResponse = $this->actingAs($this->user)->get(route('bimbingan.payment.invoice', $transaksi->id));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee('Bukti Pembayaran Sedang Diverifikasi Admin');
        $invoiceResponse->assertSee('Lihat Bukti Pembayaran Terlampir');

        // Admin verifies the payment
        $verifyResp = $this->actingAs($this->user)->post(route('master.programs.transaksi.verifikasi', $transaksi->id));
        $verifyResp->assertStatus(200);
        $verifyResp->assertJson(['status' => 'success']);

        $transaksi->refresh();
        $this->assertEquals('Lunas', $transaksi->status_pembayaran);
        $this->assertEquals('Aktif', $transaksi->status);
    }

    public function test_program_catalog_links_to_payment_page()
    {
        $response = $this->actingAs($this->user)->get(route('bimbingan.programs'));
        $response->assertStatus(200);
        $response->assertSee('Beli / Ikuti Pelatihan');
        $response->assertSee('/bimbingan/programs/');
    }

    public function test_program_detail_page_can_be_rendered_with_buyers_list_and_payment_info()
    {
        $program = MasterProgram::firstOrCreate(
            ['kode_program' => 'PRG-DETAIL-TEST'],
            [
                'nama_program' => 'Program Bimbingan Skripsi Fast Track',
                'kategori' => 'Bimbingan Skripsi',
                'jenjang' => 'S1 (Sarjana)',
                'biaya_standar' => 750000,
                'durasi_sesi' => 90,
                'jumlah_sesi' => 6,
                'badge' => 'Terlaris',
                'is_active' => true,
            ]
        );

        $transaksi = \Modules\Bimbingan\Entities\TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-DET-' . rand(1000, 9999),
            'user_id'           => $this->user->id_user ?? $this->user->id,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Ahmad Zaki',
            'email_pemesan'     => 'zaki@kampus.ac.id',
            'telepon_pemesan'   => '081298765432',
            'universitas'       => 'Universitas Indonesia',
            'judul_skripsi'     => 'Implementasi Deep Learning untuk Deteksi Polusi',
            'biaya'             => 750000,
            'metode_pembayaran' => 'BCA Virtual Account',
            'kode_pembayaran'   => '80777999888111',
            'total_bayar'       => 752500,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'total_sesi'        => 6,
            'sisa_sesi'         => 4,
            'sesi_digunakan'    => 2,
        ]);

        $response = $this->actingAs($this->user)->get(route('master.programs.show', $program->id));
        $response->assertStatus(200);
        $response->assertSee('Program Bimbingan Skripsi Fast Track');
        $response->assertSee('Daftar Mahasiswa Pembeli Program');
        $response->assertSee('Ahmad Zaki');
        $response->assertSee('zaki@kampus.ac.id');
        $response->assertSee('Universitas Indonesia');
        $response->assertSee('BCA Virtual Account');
        $response->assertSee('80777999888111');
        $response->assertSee('Batalkan Bayar');
        $response->assertSee('Nonaktifkan User');
    }

    public function test_admin_can_toggle_program_active_status()
    {
        $program = MasterProgram::firstOrCreate(
            ['kode_program' => 'PRG-TOGGLE-01'],
            [
                'nama_program' => 'Paket Program Siap Toggle',
                'kategori' => 'Bimbingan Skripsi',
                'biaya_standar' => 300000,
                'is_active' => true,
            ]
        );

        $this->assertTrue((bool)$program->is_active);

        // Deactivate program
        $response = $this->actingAs($this->user)->post(route('master.programs.toggle-status', $program->id));
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'is_active' => false]);

        $program->refresh();
        $this->assertFalse((bool)$program->is_active);

        // Reactivate program
        $response2 = $this->actingAs($this->user)->post(route('master.programs.toggle-status', $program->id));
        $response2->assertStatus(200);
        $response2->assertJson(['status' => 'success', 'is_active' => true]);

        $program->refresh();
        $this->assertTrue((bool)$program->is_active);
    }

    public function test_admin_can_cancel_buyer_payment_and_deactivate_user()
    {
        $program = MasterProgram::where('is_active', true)->firstOrFail();
        $transaksi = \Modules\Bimbingan\Entities\TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-CANCEL-' . rand(1000, 9999),
            'user_id'           => $this->user->id_user ?? $this->user->id,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Pembeli Batal',
            'email_pemesan'     => 'batal@test.com',
            'biaya'             => 500000,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'total_sesi'        => 4,
            'sisa_sesi'         => 4,
            'sesi_digunakan'    => 0,
        ]);

        // Cancel payment
        $response = $this->actingAs($this->user)->post(route('master.programs.transaksi.batalkan', $transaksi->id));
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $transaksi->refresh();
        $this->assertEquals('Dibatalkan', $transaksi->status_pembayaran);
        $this->assertEquals('Dibatalkan', $transaksi->status);

        // Toggle user access
        $toggleResp = $this->actingAs($this->user)->post(route('master.programs.transaksi.toggle-user', $transaksi->id));
        $toggleResp->assertStatus(200);
        $transaksi->refresh();
        $this->assertEquals('Aktif', $transaksi->status);
    }

    public function test_user_can_change_payment_method_and_generate_new_transaction()
    {
        $program = MasterProgram::where('is_active', true)->firstOrFail();
        $oldTransaksi = \Modules\Bimbingan\Entities\TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-OLD-' . rand(1000, 9999),
            'user_id'           => $this->user->id_user ?? $this->user->id,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Aisyah Putri',
            'email_pemesan'     => 'aisyah@test.com',
            'telepon_pemesan'   => '081234567890',
            'biaya'             => $program->biaya_standar,
            'metode_pembayaran' => 'BCA Virtual Account',
            'kode_pembayaran'   => '80777123456789',
            'status'            => 'Menunggu Pembayaran',
            'status_pembayaran' => 'Menunggu Pembayaran',
            'total_sesi'        => 4,
            'sisa_sesi'         => 4,
            'sesi_digunakan'    => 0,
        ]);

        // Submit change payment method to Transfer Bank BCA (rekening)
        $response = $this->actingAs($this->user)->post(route('bimbingan.payment.ganti-metode', $oldTransaksi->id), [
            'metode_pembayaran' => 'Transfer Bank BCA',
        ]);

        $oldTransaksi->refresh();
        $this->assertEquals('Dibatalkan', $oldTransaksi->status_pembayaran);
        $this->assertEquals('Dibatalkan', $oldTransaksi->status);

        $newTransaksi = \Modules\Bimbingan\Entities\TransaksiProgram::where('email_pemesan', 'aisyah@test.com')
            ->where('id', '!=', $oldTransaksi->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($newTransaksi);
        $this->assertEquals('Transfer Bank BCA', $newTransaksi->metode_pembayaran);
        $this->assertEquals('7310889201', $newTransaksi->kode_pembayaran);
        $this->assertEquals('Menunggu Pembayaran', $newTransaksi->status_pembayaran);

        $response->assertRedirect(route('bimbingan.payment.instruction', $newTransaksi->id));

        // Check instruction view displays bank transfer instructions
        $instResp = $this->actingAs($this->user)->get(route('bimbingan.payment.instruction', $newTransaksi->id));
        $instResp->assertStatus(200);
        $instResp->assertSee('Transfer Bank BCA');
        $instResp->assertSee('Nomor Rekening Tujuan');
        $instResp->assertSee('7310889201');
        $instResp->assertSee('Ganti Metode Bayar');
    }

    public function test_admin_can_mark_program_as_completed_and_prevent_further_applications()
    {
        $program = MasterProgram::where('is_active', true)->firstOrFail();
        $transaksi = \Modules\Bimbingan\Entities\TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-DONE-' . rand(1000, 9999),
            'user_id'           => $this->user->id_user ?? $this->user->id,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => 'Mahasiswa Lulus',
            'email_pemesan'     => 'lulus@test.com',
            'biaya'             => 500000,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'total_sesi'        => 4,
            'sisa_sesi'         => 0,
            'sesi_digunakan'    => 4,
        ]);

        // Admin marks program as completed
        $response = $this->actingAs($this->user)->post(route('master.programs.transaksi.selesaikan', $transaksi->id));
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $transaksi->refresh();
        $this->assertEquals('Selesai', $transaksi->status);

        // Check if role 99 user with Selesai status is blocked from creating new bimbingan session
        $role99User = $this->user;
        $createResp = $this->actingAs($role99User)
            ->withSession(['role' => ['id_role' => 99]])
            ->get(route('bimbingan.create'));

        // Since no active (only Selesai) program exists for this user, it redirects to programs catalog
        $createResp->assertRedirect(route('bimbingan.programs'));
    }
}



