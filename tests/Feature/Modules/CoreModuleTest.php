<?php

namespace Tests\Feature\Modules;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\RoleUser;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoreModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test - membuat data master yang diperlukan
     * - User admin
     * - Role
     * - RoleUser (relasi user-role)
     * - Menu untuk permission check
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat user admin
        $user = User::create([
            'id_user'  => 1,
            'nm_user'  => 'Admin User',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'email'    => 'admin@example.com',
        ]);

        // Buat role admin
        $role = Role::create([
            'id_role'    => 1,
            'nm_role'    => 'Super Admin',
            'app_id'     => 'saas',
            'id_creator' => 1,
        ]);

        // Relasikan user dengan role (pakai DB karena RoleUser tidak punya fillable)
        DB::table('role_user')->insert([
            'id_role' => 1,
            'id_user' => 1,
        ]);

        // Set session role untuk bypass permission check (RoleMenu::checkAccess)
        session(["role" => ["id_role" => 1]]);

        // Login sebagai admin
        $this->actingAs($user);
    }

    /**
     * Test halaman dashboard - halaman utama setelah login
     * Memastikan dashboard dapat diakses oleh user yang sudah login
     */
    public function test_dashboard_page_returns_success()
    {
        // Akses halaman dashboard
        $response = $this->get('/dashboard');
        // Halaman harus berhasil diakses
        $response->assertStatus(200);
    }

    /**
     * Test proses ubah password dengan data valid
     */
    public function test_change_password_with_valid_data()
    {
        // Kirim POST request untuk ubah password
        $response = $this->post('/change-password', [
            'password_lama'    => 'password',
            'password'         => 'newpassword123',
            'password_confirm' => 'newpassword123',
        ]);
        // Harus redirect setelah sukses
        $response->assertStatus(302);
    }
}
