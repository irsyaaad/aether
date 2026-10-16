<?php

namespace Modules\Bimbingan\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Modules\Bimbingan\Entities\TransaksiProgram;
use Modules\Olahdata\Entities\TransaksiOlahdata;
use Modules\Pelatihan\Entities\TransaksiPelatihan;

trait NormalizesProgramTransactions
{
    /**
     * Normalize TransaksiProgram (Bimbingan Skripsi) into Fluent object
     */
    protected function normalizeBimbingan(TransaksiProgram $item): Fluent
    {
        $adminFee = (float) ($item->admin_fee ?? 5000);
        $biaya = (float) ($item->biaya ?? 0);
        $totalBayar = (float) ($item->total_bayar ?: ($biaya + $adminFee));
        $tanggal = $item->tanggal_pembelian ?: ($item->created_at ?: now());
        if (!$tanggal instanceof Carbon) {
            $tanggal = Carbon::parse($tanggal);
        }

        return new Fluent([
            'id'                => $item->id,
            'jenis'             => 'bimbingan',
            'jenis_label'       => 'Bimbingan Skripsi',
            'jenis_badge_color' => 'sky',
            'jenis_icon'        => 'graduation-cap',
            'nomor_transaksi'   => $item->nomor_transaksi,
            'nomor_invoice'     => $item->nomor_transaksi,
            'user_id'           => $item->user_id,
            'nama_pemesan'      => $item->nama_pemesan ?: ($item->user->nm_user ?? 'Mahasiswa'),
            'email_pemesan'     => $item->email_pemesan ?: ($item->user->email ?? '-'),
            'telepon_pemesan'   => $item->telepon_pemesan ?: '-',
            'universitas'       => $item->universitas ?: '-',
            'judul_skripsi'     => $item->judul_skripsi,
            'nama_program'      => $item->nama_program ?: 'Bimbingan Skripsi 1-on-1',
            'nama_layanan'      => $item->nama_program ?: 'Bimbingan Skripsi 1-on-1',
            'detail_layanan'    => ($item->total_sesi ?? 1) . ' Sesi Bimbingan (Sisa ' . ($item->sisa_sesi ?? 1) . ')',
            'total_sesi'        => $item->total_sesi ?? 1,
            'sisa_sesi'         => $item->sisa_sesi ?? 1,
            'biaya'             => $biaya,
            'admin_fee'         => $adminFee,
            'total_bayar'       => $totalBayar,
            'formatted_biaya'   => 'Rp ' . number_format($totalBayar, 0, ',', '.'),
            'metode_pembayaran' => $item->metode_pembayaran ?: 'Virtual Account',
            'kode_pembayaran'   => $item->kode_pembayaran ?: '-',
            'status_pembayaran' => $item->status_pembayaran ?: 'Menunggu Pembayaran',
            'status'            => $item->status ?: 'Aktif',
            'status_layanan'    => $item->status ?: 'Aktif',
            'has_bukti'         => !empty($item->bukti_pembayaran),
            'bukti_pembayaran'  => $item->bukti_pembayaran,
            'bukti_pembayaran_url' => $item->bukti_pembayaran_url,
            'catatan'           => $item->catatan,
            'tanggal'           => $tanggal,
            'tanggal_pembelian' => $tanggal,
            'created_at'        => $item->created_at instanceof Carbon ? $item->created_at : ($item->created_at ? Carbon::parse($item->created_at) : $tanggal),
            'user'              => $item->user,
            'raw_model'         => $item,
        ]);
    }

