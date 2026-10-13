<?php
use Illuminate\Support\Str;

if (! function_exists('dateindo')) {
    function dateindo($tanggal)
    {
        if (empty($tanggal)) {
            return '-';
        }

        $date = date("d-m-Y", strtotime($tanggal));

        $bulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $pecahkan = explode('-', $date);

        return $pecahkan[0] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[2];
    }
}

if (! function_exists('parseDateIndo')) {
    function parseDateIndo($tanggal)
    {
        if (empty($tanggal) || $tanggal == '-') {
            return null;
        }

        if ($tanggal instanceof \DateTimeInterface) {
            return $tanggal->format('Y-m-d');
        }

        // Check if already in Y-m-d format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return $tanggal;
        }

        // Handle d-m-Y or d/m/Y format
        if (preg_match('/^(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{4})$/', $tanggal, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }

        // Parse Indonesian date format with month names (e.g., "12 DESEMBER 2030")
        $bulanMap = [
            'JANUARI' => '01', 'JANUARY' => '01',
            'FEBRUARI' => '02', 'FEBRUARY' => '02',
            'MARET' => '03', 'MARCH' => '03',
            'APRIL' => '04',
            'MEI' => '05', 'MAY' => '05',
            'JUNI' => '06', 'JUNE' => '06',
            'JULI' => '07', 'JULY' => '07',
            'AGUSTUS' => '08', 'AUGUST' => '08',
            'SEPTEMBER' => '09', 'SEPT' => '09',
            'OKTOBER' => '10', 'OCTOBER' => '10', 'OCT' => '10',
            'NOVEMBER' => '11', 'NOV' => '11',
            'DESEMBER' => '12', 'DECEMBER' => '12', 'DEC' => '12',
        ];

        $tanggalUpper = strtoupper(trim($tanggal));

        // Try to parse format like "12 DESEMBER 2030"
        if (preg_match('/(\d{1,2})\s+([A-Z]+)\s+(\d{4})/', $tanggalUpper, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = $bulanMap[$matches[2]] ?? null;
            $year = $matches[3];

            if ($month) {
                return $year . '-' . $month . '-' . $day;
            }
        }

        // Try standard parse as fallback
        $ts = strtotime($tanggal);
        if ($ts === false) {
            return null;
        }

        // Ensure we don't return 1970-01-01 if it's not intended
        $parsed = date('Y-m-d', $ts);
        return $parsed == '1970-01-01' && !str_contains($tanggal, '1970') ? null : $parsed;
    }
}

if (! function_exists('getParamsUrl')) {
    function getParamsUrl($params)
    {
        $par = null;
        $i   = 0;
        foreach ($params as $key => $value) {
            if ($i == 0) {
                $par .= "?" . $key . "=" . $value;
            } else {
                $par .= "&" . $key . "=" . $value;
            }
            $i++;
        }
        return $par;
    }
}

if (! function_exists('daydate')) {
    function daydate($tanggal)
    {
        $date = strtolower(date_format(date_create($tanggal), "D"));
        //dd($date);
        $hari = "";
        if ($date == "sun") {
            $hari = "Minggu";
        } elseif ($date == "mon") {
            $hari = "Senin";
        } elseif ($date == "tue") {
            $hari = "Selasa";
        } elseif ($date == "wed") {
            $hari = "Rabu";
        } elseif ($date == "thu") {
            $hari = "Kamis";
        } elseif ($date == "fri") {
            $hari = "Jum'at";
        } elseif ($date == "sat") {
            $hari = "Sabtu";
        } else {
            $hari = "tidak valid";
        }

        return $hari;
    }
}
if (! function_exists('terbilang')) {

    function penyebut($nilai)
    {
        $nilai = (int) floor(abs($nilai));
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp  = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = penyebut($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai / 10) . " Puluh" . penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai / 100) . " Ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai / 1000) . " Ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai / 1000000) . " Juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai / 1000000000) . " Milyar" . penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai / 1000000000000) . " Trilyun" . penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim(penyebut($nilai));
        } else {
            $hasil = trim(penyebut($nilai));
        }
        return $hasil;
    }

}

if (! function_exists('tonumber')) {
    function tonumber($data)
    {

        $data = number_format($data, 0, ',', '.');
        return $data;

    }
}

if (! function_exists('tonumberround')) {
    function tonumberround($data)
    {

        $data = number_format($data, 2, ',', '.');
        return $data;

    }
}

if (! function_exists('tonumberdown')) {
    function tonumberdown($data)
    {

        $data = number_format(floor($data), 0, ',', '.');
        return $data;

    }
}

if (! function_exists('tanggalMerah')) {
    //fungsi check tanggal merah
    function tanggalMerah($value)
    {
        $tgl  = date("Ymd", strtotime($value));
        $tgl1 = date("Y-m-d", strtotime($value));
        //default time zone
        date_default_timezone_set("Asia/Jakarta");
        $array = json_decode(file_get_contents("https://raw.githubusercontent.com/guangrei/Json-Indonesia-holidays/master/calendar.json"), true);
        $d_tgl = false;
        if (isset($array[$tgl])) {
            $d_tgl = true;
        } elseif (date("D", strtotime($tgl1)) === "Sun") {
            $d_tgl = true;
        } else {
            $d_tgl = false;
        }

        return $d_tgl;
    }
}

