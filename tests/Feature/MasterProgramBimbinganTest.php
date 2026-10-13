<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    public function test_can_create_new_program_via_ajax()
    {
        $kode = 'PRG-NEW-' . rand(1000, 9999);
        $payload = [
            'kode_program' => $kode,
            'nama_program' => 'Paket Bimbingan Skripsi Bab 1 Sampai 5 Full',
            'kategori' => 'Bimbingan Skripsi',
            'jenjang' => 'S1 (Sarjana)',
            'target_bab' => 'Bab 1-5',
            'biaya_standar' => 1200000,
            'durasi_sesi' => 90,
            'jumlah_sesi' => 6,
            'badge' => 'Terlaris',
            'fasilitas' => "Konsultasi 1-on-1\nFree Turnitin\nSimulasi Sidang",
            'deskripsi' => 'Deskripsi program bimbingan uji coba.',
            'is_populer' => 1,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->post(route('master.programs.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('master_programs', [
            'kode_program' => $kode,
            'nama_program' => 'Paket Bimbingan Skripsi Bab 1 Sampai 5 Full',
            'jumlah_sesi' => 6,
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
}
