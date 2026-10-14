<?php

namespace Modules\Bimbingan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bimbingan\Entities\TransaksiProgram;

class ProgramPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        // Menu Pembayaran khusus menampilkan bukti bayar yang diupload user
        $query = TransaksiProgram::with(['program', 'user'])
            ->whereNotNull('bukti_pembayaran')
            ->where('bukti_pembayaran', '!=', '')
            ->orderBy('id', 'desc');

        if ($isRole99) {
            $query->where('user_id', $userId);
        }

        // Filter: Search Keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_pemesan', 'like', "%{$search}%")
                    ->orWhere('kode_pembayaran', 'like', "%{$search}%")
                    ->orWhere('telepon_pemesan', 'like', "%{$search}%")
                    ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                    ->orWhere('nama_program', 'like', "%{$search}%");
            });
        }

        // Filter: Status Pembayaran / Verifikasi
        if ($request->filled('status_pembayaran') && $request->status_pembayaran !== 'Semua') {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Filter: Metode Pembayaran
        if ($request->filled('metode_pembayaran') && $request->metode_pembayaran !== 'Semua') {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        // Filter: Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pembelian', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pembelian', '<=', $request->end_date);
        }

        // Summary Statistics untuk Bukti Pembayaran
        $statsQuery = clone $query;
        $totalBuktiUploaded = (clone $statsQuery)->count();
        $totalNominalMasuk = (float) (clone $statsQuery)->where('status_pembayaran', 'Lunas')->sum('total_bayar');

        $totalLunasCount = (clone $statsQuery)->where('status_pembayaran', 'Lunas')->count();
        $totalMenungguVerifikasiCount = (clone $statsQuery)->whereIn('status_pembayaran', ['Menunggu Verifikasi', 'Menunggu Pembayaran'])->count();
        $totalMenungguVerifikasiNominal = (float) (clone $statsQuery)->whereIn('status_pembayaran', ['Menunggu Verifikasi', 'Menunggu Pembayaran'])->sum('total_bayar');
        $totalDitolakCount = (clone $statsQuery)->where('status_pembayaran', 'Ditolak')->count();

        $summary = [
            'total_bukti_uploaded'             => $totalBuktiUploaded,
            'total_nominal_masuk'              => $totalNominalMasuk,
            'total_lunas_count'                => $totalLunasCount,
            'total_menunggu_verifikasi_count'  => $totalMenungguVerifikasiCount,
            'total_menunggu_verifikasi_nominal'=> $totalMenungguVerifikasiNominal,
            'total_ditolak_count'              => $totalDitolakCount,
        ];

        $pembayarans = $query->paginate(15)->appends($request->query());

        $metodeList = TransaksiProgram::whereNotNull('bukti_pembayaran')
            ->where('bukti_pembayaran', '!=', '')
            ->whereNotNull('metode_pembayaran')
            ->where('metode_pembayaran', '!=', '')
            ->select('metode_pembayaran')
            ->distinct()
            ->pluck('metode_pembayaran');

        return view('bimbingan::pembayaran.index', compact('pembayarans', 'summary', 'metodeList', 'isRole99'));
    }

    public function show($id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $transaksi = TransaksiProgram::with(['program', 'user'])
            ->whereNotNull('bukti_pembayaran')
            ->where('bukti_pembayaran', '!=', '')
            ->findOrFail($id);

        if ($isRole99 && (int)$transaksi->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses melihat transaksi pembayaran ini.');
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success'   => true,
                'transaksi' => $transaksi,
            ]);
        }

        return view('bimbingan::pembayaran.show', compact('transaksi', 'isRole99'));
    }

    public function updateStatus(Request $request, $id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        if ($isRole99) {
            abort(403, 'Akses ditolak. Hanya administrator atau bagian keuangan yang dapat mengubah status pembayaran.');
        }

        $transaksi = TransaksiProgram::with('program')->findOrFail($id);

        $validated = $request->validate([
            'status_pembayaran' => 'required|in:Lunas,Menunggu Verifikasi,Ditolak,Menunggu Pembayaran',
            'catatan'          => 'nullable|string|max:255',
        ]);

        $transaksi->status_pembayaran = $validated['status_pembayaran'];

        if ($validated['status_pembayaran'] === 'Lunas') {
            $transaksi->status = 'Aktif';
            if (!$transaksi->tanggal_pembelian) {
                $transaksi->tanggal_pembelian = now();
            }
        } elseif ($validated['status_pembayaran'] === 'Ditolak') {
            $transaksi->status = 'Menunggu Pembayaran';
        } elseif ($validated['status_pembayaran'] === 'Menunggu Verifikasi') {
            $transaksi->status = 'Menunggu Verifikasi';
        } else {
            $transaksi->status = 'Menunggu Pembayaran';
        }

        if ($request->filled('catatan')) {
            $transaksi->catatan = $validated['catatan'];
        }

        $transaksi->save();

        return redirect()->back()->with('success', 'Status verifikasi bukti pembayaran ' . $transaksi->nomor_transaksi . ' berhasil diperbarui menjadi [' . $transaksi->status_pembayaran . '].');
    }
}
