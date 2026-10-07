<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\Jabatan;
use Modules\Sdm\Entities\Kpi;
use Yajra\DataTables\Facades\DataTables;

class KpiController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('kpi.index')) {
            return $denied;
        }
        return view('sdm::kpi.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('kpi.create')) {
            return $denied;
        }
        
        $jabatan = Jabatan::active()->get();
        return view('sdm::kpi.create', compact('jabatan'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('kpi.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'nama_kpi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'satuan' => 'required|string|max:50',
            'tipe_target' => 'required|in:bulanan,triwulan,semester,tahunan',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();
            
            // Generate kode KPI
            $validated['kode_kpi'] = Kpi::generateKodeKpi($validated['jabatan_id']);
            
            $kpi = Kpi::create($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data KPI berhasil disimpan'
                ]);
            }
            
            return redirect()->route('kpi.index')->with('success', 'Data KPI berhasil disimpan');
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
        if ($denied = $this->requireAccess('kpi.show')) {
            return $denied;
        }
        
        $kpi = Kpi::with(['jabatan', 'targets'])->findOrFail($id);
        return view('sdm::kpi.show', compact('kpi'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('kpi.edit')) {
            return $denied;
        }
        
        $kpi = Kpi::findOrFail($id);
        $jabatan = Jabatan::active()->get();
        return view('sdm::kpi.create', compact('kpi', 'jabatan'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('kpi.edit')) {
            return $denied;
        }

        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'nama_kpi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'satuan' => 'required|string|max:50',
            'tipe_target' => 'required|in:bulanan,triwulan,semester,tahunan',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();
            
            $kpi = Kpi::findOrFail($id);
            $kpi->update($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data KPI berhasil diupdate'
                ]);
            }
            
            return redirect()->route('kpi.index')->with('success', 'Data KPI berhasil diupdate');
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
        if ($denied = $this->requireAccess('kpi.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            
            $kpi = Kpi::findOrFail($id);
            
            // Check if KPI has targets
            if ($kpi->targets()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'KPI tidak dapat dihapus karena masih memiliki target'
                ], 400);
            }
            
            $kpi->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data KPI berhasil dihapus'
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
        $kpi = Kpi::with(['jabatan', 'creator']);
        
        return DataTables::of($kpi)
            ->addColumn('jabatan_nama', function ($row) {
                return $row->jabatan->nama_jabatan ?? '-';
            })
            ->addColumn('bobot_formatted', function ($row) {
                return $row->bobot . '%';
            })
            ->addColumn('status_badge', function ($row) {
                $color = $row->status == 'active' ? 'success' : 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
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
                                <a class="dropdown-item" href="' . route('kpi.show', $row->id) . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="' . route('kpi.edit', $row->id) . '">
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
            ->rawColumns(['status_badge', 'dibuat_oleh', 'action'])
            ->make(true);
    }
}