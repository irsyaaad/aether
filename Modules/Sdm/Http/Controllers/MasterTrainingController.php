<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\MasterTraining;
use Yajra\DataTables\Facades\DataTables;

class MasterTrainingController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('master-training.index')) {
            return $denied;
        }
        return view('sdm::master-training.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('master-training.create')) {
            return $denied;
        }
        return view('sdm::master-training.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('master-training.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'kode_training' => 'required|string|max:50|unique:master_trainings,kode_training',
            'nama_training' => 'required|string|max:255',
            'kategori'      => 'nullable|string|max:100',
            'deskripsi'     => 'nullable|string',
            'file_materi'   => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,mp4,zip|max:51200',
            'link_youtube'  => 'nullable|string|max:500',
            'durasi_jam'    => 'nullable|integer|min:1',
            'level'         => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('file_materi')) {
                $validated['file_materi'] = $request->file('file_materi')->store('training-materi', 'public');
            }

            MasterTraining::create($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Data master training berhasil disimpan',
                ]);
            }

            return redirect()->route('master-training.index')->with('success', 'Data master training berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
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
        if ($denied = $this->requireAccess('master-training.index')) {
            return $denied;
        }

        $masterTraining = MasterTraining::with('creator')->findOrFail($id);
        return view('sdm::master-training.show', compact('masterTraining'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('master-training.update')) {
            return $denied;
        }

        $masterTraining = MasterTraining::findOrFail($id);
        return view('sdm::master-training.create', compact('masterTraining'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('master-training.update')) {
            return $denied;
        }

        $validated = $request->validate([
            'kode_training' => 'required|string|max:50|unique:master_trainings,kode_training,' . $id,
            'nama_training' => 'required|string|max:255',
            'kategori'      => 'nullable|string|max:100',
            'deskripsi'     => 'nullable|string',
            'file_materi'   => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,mp4,zip|max:51200',
            'link_youtube'  => 'nullable|string|max:500',
            'durasi_jam'    => 'nullable|integer|min:1',
            'level'         => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $masterTraining = MasterTraining::findOrFail($id);

            // Handle file upload
            if ($request->hasFile('file_materi')) {
                // Delete old file if exists
                if ($masterTraining->file_materi && \Storage::disk('public')->exists($masterTraining->file_materi)) {
                    \Storage::disk('public')->delete($masterTraining->file_materi);
                }
                $validated['file_materi'] = $request->file('file_materi')->store('training-materi', 'public');
            }

            $masterTraining->update($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Data master training berhasil diupdate',
                ]);
            }

            return redirect()->route('master-training.index')->with('success', 'Data master training berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal mengupdate data: ' . $e->getMessage(),
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
        if ($denied = $this->requireAccess('master-training.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();

            $masterTraining = MasterTraining::findOrFail($id);
            $masterTraining->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data master training berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $masterTraining = MasterTraining::with('creator');

        return DataTables::of($masterTraining)
            ->addColumn('status_badge', function ($row) {
                $badge = $row->is_active ? 'success' : 'secondary';
                $text  = $row->is_active ? 'Aktif' : 'Tidak Aktif';
                return '<span class="badge bg-' . $badge . '">' . $text . '</span>';
            })
            ->addColumn('durasi_display', function ($row) {
                return $row->durasi_jam ? $row->durasi_jam . ' jam' : '-';
            })
            ->addColumn('dibuat_oleh', function ($row) {
                $creator = $row->creator->nm_user ?? '-';
                $date    = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
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
                                <a class="dropdown-item" href="' . route('master-training.show', $row->id) . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="' . route('master-training.edit', $row->id) . '">
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
