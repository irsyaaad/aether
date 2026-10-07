<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    /**
     * Test dateindo function - konversi tanggal ke format Indonesia
     * Menguji format output "dd bulan_indo YYYY"
     */
    public function test_dateindo_returns_formatted_indonesian_date()
    {
        // Panggil fungsi dateindo dengan tanggal 2024-01-15
        $result = dateindo('2024-01-15');
        // Hasil harus berupa "15 Januari 2024"
        $this->assertEquals('15 Januari 2024', $result);
    }

    /**
     * Test dateindo dengan tanggal di bulan berbeda
     * Memastikan semua bulan berfungsi dengan benar
     */
    public function test_dateindo_handles_different_months()
    {
        // Test bulan Februari
        $this->assertEquals('14 Februari 2024', dateindo('2024-02-14'));
        // Test bulan Maret
        $this->assertEquals('20 Maret 2024', dateindo('2024-03-20'));
        // Test bulan Desember
        $this->assertEquals('25 Desember 2024', dateindo('2024-12-25'));
    }

    /**
     * Test dateindo dengan input kosong
     * Fungsi harus mengembalikan '-' jika $tanggal kosong
     */
    public function test_dateindo_returns_dash_for_empty_date()
    {
        // Panggil dengan string kosong
        $this->assertEquals('-', dateindo(''));
        // Panggil dengan null
        $this->assertEquals('-', dateindo(null));
    }

    /**
     * Test parseDateIndo function - konversi berbagai format tanggal ke Y-m-d
     * Menguji format dd-mm-yyyy
     */
    public function test_parsedateindo_converts_dmy_format()
    {
        // Format dd-mm-yyyy
        $result = parseDateIndo('15-01-2024');
        // Harus dikonversi ke Y-m-d
        $this->assertEquals('2024-01-15', $result);
    }

    /**
     * Test parseDateIndo dengan format dd/mm/yyyy
     * Memastikan format dengan slash juga dikenali
     */
    public function test_parsedateindo_converts_dmy_slash_format()
    {
        // Format dd/mm/yyyy
        $result = parseDateIndo('15/01/2024');
        // Harus dikonversi ke Y-m-d
        $this->assertEquals('2024-01-15', $result);
    }

    /**
     * Test parseDateIndo dengan format Indonesian "dd BULAN yyyy"
     * Format umum yang dipakai di Indonesia
     */
    public function test_parsedateindo_converts_indonesian_month_name_format()
    {
        // Format "15 JANUARI 2024"
        $result = parseDateIndo('15 JANUARI 2024');
        $this->assertEquals('2024-01-15', $result);

        // Format "20 DESEMBER 2030"
        $result = parseDateIndo('20 DESEMBER 2030');
        $this->assertEquals('2030-12-20', $result);
    }

    /**
     * Test parseDateIndo dengan format Y-m-d (sudah standar)
     * Fungsi harus mengembalikan nilai yang sama jika sudah dalam format Y-m-d
     */
    public function test_parsedateindo_passes_through_ymd_format()
    {
        // Format Y-md (sudah standar)
        $result = parseDateIndo('2024-01-15');
        $this->assertEquals('2024-01-15', $result);
    }

    /**
     * Test parseDateIndo dengan input kosong
     * Fungsi harus mengembalikan null untuk input kosong
     */
    public function test_parsedateindo_returns_null_for_empty_input()
    {
        // Input string kosong
        $this->assertNull(parseDateIndo(''));
        // Input null
        $this->assertNull(parseDateIndo(null));
        // Input tanda strip
        $this->assertNull(parseDateIndo('-'));
    }

    /**
     * Test parseDateIndo dengan objek DateTimeInterface
     * Fungsi harus bisa menerima objek DateTime
     */
    public function test_parsedateindo_accepts_datetime_object()
    {
        // Buat objek DateTime
        $date = new \DateTime('2024-06-15');
        $result = parseDateIndo($date);
        // Harus mengembalikan format Y-m-d
        $this->assertEquals('2024-06-15', $result);
    }

    /**
     * Test daydate function - konversi hari dalam bahasa Inggris ke Indonesia
     * Menguji semua 7 hari dalam seminggu
     */
    public function test_daydate_returns_correct_indonesian_day()
    {
        // Test hari Minggu
        $this->assertEquals('Minggu', daydate('2024-01-07'));
        // Test hari Senin
        $this->assertEquals('Senin', daydate('2024-01-08'));
        // Test hari Selasa
        $this->assertEquals('Selasa', daydate('2024-01-09'));
        // Test hari Rabu
        $this->assertEquals('Rabu', daydate('2024-01-10'));
        // Test hari Kamis
        $this->assertEquals('Kamis', daydate('2024-01-11'));
        // Test hari Jumat
        $this->assertEquals("Jum'at", daydate('2024-01-12'));
        // Test hari Sabtu
        $this->assertEquals('Sabtu', daydate('2024-01-13'));
    }

    /**
     * Test terbilang function - konversi angka ke huruf Indonesia
     * Menguji angka satuan
     */
    public function test_terbilang_converts_small_numbers()
    {
        // Test angka 0
        $this->assertEquals('', trim(terbilang(0)));
        // Test angka 1
        $this->assertEquals('Satu', trim(terbilang(1)));
        // Test angka 5
        $this->assertEquals('Lima', trim(terbilang(5)));
        // Test angka 11
        $this->assertEquals('Sebelas', trim(terbilang(11)));
    }

    /**
     * Test terbilang dengan angka belasan
     */
    public function test_terbilang_converts_teen_numbers()
    {
        // Angka 12
        $this->assertEquals('Dua Belas', trim(terbilang(12)));
        // Angka 15
        $this->assertEquals('Lima Belas', trim(terbilang(15)));
        // Angka 19
        $this->assertEquals('Sembilan Belas', trim(terbilang(19)));
    }

    /**
     * Test terbilang dengan angka puluhan
     */
    public function test_terbilang_converts_tens()
    {
        // Angka 20
        $this->assertEquals('Dua Puluh', trim(terbilang(20)));
        // Angka 25
        $this->assertEquals('Dua Puluh Lima', trim(terbilang(25)));
        // Angka 50
        $this->assertEquals('Lima Puluh', trim(terbilang(50)));
    }

    /**
     * Test terbilang dengan angka ratusan
     */
    public function test_terbilang_converts_hundreds()
    {
        // Angka 100
        $this->assertEquals('Seratus', trim(terbilang(100)));
        // Angka 150
        $this->assertEquals('Seratus Lima Puluh', trim(terbilang(150)));
        // Angka 999
        $this->assertEquals('Sembilan Ratus Sembilan Puluh Sembilan', trim(terbilang(999)));
    }

    /**
     * Test terbilang dengan angka ribuan
     */
    public function test_terbilang_converts_thousands()
    {
        // Angka 1000
        $this->assertEquals('Seribu', trim(terbilang(1000)));
        // Angka 2500
        $this->assertEquals('Dua Ribu Lima Ratus', trim(terbilang(2500)));
        // Angka 11000
        $this->assertEquals('Sebelas Ribu', trim(terbilang(11000)));
    }

    /**
     * Test terbilang dengan angka jutaan
     */
    public function test_terbilang_converts_millions()
    {
        // Angka 1 juta
        $this->assertEquals('Satu Juta', trim(terbilang(1000000)));
        // Angka 5 juta
        $this->assertEquals('Lima Juta', trim(terbilang(5000000)));
    }

    /**
     * Test terbilang dengan angka negatif
     * Fungsi harus menambahkan prefix "minus"
     */
    public function test_terbilang_handles_negative_numbers()
    {
        // Angka -5
        $this->assertEquals('minus Lima', trim(terbilang(-5)));
        // Angka -100
        $this->assertEquals('minus Seratus', trim(terbilang(-100)));
    }

    /**
     * Test tonumber function - format angka dengan pemisah ribuan
     * Format Indonesia: . sebagai pemisah ribuan
     */
    public function test_tonumber_formats_with_thousands_separator()
    {
        // Angka 1000
        $this->assertEquals('1.000', tonumber(1000));
        // Angka 1000000
        $this->assertEquals('1.000.000', tonumber(1000000));
        // Angka 0
        $this->assertEquals('0', tonumber(0));
    }

    /**
     * Test tonumberround function - format angka dengan 2 desimal
     * Format Indonesia: , sebagai pemisah desimal
     */
    public function test_tonumberround_formats_with_two_decimals()
    {
        // Angka 1000.5
        $result = tonumberround(1000.5);
        $this->assertStringContainsString(',', $result);
        // Angka 0
        $this->assertEquals('0,00', tonumberround(0));
    }

    /**
     * Test getParamsUrl function - build query string dari array
     */
    public function test_getparamsurl_builds_query_string()
    {
        // Array dengan satu parameter
        $result = getParamsUrl(['page' => '1']);
        $this->assertEquals('?page=1', $result);

        // Array dengan dua parameter
        $result = getParamsUrl(['page' => '1', 'limit' => '10']);
        $this->assertEquals('?page=1&limit=10', $result);
    }

    /**
     * Test toMinutes function - konversi waktu string ke menit
     */
    public function test_tominutes_converts_time_string_to_minutes()
    {
        // 1 jam = 60 menit
        $result = toMinutes('01:00:00');
        $this->assertEquals(60, $result);

        // 2 jam 30 menit = 150 menit
        $result = toMinutes('02:30:00');
        $this->assertEquals(150, $result);

        // 0 jam 0 menit = 0
        $result = toMinutes('00:00:00');
        $this->assertEquals(0, $result);
    }

    /**
     * Test my_encrypt dan my_decrypt functions - enkripsi dan dekripsi
     * CATATAN: Fungsi ini membutuhkan Laravel app context (APP_KEY)
     * Untuk test penuh dengan app context, lihat HelperFeatureTest
     */
    public function test_my_encrypt_and_decrypt_require_app_context()
    {
        // Test ini hanya memverifikasi bahwa fungsi terdefinisi
        $this->assertTrue(function_exists('my_encrypt'));
        $this->assertTrue(function_exists('my_decrypt'));
    }

    /**
     * Test change_form function - sanitasi teks untuk form slug
     * Menghapus karakter spesial dan mengganti spasi dengan underscore
     */
    public function test_change_form_sanitizes_text()
    {
        // Teks dengan spasi
        $result = change_form('Nama Lengkap');
        $this->assertEquals('nama_lengkap', $result);

        // Teks dengan karakter spesial
        $result = change_form('Harga @# $pecial!');
        $this->assertEquals('harga__pecial', $result);
    }

    /**
     * Test detect_chat_id function - normalisasi nomor WhatsApp
     */
    public function test_detect_chat_id_normalizes_phone_number()
    {
        // Nomor dengan prefix 08
        $result = detect_chat_id('08123456789');
        $this->assertEquals('628123456789', $result);

        // Nomor dengan prefix 628
        $result = detect_chat_id('628123456789');
        $this->assertEquals('628123456789', $result);
    }

    /**
     * Test ambilNilai function - ekstrak nilai dari format nomor tertentu
     */
    public function test_ambilNilai_extracts_value_from_number_format()
    {
        // Format: 100.3.3.3/{angka}/IPT/436.8.2/{tahun}
        $result = ambilNilai('100.3.3.3/54/IPT/436.8.2/2024');
        $this->assertEquals('54', $result);

        // Format tidak cocok harus return null
        $this->assertNull(ambilNilai('invalid-format'));
    }

    /**
     * Test ambilTahun function - ekstrak tahun dari format nomor
     */
    public function test_ambilTahun_extracts_year_from_number()
    {
        // Format dengan tahun di akhir
        $result = ambilTahun('100.3.3.3/54/IPT/436.8.2/2024');
        $this->assertEquals('2024', $result);

        // Format tidak valid harus return null
        $this->assertNull(ambilTahun('no-year-here'));
    }

    /**
     * Test generateJadwalCicilan function - menghasilkan struktur array
     * CATATAN: Test penuh membutuhkan Laravel app context (Carbon/now())
     * Untuk test lengkap, lihat HelperFeatureTest
     */
    public function test_generateJadwalCicilan_is_callable()
    {
        // Verifikasi fungsi terdefinisi
        $this->assertTrue(function_exists('generateJadwalCicilan'));
    }
}
