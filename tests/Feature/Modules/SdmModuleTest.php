<?php

namespace Tests\Feature\Modules;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sdm\Entities\Karyawan;
use Modules\Sdm\Entities\Jabatan;
use Modules\Sdm\Entities\Absensi;
use Modules\Sdm\Entities\Izin;
use Modules\Sdm\Entities\Training;
use Modules\Sdm\Entities\Kpi;
use Modules\Sdm\Entities\KontrakKaryawan;
use Tests\TestCase;

class SdmModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test - membuat data master SDM
     * Data dasar untuk test fitur HR / SDM
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat user admin dan login
        $user = User::create([
            'id_user'  => 1,
            'nm_user'  => 'Admin SDM',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'email'    => 'admin@example.com',
        ]);
        $this->actingAs($user);

        // Buat data jabatan
        Jabatan::create(['id_jabatan' => 1, 'nm_jabatan' => 'Manager', 'level' => 1]);

        // Buat data karyawan
        Karyawan::create([
            'id_karyawan' => 1,
            'nm_karyawan' => 'Budi Santoso',
            'id_jabatan'  => 1,
            'nik'         => '1234567890',
            'alamat'      => 'Jakarta',
            'telp'        => '08123456789',
        ]);

        // Buat data absensi
        Absensi::create([
            'id_absensi'   => 1,
            'id_karyawan'  => 1,
            'tgl_absensi'  => '2024-01-15',
            'jam_masuk'    => '08:00:00',
            'jam_keluar'   => '17:00:00',
            'status'       => 'hadir',
        ]);

        // Buat data izin
        Izin::create([
            'id_izin'      => 1,
            'id_karyawan'  => 1,
            'tgl_mulai'    => '2024-01-20',
            'tgl_selesai'  => '2024-01-22',
            'jenis_izin'   => 'sakit',
            'keterangan'   => 'Demam',
        ]);

        // Buat data training
        Training::create([
            'id_training'  => 1,
            'nm_training'  => 'Pelatihan Kepemimpinan',
            'tgl_mulai'    => '2024-02-01',
            'tgl_selesai'  => '2024-02-03',
        ]);

        // Buat data KPI
        Kpi::create([
            'id_kpi'       => 1,
            'nm_kpi'       => 'KPI Bulanan',
            'tahun'        => 2024,
            'bulan'        => 1,
        ]);

        // Buat data kontrak karyawan
        KontrakKaryawan::create([
            'id_kontrak'   => 1,
            'id_karyawan'  => 1,
            'nm_kontrak'   => 'Kontrak 2024',
            'tgl_mulai'    => '2024-01-01',
            'tgl_selesai'  => '2024-12-31',
        ]);
    }

    /**
     * Test halaman index karyawan
     * Menampilkan daftar semua karyawan
     */
    public function test_karyawan_index_page_returns_success()
    {
        $response = $this->get('/karyawan');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index jabatan
     * Menampilkan daftar jabatan/posisi
     */
    public function test_jabatan_index_page_returns_success()
    {
        $response = $this->get('/jabatan');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index absensi
     * Menampilkan daftar absensi karyawan
     */
    public function test_absensi_index_page_returns_success()
    {
        $response = $this->get('/absensi');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index izin/cuti
     * Menampilkan daftar pengajuan izin
     */
    public function test_izin_index_page_returns_success()
    {
        $response = $this->get('/izin');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index employee activities
     * Menampilkan aktivitas karyawan
     */
    public function test_employee_activities_index_page_returns_success()
    {
        $response = $this->get('/employee-activities');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index daily report
     * Menampilkan laporan harian karyawan
     */
    public function test_daily_report_index_page_returns_success()
    {
        $response = $this->get('/daily-report');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index training
     * Menampilkan daftar pelatihan
     */
    public function test_training_index_page_returns_success()
    {
        $response = $this->get('/training');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index kontrak karyawan
     * Menampilkan daftar kontrak kerja
     */
    public function test_kontrak_karyawan_index_page_returns_success()
    {
        $response = $this->get('/kontrak-karyawan');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index KPI
     * Menampilkan daftar KPI
     */
    public function test_kpi_index_page_returns_success()
    {
        $response = $this->get('/kpi');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index KPI target
     * Menampilkan target KPI
     */
    public function test_kpi_target_index_page_returns_success()
    {
        $response = $this->get('/kpi-target');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index KPI realisasi
     * Menampilkan realisasi KPI
     */
    public function test_kpi_realisasi_index_page_returns_success()
    {
        $response = $this->get('/kpi-realisasi');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index payroll period
     * Menampilkan periode penggajian
     */
    public function test_payroll_period_index_page_returns_success()
    {
        $response = $this->get('/payroll-period');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index payroll component
     * Menampilkan komponen penggajian
     */
    public function test_payroll_component_index_page_returns_success()
    {
        $response = $this->get('/payroll-component');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index payroll slip
     * Menampilkan slip gaji
     */
    public function test_payroll_slip_index_page_returns_success()
    {
        $response = $this->get('/payroll-slip');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index piutang karyawan
     * Menampilkan daftar pinjaman karyawan
     */
    public function test_piutang_karyawan_index_page_returns_success()
    {
        $response = $this->get('/piutang-karyawan');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index kartu piutang
     * Menampilkan kartu piutang karyawan
     */
    public function test_kartu_piutang_index_page_returns_success()
    {
        $response = $this->get('/kartu-piutang');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index handover record
     * Menampilkan catatan serah terima
     */
    public function test_handover_record_index_page_returns_success()
    {
        $response = $this->get('/handover');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index perusahaan
     * Menampilkan data perusahaan
     */
    public function test_perusahaan_index_page_returns_success()
    {
        $response = $this->get('/perusahaan');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index iklan/rekrutmen
     * Menampilkan daftar iklan lowongan
     */
    public function test_iklan_index_page_returns_success()
    {
        $response = $this->get('/iklan');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index kandidat
     * Menampilkan daftar kandidat
     */
    public function test_kandidat_index_page_returns_success()
    {
        $response = $this->get('/kandidat');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index rekrutmen
     * Menampilkan proses rekrutmen
     */
    public function test_rekrutment_index_page_returns_success()
    {
        $response = $this->get('/rekrutment');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index SOP
     * Menampilkan daftar SOP
     */
    public function test_sop_index_page_returns_success()
    {
        $response = $this->get('/sop');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index job description
     * Menampilkan daftar job desc
     */
    public function test_job_description_index_page_returns_success()
    {
        $response = $this->get('/job-description');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index master training
     * Menampilkan daftar master training
     */
    public function test_master_training_index_page_returns_success()
    {
        $response = $this->get('/master-training');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index meeting
     * Menampilkan daftar meeting
     */
    public function test_meeting_index_page_returns_success()
    {
        $response = $this->get('/meeting');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index PKWT
     * Menampilkan daftar PKWT
     */
    public function test_pkwt_index_page_returns_success()
    {
        $response = $this->get('/pkwt');
        $response->assertStatus(200);
    }
}
