<?php

namespace Tests\Feature;

use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['captcha.disable' => true]);
    }

    /**
     * 1. Halaman registrasi dapat diakses dengan sukses (HTTP 200).
     */
    public function test_register_page_is_accessible()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Aether');
    }

    /**
     * 2. Registrasi user baru otomatis mendapatkan role 99 (Karyawan).
     */
    public function test_new_user_registration_automatically_assigns_role_99()
    {
        $email = 'user_auto99_' . uniqid() . '@example.com';
        $postData = [
            'full_name'             => 'Karyawan Baru Otomatis',
            'email'                 => $email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'jenis_kelamin'         => 'L',
            'tgl_lahir'             => '1996-08-20',
        ];

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/register', $postData);

        $response->assertRedirect('auth/login');
        $response->assertSessionHas('success', 'User Sukses Register');

        // Pastikan user berhasil dibuat di database
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user, 'User baru harus tersimpan di database.');
        $this->assertEquals($email, $user->username);
        $this->assertEquals('Karyawan Baru Otomatis', $user->nm_user);
        $this->assertTrue(Hash::check('password123', $user->password));

        // Pastikan role_user otomatis terisi dengan id_role = 99
        $roleUser = RoleUser::where('id_user', $user->id_user)->first();
        $this->assertNotNull($roleUser, 'Data role_user harus terbuat untuk user baru.');
        $this->assertEquals(99, $roleUser->id_role, 'Role user baru harus otomatis bernilai 99.');
    }

    /**
     * 3. Registrasi aman dari manipulasi parameter (injeksi id_role tetap menghasilkan role 99).
     */
    public function test_registration_ignores_tampered_role_id_and_forces_role_99()
    {
        $email = 'tamper_test_' . uniqid() . '@example.com';
        $postData = [
            'full_name'             => 'Percobaan Injeksi Role Admin',
            'email'                 => $email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'jenis_kelamin'         => 'P',
            'tgl_lahir'             => '1999-12-01',
            'id_role'               => 1, // Mencoba memaksa role 1 (Administrator)
        ];

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/register', $postData);

        $response->assertRedirect('auth/login');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);

        // Verifikasi bahwa id_role yang tersimpan TETAP 99 dan BUKAN 1
        $roleUser = RoleUser::where('id_user', $user->id_user)->first();
        $this->assertNotNull($roleUser);
        $this->assertEquals(99, $roleUser->id_role, 'Role harus selalu 99 meskipun request mengirim id_role lain.');
    }

    /**
     * 4. User yang baru terdaftar dengan role 99 dapat login ke sistem.
     */
    public function test_newly_registered_user_can_login_successfully()
    {
        $email = 'login_test_' . uniqid() . '@example.com';
        $password = 'secret123456';

        // Daftarkan user baru
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/register', [
                'full_name'             => 'User Siap Login',
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $password,
                'jenis_kelamin'         => 'L',
                'tgl_lahir'             => '1995-01-01',
            ]);

        // Login dengan kredensial baru
        $loginResponse = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/auth/login', [
                'email'    => $email,
                'password' => $password,
                'captcha'  => 'bypass',
            ]);

        // Berhasil login dan diarahkan ke dashboard
        $loginResponse->assertRedirect('dashboard');
        $this->assertAuthenticated();
        $this->assertEquals(99, session('role')['id_role']);
    }
}
