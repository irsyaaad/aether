<?php

namespace Tests\Feature\Modules;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Master\Entities\RegProvince;
use Modules\Master\Entities\RegRegency;
use Modules\Master\Entities\RegDistrict;
use Modules\Master\Entities\RegVillage;
use Modules\Master\Entities\Layanan;
use Tests\TestCase;

class MasterModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test - membuat user admin dan data master
     * Data master diperlukan untuk test fitur CRUD wilayah
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat tabel wilayah secara manual karena tabel-tabel ini
        // dibuat dari file import wilayah_indonesia.sql, bukan dari migration
        Schema::create('reg_provinces', function ($table) {
            $table->char('id', 2)->primary();
            $table->string('name', 255);
            $table->timestamps();
        });
        Schema::create('reg_regencies', function ($table) {
            $table->char('id', 4)->primary();
            $table->char('province_id', 2);
            $table->string('name', 255);
            $table->timestamps();
        });
        Schema::create('reg_districts', function ($table) {
            $table->char('id', 6)->primary();
            $table->char('regency_id', 4);
            $table->string('name', 255);
            $table->timestamps();
        });
        Schema::create('reg_villages', function ($table) {
            $table->char('id', 10)->primary();
            $table->char('district_id', 6);
            $table->string('name', 255);
            $table->timestamps();
        });

        // Buat user dan login
        $user = User::create([
            'id_user'  => 1,
            'nm_user'  => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'email'    => 'admin@example.com',
        ]);

        // Set session role untuk bypass permission check (RoleMenu::checkAccess)
        session(["role" => ["id_role" => 1]]);

        $this->actingAs($user);

        // Buat data provinsi untuk test
        RegProvince::create(['id' => '11', 'name' => 'Aceh']);
        RegProvince::create(['id' => '12', 'name' => 'Sumatera Utara']);
        RegProvince::create(['id' => '31', 'name' => 'DKI Jakarta']);

        // Buat data kota/kabupaten untuk test
        RegRegency::create(['id' => '1101', 'province_id' => '11', 'name' => 'Kota Sabang']);
        RegRegency::create(['id' => '1102', 'province_id' => '11', 'name' => 'Aceh Besar']);
        RegRegency::create(['id' => '3171', 'province_id' => '31', 'name' => 'Kota Jakarta Pusat']);

        // Buat data kecamatan untuk test
        RegDistrict::create(['id' => '110101', 'regency_id' => '1101', 'name' => 'Sukakarya']);
        RegDistrict::create(['id' => '317101', 'regency_id' => '3171', 'name' => 'Menteng']);

        // Buat data desa untuk test
        RegVillage::create(['id' => '11010101', 'district_id' => '110101', 'name' => 'Paya']);
        RegVillage::create(['id' => '31710101', 'district_id' => '317101', 'name' => 'Menteng']);

        // Buat data layanan untuk test
        Layanan::create(['kode_layanan' => 'EXP', 'nama_layanan' => 'Express']);
    }

    /**
     * Test halaman index master wilayah
     * Menampilkan tree view wilayah
     */
    public function test_wilayah_index_page_returns_success()
    {
        // Akses halaman wilayah
        $response = $this->get('/master/wilayah');
        $response->assertStatus(200);
    }

    /**
     * Test endpoint get children wilayah - mengambil data child node
     */
    public function test_wilayah_get_children_returns_data()
    {
        // Akses endpoint children wilayah dengan parent ID
        $response = $this->getJson('/master/wilayah/children?parent=11&type=province');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index provinsi
     */
    public function test_provinsi_index_page_returns_success()
    {
        $response = $this->get('/master/provinsi');
        $response->assertStatus(200);
    }

    /**
     * Test halaman create provinsi
     */
    public function test_provinsi_create_page_returns_success()
    {
        $response = $this->get('/master/provinsi/create');
        $response->assertStatus(200);
    }

    /**
     * Test proses store provinsi baru
     */
    public function test_provinsi_store_creates_new_province()
    {
        $response = $this->post('/master/provinsi', [
            'id'   => '14',
            'name' => 'Riau',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reg_provinces', ['id' => '14', 'name' => 'Riau']);
    }

    /**
     * Test halaman edit provinsi
     */
    public function test_provinsi_edit_page_returns_success()
    {
        $response = $this->get('/master/provinsi/11/edit');
        $response->assertStatus(200);
    }

    /**
     * Test proses update provinsi
     */
    public function test_provinsi_update_modifies_province()
    {
        $response = $this->put('/master/provinsi/11', [
            'name' => 'Aceh Darussalam',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reg_provinces', ['id' => '11', 'name' => 'Aceh Darussalam']);
    }

    /**
     * Test proses delete provinsi
     */
    public function test_provinsi_destroy_deletes_province()
    {
        $response = $this->delete('/master/provinsi/12');
        $response->assertStatus(200);
        $this->assertDatabaseMissing('reg_provinces', ['id' => '12']);
    }

    /**
     * Test halaman index kota/kabupaten
     */
    public function test_kota_kab_index_page_returns_success()
    {
        $response = $this->get('/master/kota-kab');
        $response->assertStatus(200);
    }

    /**
     * Test halaman create kota/kabupaten
     */
    public function test_kota_kab_create_page_returns_success()
    {
        $response = $this->get('/master/kota-kab/create');
        $response->assertStatus(200);
    }

    /**
     * Test proses store kota/kabupaten baru
     */
    public function test_kota_kab_store_creates_new_regency()
    {
        $response = $this->post('/master/kota-kab', [
            'id'          => '1103',
            'province_id' => '11',
            'name'        => 'Aceh Timur',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reg_regencies', ['id' => '1103', 'name' => 'Aceh Timur']);
    }

    /**
     * Test endpoint get regencies by province
     */
    public function test_kota_kab_get_regencies_by_province()
    {
        $response = $this->get('/master/kota-kab/get-regencies/11');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index kecamatan
     */
    public function test_kecamatan_index_page_returns_success()
    {
        $response = $this->get('/master/kecamatan');
        $response->assertStatus(200);
    }

    /**
     * Test proses store kecamatan baru
     */
    public function test_kecamatan_store_creates_new_district()
    {
        $response = $this->post('/master/kecamatan', [
            'id'         => '110102',
            'regency_id' => '1101',
            'name'       => 'Kecamatan Baru',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reg_districts', ['id' => '110102']);
    }

    /**
     * Test endpoint get districts by regency
     */
    public function test_kecamatan_get_districts_by_regency()
    {
        $response = $this->get('/master/kecamatan/get-districts/1101');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index desa
     */
    public function test_desa_index_page_returns_success()
    {
        $response = $this->get('/master/desa');
        $response->assertStatus(200);
    }

    /**
     * Test proses store desa baru
     */
    public function test_desa_store_creates_new_village()
    {
        $response = $this->post('/master/desa', [
            'id'          => '11010102',
            'district_id' => '110101',
            'name'        => 'Desa Baru',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('reg_villages', ['id' => '11010102']);
    }

    /**
     * Test endpoint get villages by district
     */
    public function test_desa_get_villages_by_district()
    {
        $response = $this->get('/master/desa/get-villages/110101');
        $response->assertStatus(200);
    }

    /**
     * Test halaman index layanan
     */
    public function test_layanan_index_page_returns_success()
    {
        $response = $this->get('/master/layanan');
        $response->assertStatus(200);
    }

    /**
     * Test proses store layanan baru
     */
    public function test_layanan_store_creates_new_service()
    {
        $response = $this->post('/master/layanan', [
            'kode_layanan' => 'REG',
            'nama_layanan' => 'Reguler',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('layanan', ['kode_layanan' => 'REG']);
    }
}
