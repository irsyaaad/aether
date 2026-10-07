<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Sdm\Entities\Izin;
use Modules\Sdm\Entities\Karyawan;
use Yajra\DataTables\Facades\DataTables;

class IzinController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('izin.index')) return $denied;
        
        $data['filter'] = [
            'nm_filter' => 'filter-do',
            'dr_tgl' => $request->dr_tgl ?? date('Y-m-01'),
            'sp_tgl' => $request->sp_tgl ?? date('Y-m-d'),
        ];
        return view('sdm::izin.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('izin.create')) return $denied;
        
        return view('sdm::izin.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('izin.create')) return $denied;

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|in:sakit,cuti,izin_khusus,dinas_luar',
            'alasan' => 'required|string|max:1000',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Calculate jumlah_hari
            $tanggalMulai = \Carbon\Carbon::parse($validated['tanggal_mulai']);
            $tanggalSelesai = \Carbon\Carbon::parse($validated['tanggal_selesai']);
            $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            $izin = Izin::create([
                'kode_izin' => Izin::generateKodeIzin(),
                'karyawan_id' => $validated['karyawan_id'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'jumlah_hari' => $jumlahHari,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'status' => 'pending',
            ]);

            // Handle file upload
            if ($request->hasFile('dokumen_pendukung')) {
                $file = $request->file('dokumen_pendukung');
                $path = $file->store('izin_documents', 'public');
                $izin->update(['dokumen_pendukung' => $path]);
            }

            DB::commit();
            return redirect()->route('izin.show', $izin->id)->with('success', 'Data izin berhasil disimpan');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('izin.index')) return $denied;
        
        $data['izin'] = Izin::with(['karyawan', 'approver', 'creator'])->findOrFail($id);
        return view('sdm::izin.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('izin.edit')) return $denied;
        
        $data['izin'] = Izin::findOrFail($id);
        return view('sdm::izin.create', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('izin.edit')) return $denied;

        $izin = Izin::findOrFail($id);
        
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|in:sakit,cuti,izin_khusus,dinas_luar',
            'alasan' => 'required|string|max:1000',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Calculate jumlah_hari
            $tanggalMulai = \Carbon\Carbon::parse($validated['tanggal_mulai']);
            $tanggalSelesai = \Carbon\Carbon::parse($validated['tanggal_selesai']);
            $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            $izin->update([
                'karyawan_id' => $validated['karyawan_id'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'jumlah_hari' => $jumlahHari,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
            ]);

            // Handle file upload
            if ($request->hasFile('dokumen_pendukung')) {
                // Delete old file if exists
                if ($izin->dokumen_pendukung) {
                    Storage::disk('public')->delete($izin->dokumen_pendukung);
                }
                
                $file = $request->file('dokumen_pendukung');
                $path = $file->store('izin_documents', 'public');
                $izin->update(['dokumen_pendukung' => $path]);
            }

            DB::commit();
            return redirect()->route('izin.show', $izin->id)->with('success', 'Data izin berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('izin.delete')) return $denied;

        try {
            DB::beginTransaction();
            $izin = Izin::findOrFail($id);
            
            // Delete file if exists
            if ($izin->dokumen_pendukung) {
                Storage::disk('public')->delete($izin->dokumen_pendukung);
            }
            
            $izin->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data izin berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Data izin gagal dihapus: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Approve or reject izin
     */
    public function updateStatus(Request $request, $id)
    {
        if ($denied = $this->requireAccess('izin.approve')) return $denied;

        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_atasan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            $izin = Izin::findOrFail($id);
            
            $izin->update([
                'status' => $validated['status'],
                'catatan_atasan' => $validated['catatan_atasan'],
                'disetujui_oleh' => Auth::user()->id_user,
                'tanggal_disetujui' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Status izin berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = Izin::with(['karyawan', 'approver'])
            ->select('izin.*');

        if ($request->dr_tgl && $request->sp_tgl) {
            $query->whereBetween('tanggal_mulai', [$request->dr_tgl, $request->sp_tgl]);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('kode_izin', function ($izin) {
                return '<a href="' . route('izin.show', $izin->id) . '" class="text-primary fw-bold">' . $izin->kode_izin . '</a>';
            })
            ->editColumn('karyawan', function ($izin) {
                return $izin->karyawan->nama_lengkap ?? '-';
            })
            ->editColumn('tanggal', function ($izin) {
                return date('d M Y', strtotime($izin->tanggal_mulai)) . ' - ' . date('d M Y', strtotime($izin->tanggal_selesai));
            })
            ->editColumn('jenis_izin', function ($izin) {
                return $izin->jenis_izin_label;
            })
            ->editColumn('status', function ($izin) {
                return $izin->status_badge;
            })
            ->addColumn('action', function ($izin) {
                $show = route('izin.show', $izin->id);
                $edit = route('izin.edit', $izin->id);

                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa-solid fa-eye text-primary"></i> Detail</a></li>';
                
                if ($izin->status === 'pending') {
                    $actions .= '<li><a class="dropdown-item" href="' . $edit . '"><i class="fa-solid fa-pen text-warning"></i> Edit</a></li>';
                }
                
                $actions .= '<li><button class="dropdown-item text-danger btn-delete" data-id="' . $izin->id . '"><i class="fa-solid fa-trash"></i> Hapus</button></li>
                        </ul>
                    </div>';

                return $actions;
            })
            ->rawColumns(['kode_izin', 'status', 'action'])
            ->make(true);
    }
}
