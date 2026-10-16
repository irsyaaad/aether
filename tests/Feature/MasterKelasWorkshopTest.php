<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Master\Entities\MasterKelas;
use Modules\Master\Entities\MasterMateri;
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

    public function test_master_kelas_index_can_be_rendered_with_link_to_create_page()
    {
        $response = $this->actingAs($this->user)->get(route('master.kelas.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Kelas &amp; Workshop', false);
        $response->assertSee(route('master.kelas.create'));
        $response->assertDontSee('id="modal-kelas"', false);
    }

    public function test_master_kelas_create_page_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get(route('master.kelas.create'));
        $response->assertStatus(200);
        $response->assertSee('Halaman Tambah Kelas');
        $response->assertSee('Foto Thumbnail Kelas');
        $response->assertSee(route('master.kelas.store'));
    }

    public function test_master_kelas_can_be_stored_with_thumbnail()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('thumbnail_workshop.jpg', 600, 400);

        $payload = [
            'kode_kelas' => 'TEST-STORE-' . rand(100, 999),
            'judul' => 'Mastering AI Tools untuk Tesis Magister',
            'level' => 'Intermediate',
            'kategori' => 'Metodologi & AI',
            'tipe_kelas' => 'Bootcamp Intensif',
            'harga' => 350000,
            'kuota_maksimal' => 50,
            'status' => 'Dibuka',
            'thumbnail' => $file,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->user)->post(route('master.kelas.store'), $payload);
        $response->assertRedirect(route('master.kelas.index'));

        $kelas = MasterKelas::where('kode_kelas', $payload['kode_kelas'])->first();
        $this->assertNotNull($kelas);
        $this->assertNotNull($kelas->thumbnail);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($kelas->thumbnail);
    }

    public function test_master_kelas_edit_page_can_be_rendered()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-EDIT-PG-' . rand(100, 999),
            'judul' => 'Kelas Uji Edit Page',
            'level' => 'Pemula',
            'harga' => 150000,
            'kuota_maksimal' => 20,
            'status' => 'Dibuka',
        ]);

        $response = $this->actingAs($this->user)->get(route('master.kelas.edit', $kelas->id));
        $response->assertStatus(200);
        $response->assertSee('Halaman Edit Kelas');
        $response->assertSee($kelas->judul);
        $response->assertSee(route('master.kelas.update', $kelas->id));
    }

    public function test_master_kelas_can_be_updated_with_new_thumbnail()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $initialFile = \Illuminate\Http\UploadedFile::fake()->image('initial.jpg', 600, 400);
        $path = $initialFile->storeAs('kelas', 'initial.jpg', 'public');

        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-UPD-IMG-' . rand(100, 999),
            'judul' => 'Kelas Awal',
            'level' => 'Pemula',
            'harga' => 150000,
            'kuota_maksimal' => 20,
            'status' => 'Dibuka',
            'thumbnail' => $path,
        ]);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($path);

        $newFile = \Illuminate\Http\UploadedFile::fake()->image('updated.png', 800, 600);
        $response = $this->actingAs($this->user)->put(route('master.kelas.update', $kelas->id), [
            'kode_kelas' => $kelas->kode_kelas,
            'judul' => 'Kelas Diperbarui Judulnya',
            'level' => 'Advanced',
            'harga' => 275000,
            'kuota_maksimal' => 30,
            'status' => 'Dibuka',
            'thumbnail' => $newFile,
        ]);

        $response->assertRedirect(route('master.kelas.index'));
        $kelas->refresh();
        $this->assertEquals('Kelas Diperbarui Judulnya', $kelas->judul);
        $this->assertNotEquals($path, $kelas->thumbnail);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($kelas->thumbnail);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($path);
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
        $this->assertStringContainsString(route('master.kelas.edit', $kelas->id), $firstItem['action']);
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
        $response->assertSee('Pengaturan Silabus &amp; Modul Pembelajaran', false);
        $response->assertSee('materi-table');
        $response->assertSee('modal-materi');
        $response->assertSee('Tambah Materi Baru');
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

    public function test_pelatihan_menu_displays_class_thumbnail_and_details()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-PELATIH-' . rand(100, 999),
            'judul' => 'Kelas Pelatihan Olah Data Tingkat Lanjut',
            'level' => 'Advanced',
            'kategori' => 'Ekonometrika',
            'harga' => 350000,
            'kuota_maksimal' => 50,
            'status' => 'Dibuka',
            'thumbnail' => 'kelas/sample_thumbnail.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('pelatihan.index'));
        $response->assertStatus(200);
        $response->assertSee('Pelatihan, Workshop &amp; Sertifikasi Data', false);
        $response->assertSee($kelas->judul);
        $response->assertSee('sample_thumbnail.jpg');
        $response->assertSee('Rp 350.000');
    }

    public function test_pelatihan_show_renders_70_30_layout_with_viewer_and_silabus()
    {
        $kelas = MasterKelas::firstOrCreate(
            ['kode_kelas' => 'KLS-PLS-01'],
            [
                'judul' => 'Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4',
                'level' => 'Intermediate',
                'kategori' => 'Kuantitatif & SEM',
                'harga' => 250000,
                'kuota_maksimal' => 100,
                'status' => 'Dibuka',
            ]
        );

        $response = $this->actingAs($this->user)->get(route('pelatihan.show', $kelas->id));
        $response->assertStatus(200);

        // Pastikan layout 70% dan 30% ada
        $response->assertSee('lg:grid-cols-10');
        $response->assertSee('lg:col-span-7'); // 70% Materi viewer
        $response->assertSee('lg:col-span-3'); // 30% Silabus list

        // Pastikan komponen materi viewer dan silabus kurikulum ada
        $response->assertSee('Silabus &amp; Daftar Materi', false);
        $response->assertSee('viewer-container');
        $response->assertSee($kelas->judul);
    }

    public function test_pelatihan_show_youtube_iframe_has_no_logo_and_sandbox_no_redirect()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-YT-' . rand(100, 999),
            'judul' => 'Kelas Uji YouTube No-Logo',
            'level' => 'Pemula',
            'harga' => 100000,
            'kuota_maksimal' => 50,
            'status' => 'Dibuka',
        ]);

        $materi = \Modules\Master\Entities\MasterMateri::create([
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'judul_materi' => 'Pengenalan Tool Riset',
            'tipe_materi' => 'Video',
            'durasi_menit' => 15,
            'link_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('pelatihan.show', $kelas->id));
        $response->assertStatus(200);

        // Pastikan custom cover dan custom play button pengganti icon YouTube ada
        $response->assertSee('id="video-custom-cover"', false);
        $response->assertSee('id="custom-cover-img"', false);
        $response->assertSee('Mulai Putar Video');

        // Pastikan embed script/template menggunakan domain privacy / no-cookie, controls=0, dan parameter modestbranding
        $response->assertSee('youtube-nocookie.com/embed/');
        $response->assertSee('controls=0');
        $response->assertSee('modestbranding=1');
        $response->assertSee('rel=0');

        // Pastikan sandbox aktif dan TIDAK mengizinkan allow-top-navigation atau allow-popups
        $response->assertSee('sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"', false);
        $response->assertDontSee('allow-top-navigation');
        $response->assertDontSee('allow-popups');

        // Pastikan overlay click-guards dan pause overlay ada
        $response->assertSee('id="video-top-guard"', false);
        $response->assertSee('id="video-bottom-guard"', false);
        $response->assertSee('id="video-pause-overlay"', false);
        $response->assertSee('Video Dijeda • Klik untuk Melanjutkan');

        // Pastikan custom player controls bar ada
        $response->assertSee('id="custom-player-controls"', false);

        // Pastikan tidak ada tombol redirect keluar ke tab youtube
        $response->assertDontSee('target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#135392] text-white text-xs font-bold transition shadow-sm">', false);

        // Pastikan tombol action set selesai ditonton ada
        $response->assertSee('id="btn-mark-selesai"', false);
        $response->assertSee('Tandai Selesai Ditonton');
    }

    public function test_pelatihan_show_renders_pdf_viewer_when_materi_is_pdf_document()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-PDF-' . rand(100, 999),
            'judul' => 'Kelas Uji Viewer PDF',
            'level' => 'Pemula',
            'harga' => 100000,
            'kuota_maksimal' => 50,
            'status' => 'Dibuka',
        ]);

        $materiPdf = \Modules\Master\Entities\MasterMateri::create([
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'judul_materi' => 'Buku Panduan Skripsi dan Analisis Data',
            'tipe_materi' => 'Dokumen PDF',
            'durasi_menit' => 20,
            'file_materi' => 'materi/files/panduan_skripsi.pdf',
            'deskripsi' => 'File panduan format PDF lengkap.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('pelatihan.show', $kelas->id));
        $response->assertStatus(200);

        // Pastikan container PDF Viewer tampil dan Video Player tersembunyi
        $response->assertSee('id="pdf-viewer-wrapper"', false);
        $response->assertSee('id="pdf-embed-frame"', false);
        $response->assertSee('title="Viewer Dokumen PDF"', false);
        $response->assertSee('panduan_skripsi.pdf#toolbar=0&amp;navpanes=0', false);

        // Header toolbar PDF proteksi baca saja
        $response->assertSee('Dokumen PDF');
        $response->assertSee($materiPdf->judul_materi);
        $response->assertSee('Mode Baca Saja (Unduh Dinonaktifkan)');
        $response->assertDontSee('Unduh PDF');
        $response->assertDontSee('id="btn-pdf-download"', false);
        $response->assertDontSee('id="btn-pdf-newtab"', false);

        // Pastikan container video player di-hide saat active materi adalah PDF
        $response->assertSee('<div id="video-player-wrapper" class="hidden">', false);

        // Pastikan label tombol aksi disesuaikan untuk dokumen PDF
        $response->assertSee('Tandai Selesai Dipelajari');
    }

    public function test_pelatihan_show_handles_presentation_ppt_files_without_auto_download()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-PPT-' . rand(100, 999),
            'judul' => 'Kelas Presentasi Riset',
            'level' => 'Pemula',
            'harga' => 100000,
            'kuota_maksimal' => 50,
            'status' => 'Dibuka',
        ]);

        $materiPpt = \Modules\Master\Entities\MasterMateri::create([
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'judul_materi' => 'Slide Paparan Proposal Tesis & Disertasi',
            'tipe_materi' => 'Dokumen PDF', // Admin memilih tipe dokumen tapi filenya pptx
            'durasi_menit' => 30,
            'file_materi' => 'materi/files/paparan_seminar.pptx',
            'deskripsi' => 'Slide presentasi format PowerPoint.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('pelatihan.show', $kelas->id));
        $response->assertStatus(200);

        // Pastikan container presentation tampil dan container PDF viewer tersembunyi
        $response->assertSee('id="presentation-wrapper"', false);
        $response->assertSee('Slide Presentasi (PPT)');
        $response->assertSee('Mode Tayangan (Auto-Download Dinonaktifkan)');
        $response->assertSee($materiPpt->judul_materi);

        // Pastikan iframe pdf TIDAK memuat file pptx (agar browser TIDAK auto-download)
        $response->assertDontSee('paparan_seminar.pptx#toolbar=0', false);
        $response->assertDontSee('paparan_seminar.pptx#toolbar=1', false);
        $response->assertSee('<div id="pdf-viewer-wrapper" class="hidden">', false);
    }

    public function test_user_can_mark_materi_as_completed_and_update_progress()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-SELESAI-' . rand(100, 999),
            'judul' => 'Kelas Praktik Selesai Modul',
            'level' => 'Intermediate',
            'harga' => 120000,
            'kuota_maksimal' => 30,
            'status' => 'Dibuka',
        ]);

        $m1 = \Modules\Master\Entities\MasterMateri::create([
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'judul_materi' => 'Modul 1: Landasan Konsep',
            'tipe_materi' => 'Video',
            'durasi_menit' => 20,
            'link_video' => 'https://youtu.be/dQw4w9WgXcQ',
            'is_active' => true,
        ]);

        $m2 = \Modules\Master\Entities\MasterMateri::create([
            'kelas_id' => $kelas->id,
            'urutan' => 2,
            'judul_materi' => 'Modul 2: Analisis Praktik',
            'tipe_materi' => 'Video',
            'durasi_menit' => 25,
            'link_video' => 'https://youtu.be/dQw4w9WgXcQ',
            'is_active' => true,
        ]);

        $transaksi = TransaksiPelatihan::create([
            'nomor_registrasi' => 'REG-TST-' . rand(1000, 9999),
            'user_id' => $this->user->id_user ?? $this->user->id,
            'kelas_id' => $kelas->id,
            'judul_kelas' => $kelas->judul,
            'total_modul' => 2,
            'modul_selesai' => 0,
            'progress' => 0,
            'status' => 'Aktif Berjalan',
            'status_pembayaran' => 'Lunas',
        ]);

        // Action selesaikan modul 1
        $res1 = $this->actingAs($this->user)->postJson(route('pelatihan.materi.selesai', $m1->id), [
            'action' => 'complete'
        ]);

        $res1->assertStatus(200);
        $res1->assertJson([
            'success' => true,
            'is_completed' => true,
            'modul_selesai' => 1,
            'total_modul' => 2,
            'progress' => 50,
        ]);

        // Pastikan transaksi di DB ikut terupdate
        $transaksi->refresh();
        $this->assertEquals(1, $transaksi->modul_selesai);
        $this->assertEquals(50, $transaksi->progress);

        // Action selesaikan modul 2
        $res2 = $this->actingAs($this->user)->postJson(route('pelatihan.materi.selesai', $m2->id), [
            'action' => 'complete'
        ]);

        $res2->assertStatus(200);
        $res2->assertJson([
            'success' => true,
            'is_completed' => true,
            'modul_selesai' => 2,
            'total_modul' => 2,
            'progress' => 100,
        ]);

        $transaksi->refresh();
        $this->assertEquals(2, $transaksi->modul_selesai);
        $this->assertEquals(100, $transaksi->progress);
        $this->assertEquals('Selesai', $transaksi->status);
    }

    public function test_admin_can_store_and_update_materi_with_file_upload()
    {
        Storage::fake('public');

        $kelas = MasterKelas::create([
            'kode_kelas' => 'TEST-SILABUS-' . rand(100, 999),
            'judul' => 'Kelas Uji Silabus dan File Dokumen',
            'level' => 'Intermediate',
            'harga' => 200000,
            'kuota_maksimal' => 30,
            'status' => 'Dibuka',
        ]);

        // 1. Simpan materi baru dengan upload file pptx
        $filePpt = UploadedFile::fake()->create('slide_presentasi.pptx', 1024, 'application/vnd.openxmlformats-officedocument.presentationml.presentation');

        $resStore = $this->actingAs($this->user)->post(route('master.materi.store'), [
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'judul_materi' => 'Slide Pengenalan Metodologi Riset',
            'tipe_materi' => 'Dokumen PDF',
            'durasi_menit' => 30,
            'is_free_preview' => 1,
            'file_upload' => $filePpt,
            'deskripsi' => 'File slide materi pengantar.',
        ]);

        $resStore->assertStatus(200);
        $resStore->assertJson([
            'status' => 'success',
            'message' => 'Materi berhasil ditambahkan',
        ]);

        $materi = MasterMateri::where('kelas_id', $kelas->id)->first();
        $this->assertNotNull($materi);
        $this->assertNotNull($materi->file_materi);
        Storage::disk('public')->assertExists($materi->file_materi);
        $this->assertStringContainsString('slide_presentasi.pptx', $materi->file_materi);

        // 2. Update materi dengan mengganti file menjadi dokumen PDF
        $oldFilePath = $materi->file_materi;
        $filePdf = UploadedFile::fake()->create('modul_revisi.pdf', 2048, 'application/pdf');

        $resUpdate = $this->actingAs($this->user)->put(route('master.materi.update', $materi->id), [
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'judul_materi' => 'Modul Lengkap Metodologi Riset Revisi',
            'tipe_materi' => 'Dokumen PDF',
            'durasi_menit' => 45,
            'is_free_preview' => 0,
            'file_upload' => $filePdf,
            'deskripsi' => 'Modul diperbarui ke PDF.',
        ]);

        $resUpdate->assertStatus(200);
        $resUpdate->assertJson([
            'status' => 'success',
            'message' => 'Materi berhasil diperbarui',
        ]);

        $materi->refresh();
        $this->assertNotEquals($oldFilePath, $materi->file_materi);
        Storage::disk('public')->assertMissing($oldFilePath);
        Storage::disk('public')->assertExists($materi->file_materi);
        $this->assertStringContainsString('modul_revisi.pdf', $materi->file_materi);

        // 3. Test getData DataTables contains file attachment link
        $resData = $this->actingAs($this->user)->getJson(route('master.materi.get-data', ['kelas_id' => $kelas->id]));
        $resData->assertStatus(200);
        $json = $resData->json();
        $this->assertNotEmpty($json['data']);
        $item = collect($json['data'])->firstWhere('id', $materi->id);
        $this->assertNotNull($item);
        $this->assertStringContainsString('modul_revisi.pdf', $item['materi_info']);
        $this->assertStringContainsString('paperclip', $item['materi_info']);
    }

    public function test_pelatihan_sertifikat_index_displays_completed_certificate()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'CERT-KLS-' . rand(100, 999),
            'judul' => 'Workshop Analisis SEM-PLS Profesional',
            'level' => 'Advanced',
            'harga' => 200000,
            'kuota_maksimal' => 50,
            'status' => 'Dibuka',
        ]);

        $transaksi = TransaksiPelatihan::create([
            'nomor_registrasi' => 'REG-CERT-' . rand(1000, 9999),
            'user_id' => $this->user->id_user,
            'kelas_id' => $kelas->id,
            'judul_kelas' => $kelas->judul,
            'instruktur' => 'Prof. Dr. Ir. Hendra, M.Si.',
            'kode_kelas' => $kelas->kode_kelas,
            'progress' => 100,
            'total_modul' => 5,
            'modul_selesai' => 5,
            'status' => 'Selesai',
            'nama_pemesan' => 'Budi Santoso',
            'email_pemesan' => 'budi@test.com',
            'status_pembayaran' => 'Lunas',
            'biaya' => 200000,
            'total_bayar' => 200000,
        ]);

        $response = $this->actingAs($this->user)->get(route('pelatihan.sertifikat'));
        $response->assertStatus(200);
        $response->assertSee('E-Sertifikat Pelatihan');
        $response->assertSee($transaksi->judul_kelas);
        $response->assertSee($transaksi->nomor_sertifikat);
        $response->assertSee('Lihat Sertifikat');
        $response->assertSee('Unduh PDF');
        $response->assertSee(route('pelatihan.sertifikat.preview', $transaksi->id));
        $response->assertSee(route('pelatihan.sertifikat.download', $transaksi->id));
    }

    public function test_pelatihan_sertifikat_preview_and_download()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'DL-KLS-' . rand(100, 999),
            'judul' => 'Mastering Python Data Science',
            'level' => 'Pemula',
            'harga' => 150000,
            'kuota_maksimal' => 50,
            'status' => 'Dibuka',
        ]);

        $transaksi = TransaksiPelatihan::create([
            'nomor_registrasi' => 'REG-CERT-DL-' . rand(1000, 9999),
            'user_id' => $this->user->id_user,
            'kelas_id' => $kelas->id,
            'judul_kelas' => $kelas->judul,
            'instruktur' => 'Aditya Pratama, S.Kom.',
            'kode_kelas' => $kelas->kode_kelas,
            'progress' => 100,
            'total_modul' => 3,
            'modul_selesai' => 3,
            'status' => 'Selesai',
            'nama_pemesan' => 'Budi Santoso',
            'email_pemesan' => 'budi@test.com',
            'universitas' => 'Universitas Indonesia',
            'status_pembayaran' => 'Lunas',
            'biaya' => 150000,
            'total_bayar' => 150000,
        ]);

        // 1. Test Preview Page (Harus ada QR Code dan tautan verifikasi)
        $resPreview = $this->actingAs($this->user)->get(route('pelatihan.sertifikat.preview', $transaksi->id));
        $resPreview->assertStatus(200);
        $resPreview->assertSee('SERTIFIKAT KELULUSAN');
        $resPreview->assertSee($transaksi->nomor_sertifikat);
        $resPreview->assertSee('Budi Santoso');
        $resPreview->assertSee($transaksi->judul_kelas);
        $resPreview->assertSee('Universitas Indonesia');
        $resPreview->assertSee('Pindai untuk Verifikasi');
        $resPreview->assertSee($transaksi->verify_url);

        // 2. Test Download Action
        $resDownload = $this->actingAs($this->user)->get(route('pelatihan.sertifikat.download', $transaksi->id));
        $resDownload->assertStatus(200);
        $resDownload->assertHeader('content-type', 'application/pdf');
    }

    public function test_pelatihan_sertifikat_verification_page_verifies_certificate()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'VRF-KLS-' . rand(100, 999),
            'judul' => 'Pelatihan SmartPLS 4 Mahir',
            'level' => 'Intermediate',
            'harga' => 175000,
            'kuota_maksimal' => 30,
            'status' => 'Dibuka',
        ]);

        $transaksi = TransaksiPelatihan::create([
            'nomor_registrasi' => 'REG-VRF-' . rand(1000, 9999),
            'user_id' => $this->user->id_user,
            'kelas_id' => $kelas->id,
            'judul_kelas' => $kelas->judul,
            'instruktur' => 'Dr. Ir. Hendra, M.Si.',
            'kode_kelas' => $kelas->kode_kelas,
            'progress' => 100,
            'total_modul' => 4,
            'modul_selesai' => 4,
            'status' => 'Selesai',
            'nama_pemesan' => 'Citra Kirana',
            'email_pemesan' => 'citra@test.com',
            'universitas' => 'Universitas Gadjah Mada',
            'status_pembayaran' => 'Lunas',
            'biaya' => 175000,
            'total_bayar' => 175000,
        ]);

        // Akses publik halaman verifikasi sertifikat via nomor registrasi
        $response = $this->get(route('pelatihan.sertifikat.verifikasi', $transaksi->nomor_registrasi));
        $response->assertStatus(200);
        $response->assertSee('E-Sertifikat Kelulusan Terdaftar Resmi');
        $response->assertSee('Status: Terverifikasi Asli &amp; Sah', false);
        $response->assertSee($transaksi->nomor_sertifikat);
        $response->assertSee('Citra Kirana');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee($transaksi->judul_kelas);
        $response->assertSee(route('pelatihan.sertifikat.preview', $transaksi->id));

        // Akses publik verifikasi dengan nomor invalid
        $resInvalid = $this->get(route('pelatihan.sertifikat.verifikasi', 'NOMOR-PALSU-999'));
        $resInvalid->assertStatus(200);
        $resInvalid->assertSee('Sertifikat Tidak Ditemukan atau Tidak Valid');
    }

    public function test_pelatihan_sertifikat_does_not_display_if_class_is_not_100_percent_completed()
    {
        $kelas = MasterKelas::create([
            'kode_kelas' => 'NOT100-KLS-' . rand(100, 999),
            'judul' => 'Kelas Metodologi Riset Eksperimen',
            'level' => 'Intermediate',
            'harga' => 150000,
            'kuota_maksimal' => 30,
            'status' => 'Dibuka',
        ]);

        // Transaksi berstatus Selesai tapi progress HANYA 75%
        $transaksiIncomplete = TransaksiPelatihan::create([
            'nomor_registrasi' => 'REG-INC-' . rand(1000, 9999),
            'user_id' => $this->user->id_user,
            'kelas_id' => $kelas->id,
            'judul_kelas' => $kelas->judul,
            'instruktur' => 'Instruktur Belum 100%',
            'kode_kelas' => $kelas->kode_kelas,
            'progress' => 75,
            'total_modul' => 4,
            'modul_selesai' => 3,
            'status' => 'Selesai',
            'nama_pemesan' => 'Peserta Belum Tuntas',
            'email_pemesan' => 'belum100@test.com',
            'status_pembayaran' => 'Lunas',
            'biaya' => 150000,
            'total_bayar' => 150000,
        ]);

        // 1. Pada menu /pelatihan/sertifikat: Jangan munculkan sertifikat yang belum 100%
        $resIndex = $this->actingAs($this->user)->get(route('pelatihan.sertifikat'));
        $resIndex->assertStatus(200);
        $resIndex->assertDontSee($transaksiIncomplete->nomor_registrasi);
        $resIndex->assertDontSee($transaksiIncomplete->nomor_sertifikat);
        $resIndex->assertDontSee(route('pelatihan.sertifikat.preview', $transaksiIncomplete->id));

        // 2. Akses preview langsung ke sertifikat yang belum 100% harus ditolak (403 Forbidden)
        $resPreview = $this->actingAs($this->user)->get(route('pelatihan.sertifikat.preview', $transaksiIncomplete->id));
        $resPreview->assertStatus(403);

        // 3. Akses unduh langsung ke sertifikat yang belum 100% harus ditolak (403 Forbidden)
        $resDownload = $this->actingAs($this->user)->get(route('pelatihan.sertifikat.download', $transaksiIncomplete->id));
        $resDownload->assertStatus(403);

        // 4. Akses publik halaman verifikasi harus menampilkan status 'Pembelajaran Belum 100% Selesai'
        $resVerify = $this->get(route('pelatihan.sertifikat.verifikasi', $transaksiIncomplete->nomor_registrasi));
        $resVerify->assertStatus(200);
        $resVerify->assertSee('Pembelajaran Belum 100% Selesai');
        $resVerify->assertSee('75%');
        $resVerify->assertDontSee('Status: Terverifikasi Asli &amp; Sah', false);
    }
}