if (! function_exists('toMinutes')) {
    function toMinutes($str)
    {
        $jam    = (Double) date("H", strtotime($str)) * 60;
        $minute = (Double) date("i", strtotime($str));
        $second = (Double) date("s", strtotime($str)) / 60;

        $total = $jam + $minute + $second;

        return $total;
    }
}

if (! function_exists('toHours')) {
    function toHours($minutes)
    {
        $hours = ($minutes - floor($minutes / 60) * 60);

        return $hours;
    }
}

if (! function_exists('detect_chat_id')) {
    /**
     * Normalize chat id.
     *
     * @param $chatId
     * @return string
     */
    function detect_chat_id($chatId)
    {
        $chatId = str_replace([' ', '+'], '', $chatId ?? '');
        /*if (strpos($chatId, '-') !== false) {
        if (!(strpos($chatId, '@g.us') !== false)) {
        $chatId .= '@g.us';
        }
        } else if (!(strpos($chatId, '@c.us') !== false)) {
        $chatId = preg_replace('/^08/', '628', $chatId);
        $chatId .= '@c.us';
        }*/

        if (! (strpos($chatId, '@c.us') !== false) && (preg_match('/^08/', $chatId) || preg_match('/^628/', $chatId)) && strlen($chatId) <= 15) {
            $chatId = preg_replace('/^08/', '628', $chatId);
            // $chatId .= '@c.us';
        } else if (! (strpos($chatId, '@g.us') !== false) && ! (strpos($chatId, '@c.us') !== false)) {
            // $chatId .= '@g.us';
        }

        return $chatId;
    }
}

if (! function_exists('change_form')) {
    function change_form($teks)
    {
        $teks = preg_replace('/[^\w\s_]/', '', $teks);
        $teks = str_replace(' ', '_', $teks);
        $teks = strtolower($teks);
        return $teks;
    }
}

if (! function_exists('my_encrypt')) {
    function my_encrypt($data)
    {
        $key       = substr(config('app.key'), 0, 16); // Ambil 16 karakter pertama APP_KEY
        $iv        = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-128-CTR', $key, 0, $iv);
        return Str::substr(base64_encode($iv . $encrypted), 0, 20); // Potong ke 20 karakter
    }
}

if (! function_exists('my_decrypt')) {
    function my_decrypt($encrypted)
    {
        $key        = substr(config('app.key'), 0, 16);
        $data       = base64_decode($encrypted);
        $iv         = substr($data, 0, 16);
        $ciphertext = substr($data, 16);
        return openssl_decrypt($ciphertext, 'AES-128-CTR', $key, 0, $iv);
    }
}
if (! function_exists('generateJadwalCicilan')) {
    function generateJadwalCicilan($total, $frekuensi)
    {
        $hasil            = [];
        $tanggalMulai     = now();
        $angsuranPerBulan = floor($total / $frekuensi);
        $sisa             = $total - ($angsuranPerBulan * ($frekuensi - 1));

        for ($i = 0; $i < $frekuensi; $i++) {
            $bulanKe   = $tanggalMulai->copy()->addMonths($i);
            $tahun     = $bulanKe->format('Y');
            $bulanNama = strtoupper(\Carbon\Carbon::create()->month($bulanKe->format('m'))->translatedFormat('F'));

            $angsuran = ($i == $frekuensi - 1) ? $sisa : $angsuranPerBulan;

            $hasil[$tahun][] = [
                'no'                 => $i + 1,
                'tahun'              => $tahun,
                'bulan'              => $bulanNama,
                'pokok_retribusi'    => $angsuran,
                'denda_retribusi'    => 0,
                'angsuran_per_bulan' => $angsuran,
            ];
            $listTahun[$tahun] = $tahun;
        }

        return [
            'tahun'    => $listTahun,
            'angsuran' => $hasil,
        ];
    }
}

if (! function_exists('ambilNilai')) {
    function ambilNilai($nomer)
    {
        // Pola regex:
        // ^100\.3\.3\. -> diawali 100.3.3.
        // (\d+) -> ambil angka berikutnya (misal 54)
        // \/IPT\/ -> harus diikuti /IPT/
        // 436\.8\.2\/ -> harus diikuti 436.8.2/
        // \d+$ -> diakhiri angka berapapun (tahun diabaikan isinya)
        $pattern = '/^100\.3\.3\.3\/(\d+)\/IPT\/436\.8\.2\/\d+$/';

        if (preg_match($pattern, $nomer, $matches)) {
            return $matches[1]; // angka yang ditangkap, misalnya 54
        } else {
            return null; // tidak cocok pola
        }
    }
}

if (! function_exists('ambilTahun')) {
    function ambilTahun($nomer)
    {
        $parts = explode('/', $nomer);
        $tahun = end($parts);
        $tahun = trim($tahun); // hilangkan spasi samping
        if (preg_match('/^\d{4}$/', $tahun)) {
            return $tahun;
        } else {
            return null;
        }
    }
}
