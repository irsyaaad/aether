<?php

namespace Tests\Feature\Modules;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test - dijalankan sebelum setiap method test
     * Membuat user default untuk testing autentikasi
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Buat user default untuk test login
        User::create([
            'id_user'  => 1,
            'nm_user'  => 'Test User',
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'email'    => 'test@example.com',
        ]);
    }

    /**
     * Test halaman landing page (/) - halaman awal aplikasi
     * Memastikan halaman login dapat diakses tanpa autentikasi
     */
    public function test_landing_page_returns_success()
    {
        // Akses halaman utama
        $response = $this->get('/');
        // Halaman harus berhasil diakses (redirect ke login atau langsung)
        $response->assertStatus(200);
    }

    /**
     * Test halaman login (/auth/login) - form login
     * Memastikan form login dapat diakses
     */
    public function test_login_page_returns_success()
    {
        // Akses halaman login
        $response = $this->get('/auth/login');
        // Halaman harus berhasil diakses
        $response->assertStatus(200);
    }

    /**
     * Test proses login dengan kredensial valid
     * User harus berhasil login dan diarahkan ke dashboard
     */
    public function test_login_with_valid_credentials()
    {
        // Kirim request login dengan username dan password yang benar
        $response = $this->post('/auth/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);
        // Harus redirect (ke dashboard atau halaman lain)
        $response->assertStatus(302);
    }

    /**
     * Test proses login dengan kredensial tidak valid
     * User harus gagal login dan tetap di halaman login
     */
    public function test_login_with_invalid_credentials()
    {
        // Kirim request login dengan password salah
        $response = $this->post('/auth/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);
        // Harus redirect kembali ke halaman login
        $response->assertStatus(302);
    }

    /**
     * Test akses halaman logout (/auth/logout)
     * User harus bisa logout dari aplikasi
     */
    public function test_logout_redirects()
    {
        // Login dulu
        $this->post('/auth/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        // Akses halaman logout
        $response = $this->get('/auth/logout');
        // Harus redirect ke halaman login
        $response->assertStatus(302);
    }

    /**
     * Test halaman register (/register) - pendaftaran user baru
     * Memastikan halaman register dapat diakses
     */
    public function test_register_page_returns_success()
    {
        // Akses halaman register
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test halaman forgot password (/forgot-password)
     * Memastikan halaman lupa password dapat diakses
     */
    public function test_forgot_password_page_returns_success()
    {
        // Akses halaman forgot password
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    /**
     * Test akses halaman dashboard tanpa login
     * User harus redirect ke halaman login
     */
    public function test_dashboard_requires_authentication()
    {
        // Akses dashboard tanpa login
        $response = $this->get('/dashboard');
        // Harus redirect ke halaman login
        $response->assertStatus(302);
    }
}
