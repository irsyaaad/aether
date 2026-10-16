<?php

namespace Modules\Bimbingan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bimbingan\Traits\NormalizesProgramTransactions;

class ProgramPembayaranController extends Controller
{
    use NormalizesProgramTransactions;

    public function index(Request $request)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $unified = $this->getUnifiedPembayaran($request, $isRole99, $userId);

        $pembayarans = $unified['pembayarans'];
        $summary = $unified['summary'];
        $metodeList = $unified['metodeList'];

        return view('bimbingan::pembayaran.index', compact('pembayarans', 'summary', 'metodeList', 'isRole99'));
    }

    public function show(Request $request, $id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $transaksi = $this->findUnifiedTransaction($id, $request->query('jenis'));

        if (!$transaksi || !$transaksi->has_bukti) {
            abort(404, 'Data bukti pembayaran tidak ditemukan.');
        }

        if ($isRole99 && (int)$transaksi->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses melihat transaksi pembayaran ini.');
        }

        if ($request->wantsJson()) {
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

        $validated = $request->validate([
            'status_pembayaran' => 'required|in:Lunas,Menunggu Verifikasi,Ditolak,Menunggu Pembayaran',
            'jenis'             => 'nullable|in:bimbingan,olahdata,pelatihan',
            'catatan'           => 'nullable|string|max:255',
        ]);

        $jenis = $validated['jenis'] ?? $request->input('jenis', 'bimbingan');

        $result = $this->performPaymentVerification(
            $jenis,
            $id,
            $validated['status_pembayaran'],
            $request->input('catatan')
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
