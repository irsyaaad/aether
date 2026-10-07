<?php

namespace Tests\Feature\Modules;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test - membuat user untuk testing setting
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat user admin dan login
        $user = User::create([
            'id_user'  => 1,
            'nm_user'  => 'Admin Setting',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'email'    => 'admin@example.com',
        ]);
        $this->actingAs($user);
    }

    /**
     * Test halaman setting perusahaan
     * Menampilkan pengaturan profil perusahaan
     */
    public function test_setting_perusahaan_index_page_returns_success()
    {
        $response = $this->get('/setting/perusahaan');
        $response->assertStatus(200);
    }
}
