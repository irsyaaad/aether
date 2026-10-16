<?php

namespace Modules\Bimbingan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bimbingan\Traits\NormalizesProgramTransactions;
use Modules\Master\Entities\MasterProgram;

class ProgramInvoiceController extends Controller
{
    use NormalizesProgramTransactions;

    public function index(Request $request)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $unified = $this->getUnifiedInvoices($request, $isRole99, $userId);

        $invoices = $unified['invoices'];
        $summary = $unified['summary'];
        $programs = MasterProgram::select('id', 'nama_program')->orderBy('nama_program')->get();

        return view('bimbingan::invoices.index', compact('invoices', 'summary', 'programs', 'isRole99'));
    }

    public function show(Request $request, $id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $transaksi = $this->findUnifiedTransaction($id, $request->query('jenis'));

        if (!$transaksi) {
            abort(404, 'Data invoice tidak ditemukan.');
        }

        if ($isRole99 && (int)$transaksi->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses melihat invoice transaksi ini.');
        }

        return view('bimbingan::invoices.show', compact('transaksi', 'isRole99'));
    }

    public function print(Request $request, $id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $transaksi = $this->findUnifiedTransaction($id, $request->query('jenis'));

        if (!$transaksi) {
            abort(404, 'Data invoice tidak ditemukan.');
        }

        if ($isRole99 && (int)$transaksi->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses mencetak invoice ini.');
        }

        return view('bimbingan::invoices.print', compact('transaksi'));
    }
}