    /**
     * Normalize TransaksiOlahdata (Olah Data Riset) into Fluent object
     */
    protected function normalizeOlahdata(TransaksiOlahdata $item): Fluent
    {
        $adminFee = (float) ($item->admin_fee ?? 5000);
        $biaya = (float) ($item->total_biaya ?? 450000);
        $totalBayar = (float) ($item->total_bayar ?: ($biaya + $adminFee));
        $tanggal = $item->tanggal_pembelian ?: ($item->created_at ?: now());
        if (!$tanggal instanceof Carbon) {
            $tanggal = Carbon::parse($tanggal);
        }

        $namaLayanan = $item->judul_penelitian 
            ? 'Olah Data: ' . $item->judul_penelitian 
            : 'Olah Data Riset (' . ($item->software_digunakan ?: 'SPSS') . ')';

        $detailLayanan = trim(($item->software_digunakan ?: 'Software Riset') . ($item->metode_analisis ? ' • ' . $item->metode_analisis : ''));

        return new Fluent([
            'id'                => $item->id,
            'jenis'             => 'olahdata',
            'jenis_label'       => 'Olah Data',
            'jenis_badge_color' => 'amber',
            'jenis_icon'        => 'bar-chart-2',
            'nomor_transaksi'   => $item->nomor_order ?: ('OD-' . $item->id),
            'nomor_invoice'     => $item->nomor_order ?: ('OD-' . $item->id),
            'user_id'           => $item->user_id,
            'nama_pemesan'      => $item->nama_mahasiswa ?: ($item->user->nm_user ?? 'Mahasiswa'),
            'email_pemesan'     => $item->email ?: ($item->user->email ?? '-'),
            'telepon_pemesan'   => $item->no_whatsapp ?: '-',
            'universitas'       => $item->universitas ?: ($item->jurusan ? 'Jurusan ' . $item->jurusan : '-'),
            'judul_skripsi'     => $item->judul_penelitian,
            'nama_program'      => $namaLayanan,
            'nama_layanan'      => $namaLayanan,
            'detail_layanan'    => $detailLayanan,
            'total_sesi'        => 1,
            'sisa_sesi'         => 1,
            'biaya'             => $biaya,
            'admin_fee'         => $adminFee,
            'total_bayar'       => $totalBayar,
            'formatted_biaya'   => 'Rp ' . number_format($totalBayar, 0, ',', '.'),
            'metode_pembayaran' => $item->metode_pembayaran ?: 'Transfer Bank',
            'kode_pembayaran'   => $item->kode_pembayaran ?: '-',
            'status_pembayaran' => $item->status_pembayaran ?: 'Menunggu Pembayaran',
            'status'            => $item->status_pengerjaan ?: 'Proses Running Data',
            'status_layanan'    => $item->status_pengerjaan ?: 'Proses Running Data',
            'has_bukti'         => !empty($item->bukti_pembayaran),
            'bukti_pembayaran'  => $item->bukti_pembayaran,
            'bukti_pembayaran_url' => $item->bukti_pembayaran_url,
            'catatan'           => $item->catatan_pembayaran ?: $item->catatan_khusus,
            'tanggal'           => $tanggal,
            'tanggal_pembelian' => $tanggal,
            'created_at'        => $item->created_at instanceof Carbon ? $item->created_at : ($item->created_at ? Carbon::parse($item->created_at) : $tanggal),
            'user'              => $item->user,
            'raw_model'         => $item,
        ]);
    }

