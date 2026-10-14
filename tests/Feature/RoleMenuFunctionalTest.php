<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleMenuFunctionalTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;
    protected $karyawanUser;
    protected $roleKaryawan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::find(1);
        $this->karyawanUser = User::find(2) ?? $this->adminUser;

        DB::table('role_user')->updateOrInsert(
            ['id_user' => $this->karyawanUser->id_user],
            ['id_role' => 99, 'updated_at' => now()]
        );
    }

    /**
     * 1. Uji config/constant.php mendefinisikan menu sesuai sidebar
     */
    public function test_constant_php_defines_sidebar_permissions()
    {
        $permissions = config('constant.permissions');
        $this->assertIsArray($permissions);

        // Pastikan modul utama sesuai sidebar ada
        $this->assertArrayHasKey('DASHBOARD', $permissions);
        $this->assertArrayHasKey('LAYANAN AKADEMIK', $permissions);
        $this->assertArrayHasKey('MANAJEMEN TUGAS', $permissions);
        $this->assertArrayHasKey('MASTER DATA', $permissions);
        $this->assertArrayHasKey('KEUANGAN', $permissions);
        $this->assertArrayHasKey('SETTING', $permissions);

        // Layanan Akademik
        $akademik = $permissions['LAYANAN AKADEMIK'];
        $this->assertArrayHasKey('bimbingan', $akademik);
        $this->assertArrayHasKey('olahdata', $akademik);
        $this->assertArrayHasKey('pelatihan', $akademik);

        // Manajemen Tugas
        $tugas = $permissions['MANAJEMEN TUGAS'];
        $this->assertArrayHasKey('my-tasks', $tugas);
        $this->assertArrayHasKey('tasks', $tugas);
        $this->assertArrayHasKey('task-assignment', $tugas);
        $this->assertArrayHasKey('task-monitoring', $tugas);
        $this->assertArrayHasKey('task-categories', $tugas);
        $this->assertArrayHasKey('task-priorities', $tugas);

        // Master Data
        $master = $permissions['MASTER DATA'];
        $this->assertArrayHasKey('master.programs', $master);
        $this->assertArrayHasKey('master.mentors', $master);
        $this->assertArrayHasKey('master.kelas', $master);
        $this->assertArrayHasKey('master.materi', $master);
        $this->assertArrayHasKey('master.metode', $master);

        // Keuangan
        $keuangan = $permissions['KEUANGAN'];
        $this->assertArrayHasKey('kas-bank-masuk', $keuangan);
        $this->assertArrayHasKey('kas-bank-keluar', $keuangan);
        $this->assertArrayHasKey('jurnal', $keuangan);
        $this->assertArrayHasKey('neraca', $keuangan);
        $this->assertArrayHasKey('rugi-laba', $keuangan);
        $this->assertArrayHasKey('cashflow', $keuangan);

        // Setting
        $setting = $permissions['SETTING'];
        $this->assertArrayHasKey('master.coa', $setting);
        $this->assertArrayHasKey('perusahaan', $setting);
        $this->assertArrayHasKey('user', $setting);
        $this->assertArrayHasKey('role', $setting);
        $this->assertArrayHasKey('role-menu', $setting);
    }

    /**
     * 2. Uji halaman detail role 99 (/roles/detail/99) menampilkan settingan constant.php
     */
    public function test_roles_detail_99_renders_all_constant_permissions()
    {
        $this->actingAs($this->adminUser);
        session(['role' => ['id_role' => 1]]);

        $response = $this->get(route('roles.show', 99));
        $response->assertStatus(200);

        // Modul-modul sidebar
        $response->assertSee('MODUL: DASHBOARD');
        $response->assertSee('MODUL: LAYANAN AKADEMIK');
        $response->assertSee('MODUL: MANAJEMEN TUGAS');
        $response->assertSee('MODUL: MASTER DATA');
        $response->assertSee('MODUL: KEUANGAN');
        $response->assertSee('MODUL: SETTING');

        // Fitur-fitur sidebar
        $response->assertSee('Bimbingan Skripsi');
        $response->assertSee('Olah Data Riset');
        $response->assertSee('Pelatihan &amp; Workshop', false);
        $response->assertSee('Tugas Saya');
        $response->assertSee('Daftar Tugas');
        $response->assertSee('Plotting Tugas');
        $response->assertSee('Monitoring Submission');
        $response->assertSee('Master Program Riset');
        $response->assertSee('Kas Bank Masuk');
        $response->assertSee('Kas Bank Keluar');
        $response->assertSee('Jurnal Umum');
        $response->assertSee('Neraca Keuangan');
        $response->assertSee('Laba Rugi');
        $response->assertSee('Arus Kas (Cash Flow)');
        $response->assertSee('Master Chart of Account (CoA)');
        $response->assertSee('Hak Akses &amp; Permission', false);

        // Checkbox values
        $response->assertSee('value="bimbingan.index"', false);
        $response->assertSee('value="bimbingan.create"', false);
        $response->assertSee('value="my-tasks.index"', false);
        $response->assertSee('value="kas-bank-masuk.index"', false);
        $response->assertSee('value="master.programs.index"', false);
    }

    /**
     * 3. Uji update hak akses role 99 via role-menu.update
     */
    public function test_update_role_menu_for_role_99()
    {
        $this->actingAs($this->adminUser);
        session(['role' => ['id_role' => 1]]);

        $selectedPermissions = [
            'dashboard.index',
            'bimbingan.index',
            'bimbingan.create',
            'my-tasks.index',
        ];

        $response = $this->put(route('role-menu.update', 99), [
            'permissions' => $selectedPermissions,
        ]);

        $response->assertRedirect(route('roles.show', 99));
        $response->assertSessionHas('success');

        // Pastikan role_menu terupdate di DB
        $savedPermissions = RoleMenu::where('id_role', 99)->pluck('permission')->toArray();
        $this->assertEqualsCanonicalizing($selectedPermissions, $savedPermissions);

        // Uji RoleMenu::checkAccess
        session(['role' => ['id_role' => 99]]);
        $this->assertTrue(RoleMenu::checkAccess('bimbingan.index'));
        $this->assertTrue(RoleMenu::checkAccess('my-tasks.index'));
        $this->assertFalse(RoleMenu::checkAccess('bimbingan.delete'));
        $this->assertFalse(RoleMenu::checkAccess('kas-bank-masuk.index'));
    }

    /**
     * 4. Uji Sidebar Layout merespon hak akses role 99
     */
    public function test_sidebar_respects_role_99_permissions()
    {
        // Berikan izin tertentu ke role 99
        RoleMenu::where('id_role', 99)->delete();
        RoleMenu::create(['id_role' => 99, 'permission' => 'bimbingan.index']);
        RoleMenu::create(['id_role' => 99, 'permission' => 'my-tasks.index']);

        $this->actingAs($this->karyawanUser);
        session(['role' => ['id_role' => 99, 'nm_role' => 'Karyawan']]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Menu yang diizinkan harus tampil
        $response->assertSee('Bimbingan Skripsi');
        $response->assertSee('Tugas Saya');

        // Menu yang tidak diizinkan tidak boleh tampil
        $response->assertDontSee('Olah Data Riset');
        $response->assertDontSee('Pelatihan &amp; Workshop', false);
        $response->assertDontSee('Kas / Bank Masuk');
        $response->assertDontSee('Master Program Riset');
    }
}
