<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\Karyawan;
use Modules\Sdm\Entities\KpiRealisasi;
use Modules\Sdm\Entities\KpiTarget;
use Yajra\DataTables\Facades\DataTables;

class KpiRealisasiController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('kpi-realisasi.index')) {
            return $denied;
        }
        return view('sdm::kpi-realisasi.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('kpi-realisasi.create')) {
            return $denied;
        }
        
        $kpiTargets = KpiTarget::with(['kpi.jabatan'])->get();
        $karyawan = Karyawan::where('status', 'aktif')->get();
        return view('sdm::kpi-realisasi.create', compact('kpiTargets', 'karyawan'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('kpi-realisasi.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'kpi_target_id' => 'required|exists:kpi_target,id',
            'karyawan_id' => 'required|exists:karyawan,id',
            'realisasi_value' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'tanggal_input' => 'required|date',
            'status' => 'required|in:draft,submitted,approved,rejected',
        ]);

        try {
            DB::beginTransaction();
            
            // Check if realisasi already exists for this target and karyawan
            $exists = KpiRealisasi::where('kpi_target_id', $validated['kpi_target_id'])
                ->where('karyawan_id', $validated['karyawan_id'])
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Realisasi untuk target KPI dan karyawan ini sudah ada'
                ], 400);
            }
            
            $realisasi = KpiRealisasi::create($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Realisasi KPI berhasil disimpan'
                ]);
            }
            
            return redirect()->route('kpi-realisasi.index')->with('success', 'Realisasi KPI berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ], 500);
            }
            
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
        if ($denied = $this->requireAccess('kpi-realisasi.show')) {
            return $denied;
        }
        
        $realisasi = KpiRealisasi::with(['kpiTarget.kpi.jabatan', 'karyawan', 'approver'])->findOrFail($id);
        return view('sdm::kpi-realisasi.show', compact('realisasi'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('kpi-realisasi.edit')) {
            return $denied;
        }
        
        $realisasi = KpiRealisasi::findOrFail($id);
        $kpiTargets = KpiTarget::with(['kpi.jabatan'])->get();
        $karyawan = Karyawan::where('status_kerja', 'aktif')->get();
        return view('sdm::kpi-realisasi.create', compact('realisasi', 'kpiTargets', 'karyawan'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('kpi-realisasi.edit')) {
            return $denied;
        }

        $validated = $request->validate([
            'kpi_target_id' => 'required|exists:kpi_target,id',
            'karyawan_id' => 'required|exists:karyawan,id',
            'realisasi_value' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'tanggal_input' => 'required|date',
            'status' => 'required|in:draft,submitted,approved,rejected',
        ]);

        try {
            DB::beginTransaction();
            
            $realisasi = KpiRealisasi::findOrFail($id);
            
            // Check if realisasi already exists for this target and karyawan (excluding current record)
            $exists = KpiRealisasi::where('kpi_target_id', $validated['kpi_target_id'])
                ->where('karyawan_id', $validated['karyawan_id'])
                ->where('id', '!=', $id)
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Realisasi untuk target KPI dan karyawan ini sudah ada'
                ], 400);
            }
            
            $realisasi->update($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Realisasi KPI berhasil diupdate'
                ]);
            }
            
            return redirect()->route('kpi-realisasi.index')->with('success', 'Realisasi KPI berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengupdate data: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('kpi-realisasi.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            
            $realisasi = KpiRealisasi::findOrFail($id);
            
            // Only allow deletion if status is draft
            if ($realisasi->status !== 'draft') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya realisasi dengan status draft yang dapat dihapus'
                ], 400);
            }
            
            $realisasi->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Realisasi KPI berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve realisasi
     */
    public function approve($id)
    {
        if ($denied = $this->requireAccess('kpi-realisasi.approve')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            
            $realisasi = KpiRealisasi::findOrFail($id);
            
            if ($realisasi->status !== 'submitted') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya realisasi dengan status submitted yang dapat diapprove'
                ], 400);
            }
            
            $realisasi->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Realisasi KPI berhasil diapprove'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal approve realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject realisasi
     */
    public function reject($id)
    {
        if ($denied = $this->requireAccess('kpi-realisasi.reject')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            
            $realisasi = KpiRealisasi::findOrFail($id);
            
            if ($realisasi->status !== 'submitted') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya realisasi dengan status submitted yang dapat direject'
                ], 400);
            }
            
            $realisasi->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Realisasi KPI berhasil direject'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal reject realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $realisasi = KpiRealisasi::with(['kpiTarget.kpi.jabatan', 'karyawan', 'creator']);
        
        return DataTables::of($realisasi)
            ->addColumn('kpi_info', function ($row) {
                $jabatan = $row->kpiTarget->kpi->jabatan->nama_jabatan ?? '-';
                $kpi = $row->kpiTarget->kpi->nama_kpi ?? '-';
                return '<div><strong>' . $kpi . '</strong><br><small class="text-muted">' . $jabatan . '</small></div>';
            })
            ->addColumn('karyawan_nama', function ($row) {
                return $row->karyawan->nama_lengkap ?? '-';
            })
            ->addColumn('periode_info', function ($row) {
                return $row->kpiTarget->tahun . ' - ' . $row->kpiTarget->nama_periode;
            })
            ->addColumn('pencapaian_info', function ($row) {
                $target = number_format($row->kpiTarget->target_value ?? 0, 2);
                $realisasi = number_format($row->realisasi_value, 2);
                $persen = number_format($row->pencapaian_persen ?? 0, 2);
                $satuan = $row->kpiTarget->kpi->satuan ?? '';
                
                return '<div>
                    <strong>Target:</strong> ' . $target . ' ' . $satuan . '<br>
                    <strong>Realisasi:</strong> ' . $realisasi . ' ' . $satuan . '<br>
                    <strong>Pencapaian:</strong> ' . $persen . '%
                </div>';
            })
            ->addColumn('status_badge', function ($row) {
                return '<span class="badge bg-' . $row->status_badge . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('dibuat_oleh', function ($row) {
                $creator = $row->creator->nm_user ?? '-';
                $date = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $creator . '</span><br><small class="text-muted">' . $date . '</small></div>';
            })
            ->addColumn('action', function ($row) {
                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="' . route('kpi-realisasi.show', $row->id) . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Detail
                                </a>
                            </li>';
                
                if ($row->status === 'draft') {
                    $actions .= '
                            <li>
                                <a class="dropdown-item" href="' . route('kpi-realisasi.edit', $row->id) . '">
                                    <i class="fa-solid fa-pen text-warning"></i> Edit
                                </a>
                            </li>';
                }
                
                if ($row->status === 'submitted') {
                    $actions .= '
                            <li>
                                <button class="dropdown-item text-success btn-approve" data-id="' . $row->id . '">
                                    <i class="fa-solid fa-check"></i> Approve
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item text-warning btn-reject" data-id="' . $row->id . '">
                                    <i class="fa-solid fa-times"></i> Reject
                                </button>
                            </li>';
                }
                
                if ($row->status === 'draft') {
                    $actions .= '
                            <li>
                                <button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </li>';
                }
                
                $actions .= '
                        </ul>
                    </div>';
                
                return $actions;
            })
            ->rawColumns(['kpi_info', 'pencapaian_info', 'status_badge', 'dibuat_oleh', 'action'])
            ->make(true);
    }
}