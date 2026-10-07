<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Sdm\Entities\Jabatan;
use Modules\Sdm\Entities\Sop;
use Yajra\DataTables\Facades\DataTables;

class SopController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('sop.index')) {
            return $denied;
        }
        return view('sdm::sop.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('sop.create')) {
            return $denied;
        }
        
        $jabatan = Jabatan::active()->get();
        return view('sdm::sop.create', compact('jabatan'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('sop.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'judul_sop' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'prosedur' => 'required|string',
            'versi' => 'required|string|max:20',
            'tanggal_berlaku' => 'required|date',
            'tanggal_review' => 'nullable|date|after:tanggal_berlaku',
            'status' => 'required|in:draft,active,inactive,archived',
            'dokumen_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            DB::beginTransaction();
            
            // Generate kode SOP
            $validated['kode_sop'] = Sop::generateKodeSop($validated['jabatan_id']);
            
            // Handle file upload
            if ($request->hasFile('dokumen_file')) {
                $validated['dokumen_file'] = $request->file('dokumen_file')->store('sop', 'public');
            }
            
            $sop = Sop::create($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data SOP berhasil disimpan'
                ]);
            }
            
            return redirect()->route('sop.index')->with('success', 'Data SOP berhasil disimpan');
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
        if ($denied = $this->requireAccess('sop.show')) {
            return $denied;
        }
        
        $sop = Sop::with('jabatan')->findOrFail($id);
        return view('sdm::sop.show', compact('sop'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('sop.edit')) {
            return $denied;
        }
        
        $sop = Sop::findOrFail($id);
        $jabatan = Jabatan::active()->get();
        return view('sdm::sop.create', compact('sop', 'jabatan'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('sop.edit')) {
            return $denied;
        }

        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'judul_sop' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'prosedur' => 'required|string',
            'versi' => 'required|string|max:20',
            'tanggal_berlaku' => 'required|date',
            'tanggal_review' => 'nullable|date|after:tanggal_berlaku',
            'status' => 'required|in:draft,active,inactive,archived',
            'dokumen_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            DB::beginTransaction();
            
            $sop = Sop::findOrFail($id);
            
            // Handle file upload
            if ($request->hasFile('dokumen_file')) {
                // Delete old file
                if ($sop->dokumen_file) {
                    Storage::disk('public')->delete($sop->dokumen_file);
                }
                $validated['dokumen_file'] = $request->file('dokumen_file')->store('sop', 'public');
            }
            
            $sop->update($validated);
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data SOP berhasil diupdate'
                ]);
            }
            
            return redirect()->route('sop.index')->with('success', 'Data SOP berhasil diupdate');
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
        if ($denied = $this->requireAccess('sop.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            
            $sop = Sop::findOrFail($id);
            
            // Delete file if exists
            if ($sop->dokumen_file) {
                Storage::disk('public')->delete($sop->dokumen_file);
            }
            
            $sop->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data SOP berhasil dihapus'
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
        $sop = Sop::with(['jabatan', 'creator']);
        
        return DataTables::of($sop)
            ->addColumn('jabatan_nama', function ($row) {
                return $row->jabatan->nama_jabatan ?? '-';
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'draft' => 'secondary',
                    'active' => 'success',
                    'inactive' => 'warning',
                    'archived' => 'dark'
                ];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('file_badge', function ($row) {
                if ($row->dokumen_file) {
                    return '<a href="' . asset('storage/' . $row->dokumen_file) . '" target="_blank" class="badge bg-info"><i class="fa fa-file"></i> File</a>';
                }
                return '<span class="badge bg-secondary">No File</span>';
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
                                <a class="dropdown-item" href="' . route('sop.show', $row->id) . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="' . route('sop.edit', $row->id) . '">
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
            ->rawColumns(['status_badge', 'file_badge', 'dibuat_oleh', 'action'])
            ->make(true);
    }
}