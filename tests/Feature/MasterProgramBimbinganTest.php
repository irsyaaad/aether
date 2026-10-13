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
        $response->assertSee('Pilih Program Ini');
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
}
