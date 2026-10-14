<?php

namespace Modules\Olahdata\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Olahdata\Entities\TransaksiOlahdata;

class OlahdataController extends Controller
{
    public function index()
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $query = TransaksiOlahdata::orderBy('id', 'desc');
        if ($isRole99) {
            $query->where('user_id', $userId);
        }

        $items = $query->get();

        $projects = [];
        if ($items->isNotEmpty()) {
            foreach ($items as $item) {
                $progress = match ($item->status_pengerjaan) {
                    'Selesai & ACC', 'Selesai'         => 100,
                    'Review Hasil', 'Penyusunan Bab 4' => 85,
                    'Uji Asumsi Klasik'                 => 45,
                    'Proses Running Data'               => 30,
                    default                             => 20,
                };

                $projects[] = [
                    'id'                => $item->id,
                    'nomor_order'       => $item->nomor_order,
                    'judul'             => $item->judul_penelitian,
                    'metode'            => $item->metode_analisis,
                    'software'          => $item->software_digunakan,
                    'dataset'           => $item->file_dataset ?: 'dataset.xlsx',
                    'progress'          => $progress,
                    'status'            => $item->status_pengerjaan,
                    'status_pembayaran' => $item->status_pembayaran ?: 'Menunggu Pembayaran',
                    'total_bayar'       => $item->total_bayar,
                    'analis'            => 'Tim Analis Aether',
                    'updated_at'        => $item->updated_at ? $item->updated_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                ];
            }
        } elseif (!$isRole99 && TransaksiOlahdata::count() === 0) {
            $projects = [
                [
                    'id'         => 101,
                    'judul'      => 'Analisis Jalur Pengaruh Brand Equity Terhadap Loyalitas Pelanggan',
                    'metode'     => 'SEM-PLS (SmartPLS 4)',
                    'software'   => 'SmartPLS',
                    'dataset'    => 'dataset_responden_240.xlsx',
                    'progress'   => 85,
                    'status'     => 'Penyusunan Bab 4',
                    'analis'     => 'Nur Atika Rochmah, M.Si.',
                    'updated_at' => '07 Okt 2026, 09:30 WIB'
                ],
                [
                    'id'         => 102,
                    'judul'      => 'Model Regresi Data Panel Determinan Profitabilitas Bank BUKU IV',
                    'metode'     => 'Regresi Data Panel (Fixed Effect Model)',
                    'software'   => 'EViews 12',
                    'dataset'    => 'data_keuangan_perbankan_2018_2025.csv',
                    'progress'   => 100,
                    'status'     => 'Selesai & ACC',
                    'analis'     => 'Deny Aditya Pratama, S.Stat.',
                    'updated_at' => '06 Okt 2026, 17:00 WIB'
                ],
                [
                    'id'         => 103,
                    'judul'      => 'Uji Beda Pengaruh Pembelajaran Berbasis AI Terhadap Literasi Sains',
                    'metode'     => 'Independent Samples t-Test & ANCOVA',
                    'software'   => 'SPSS 29',
                    'dataset'    => 'nilai_pretest_posttest.sav',
                    'progress'   => 45,
                    'status'     => 'Uji Asumsi Klasik',
                    'analis'     => 'Arrum Rochmani Hidayah, M.Kom.',
                    'updated_at' => '05 Okt 2026, 11:20 WIB'
                ]
            ];
        }