    /**
     * Normalize TransaksiPelatihan (Kelas & Workshop) into Fluent object
     */
    protected function normalizePelatihan(TransaksiPelatihan $item): Fluent
    {
        $adminFee = (float) ($item->admin_fee ?? 5000);
        $biaya = (float) ($item->biaya ?? 250000);
        $totalBayar = (float) ($item->total_bayar ?: ($biaya + $adminFee));
        $tanggal = $item->tanggal_pembelian ?: ($item->created_at ?: now());
        if (!$tanggal instanceof Carbon) {
            $tanggal = Carbon::parse($tanggal);
        }

        $namaLayanan = $item->judul_kelas ?: 'Workshop & Kelas Riset';
        $detailLayanan = 'Instruktur: ' . ($item->instruktur ?: 'Tim Pengajar Aether') . ($item->kode_kelas ? ' (' . $item->kode_kelas . ')' : '');

        return new Fluent([
            'id'                => $item->id,
            'jenis'             => 'pelatihan',
            'jenis_label'       => 'Kelas & Workshop',
            'jenis_badge_color' => 'purple',
            'jenis_icon'        => 'award',
            'nomor_transaksi'   => $item->nomor_registrasi ?: ('REG-' . $item->id),
            'nomor_invoice'     => $item->nomor_registrasi ?: ('REG-' . $item->id),
            'user_id'           => $item->user_id,
            'nama_pemesan'      => $item->nama_pemesan ?: ($item->user->nm_user ?? 'Peserta'),
            'email_pemesan'     => $item->email_pemesan ?: ($item->user->email ?? '-'),
            'telepon_pemesan'   => $item->telepon_pemesan ?: '-',
            'universitas'       => $item->universitas ?: '-',
            'judul_skripsi'     => null,
            'nama_program'      => $namaLayanan,
            'nama_layanan'      => $namaLayanan,
            'detail_layanan'    => $detailLayanan,
            'total_sesi'        => $item->total_modul ?: 8,
            'sisa_sesi'         => max(0, ($item->total_modul ?: 8) - ($item->modul_selesai ?: 0)),
            'biaya'             => $biaya,
            'admin_fee'         => $adminFee,
            'total_bayar'       => $totalBayar,
            'formatted_biaya'   => 'Rp ' . number_format($totalBayar, 0, ',', '.'),
            'metode_pembayaran' => $item->metode_pembayaran ?: 'Virtual Account',
            'kode_pembayaran'   => $item->kode_pembayaran ?: '-',
            'status_pembayaran' => $item->status_pembayaran ?: 'Menunggu Pembayaran',
            'status'            => $item->status ?: 'Aktif Berjalan',
            'status_layanan'    => $item->status ?: 'Aktif Berjalan',
            'has_bukti'         => !empty($item->bukti_pembayaran),
            'bukti_pembayaran'  => $item->bukti_pembayaran,
            'bukti_pembayaran_url' => $item->bukti_pembayaran_url,
            'catatan'           => $item->catatan,
            'tanggal'           => $tanggal,
            'tanggal_pembelian' => $tanggal,
            'created_at'        => $item->created_at instanceof Carbon ? $item->created_at : ($item->created_at ? Carbon::parse($item->created_at) : $tanggal),
            'user'              => $item->user,
            'raw_model'         => $item,
        ]);
    }

    /**
     * Query and normalize unified invoice records across programs with summary
     */
    protected function getUnifiedInvoices(Request $request, bool $isRole99, int|string|null $userId): array
    {
        $jenisFilter = $request->query('jenis');
        $search = trim($request->query('search', ''));
        $statusPembayaran = $request->query('status_pembayaran');
        $programId = $request->query('program_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $collection = collect();

        // 1. TransaksiProgram (Bimbingan)
        if (!$jenisFilter || $jenisFilter === 'Semua' || $jenisFilter === 'bimbingan') {
            $q = TransaksiProgram::with(['program', 'user']);
            if ($isRole99) {
                $q->where('user_id', $userId);
            }
            if ($search !== '') {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nomor_transaksi', 'like', "%{$search}%")
                        ->orWhere('nama_pemesan', 'like', "%{$search}%")
                        ->orWhere('email_pemesan', 'like', "%{$search}%")
                        ->orWhere('nama_program', 'like', "%{$search}%")
                        ->orWhere('universitas', 'like', "%{$search}%");
                });
            }
            if ($statusPembayaran && $statusPembayaran !== 'Semua') {
                $q->where('status_pembayaran', $statusPembayaran);
            }
            if ($programId && $programId !== 'Semua') {
                $q->where('program_id', $programId);
            }
            if ($startDate) {
                $q->whereDate('tanggal_pembelian', '>=', $startDate);
            }
            if ($endDate) {
                $q->whereDate('tanggal_pembelian', '<=', $endDate);
            }

            foreach ($q->get() as $item) {
                $collection->push($this->normalizeBimbingan($item));
            }
        }

