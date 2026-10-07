<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Keuangan\Entities\Ac4;
use Modules\Sdm\Entities\Karyawan;
use Modules\Sdm\Entities\PiutangKaryawan;
use Modules\Sdm\Entities\PiutangKaryawanPembayaran;
use Yajra\DataTables\Facades\DataTables;

class PiutangKaryawanController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.index')) {
            return $denied;
        }

        $data['filter'] = [
            'dr_tgl'        => $request->dr_tgl ?? null,
            'sp_tgl'        => $request->sp_tgl ?? null,
            'status'        => $request->status ?? '',
            'jenis_piutang' => $request->jenis_piutang ?? '',
        ];

        return view('sdm::piutang-karyawan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('piutang-karyawan.create')) {
            return $denied;
        }

        $data['karyawan'] = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $data['kasBank']  = Ac4::where('is_kas', true)->orWhere('is_bank', true)->get();

        return view('sdm::piutang-karyawan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'karyawan_id'     => 'required|exists:karyawan,id',
            'tanggal_piutang' => 'required|date',
            'jumlah_piutang'  => 'required',
            'jenis_piutang'   => 'required|in:kasbon,pinjaman,lainnya',
            'keterangan'      => 'nullable|string|max:1000',
            'jatuh_tempo'     => 'nullable|date|after_or_equal:tanggal_piutang',
            'kas_bank_id'     => 'required|exists:m_ac_4,id_ac',
        ]);

        try {
            DB::beginTransaction();

            $piutang = PiutangKaryawan::create([
                'karyawan_id'     => $validated['karyawan_id'],
                'tanggal_piutang' => $validated['tanggal_piutang'],
                'jumlah_piutang'  => preg_replace('/[^0-9]/', '', $validated['jumlah_piutang']),
                'jenis_piutang'   => $validated['jenis_piutang'],
                'keterangan'      => $validated['keterangan'],
                'jatuh_tempo'     => $validated['jatuh_tempo'],
                'ac_debet'        => 1041,
                'ac_kredit'       => $validated['kas_bank_id'],
            ]);

            DB::commit();
            return redirect()->route('piutang-karyawan.show', $piutang->id)->with('success', 'Piutang karyawan berhasil dibuat');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat piutang: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.index')) {
            return $denied;
        }

        $piutang = PiutangKaryawan::findOrFail($id);
        $this->syncPiutangPaymentStatus($piutang);

        $data['piutang'] = PiutangKaryawan::with(['karyawan', 'creator', 'updater', 'pembayaran' => function ($query) {
            $query->withTrashed();
        }, 'pembayaran.creator', 'pembayaran.acDebet', 'pembayaran.acKredit'])->findOrFail($id);
        $data['kasBank'] = Ac4::where('is_kas', true)->orWhere('is_bank', true)->get();
        return view('sdm::piutang-karyawan.show', $data);
    }

    /**
     * Sinkronisasi nominal terbayar, sisa, dan status berdasarkan pembayaran aktif.
     */
    private function syncPiutangPaymentStatus(PiutangKaryawan $piutang): void
    {
        $totalTerbayar = (float) $piutang->pembayaran()->sum('jumlah_bayar');
        $jumlahPiutang = (float) $piutang->jumlah_piutang;
        $sisaPiutang   = $jumlahPiutang - $totalTerbayar;
        $status        = $sisaPiutang <= 0 ? 'lunas' : 'aktif';

        if ((float) $piutang->jumlah_terbayar !== $totalTerbayar || (float) $piutang->sisa_piutang !== $sisaPiutang || $piutang->status !== $status) {
            PiutangKaryawan::where('id', $piutang->id)->update([
                'jumlah_terbayar' => $totalTerbayar,
                'sisa_piutang'    => $sisaPiutang,
                'status'          => $status,
                'updated_at'      => now(),
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.update')) {
            return $denied;
        }

        $data['piutang'] = PiutangKaryawan::findOrFail($id);

        if (! $data['piutang']->canBeEdited()) {
            return redirect()->back()->with('error', 'Piutang ini tidak dapat diedit');
        }

        $data['karyawan'] = Karyawan::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        $data['kasBank']  = Ac4::where('is_kas', true)->orWhere('is_bank', true)->get();

        return view('sdm::piutang-karyawan.create', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.update')) {
            return $denied;
        }

        $piutang = PiutangKaryawan::findOrFail($id);

        if (! $piutang->canBeEdited()) {
            return redirect()->back()->with('error', 'Piutang ini tidak dapat diedit');
        }

        $validated = $request->validate([
            'karyawan_id'     => 'required|exists:karyawan,id',
            'tanggal_piutang' => 'required|date',
            'jumlah_piutang'  => 'required|numeric|min:1',
            'jenis_piutang'   => 'required|in:kasbon,pinjaman,lainnya',
            'keterangan'      => 'nullable|string|max:1000',
            'jatuh_tempo'     => 'nullable|date|after_or_equal:tanggal_piutang',
            'kas_bank_id'     => 'required|exists:m_ac_4,id_ac',
        ]);

        try {
            DB::beginTransaction();

            $piutang->update([
                'karyawan_id'     => $validated['karyawan_id'],
                'tanggal_piutang' => $validated['tanggal_piutang'],
                'jumlah_piutang'  => preg_replace('/[^0-9]/', '', $validated['jumlah_piutang']),
                'jenis_piutang'   => $validated['jenis_piutang'],
                'keterangan'      => $validated['keterangan'],
                'jatuh_tempo'     => $validated['jatuh_tempo'],
                'ac_debet'        => 1041,
                'ac_kredit'       => $validated['kas_bank_id'],
            ]);

            DB::commit();
            return redirect()->route('piutang-karyawan.show', $piutang->id)->with('success', 'Piutang karyawan berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui piutang: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();

            $piutang = PiutangKaryawan::findOrFail($id);

            if (! $piutang->canBeDeleted()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Piutang ini tidak dapat dihapus karena sudah ada pembayaran',
                ]);
            }

            $piutang->delete();
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Piutang karyawan berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Piutang karyawan gagal dihapus: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Store pembayaran piutang
     */
    public function storePembayaran(Request $request, $id)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.bayar')) {
            return $denied;
        }

        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar'  => 'required',
            'metode_bayar'  => 'required|exists:m_ac_4,id_ac',
            'keterangan'    => 'nullable|string|max:1000',
            'bukti_bayar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $piutang = PiutangKaryawan::findOrFail($id);

            if ($piutang->status !== 'aktif') {
                return redirect()->back()->with('error', 'Piutang ini tidak dapat dibayar');
            }

            $jumlahBayar = $validated['jumlah_bayar'];
            $sisaPiutang = $piutang->sisa_piutang;

            if ($jumlahBayar > $sisaPiutang) {
                return redirect()->back()->with('error', 'Jumlah pembayaran tidak boleh melebihi sisa piutang');
            }

            // Handle file upload
            $buktiBayarPath = null;
            if ($request->hasFile('bukti_bayar')) {
                $file           = $request->file('bukti_bayar');
                $fileName       = time() . '_' . $file->getClientOriginalName();
                $buktiBayarPath = $file->storeAs('piutang-karyawan/bukti-bayar', $fileName, 'public');
            }

            // Create pembayaran record
            $pembayaran = PiutangKaryawanPembayaran::create([
                'piutang_karyawan_id' => $piutang->id,
                'tanggal_bayar'       => $validated['tanggal_bayar'],
                'jumlah_bayar'        => $jumlahBayar,
                'metode_bayar'        => $validated['metode_bayar'],
                'keterangan'          => $validated['keterangan'],
                'bukti_bayar'         => $buktiBayarPath ? basename($buktiBayarPath) : null,
                'ac_debet'            => $validated['metode_bayar'],
                'ac_kredit'           => 1041,
            ]);

            // Update piutang
            $totalTerbayar = $piutang->pembayaran()->sum('jumlah_bayar');
            $piutang->update([
                'jumlah_terbayar' => $totalTerbayar,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran piutang berhasil dicatat');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Delete pembayaran piutang
     */
    public function deletePembayaran($id, $pembayaranId)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.bayar')) {
            return $denied;
        }

        try {
            DB::beginTransaction();

            $piutang    = PiutangKaryawan::findOrFail($id);
            $pembayaran = PiutangKaryawanPembayaran::where('piutang_karyawan_id', $id)
                ->findOrFail($pembayaranId);

            // Delete bukti bayar file if exists
            if ($pembayaran->bukti_bayar) {
                Storage::disk('public')->delete('piutang-karyawan/bukti-bayar/' . $pembayaran->bukti_bayar);
            }

            $pembayaran->delete();

            // Update piutang
            $totalTerbayar = $piutang->pembayaran()->sum('jumlah_bayar');
            $piutang->update([
                'jumlah_terbayar' => $totalTerbayar,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Pembayaran berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus pembayaran: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show form edit pembayaran
     */
    public function editPembayaran($id, $pembayaranId)
    {
        try {
            if ($denied = $this->requireAccess('piutang-karyawan.bayar')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Akses ditolak',
                ], 403);
            }

            $piutang    = PiutangKaryawan::findOrFail($id);
            $pembayaran = PiutangKaryawanPembayaran::where('piutang_karyawan_id', $id)->findOrFail($pembayaranId);
            $kasBank    = Ac4::where('is_kas', true)->orWhere('is_bank', true)->get();

            if ($pembayaran->trashed()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Pembayaran yang dihapus tidak dapat diedit',
                ]);
            }

            return response()->json([
                'status'     => 'success',
                'pembayaran' => [
                    'id'               => $pembayaran->id,
                    'nomor_pembayaran' => $pembayaran->nomor_pembayaran,
                    'tanggal_bayar'    => $pembayaran->tanggal_bayar->format('Y-m-d'),
                    'jumlah_bayar'     => (float) $pembayaran->jumlah_bayar,
                    'keterangan'      => $pembayaran->keterangan,
                    'bukti_bayar'     => $pembayaran->bukti_bayar,
                    'metode_bayar'    => $pembayaran->metode_bayar,
                    'bukti_bayar_url' => $pembayaran->bukti_bayar_url,
                ],
                'piutang' => [
                    'id'           => $piutang->id,
                    'sisa_piutang' => (float) ($piutang->sisa_piutang + $pembayaran->jumlah_bayar),
                ],
                'kasBank' => $kasBank->map(function ($item) use ($pembayaran) {
                    return [
                        'id_ac'    => $item->id_ac,
                        'nama'     => $item->nama,
                        'selected' => $item->id_ac == $pembayaran->metode_bayar,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Update pembayaran piutang
     */
    public function updatePembayaran(Request $request, $id, $pembayaranId)
    {
        if ($denied = $this->requireAccess('piutang-karyawan.bayar')) {
            return $denied;
        }

        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar'  => 'required',
            'metode_bayar'  => 'required|exists:m_ac_4,id_ac',
            'keterangan'    => 'nullable|string|max:1000',
            'bukti_bayar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $piutang    = PiutangKaryawan::findOrFail($id);
            $pembayaran = PiutangKaryawanPembayaran::where('piutang_karyawan_id', $id)->findOrFail($pembayaranId);

            $jumlahBayarLama = $pembayaran->jumlah_bayar;
            $jumlahBayarBaru = $validated['jumlah_bayar'];
            $sisaPiutang     = $piutang->sisa_piutang + $jumlahBayarLama;

            if ($jumlahBayarBaru > $sisaPiutang) {
                return redirect()->back()->with('error', 'Jumlah pembayaran tidak boleh melebihi sisa piutang');
            }

            // Handle file upload
            $buktiBayarPath = $pembayaran->bukti_bayar;
            if ($request->hasFile('bukti_bayar')) {
                // Delete old file
                if ($pembayaran->bukti_bayar) {
                    Storage::disk('public')->delete('piutang-karyawan/bukti-bayar/' . $pembayaran->bukti_bayar);
                }
                $file           = $request->file('bukti_bayar');
                $fileName       = time() . '_' . $file->getClientOriginalName();
                $buktiBayarPath = $file->storeAs('piutang-karyawan/bukti-bayar', $fileName, 'public');
                $buktiBayarPath = basename($buktiBayarPath);
            }

            $pembayaran->update([
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah_bayar'  => $jumlahBayarBaru,
                'metode_bayar'  => $validated['metode_bayar'],
                'keterangan'    => $validated['keterangan'],
                'bukti_bayar'   => $buktiBayarPath,
                'ac_debet'      => $validated['metode_bayar'],
                'ac_kredit'    => 1041,
            ]);

            // Update piutang
            $totalTerbayar = $piutang->pembayaran()->sum('jumlah_bayar');
            $piutang->update([
                'jumlah_terbayar' => $totalTerbayar,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = PiutangKaryawan::with(['karyawan'])
            ->select('piutang_karyawan.*');

        if ($request->dr_tgl && $request->sp_tgl) {
            $query->whereBetween('tanggal_piutang', [$request->dr_tgl, $request->sp_tgl]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->jenis_piutang) {
            $query->where('jenis_piutang', $request->jenis_piutang);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('nomor_piutang', function ($piutang) {
                return '<a href="' . route('piutang-karyawan.show', $piutang->id) . '" class="text-primary fw-bold">' . $piutang->nomor_piutang . '</a>';
            })
            ->editColumn('karyawan', function ($piutang) {
                return $piutang->karyawan->nama_lengkap ?? '-';
            })
            ->editColumn('tanggal_piutang', function ($piutang) {
                return $piutang->tanggal_piutang->format('d M Y');
            })
            ->editColumn('jumlah_piutang', function ($piutang) {
                return 'Rp ' . number_format($piutang->jumlah_piutang, 0, ',', '.');
            })
            ->editColumn('sisa_piutang', function ($piutang) {
                return 'Rp ' . number_format($piutang->sisa_piutang, 0, ',', '.');
            })
            ->editColumn('jenis_piutang', function ($piutang) {
                return $piutang->jenis_piutang_badge;
            })
            ->editColumn('status', function ($piutang) {
                return $piutang->status_badge;
            })
            ->addColumn('action', function ($piutang) {
                $show = route('piutang-karyawan.show', $piutang->id);
                $edit = route('piutang-karyawan.edit', $piutang->id);

                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa-solid fa-eye text-primary"></i> Detail</a></li>';

                if ($piutang->canBeEdited()) {
                    $actions .= '<li><a class="dropdown-item" href="' . $edit . '"><i class="fa-solid fa-pen text-warning"></i> Edit</a></li>';
                }

                if ($piutang->status === 'aktif' && $piutang->sisa_piutang > 0) {
                    $actions .= '<li><a class="dropdown-item btn-bayar" href="#" data-id="' . $piutang->id . '" data-sisa="' . $piutang->sisa_piutang . '"><i class="fa-solid fa-money-bill text-success"></i> Bayar</a></li>';
                }

                if ($piutang->canBeDeleted()) {
                    $actions .= '<li><button class="dropdown-item text-danger btn-delete" data-id="' . $piutang->id . '"><i class="fa-solid fa-trash"></i> Hapus</button></li>';
                }

                $actions .= '</ul></div>';

                return $actions;
            })
            ->rawColumns(['nomor_piutang', 'jenis_piutang', 'status', 'action'])
            ->make(true);
    }
}