        return view('olahdata::index', compact('projects', 'isRole99'));
    }

    public function create()
    {
        return view('olahdata::create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_riset'     => 'required|string|max:255',
            'metode_analisis' => 'required|string',
            'software'        => 'required|string',
            'dataset'         => 'nullable|file|max:10240',
            'keterangan'      => 'nullable|string',
        ]);

        $fileName = null;
        if ($request->hasFile('dataset')) {
            $file = $request->file('dataset');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->storeAs('datasets', $fileName, 'public');
        }

        $user = Auth::user();
        $nomorOrder = 'OD-' . date('Ym') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $biaya = 450000;
        $adminFee = 5000;

        $transaksi = TransaksiOlahdata::create([
            'nomor_order'        => $nomorOrder,
            'user_id'            => Auth::id(),
            'nama_mahasiswa'     => $user ? ($user->nm_user ?? $user->name ?? 'Mahasiswa') : 'Mahasiswa',
            'email'              => $user ? $user->email : null,
            'no_whatsapp'        => $user ? ($user->telp ?? null) : null,
            'judul_penelitian'   => $validated['judul_riset'],
            'software_digunakan' => $validated['software'],
            'metode_analisis'    => $validated['metode_analisis'],
            'file_dataset'       => $fileName ?: ($request->hasFile('dataset') ? $request->file('dataset')->getClientOriginalName() : 'dataset.xlsx'),
            'catatan_khusus'     => $request->input('keterangan'),
            'status_pengerjaan'  => 'Menunggu Pembayaran',
            'total_biaya'        => $biaya,
            'admin_fee'          => $adminFee,
            'total_bayar'        => $biaya + $adminFee,
            'status_pembayaran'  => 'Menunggu Pembayaran',
            'tanggal_pembelian'  => now(),
        ]);

        return redirect()->route('olahdata.payment', $transaksi->id)
            ->with('info', 'Dataset berhasil disimpan. Silakan pilih metode pembayaran untuk memproses antrean pengerjaan!');
    }

    public function paymentCheckout($id)
    {
        $user = Auth::user();
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $project = TransaksiOlahdata::findOrFail($id);

        if ($isRole99 && $project->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $adminPrice = $project->admin_fee ?: 5000;
        $totalPrice = $project->total_bayar ?: ($project->total_biaya + $adminPrice);

        return view('olahdata::payment', compact('project', 'user', 'adminPrice', 'totalPrice'));
    }

    public function processPayment(Request $request, $id)
    {
        $project = TransaksiOlahdata::findOrFail($id);
        $user = Auth::user();

        $validated = $request->validate([
            'nama_pemesan'      => 'required|string|max:150',
            'email_pemesan'     => 'required|email|max:150',
            'telepon_pemesan'   => 'required|string|max:30',
            'universitas'       => 'nullable|string|max:150',
            'metode_pembayaran' => 'required|string|max:100',
        ], [
            'nama_pemesan.required'      => 'Nama lengkap wajib diisi.',
            'email_pemesan.required'     => 'Email aktif wajib diisi.',
            'telepon_pemesan.required'   => 'Nomor WhatsApp wajib diisi.',
            'metode_pembayaran.required' => 'Pilih salah satu metode pembayaran.',
        ]);

        $adminPrice = 5000;
        $biaya = $project->total_biaya ?: 450000;
        $totalBayar = $biaya + $adminPrice;

        $paymentCode = match ($validated['metode_pembayaran']) {
            'BCA Virtual Account'     => '80777' . rand(10000000, 99999999),
            'Mandiri Virtual Account' => '88908' . rand(10000000, 99999999),
            'BNI Virtual Account'     => '98801' . rand(10000000, 99999999),
            'BRI Virtual Account'     => '12800' . rand(10000000, 99999999),
            'QRIS Instant'            => 'QRIS-' . strtoupper(Str::random(10)),
            'Transfer Bank BCA'       => '7310889201',
            'Transfer Bank Mandiri'   => '1370019283741',
            default                   => '7310889201',
        };

        $project->nama_mahasiswa = $validated['nama_pemesan'];
        $project->email = $validated['email_pemesan'];
        $project->no_whatsapp = $validated['telepon_pemesan'];
        $project->universitas = $validated['universitas'];
        $project->metode_pembayaran = $validated['metode_pembayaran'];
        $project->kode_pembayaran = $paymentCode;
        $project->admin_fee = $adminPrice;
        $project->total_bayar = $totalBayar;
        $project->status_pembayaran = 'Menunggu Pembayaran';
        $project->tanggal_pembelian = now();
        $project->save();

        return redirect()->route('olahdata.payment.instruction', $project->id)
            ->with('info', 'Pesanan olah data dicatat. Silakan selesaikan pembayaran dan upload bukti transfer Anda.');
    }

    public function paymentInstruction($id)
    {
        $project = TransaksiOlahdata::findOrFail($id);
        return view('olahdata::instruction', compact('project'));
    }

    public function gantiMetodeBayar(Request $request, $id)
    {
        $oldProject = TransaksiOlahdata::findOrFail($id);

        $validated = $request->validate([
            'metode_pembayaran' => 'required|string|max:100',
        ]);

        // 1. Update status lama
        $oldProject->status_pembayaran = 'Dibatalkan';
        $oldProject->status_pengerjaan = 'Dibatalkan';
        $oldProject->catatan_pembayaran = 'Dibatalkan karena ganti metode ke ' . $validated['metode_pembayaran'];
        $oldProject->save();

        // 2. Generate kode bayar baru
        $paymentCode = match ($validated['metode_pembayaran']) {
            'BCA Virtual Account'     => '80777' . rand(10000000, 99999999),
            'Mandiri Virtual Account' => '88908' . rand(10000000, 99999999),
            'BNI Virtual Account'     => '98801' . rand(10000000, 99999999),
            'BRI Virtual Account'     => '12800' . rand(10000000, 99999999),
            'QRIS Instant'            => 'QRIS-' . strtoupper(Str::random(10)),
            'Transfer Bank BCA'       => '7310889201',
            'Transfer Bank Mandiri'   => '1370019283741',
            default                   => '7310889201',
        };

        // 3. Buat order baru
        $newNomor = 'OD-' . date('Ym') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $newProject = TransaksiOlahdata::create([
            'nomor_order'        => $newNomor,
            'user_id'            => $oldProject->user_id,
            'nama_mahasiswa'     => $oldProject->nama_mahasiswa,
            'email'              => $oldProject->email,
            'no_whatsapp'        => $oldProject->no_whatsapp,
            'universitas'        => $oldProject->universitas,
            'jurusan'            => $oldProject->jurusan,
            'judul_penelitian'   => $oldProject->judul_penelitian,
            'software_digunakan' => $oldProject->software_digunakan,
            'metode_analisis'    => $oldProject->metode_analisis,
            'file_dataset'       => $oldProject->file_dataset,
            'catatan_khusus'     => $oldProject->catatan_khusus,
            'status_pengerjaan'  => 'Menunggu Pembayaran',
            'total_biaya'        => $oldProject->total_biaya,
            'admin_fee'          => $oldProject->admin_fee,
            'total_bayar'        => $oldProject->total_bayar,
            'metode_pembayaran'  => $validated['metode_pembayaran'],
            'kode_pembayaran'    => $paymentCode,
            'status_pembayaran'  => 'Menunggu Pembayaran',
            'tanggal_pembelian'  => now(),
            'catatan_pembayaran' => 'Pergantian metode dari #' . $oldProject->nomor_order,
        ]);

        return redirect()->route('olahdata.payment.instruction', $newProject->id)
            ->with('info', "Metode pembayaran berhasil diubah ke {$validated['metode_pembayaran']}. Tagihan lama (#{$oldProject->nomor_order}) telah dibatalkan.");
    }

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $project = TransaksiOlahdata::findOrFail($id);

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'catatan'          => 'nullable|string|max:255',
        ], [
            'bukti_pembayaran.required' => 'File bukti transfer wajib diupload.',
            'bukti_pembayaran.image'    => 'File harus berupa gambar.',
            'bukti_pembayaran.max'      => 'Ukuran file maksimal 3 MB.',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = 'bukti_od_' . str_replace(['/', '\\', ' '], '_', $project->nomor_order) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $project->bukti_pembayaran = $path;
        }

        if ($request->filled('catatan')) {
            $project->catatan_pembayaran = $request->catatan;
        }

        $project->status_pembayaran = 'Menunggu Verifikasi';
        $project->status_pengerjaan = 'Menunggu Verifikasi';
        $project->tanggal_pembelian = now();
        $project->save();

        return redirect()->route('olahdata.payment.invoice', $project->id)
            ->with('success', 'Bukti pembayaran berhasil diupload! Tim analis kami akan segera memverifikasi dan memproses dataset Anda.');
    }

    public function paymentInvoice($id)
    {
        $project = TransaksiOlahdata::findOrFail($id);
        return view('olahdata::invoice', compact('project'));
    }

    public function verifikasiPembayaran($id)
    {
        $project = TransaksiOlahdata::findOrFail($id);
        $project->status_pembayaran = 'Lunas';
        $project->status_pengerjaan = 'Proses Running Data';
        $project->save();

        return response()->json([
            'status' => 'success',
            'message' => "Pembayaran order olah data #{$project->nomor_order} berhasil diverifikasi. Status pengerjaan kini PROSES RUNNING DATA!"
        ]);
    }

    public function selesaikanProject($id)
    {
        $project = TransaksiOlahdata::findOrFail($id);
        $project->status_pengerjaan = 'Selesai & ACC';
        $project->save();

        return response()->json([
            'status' => 'success',
            'message' => "Project olah data #{$project->nomor_order} berhasil ditandai SELESAI & ACC!"
        ]);
    }

    public function batalkanPembayaran($id)
    {
        $project = TransaksiOlahdata::findOrFail($id);
        $project->status_pembayaran = 'Dibatalkan';
        $project->status_pengerjaan = 'Dibatalkan';
        $project->save();

        return response()->json([
            'status' => 'success',
            'message' => "Order olah data #{$project->nomor_order} berhasil dibatalkan."
        ]);
    }

    public function diagnosis()
    {
        return view('olahdata::diagnosis');
    }

    public function show($id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $project = TransaksiOlahdata::findOrFail($id);

        if ($isRole99 && $project->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat project ini.');
        }

        return view('olahdata::show', compact('project', 'id', 'isRole99'));
    }
}
