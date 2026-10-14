<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Master\Entities\MasterKelas;
use Modules\Pelatihan\Entities\TransaksiPelatihan;
use Tests\TestCase;

class MasterKelasWorkshopTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::factory()->create([
            'username' => 'testadmin_kelas',
            'email' => 'admin_kelas@test.com',
            'id_role' => 1,
        ]);
    }

    public function test_master_kelas_index_can_be_rendered_with_responsive_modal()
    {
        $response = $this->actingAs($this->user)->get(route('master.kelas.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Kelas & Workshop');
        $response->assertSee('modal-kelas');
        $response->assertSee('max-h-[90vh]');
        $response->assertSee('Simpan Kelas');
    }

    public function test_master_kelas_datatable_returns_data_with_action_links()
    {
        $kelas = MasterKelas::firstOrCreate(
            ['kode_kelas' => 'TEST-KLS-99'],
            [
                'judul' => 'Kelas Uji Coba SEM-PLS',
                'level' => 'Pemula',
                'kategori' => 'Kuantitatif',
                'harga' => 200000,
                'kuota_maksimal' => 50,
                'status' => 'Dibuka',
            ]
        );

        $response = $this->actingAs($this->user)->get(route('master.kelas.get-data'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $json = $response->json();
        $this->assertNotEmpty($json['data']);
        
        $firstItem = collect($json['data'])->firstWhere('id', $kelas->id);
        $this->assertNotNull($firstItem);
        $this->assertStringContainsString(route('master.kelas.show', $kelas->id), $firstItem['judul_info']);
        $this->assertStringContainsString(route('master.kelas.show', $kelas->id), $firstItem['action']);
        $this->assertStringContainsString('ubahStatusKelas', $firstItem['action']);
    }

    public function test_master_kelas_detail_shows_buyers_and_stats()
    {
        $kelas = MasterKelas::firstOrCreate(
            ['kode_kelas' => 'TEST-WKS-01'],
            [
                'judul' => 'Workshop Eviews Magister',
                'level' => 'Advanced',
                'kategori' => 'Ekonometrika',
                'harga' => 300000,
                'kuota_maksimal' => 30,
                'status' => 'Dibuka',
            ]
        );

        // Buat transaksi pembeli
        $transaksi = TransaksiPelatihan::create([
            'nomor_registrasi' => 'REG-WKS-TEST-' . rand(100, 999),
            'user_id' => $this->user->id_user ?? $this->user->id,
            'kelas_id' => $kelas->id,
            'judul_kelas' => $kelas->judul,
            'kode_kelas' => $kelas->kode_kelas,
            'nama_pemesan' => 'Budi Santoso Mahasiswa',
            'email_pemesan' => 'budi.santoso@kampus.ac.id',
            'telepon_pemesan' => '08123456789',
            'universitas' => 'Universitas Indonesia',
            'biaya' => 300000,
            'admin_fee' => 5000,
            'total_bayar' => 305000,
            'metode_pembayaran' => 'BCA Virtual Account',
            'status_pembayaran' => 'Menunggu Pembayaran',
            'status' => 'Menunggu Pembayaran',
        ]);

        $response = $this->actingAs($this->user)->get(route('master.kelas.show', $kelas->id));
        $response->assertStatus(200);
        $response->assertSee('Detail Master Kelas');
        $response->assertSee($kelas->judul);
        $response->assertSee('Daftar User &amp; Mahasiswa Pembeli Kelas', false);
        $response->assertSee('Budi Santoso Mahasiswa');
        $response->assertSee($transaksi->nomor_registrasi);
        $response->assertSee('Tutup Kelas');
        $response->assertSee('Selesaikan Kelas');
    }

    public function test_admin_can_close_class()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-CLOSE-' . rand(100, 999),
            'judul' => 'Kelas Siap Ditutup',
            'level' => 'Pemula',
            'harga' => 150000,
            'kuota_maksimal' => 20,
            'status' => 'Dibuka',
        ]);

        $response = $this->actingAs($this->user)->post(route('master.kelas.update-status', $kelas->id), [
            'status' => 'Ditutup',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $kelas->id,
                'status' => 'Ditutup',
            ],
        ]);

        $this->assertEquals('Ditutup', $kelas->fresh()->status);
    }

    public function test_admin_can_complete_class()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-DONE-' . rand(100, 999),
            'judul' => 'Kelas Siap Diselesaikan',
            'level' => 'Advanced',
            'harga' => 250000,
            'kuota_maksimal' => 25,
            'status' => 'Berlangsung',
        ]);

        $response = $this->actingAs($this->user)->post(route('master.kelas.update-status', $kelas->id), [
            'status' => 'Selesai',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $kelas->id,
                'status' => 'Selesai',
            ],
        ]);

        $this->assertEquals('Selesai', $kelas->fresh()->status);
    }

    public function test_update_status_rejects_invalid_status()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-INVALID-' . rand(100, 999),
            'judul' => 'Kelas Uji Status Salah',
            'level' => 'Pemula',
            'harga' => 100000,
            'kuota_maksimal' => 10,
            'status' => 'Dibuka',
        ]);

        $response = $this->actingAs($this->user)->post(route('master.kelas.update-status', $kelas->id), [
            'status' => 'StatusNgawur',
        ]);

        $response->assertStatus(302);
        $this->assertEquals('Dibuka', $kelas->fresh()->status);
    }

    public function test_admin_can_manage_workshop_participant_transaction()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-TRX-' . rand(100, 999),
            'judul' => 'Kelas Uji Verifikasi Transaksi',
            'level' => 'Intermediate',
            'harga' => 200000,
            'kuota_maksimal' => 40,
            'status' => 'Dibuka',
        ]);

        $transaksi = TransaksiPelatihan::create([
            'nomor_registrasi' => 'REG-WKS-VFY-' . rand(100, 999),
            'user_id' => $this->user->id_user ?? $this->user->id,
            'kelas_id' => $kelas->id,
            'judul_kelas' => $kelas->judul,
            'nama_pemesan' => 'Mahasiswa Verifikasi',
            'email_pemesan' => 'vfy@test.com',
            'telepon_pemesan' => '089999999',
            'biaya' => 200000,
            'admin_fee' => 5000,
            'total_bayar' => 205000,
            'metode_pembayaran' => 'QRIS Instant',
            'status_pembayaran' => 'Menunggu Verifikasi',
            'status' => 'Menunggu Verifikasi',
        ]);

        // Verifikasi
        $resVerif = $this->actingAs($this->user)->post(route('master.kelas.transaksi.verifikasi', $transaksi->id));
        $resVerif->assertStatus(200);
        $this->assertEquals('Lunas', $transaksi->fresh()->status_pembayaran);
        $this->assertEquals('Aktif Berjalan', $transaksi->fresh()->status);

        // Selesaikan peserta
        $resSelesai = $this->actingAs($this->user)->post(route('master.kelas.transaksi.selesaikan', $transaksi->id));
        $resSelesai->assertStatus(200);
        $this->assertEquals('Selesai', $transaksi->fresh()->status);
        $this->assertEquals(100, $transaksi->fresh()->progress);
    }
}