        // 2. TransaksiOlahdata (Olah Data)
        if ((!$jenisFilter || $jenisFilter === 'Semua' || $jenisFilter === 'olahdata') && (!$programId || $programId === 'Semua')) {
            $q = TransaksiOlahdata::with('user');
            if ($isRole99) {
                $q->where('user_id', $userId);
            }
            if ($search !== '') {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nomor_order', 'like', "%{$search}%")
                        ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('judul_penelitian', 'like', "%{$search}%")
                        ->orWhere('software_digunakan', 'like', "%{$search}%")
                        ->orWhere('universitas', 'like', "%{$search}%");
                });
            }
            if ($statusPembayaran && $statusPembayaran !== 'Semua') {
                $q->where('status_pembayaran', $statusPembayaran);
            }
            if ($startDate) {
                $q->whereDate('tanggal_pembelian', '>=', $startDate);
            }
            if ($endDate) {
                $q->whereDate('tanggal_pembelian', '<=', $endDate);
            }

            foreach ($q->get() as $item) {
                $collection->push($this->normalizeOlahdata($item));
            }
        }

        // 3. TransaksiPelatihan (Kelas & Workshop)
        if ((!$jenisFilter || $jenisFilter === 'Semua' || $jenisFilter === 'pelatihan') && (!$programId || $programId === 'Semua')) {
            $q = TransaksiPelatihan::with(['kelas', 'user']);
            if ($isRole99) {
                $q->where('user_id', $userId);
            }
            if ($search !== '') {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nomor_registrasi', 'like', "%{$search}%")
                        ->orWhere('nama_pemesan', 'like', "%{$search}%")
                        ->orWhere('email_pemesan', 'like', "%{$search}%")
                        ->orWhere('judul_kelas', 'like', "%{$search}%")
                        ->orWhere('instruktur', 'like', "%{$search}%")
                        ->orWhere('universitas', 'like', "%{$search}%");
                });
            }
            if ($statusPembayaran && $statusPembayaran !== 'Semua') {
                $q->where('status_pembayaran', $statusPembayaran);
            }
            if ($startDate) {
                $q->whereDate('tanggal_pembelian', '>=', $startDate);
            }
            if ($endDate) {
                $q->whereDate('tanggal_pembelian', '<=', $endDate);
            }

            foreach ($q->get() as $item) {
                $collection->push($this->normalizePelatihan($item));
            }
        }

        // Sort descending by tanggal / timestamp
        $sorted = $collection->sortByDesc(function ($item) {
            return $item->tanggal ? $item->tanggal->timestamp : 0;
        })->values();

        // Calculate summary
        $totalInvoices = $sorted->count();
        $totalNominal = (float) $sorted->sum('total_bayar');

        $lunasItems = $sorted->where('status_pembayaran', 'Lunas');
        $totalLunasCount = $lunasItems->count();
        $totalLunasNominal = (float) $lunasItems->sum('total_bayar');

        $pendingItems = $sorted->where('status_pembayaran', 'Menunggu Pembayaran');
        $totalPendingCount = $pendingItems->count();
        $totalPendingNominal = (float) $pendingItems->sum('total_bayar');

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

        // Paginate using LengthAwarePaginator
        $allowedPerPage = [10, 15, 25, 50, 100];
        $perPage = (int) $request->input('per_page', $request->input('perpage', 10));
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $sorted->forPage($page, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $pagedItems,
            $totalInvoices,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return [
            'invoices' => $paginator,
            'summary'  => $summary,
        ];
    }

    /**
     * Query and normalize unified pembayaran records across all programs (Bimbingan, Olah Data, Kelas & Workshop)
     */
    protected function getUnifiedPembayaran(Request $request, bool $isRole99, int|string|null $userId): array
    {
        $jenisFilter = $request->query('jenis');
        $search = trim($request->query('search', ''));
        $statusPembayaran = $request->query('status_pembayaran');
        $metodePembayaran = $request->query('metode_pembayaran');
        $hasBuktiFilter = $request->query('has_bukti');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $collection = collect();
        $metodeList = collect();

        // 1. TransaksiProgram (Bimbingan Skripsi)
        if (!$jenisFilter || $jenisFilter === 'Semua' || $jenisFilter === 'bimbingan') {
            $q = TransaksiProgram::with(['program', 'user']);

            if ($isRole99) {
                $q->where('user_id', $userId);
            }
            if ($search !== '') {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nomor_transaksi', 'like', "%{$search}%")
                        ->orWhere('nama_pemesan', 'like', "%{$search}%")
                        ->orWhere('kode_pembayaran', 'like', "%{$search}%")
                        ->orWhere('telepon_pemesan', 'like', "%{$search}%")
                        ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                        ->orWhere('nama_program', 'like', "%{$search}%");
                });
            }
            if ($statusPembayaran && $statusPembayaran !== 'Semua') {
                $q->where('status_pembayaran', $statusPembayaran);
            }
            if ($metodePembayaran && $metodePembayaran !== 'Semua') {
                $q->where('metode_pembayaran', $metodePembayaran);
            }
            if ($startDate) {
                $q->whereDate('tanggal_pembelian', '>=', $startDate);
            }
            if ($endDate) {
                $q->whereDate('tanggal_pembelian', '<=', $endDate);
            }

            foreach ($q->get() as $item) {
                $collection->push($this->normalizeBimbingan($item));
                if ($item->metode_pembayaran) {
                    $metodeList->push($item->metode_pembayaran);
                }
            }
        }

        // 2. TransaksiOlahdata (Olah Data)
        if (!$jenisFilter || $jenisFilter === 'Semua' || $jenisFilter === 'olahdata') {
            $q = TransaksiOlahdata::with('user');

            if ($isRole99) {
                $q->where('user_id', $userId);
            }
            if ($search !== '') {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nomor_order', 'like', "%{$search}%")
                        ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('kode_pembayaran', 'like', "%{$search}%")
                        ->orWhere('no_whatsapp', 'like', "%{$search}%")
                        ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                        ->orWhere('judul_penelitian', 'like', "%{$search}%");
                });
            }
            if ($statusPembayaran && $statusPembayaran !== 'Semua') {
                $q->where('status_pembayaran', $statusPembayaran);
            }
            if ($metodePembayaran && $metodePembayaran !== 'Semua') {
                $q->where('metode_pembayaran', $metodePembayaran);
            }
            if ($startDate) {
                $q->whereDate('tanggal_pembelian', '>=', $startDate);
            }
            if ($endDate) {
                $q->whereDate('tanggal_pembelian', '<=', $endDate);
            }

            foreach ($q->get() as $item) {
                $collection->push($this->normalizeOlahdata($item));
                if ($item->metode_pembayaran) {
                    $metodeList->push($item->metode_pembayaran);
                }
            }
        }

        // 3. TransaksiPelatihan (Kelas & Workshop)
        if (!$jenisFilter || $jenisFilter === 'Semua' || $jenisFilter === 'pelatihan') {
            $q = TransaksiPelatihan::with(['kelas', 'user']);

            if ($isRole99) {
                $q->where('user_id', $userId);
            }
            if ($search !== '') {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nomor_registrasi', 'like', "%{$search}%")
                        ->orWhere('nama_pemesan', 'like', "%{$search}%")
                        ->orWhere('kode_pembayaran', 'like', "%{$search}%")
                        ->orWhere('telepon_pemesan', 'like', "%{$search}%")
                        ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                        ->orWhere('judul_kelas', 'like', "%{$search}%");
                });
            }
            if ($statusPembayaran && $statusPembayaran !== 'Semua') {
                $q->where('status_pembayaran', $statusPembayaran);
            }
            if ($metodePembayaran && $metodePembayaran !== 'Semua') {
                $q->where('metode_pembayaran', $metodePembayaran);
            }
            if ($startDate) {
                $q->whereDate('tanggal_pembelian', '>=', $startDate);
            }
            if ($endDate) {
                $q->whereDate('tanggal_pembelian', '<=', $endDate);
            }

            foreach ($q->get() as $item) {
                $collection->push($this->normalizePelatihan($item));
                if ($item->metode_pembayaran) {
                    $metodeList->push($item->metode_pembayaran);
                }
            }
        }

        // Optional filter by has_bukti
        if ($hasBuktiFilter !== null && $hasBuktiFilter !== '' && $hasBuktiFilter !== 'Semua') {
            $hasBuktiBool = in_array($hasBuktiFilter, [1, '1', 'ada', 'true'], true);
            $collection = $collection->filter(function ($item) use ($hasBuktiBool) {
                return (bool) $item->has_bukti === $hasBuktiBool;
            });
        }

        // Sort descending by tanggal
        $sorted = $collection->sortByDesc(function ($item) {
            return $item->tanggal ? $item->tanggal->timestamp : 0;
        })->values();

        // Summary calculations
        $totalTransaksi = $sorted->count();
        $totalBuktiUploaded = $sorted->where('has_bukti', true)->count();
        $lunasItems = $sorted->where('status_pembayaran', 'Lunas');
        $totalNominalMasuk = (float) $lunasItems->sum('total_bayar');
        $totalLunasCount = $lunasItems->count();

        $menungguItems = $sorted->filter(function ($item) {
            return in_array($item->status_pembayaran, ['Menunggu Verifikasi', 'Menunggu Pembayaran']);
        });
        $totalMenungguVerifikasiCount = $menungguItems->count();
        $totalMenungguVerifikasiNominal = (float) $menungguItems->sum('total_bayar');

        $totalDitolakCount = $sorted->where('status_pembayaran', 'Ditolak')->count();

        $summary = [
            'total_transaksi'                  => $totalTransaksi,
            'total_bukti_uploaded'             => $totalBuktiUploaded,
            'total_nominal_masuk'              => $totalNominalMasuk,
            'total_lunas_count'                => $totalLunasCount,
            'total_menunggu_verifikasi_count'  => $totalMenungguVerifikasiCount,
            'total_menunggu_verifikasi_nominal'=> $totalMenungguVerifikasiNominal,
            'total_ditolak_count'              => $totalDitolakCount,
        ];

        // Paginate
        $allowedPerPage = [10, 15, 25, 50, 100];
        $perPage = (int) $request->input('per_page', $request->input('perpage', 10));
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $sorted->forPage($page, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $pagedItems,
            $totalTransaksi,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return [
            'pembayarans' => $paginator,
            'summary'     => $summary,
            'metodeList'  => $metodeList->unique()->values(),
        ];
    }

    /**
     * Find a unified transaction by ID and optional jenis
     */
    protected function findUnifiedTransaction(string|int $id, ?string $jenis = null): ?Fluent
    {
        if ($jenis === 'olahdata') {
            $m = TransaksiOlahdata::with('user')->find($id);
            return $m ? $this->normalizeOlahdata($m) : null;
        }

        if ($jenis === 'pelatihan') {
            $m = TransaksiPelatihan::with(['kelas', 'user'])->find($id);
            return $m ? $this->normalizePelatihan($m) : null;
        }

        if ($jenis === 'bimbingan') {
            $m = TransaksiProgram::with(['program', 'user'])->find($id);
            return $m ? $this->normalizeBimbingan($m) : null;
        }

        // Auto-detect if jenis not specified
        $m = TransaksiProgram::with(['program', 'user'])->find($id);
        if ($m) {
            return $this->normalizeBimbingan($m);
        }

        $m = TransaksiOlahdata::with('user')->find($id);
        if ($m) {
            return $this->normalizeOlahdata($m);
        }

        $m = TransaksiPelatihan::with(['kelas', 'user'])->find($id);
        if ($m) {
            return $this->normalizePelatihan($m);
        }

        return null;
    }

    /**
     * Perform unified verification across programs
     */
    protected function performPaymentVerification(string $jenis, int|string $id, string $statusPembayaran, ?string $catatan = null): array
    {
        if ($jenis === 'olahdata') {
            $record = TransaksiOlahdata::findOrFail($id);
            $record->status_pembayaran = $statusPembayaran;

            if ($statusPembayaran === 'Lunas') {
                $record->status_pengerjaan = 'Proses Running Data';
                if (!$record->tanggal_pembelian) {
                    $record->tanggal_pembelian = now();
                }
            } elseif ($statusPembayaran === 'Ditolak') {
                $record->status_pengerjaan = 'Menunggu Pembayaran';
            } elseif ($statusPembayaran === 'Menunggu Verifikasi') {
                $record->status_pengerjaan = 'Menunggu Verifikasi';
            } else {
                $record->status_pengerjaan = 'Menunggu Pembayaran';
            }

            if (!empty($catatan)) {
                $record->catatan_pembayaran = $catatan;
            }
            $record->save();

            return [
                'success' => true,
                'jenis'   => 'olahdata',
                'nomor'   => $record->nomor_order,
                'status'  => $record->status_pembayaran,
                'message' => "Status verifikasi bukti pembayaran Olah Data #{$record->nomor_order} berhasil diperbarui menjadi [{$record->status_pembayaran}].",
            ];
        }

        if ($jenis === 'pelatihan') {
            $record = TransaksiPelatihan::findOrFail($id);
            $record->status_pembayaran = $statusPembayaran;

            if ($statusPembayaran === 'Lunas') {
                $record->status = 'Aktif Berjalan';
                if (!$record->tanggal_pembelian) {
                    $record->tanggal_pembelian = now();
                }
            } elseif ($statusPembayaran === 'Ditolak') {
                $record->status = 'Menunggu Pembayaran';
            } elseif ($statusPembayaran === 'Menunggu Verifikasi') {
                $record->status = 'Menunggu Verifikasi';
            } else {
                $record->status = 'Menunggu Pembayaran';
            }

            if (!empty($catatan)) {
                $record->catatan = $catatan;
            }
            $record->save();

            return [
                'success' => true,
                'jenis'   => 'pelatihan',
                'nomor'   => $record->nomor_registrasi,
                'status'  => $record->status_pembayaran,
                'message' => "Status verifikasi bukti pembayaran Kelas & Workshop #{$record->nomor_registrasi} berhasil diperbarui menjadi [{$record->status_pembayaran}].",
            ];
        }

        // Default: Bimbingan
        $record = TransaksiProgram::findOrFail($id);
        $record->status_pembayaran = $statusPembayaran;

        if ($statusPembayaran === 'Lunas') {
            $record->status = 'Aktif';
            if (!$record->tanggal_pembelian) {
                $record->tanggal_pembelian = now();
            }
        } elseif ($statusPembayaran === 'Ditolak') {
            $record->status = 'Menunggu Pembayaran';
        } elseif ($statusPembayaran === 'Menunggu Verifikasi') {
            $record->status = 'Menunggu Verifikasi';
        } else {
            $record->status = 'Menunggu Pembayaran';
        }

        if (!empty($catatan)) {
            $record->catatan = $catatan;
        }
        $record->save();

        return [
            'success' => true,
            'jenis'   => 'bimbingan',
            'nomor'   => $record->nomor_transaksi,
            'status'  => $record->status_pembayaran,
            'message' => "Status verifikasi bukti pembayaran Bimbingan #{$record->nomor_transaksi} berhasil diperbarui menjadi [{$record->status_pembayaran}].",
        ];
    }
}
