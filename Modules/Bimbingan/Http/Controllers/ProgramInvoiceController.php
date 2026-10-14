<?php

namespace Modules\Bimbingan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bimbingan\Entities\TransaksiProgram;
use Modules\Master\Entities\MasterProgram;

class ProgramInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $query = TransaksiProgram::with(['program', 'user'])
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
                    ->orWhere('email_pemesan', 'like', "%{$search}%")
                    ->orWhere('nama_program', 'like', "%{$search}%")
                    ->orWhere('universitas', 'like', "%{$search}%");
            });
        }

        // Filter: Status Pembayaran
        if ($request->filled('status_pembayaran') && $request->status_pembayaran !== 'Semua') {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Filter: Program
        if ($request->filled('program_id') && $request->program_id !== 'Semua') {
            $query->where('program_id', $request->program_id);
        }

        // Filter: Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pembelian', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pembelian', '<=', $request->end_date);
        }

        // Recap Statistics
        $statsQuery = clone $query;
        $totalInvoices = (clone $statsQuery)->count();
        $totalNominal = (float) (clone $statsQuery)->sum('total_bayar');

        $totalLunasCount = (clone $statsQuery)->where('status_pembayaran', 'Lunas')->count();
        $totalLunasNominal = (float) (clone $statsQuery)->where('status_pembayaran', 'Lunas')->sum('total_bayar');

        $totalPendingCount = (clone $statsQuery)->where('status_pembayaran', 'Menunggu Pembayaran')->count();
        $totalPendingNominal = (float) (clone $statsQuery)->where('status_pembayaran', 'Menunggu Pembayaran')->sum('total_bayar');

        $avgNominal = $totalInvoices > 0 ? ($totalNominal / $totalInvoices) : 0;

        $summary = [
            'total_invoices'        => $totalInvoices,
            'total_nominal'         => $totalNominal,
            'total_lunas_count'     => $totalLunasCount,
            'total_lunas_nominal'   => $totalLunasNominal,
            'total_pending_count'   => $totalPendingCount,
            'total_pending_nominal' => $totalPendingNominal,
            'avg_nominal'           => $avgNominal,
        ];

        $invoices = $query->paginate(15)->appends($request->query());
        $programs = MasterProgram::select('id', 'nama_program')->orderBy('nama_program')->get();

        return view('bimbingan::invoices.index', compact('invoices', 'summary', 'programs', 'isRole99'));
    }

    public function show($id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $transaksi = TransaksiProgram::with(['program', 'user'])->findOrFail($id);

        if ($isRole99 && (int)$transaksi->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses melihat invoice transaksi ini.');
        }

        return view('bimbingan::invoices.show', compact('transaksi', 'isRole99'));
    }

    public function print($id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $transaksi = TransaksiProgram::with(['program', 'user'])->findOrFail($id);

        if ($isRole99 && (int)$transaksi->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses mencetak invoice ini.');
        }

        return view('bimbingan::invoices.print', compact('transaksi'));
    }
}
