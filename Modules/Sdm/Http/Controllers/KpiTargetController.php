<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\Kpi;
use Modules\Sdm\Entities\KpiTarget;
use Yajra\DataTables\Facades\DataTables;

class KpiTargetController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('kpi-target.index')) {
            return $denied;
        }
        return view('sdm::kpi-target.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('kpi-target.create')) {
            return $denied;
        }
        
        $kpi = Kpi::active()->with('jabatan')->get();
        return view('sdm::kpi-target.create', compact('kpi'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('kpi-target.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'kpi_id' => 'required|exists:kpi,id',
            'tahun' => 'required|integer|min:2020|max:2050',
            'periode' => 'required|integer|min:1|max:12',
            'target_value' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            // Check if target already exists for this KPI, year, and period
            $exists = KpiTarget::where('kpi_id', $validated['kpi_id'])
                ->where('tahun', $validated['tahun'])
                ->where('periode', $validated['periode'])
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Target untuk KPI, tahun, dan periode ini sudah ada'
                ], 400);
            }
            
            $target = KpiTarget::create($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Target KPI berhasil disimpan'
                ]);
            }
            
            return redirect()->route('kpi-target.index')->with('success', 'Target KPI berhasil disimpan');
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
        if ($denied = $this->requireAccess('kpi-target.show')) {
            return $denied;
        }
        
        $target = KpiTarget::with(['kpi.jabatan', 'realisasi.karyawan'])->findOrFail($id);
        return view('sdm::kpi-target.show', compact('target'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('kpi-target.edit')) {
            return $denied;
        }
        
        $target = KpiTarget::findOrFail($id);
        $kpi = Kpi::active()->with('jabatan')->get();
        return view('sdm::kpi-target.create', compact('target', 'kpi'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('kpi-target.edit')) {
            return $denied;
        }

        $validated = $request->validate([
            'kpi_id' => 'required|exists:kpi,id',
            'tahun' => 'required|integer|min:2020|max:2050',
            'periode' => 'required|integer|min:1|max:12',
            'target_value' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            $target = KpiTarget::findOrFail($id);
            
            // Check if target already exists for this KPI, year, and period (excluding current record)
            $exists = KpiTarget::where('kpi_id', $validated['kpi_id'])
                ->where('tahun', $validated['tahun'])
                ->where('periode', $validated['periode'])
                ->where('id', '!=', $id)
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Target untuk KPI, tahun, dan periode ini sudah ada'
                ], 400);
            }
            
            $target->update($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Target KPI berhasil diupdate'
                ]);
            }
            
            return redirect()->route('kpi-target.index')->with('success', 'Target KPI berhasil diupdate');
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
        if ($denied = $this->requireAccess('kpi-target.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            
            $target = KpiTarget::findOrFail($id);
            
            // Check if target has realisasi
            if ($target->realisasi()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Target tidak dapat dihapus karena sudah memiliki realisasi'
                ], 400);
            }
            
            $target->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Target KPI berhasil dihapus'
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
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $targets = KpiTarget::with(['kpi.jabatan', 'creator']);
        
        return DataTables::of($targets)
            ->addColumn('kpi_info', function ($row) {
                $jabatan = $row->kpi->jabatan->nama_jabatan ?? '-';
                $kpi = $row->kpi->nama_kpi ?? '-';
                return '<div><strong>' . $kpi . '</strong><br><small class="text-muted">' . $jabatan . '</small></div>';
            })
            ->addColumn('periode_info', function ($row) {
                return $row->tahun . ' - ' . $row->nama_periode;
            })
            ->addColumn('target_formatted', function ($row) {
                return number_format($row->target_value, 2) . ' ' . ($row->kpi->satuan ?? '');
            })
            ->addColumn('dibuat_oleh', function ($row) {
                $creator = $row->creator->nm_user ?? '-';
                $date = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $creator . '</span><br><small class="text-muted">' . $date . '</small></div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="' . route('kpi-target.show', $row->id) . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="' . route('kpi-target.edit', $row->id) . '">
                                    <i class="fa-solid fa-pen text-warning"></i> Edit
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['kpi_info', 'dibuat_oleh', 'action'])
            ->make(true);
    }
}