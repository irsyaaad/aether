<?php

namespace Tests\Feature\Modules;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Chat\Entities\ChatModel;
use Tests\TestCase;

class ChatModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test - membuat user untuk testing chat
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat user admin dan login
        $user = User::create([
            'id_user'  => 1,
            'nm_user'  => 'Admin Chat',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'email'    => 'admin@example.com',
        ]);
        $this->actingAs($user);
    }

    /**
     * Test halaman index chat
     * Menampilkan dashboard chat/pesan
     */
    public function test_chat_index_page_returns_success()
    {
        $response = $this->get('/chat');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index daftar chat
     */
    public function test_chats_index_page_returns_success()
    {
        $response = $this->get('/chats');
        $response->assertStatus(200);
    }

    /**
     * Test halaman dashboard PAD (Pendapatan Asli Daerah)
     */
    public function test_dashboard_pad_page_returns_success()
    {
        $response = $this->get('/dashboard-pad');
        $response->assertStatus(200);
    }

    /**
     * Test halaman daftar mutasi
     */
    public function test_list_mutasi_page_returns_success()
    {
        $response = $this->get('/list-mutasi');
        $response->assertStatus(200);
    }
}
