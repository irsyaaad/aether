<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\Jabatan;
use Yajra\DataTables\Facades\DataTables;

class JabatanController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('jabatan.index')) {
            return $denied;
        }
        return view('sdm::jabatan.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('jabatan.create')) {
            return $denied;
        }
        return view('sdm::jabatan.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('jabatan.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'kode_jabatan' => 'required|string|max:50|unique:jabatan,kode_jabatan',
            'deskripsi' => 'nullable|string',
            'level' => 'nullable|string|max:100',
            'departemen' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $jabatan = Jabatan::create($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data jabatan berhasil disimpan'
                ]);
            }
            
            return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil disimpan');
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
        if ($denied = $this->requireAccess('jabatan.show')) {
            return $denied;
        }
        
        $jabatan = Jabatan::with(['sop', 'jobDescription', 'kpi', 'karyawan'])->findOrFail($id);
        return view('sdm::jabatan.show', compact('jabatan'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('jabatan.edit')) {
            return $denied;
        }
        
        $jabatan = Jabatan::findOrFail($id);
        return view('sdm::jabatan.create', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('jabatan.edit')) {
            return $denied;
        }

        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'kode_jabatan' => 'required|string|max:50|unique:jabatan,kode_jabatan,' . $id,
            'deskripsi' => 'nullable|string',
            'level' => 'nullable|string|max:100',
            'departemen' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $jabatan = Jabatan::findOrFail($id);
            $jabatan->update($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data jabatan berhasil diupdate'
                ]);
            }
            
            return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil diupdate');
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
        if ($denied = $this->requireAccess('jabatan.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            
            $jabatan = Jabatan::findOrFail($id);
            
            // Check if jabatan is being used by karyawan
            if ($jabatan->karyawan()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jabatan tidak dapat dihapus karena masih digunakan oleh karyawan'
                ], 400);
            }
            
            $jabatan->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data jabatan berhasil dihapus'
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
        $jabatan = Jabatan::with('creator');
        
        return DataTables::of($jabatan)
            ->addColumn('status_badge', function ($row) {
                $badge = $row->is_active ? 'success' : 'secondary';
                $text = $row->is_active ? 'Aktif' : 'Tidak Aktif';
                return '<span class="badge bg-' . $badge . '">' . $text . '</span>';
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
                                <a class="dropdown-item" href="' . route('jabatan.show', $row->id) . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="' . route('jabatan.edit', $row->id) . '">
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