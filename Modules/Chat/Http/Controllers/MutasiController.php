<?php

namespace Modules\Chat\Http\Controllers;

use Auth;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Chat\Entities\Mutasi;
use Modules\Mutasi\Entities\MasterTipeMutasi;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    protected $clientKey = 'd64986b9-5e21-4eba-9388-982684170415';
    protected $clientSecret = '0f9f49b8-8f33-44a3-b1a4-5622332cb241'; // jika dibutuhkan untuk signature
    protected $partnerId = '43a79851-b0ac-437d-9e02-20406c3d1499'; // sesuai dokumentasi kamu
    protected $baseUrl = 'https://majapahit.bankjatim.co.id/snap/rest'; // ganti jika ada endpoint lain
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $dr_tgl = $request->dr_tgl ?? date('Y-m-d');
        $sp_tgl = $request->sp_tgl ?? date('Y-m-d');
        $data['type_mutasi'] = MasterTipeMutasi::all();
        $data['filter'] = [
            'dr_tgl' => $dr_tgl,
            'sp_tgl' => $sp_tgl,
        ];
        return view('chat::mutasi.index', $data);
    }

    public function get_data(Request $request)
    {
        $mutasi = Mutasi::with('type')->select(
            'id',
            'tanggal',
            'jam',
            'keterangan',
            'jenis',
            'mutasi'
        );

        if ($request->dr_tgl) {
            $mutasi = $mutasi->where('tanggal', '>=', $request->dr_tgl);
        }
        if ($request->sp_tgl) {
            $mutasi = $mutasi->where('tanggal', '<=', $request->sp_tgl);
        }
        if ($request->type_mutasi) {
            $mutasi = $mutasi->where('jenis', $request->type_mutasi);
        }

        $mutasi = $mutasi->where('jenis', '!=', 99); // tidak ambil jenis 99 (verifikasi)

        $data = $mutasi->orderBy('tanggal', 'ASC')->orderBy('jam', 'ASC')->get();
        $totalMutasi = $data->sum('mutasi');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                return dateindo($row->tanggal);
            })
            ->addColumn('jenis', function ($row) {
                $html = '';
                if (isset($row->type->nama)) {
                    $html .= '<span class="text-success">' . $row->type->nama . '</span>';
                } else {
                    $html .= '<span class="text-danger">Belum Diketahui</span>';
                }
                return $html;
            })
            ->addColumn('mutasi', function ($row) {
                return toNumber($row->mutasi);
            })
            ->addColumn('action', function ($row) {
                $html = '';
                $html .= '<button class="btn btn-sm btn-warning btn-change" title="Rubah Jenis Mutasi" data-id-mutasi="' . $row->id . '"><i class="fa-solid fa-pencil"></i></button>';
                return $html;
            })
            ->with([
                'total_mutasi' => $totalMutasi
            ])
            ->rawColumns(['action', 'tanggal', 'jenis'])
            ->make(true);
    }

    public function dashboard_pad(Request $request)
    {
        $dr_tgl = $request->dr_tgl ?? date('Y-m-d');
        $sp_tgl = $request->sp_tgl ?? date('Y-m-d');

        $data['filter'] = [
            'dr_tgl' => $dr_tgl,
            'sp_tgl' => $sp_tgl,
        ];
        return view('chat::dashboard-pad.index', $data);
    }

    public function changeType(Request $request)
    {
        $validated = $request->validate([
            'id_mutasi' => 'required',
            'type' => 'required',
        ]);

        // Simpan ke database
        try {
            DB::beginTransaction();
            $mutasi = Mutasi::findOrFail($validated['id_mutasi']);
            $mutasi->jenis = $validated['type'] ?? 0;
            $mutasi->updated_by = Auth::user()->id_user;
            $mutasi->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Pergantian Mutasi Gagal' . $e->getMessage(),
                'data' => $mutasi,
            ]);
        }
        // Kembalikan response JSON
        return response()->json([
            'message' => 'Mutasi berhasil di rubah!',
            'data' => $mutasi,
        ]);
    }

    public function calculation(Request $request)
    {
        $data['ipt'] = $this->calculateFilter(1, $request);
        $data['ipr'] = $this->calculateFilter(3, $request);
        $data['gedung'] = $this->calculateFilter(4, $request);
        $data['lain'] = $this->calculateFilter(0, $request);
        $data['chartIpt'] = Mutasi::getDataDay(1, date('Y-m-01'), date('Y-m-d'));
        $data['chartIptBulan'] = Mutasi::getDataMonth(1, date('Y-01-01'), date('Y-m-d'));
        $data['chartLain'] = Mutasi::getDataDay(100, $request->dr_tgl, $request->sp_tgl);

        return response()->json([
            'data' => $data,
        ]);
    }

    public function calculateFilter($id_jenis, $request)
    {
        return Mutasi::where('jenis', $id_jenis)
            ->where('jenis', '<', 99)
            ->where('tanggal', '>=', $request->dr_tgl)
            ->where('tanggal', '<=', $request->sp_tgl)
            ->sum('mutasi');
    }

    public function syncMutasi(Request $request)
    {
        $tgl = $request->tgl ?? date('Y-m-d'); // default hari ini
        $mulai = Carbon::parse($tgl)->subDay()->toDateString(); // kurangi 1 hari
        $token = $this->getToken();
        $authorization = $token['accessToken'];
        $start = $this->makeDateTime($mulai, '08:00:00'); // 26 Juni 2025, 08:00 WIB
        $end = $this->makeDateTime($tgl, '09:00:00');   // 27 Juni 2025, 09:00 WIB
        $partnerReferenceNo = now('Asia/Jakarta')->format('YmdHis');

        $responses = $this->sendBankStatementWithCurl(
            $authorization,
            $this->clientSecret,
            [
                "partnerReferenceNo" => $partnerReferenceNo . '0001',
                "accountNo" => "0011255809",
                "fromDateTime" => $start->toIso8601String(),
                "toDateTime" => $end->toIso8601String()
            ]
        );
        if ($responses['responseMessage'] == 'Success') {
            foreach ($responses['detailData'] as $item) {
                $transactionDate = Carbon::parse($item['transactionDate']);
                $jenis = $this->cekJenisKeterangan($item['remark']);
                $id = Carbon::parse($item['transactionDate'])->format('YmdHis') . '-' . $item['transactionId'];

                Mutasi::firstOrCreate(
                    ['id' => $id], // kondisi pencarian
                    [ // data baru jika tidak ditemukan
                        'tanggal' => $transactionDate->toDateString(),
                        'jam' => $transactionDate->toTimeString(),
                        'keterangan' => $item['remark'],
                        'mutasi' => $item['amount']['value'],
                        'jenis' => $jenis,
                    ]
                );
            }
        }

        return true;
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('chat::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('chat::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('chat::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function tes_api(Request $request)
    {
        $token = $this->getToken();
        $authorization = $token['accessToken'];
        $dr_tgl = $request->dr_tgl ?? date('Y-m-d'); // default hari ini
        $sp_tgl = $request->sp_tgl ?? date('Y-m-d'); // default hari ini

        $start = $this->makeDateTime($dr_tgl, '08:00:00'); // 26 Juni 2025, 08:00 WIB
        $end = $this->makeDateTime($sp_tgl, '09:00:00');   // 27 Juni 2025, 09:00 WIB
        $partnerReferenceNo = now('Asia/Jakarta')->format('YmdHis');

        $responses = $this->sendBankStatementWithCurl(
            $authorization,
            $this->clientSecret,
            [
                "partnerReferenceNo" => $partnerReferenceNo . '0001',
                "accountNo" => "0011255809",
                "fromDateTime" => $start->toIso8601String(),
                "toDateTime" => $end->toIso8601String()
            ]
        );
        if (isset($responses['responseMessage']) && $responses['responseMessage'] == 'Success') {
            if (isset($responses['detailData'])) {
                foreach ($responses['detailData'] as $item) {
                    $transactionDate = Carbon::parse($item['transactionDate']);
                    $jenis = $this->cekJenisKeterangan($item['remark']);
                    $id = Carbon::parse($item['transactionDate'])->format('YmdHis') . '-' . $item['transactionId'];

                    Mutasi::firstOrCreate(
                        ['id' => $id], // kondisi pencarian
                        [ // data baru jika tidak ditemukan
                            'tanggal' => $transactionDate->toDateString(),
                            'jam' => $transactionDate->toTimeString(),
                            'keterangan' => $item['remark'],
                            'mutasi' => $item['amount']['value'],
                            'jenis' => $jenis,
                        ]
                    );
                }
            } else {
                return redirect('list-mutasi')->with('error', 'Gagal mengambil data mutasi : ' . json_encode($responses, JSON_PRETTY_PRINT));
            }

        } else {
            Log::error('API request failed', $responses);
            return redirect('list-mutasi')->with('error', 'Gagal mengambil data mutasi: ' . json_encode($responses, JSON_PRETTY_PRINT));
        }

        // dd($responses);
        return redirect('list-mutasi')->with('success', 'Berhasil mengambil data mutasi');
    }

    function loopBankStatementRange($startTime = '07:00', $endTime = '08:00', $accessToken, $clientSecret)
    {
        $responses = [];

        // Set awal dan akhir waktu dalam zona Asia/Jakarta
        $start = Carbon::createFromFormat('H:i', $startTime, 'Asia/Jakarta')->setSeconds(0);
        $end = Carbon::createFromFormat('H:i', $endTime, 'Asia/Jakarta')->setSeconds(0);

        while ($start < $end) {
            $currentStart = $start->copy();
            $currentEnd = $start->copy()->addMinutes(3);

            // Buat payload
            $payload = [
                "partnerReferenceNo" => now('Asia/Jakarta')->format('YmdHis'),
                "accountNo" => "0011255809",
                "fromDateTime" => $currentStart->toIso8601String(),
                "toDateTime" => $currentEnd->toIso8601String(),
            ];

            // Kirim request dan simpan hasilnya
            $response = $this->sendBankStatementWithCurl($accessToken, $clientSecret, $payload);
            $responses[] = [
                'from' => $payload['fromDateTime'],
                'to' => $payload['toDateTime'],
                'response' => $response
            ];

            // Geser ke 3 menit berikutnya
            $start->addMinutes(3);
        }

        return $responses;
    }

    function sendBankStatementWithCurl(string $accessToken, string $clientSecret, array $payload)
    {
        $httpMethod = 'POST';
        $relativeUrl = '/api/v1.0/bank-statement';
        $baseUrl = $this->baseUrl; // ganti jika ada endpoint lain
        $fullUrl = $baseUrl . $relativeUrl;

        $timestamp = now('Asia/Jakarta')->toIso8601String();
        $externalId = now('Asia/Jakarta')->format('YmdHis');
        $partnerId = $this->partnerId; // sesuai dokumentasi kamu
        $channelId = '00001';

        $jsonBody = json_encode($payload);
        $stringToSign = $this->generateStringToSign($httpMethod, $relativeUrl, $accessToken, $jsonBody, $timestamp);
        $signature = $this->generateXSignature($stringToSign, $clientSecret);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature,
            'X-PARTNER-ID: ' . $partnerId,
            'X-EXTERNAL-ID: ' . $externalId,
            'CHANNEL-ID: ' . $channelId,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonBody,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return $err ? ['error' => $err] : json_decode($response, true);
    }

    function sendBalanceInquiry(array $payload, string $accessToken, string $clientSecret)
    {
        $httpMethod = 'POST';
        $relativeUrl = '/api/v1.0/balance-inquiry';
        $baseUrl = 'https://sriwijaya.bankjatim.co.id/snap/rest'; // ganti sesuai environment
        $timestamp = now('Asia/Jakarta')->toIso8601String(); // ISO-8601 dengan zona waktu
        $externalId = now('Asia/Jakarta')->format('YmdHis');
        $partnerId = $this->partnerId; // sesuai dokumentasi kamu

        $jsonBody = json_encode($payload);

        // 1. Buat StringToSign
        $stringToSign = $this->generateStringToSign($httpMethod, $relativeUrl, $accessToken, $jsonBody, $timestamp);

        // 2. Buat Signature
        $signature = $this->generateXSignature($stringToSign, $clientSecret);

        // 3. Kirim request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => $partnerId,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID' => '00001',
        ])->post($baseUrl . $relativeUrl, $payload);

        return $response->json();
    }

    public function getToken()
    {
        $clientKey = $this->clientKey;
        $clientSecret = $this->clientSecret;
        $url = 'https://majapahit.bankjatim.co.id/snap/rest/access_token/v1.0/b2b';
        $timestamp = now()->toIso8601String(); // Laravel helper
        $body = [
            "grantType" => "client_credentials",
            "additionalInfo" => new \stdClass()
        ];

        // Misalnya signature = HMAC_SHA256(body + timestamp + clientKey)
        $signature = $this->generateToken($clientSecret, $clientKey, $timestamp);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-TIMESTAMP' => $timestamp,
            'X-CLIENT-KEY' => $clientKey,
            'X-SIGNATURE' => $signature,
        ])->post($url, $body);

        $token = $response->json();
        return $token;
    }

    public function generateToken(string $clientSecret, string $clientId, string $timestamp): string
    {
        $stringToSign = $clientId . '|' . $timestamp;

        // Generate HMAC-SHA512 signature lalu encode base64
        $signature = base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));

        return $signature;
    }

    function generateStringToSign(string $httpMethod, string $relativeUrl, string $accessToken, ?string $jsonBody, string $timestamp): string
    {
        // Jika tidak ada body, gunakan string kosong
        $minifiedJson = $jsonBody ? preg_replace('/\s+/', '', $jsonBody) : '';

        // Hash SHA256 dalam bentuk lowercase hexadecimal
        $hashedBody = strtolower(hash('sha256', $minifiedJson));

        // Gabungkan sesuai format
        $stringToSign = "{$httpMethod}:{$relativeUrl}:{$accessToken}:{$hashedBody}:{$timestamp}";

        return $stringToSign;
    }

    function generateXSignature(string $stringToSign, string $clientSecret): string
    {
        // Hitung HMAC-SHA512, hasil biner (raw_output = true)
        $hmac = hash_hmac('sha512', $stringToSign, $clientSecret, true);

        // Encode hasilnya ke base64
        return base64_encode($hmac);
    }

    function makeDateTime($date, $time, $timezone = 'Asia/Jakarta')
    {
        return Carbon::parse("{$date} {$time}", $timezone);
    }

    function cekJenisKeterangan(string $keterangan): int
    {
        $keterangan = strtoupper($keterangan); // supaya pencarian case-insensitive

        $keywordJenisMap = [
            'TGH' => 1,
            'MB:93600' => 4,
            'GEDUNG' => 4,
            'MB:QR' => 1,
            'ST/SSRD' => 1,
            'HGB' => 1,
            'SR/SSRD' => 3,
            'AUTOSETTLE' => 99
        ];

        foreach ($keywordJenisMap as $keyword => $jenis) {
            if (strpos($keterangan, $keyword) !== false) {
                return $jenis;
            }
        }

        return 0;
    }

}
