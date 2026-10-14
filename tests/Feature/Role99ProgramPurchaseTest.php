<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Bimbingan\Entities\TransaksiBimbingan;
use Modules\Bimbingan\Entities\TransaksiProgram;
use Modules\Master\Entities\MasterMentor;
use Modules\Master\Entities\MasterProgram;
use Tests\TestCase;

class Role99ProgramPurchaseTest extends TestCase
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

    protected function getOrCreateProgram(): MasterProgram
    {
        return MasterProgram::firstOrCreate(
            ['kode_program' => 'PRG-TEST-ROLE99'],
            [
                'nama_program'  => 'Paket Pendampingan Skripsi Eksklusif',
                'kategori'      => 'Bimbingan Skripsi',
                'jenjang'       => 'S1 (Sarjana)',
                'biaya_standar' => 750000,
                'durasi_sesi'   => 90,
                'jumlah_sesi'   => 4,
                'is_active'     => true,
            ]
        );
    }

    protected function getOrCreateMentor(): MasterMentor
    {
        return MasterMentor::firstOrCreate(
            ['nama_lengkap' => 'Dr. Test Mentor, M.Si.'],
            [
                'spesialisasi' => 'Statistika & Metodologi',
                'is_active'    => true,
            ]
        );
    }

    public function test_role_99_user_without_purchased_program_cannot_access_bimbingan_create()
    {
        $user = $this->createUserWithRole99('Mahasiswa Tanpa Program');

        $response = $this->actingAsRole99($user)->get(route('bimbingan.create'));

        $response->assertRedirect(route('bimbingan.programs'));
        $response->assertSessionHas('warning');
    }

    public function test_role_99_user_without_purchased_program_cannot_submit_session()
    {
        $user = $this->createUserWithRole99('Mahasiswa Tanpa Program');
        $program = $this->getOrCreateProgram();
        $mentor = $this->getOrCreateMentor();

        $payload = [
            'program_id'         => $program->id,
            'judul_penelitian'   => 'Pengaruh Brand Image Terhadap Loyalitas Konsumen',
            'topik_bab'          => 'Bab 4 - Hasil Olah Data & Pembahasan',
            'mentor'             => $mentor->nama_lengkap,
            'tanggal_preferensi' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'media'              => 'Google Meet',
            'catatan'            => 'Konsultasi pertama',
        ];

        $response = $this->actingAsRole99($user)->post(route('bimbingan.store'), $payload);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('transaksi_bimbingans', [
            'user_id'       => $user->id_user,
            'judul_skripsi' => 'Pengaruh Brand Image Terhadap Loyalitas Konsumen',
        ]);
    }

    public function test_role_99_dashboard_shows_empty_state_when_no_program_purchased()
    {
        $user = $this->createUserWithRole99('Mahasiswa Belum Beli');

        $response = $this->actingAsRole99($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Anda Belum Memiliki Program Bimbingan Aktif');
        $response->assertSee(route('bimbingan.programs'));
    }

    public function test_role_99_can_purchase_program_and_see_it_on_dashboard()
    {
        $user = $this->createUserWithRole99('Mahasiswa Pembeli');
        $program = $this->getOrCreateProgram();

        // Purchase program via POST route
        $buyResponse = $this->actingAsRole99($user)->post(route('bimbingan.programs.beli', $program->id));
        $buyResponse->assertRedirect(route('dashboard'));
        $buyResponse->assertSessionHas('success');

        $this->assertDatabaseHas('transaksi_programs', [
            'user_id'    => $user->id_user,
            'program_id' => $program->id,
            'status'     => 'Aktif',
        ]);

        // Dashboard now displays the active purchased program
        $dashboardResponse = $this->actingAsRole99($user)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee($program->nama_program);
        $dashboardResponse->assertSee('Program Bimbingan Aktif Saya');
        $dashboardResponse->assertDontSee('Anda Belum Memiliki Program Bimbingan Aktif');
    }

    public function test_role_99_with_purchased_program_can_access_create_and_submit_session()
    {
        $user = $this->createUserWithRole99('Mahasiswa Aktif');
        $program = $this->getOrCreateProgram();
        $mentor = $this->getOrCreateMentor();

        // Create purchased program record
        $purchased = TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-PRG-' . uniqid(),
            'user_id'           => $user->id_user,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'biaya'             => $program->biaya_standar,
            'total_sesi'        => 4,
            'sisa_sesi'         => 4,
            'sesi_digunakan'    => 0,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        // Can access create page
        $createResponse = $this->actingAsRole99($user)->get(route('bimbingan.create', ['program_id' => $program->id]));
        $createResponse->assertStatus(200);
        $createResponse->assertSee($program->nama_program);

        // Can submit session
        $payload = [
            'program_id'         => $program->id,
            'judul_penelitian'   => 'Analisis Kepuasan Pengguna Aplikasi Aether',
            'topik_bab'          => 'Bab 4 - Hasil Olah Data & Pembahasan',
            'mentor'             => $mentor->nama_lengkap,
            'tanggal_preferensi' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'media'              => 'Google Meet',
            'catatan'            => 'Uji hipotesis SEM-PLS',
        ];

        $storeResponse = $this->actingAsRole99($user)->post(route('bimbingan.store'), $payload);
        $storeResponse->assertRedirect(route('bimbingan.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('transaksi_bimbingans', [
            'user_id'       => $user->id_user,
            'judul_skripsi' => 'Analisis Kepuasan Pengguna Aplikasi Aether',
            'program_id'    => $program->id,
        ]);

        // Session count updated
        $purchased->refresh();
        $this->assertEquals(1, $purchased->sesi_digunakan);
        $this->assertEquals(3, $purchased->sisa_sesi);
    }

    public function test_admin_role_can_still_submit_session_without_purchased_program()
    {
        $admin = User::first() ?? User::factory()->create([
            'username' => 'superadmin',
            'email'    => 'admin@aether.test',
            'password' => Hash::make('secret123'),
        ]);

        $program = $this->getOrCreateProgram();
        $mentor = $this->getOrCreateMentor();

        $actingAdmin = $this->actingAs($admin)->withSession([
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

        // Admin can access create without purchased program
        $createResponse = $actingAdmin->get(route('bimbingan.create'));
        $createResponse->assertStatus(200);

        // Admin can submit session without purchased program
        $payload = [
            'program_id'         => $program->id,
            'judul_penelitian'   => 'Studi Kelayakan Bisnis Logistik',
            'topik_bab'          => 'Bab 1 - Latar Belakang & Rumusan Masalah',
            'mentor'             => $mentor->nama_lengkap,
            'tanggal_preferensi' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'media'              => 'Google Meet',
        ];

        $storeResponse = $actingAdmin->post(route('bimbingan.store'), $payload);
        $storeResponse->assertRedirect(route('bimbingan.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('transaksi_bimbingans', [
            'judul_skripsi' => 'Studi Kelayakan Bisnis Logistik',
            'program_id'    => $program->id,
        ]);
    }
}
