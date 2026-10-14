<?php

namespace Tests\Feature;

use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Modules\Bimbingan\Entities\TransaksiBimbingan;
use Modules\Olahdata\Entities\TransaksiOlahdata;
use Modules\Pelatihan\Entities\TransaksiPelatihan;
use Tests\TestCase;

class Role99DataIsolationTest extends TestCase
{
    use DatabaseTransactions;

    protected function createUserWithRole99(string $name): User
    {
        $user = User::create([
            'nm_user'       => $name,
            'username'      => 'user_' . uniqid() . '@example.com',
            'email'         => 'user_' . uniqid() . '@example.com',
            'password'      => Hash::make('secret123'),
            'is_aktif'      => 1,
        ]);

        \Illuminate\Support\Facades\DB::table('role_user')->insert([
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

    public function test_bimbingan_is_isolated_between_role_99_users()
    {
        $userA = $this->createUserWithRole99('Mahasiswa A');
        $userB = $this->createUserWithRole99('Mahasiswa B');

        $bimbinganA = TransaksiBimbingan::create([
            'user_id'              => $userA->id_user,
            'nama_mahasiswa'       => 'Mahasiswa A',
            'nim'                  => '12345678',
            'universitas'          => 'Universitas Indonesia',
            'jurusan'              => 'Teknik Informatika',
            'judul_skripsi'        => 'Analisis Machine Learning Skripsi User A',
            'topik_bidang'         => 'AI',
            'jenis_layanan'        => 'Fast Track',
            'status'               => 'Pending',
            'estimasi_biaya'       => 1500000,
            'progress_keseluruhan' => 10,
        ]);

        // User A can see their own bimbingan
        $responseA = $this->actingAsRole99($userA)->get('/bimbingan');
        $responseA->assertStatus(200);
        $responseA->assertSee('Analisis Machine Learning Skripsi User A');

        // User B cannot see User A's bimbingan
        $responseB = $this->actingAsRole99($userB)->get('/bimbingan');
        $responseB->assertStatus(200);
        $responseB->assertDontSee('Analisis Machine Learning Skripsi User A');

        // User B cannot access detail of User A's bimbingan (403 Forbidden)
        $detailB = $this->actingAsRole99($userB)->get('/bimbingan/' . $bimbinganA->id);
        $detailB->assertStatus(403);
    }

    public function test_olahdata_is_isolated_between_role_99_users()
    {
        $userA = $this->createUserWithRole99('Mahasiswa A');
        $userB = $this->createUserWithRole99('Mahasiswa B');

        $olahdataA = TransaksiOlahdata::create([
            'user_id'          => $userA->id_user,
            'nama_mahasiswa'   => 'Mahasiswa A',
            'email'            => $userA->email,
            'no_wa'            => '081234567890',
            'universitas'      => 'Universitas Gadjah Mada',
            'program_studi'    => 'Statistika',
            'jenjang'          => 'S1',
            'judul_penelitian' => 'Regresi Berganda Olah Data User A',
            'software'         => 'SPSS',
            'metode_analisis'  => 'Uji Regresi Linier',
            'deadline'         => now()->addDays(7),
            'status'           => 'Menunggu Review',
        ]);

        // User A can see their own olah data
        $responseA = $this->actingAsRole99($userA)->get('/olahdata');
        $responseA->assertStatus(200);
        $responseA->assertSee('Regresi Berganda Olah Data User A');

        // User B cannot see User A's olah data
        $responseB = $this->actingAsRole99($userB)->get('/olahdata');
        $responseB->assertStatus(200);
        $responseB->assertDontSee('Regresi Berganda Olah Data User A');

        // User B cannot access detail of User A's olah data (403 Forbidden)
        $detailB = $this->actingAsRole99($userB)->get('/olahdata/' . $olahdataA->id);
        $detailB->assertStatus(403);
    }

    public function test_pelatihan_kelas_saya_is_isolated_between_role_99_users()
    {
        $userA = $this->createUserWithRole99('Mahasiswa A');
        $userB = $this->createUserWithRole99('Mahasiswa B');

        $pelatihanA = TransaksiPelatihan::create([
            'user_id'       => $userA->id_user,
            'kelas_id'      => 1,
            'kode_kelas'    => 'KLS-PLS-TEST-A',
            'judul_kelas'   => 'Workshop PLS Eksklusif Mahasiswa A',
            'instruktur'    => 'Dr. Hendra, M.Si.',
            'status'        => 'Aktif',
            'progress'      => 50,
            'link_zoom'     => 'https://zoom.us/j/123456789',
            'jadwal_mulai'  => now()->toDateString(),
        ]);

        // User A can see their registered workshop
        $responseA = $this->actingAsRole99($userA)->get('/pelatihan/kelas-saya');
        $responseA->assertStatus(200);
        $responseA->assertSee('Workshop PLS Eksklusif Mahasiswa A');

        // User B cannot see User A's registered workshop
        $responseB = $this->actingAsRole99($userB)->get('/pelatihan/kelas-saya');
        $responseB->assertStatus(200);
        $responseB->assertDontSee('Workshop PLS Eksklusif Mahasiswa A');
        $responseB->assertSee('Belum Ada Kelas atau Workshop yang Diikuti');
    }
}
