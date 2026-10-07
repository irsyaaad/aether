<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Sdm\Entities\Meeting;
use Yajra\DataTables\Facades\DataTables;

class MeetingController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($denied = $this->requireAccess('sdm.meeting.index')) {
            return $denied;
        }

        return view('sdm::meeting.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($denied = $this->requireAccess('sdm.meeting.create')) {
            return $denied;
        }

        return view('sdm::meeting.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('sdm.meeting.create')) {
            return $denied;
        }

        $request->validate([
            'tanggal_meeting' => 'required|date',
            'peserta'         => 'required|string',
            'hasil_meeting'   => 'required|string',
            'jenis_meeting'   => 'required|in:Weekly,Monthly,Quartal,Yearly',
            'foto'            => 'nullable|array',
            'foto.*'          => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $uploadedPaths = [];
        try {
            DB::beginTransaction();

            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $filename        = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path            = $file->storeAs('sdm/meeting', $filename, 'public');
                    $uploadedPaths[] = $path;
                }
            }

            // Convert participants string to array if needed (assumed comma separated from UI)
            $peserta = array_map('trim', explode(',', $request->peserta));

            Meeting::create([
                'tanggal_meeting' => $request->tanggal_meeting,
                'peserta'         => $peserta,
                'foto'            => $uploadedPaths,
                'hasil_meeting'   => $request->hasil_meeting,
                'jenis_meeting'   => $request->jenis_meeting,
            ]);

            DB::commit();
            return redirect()->route('meeting.index')->with('success', 'Kegiatan meeting berhasil dicatat.');

        } catch (\Throwable $th) {
            DB::rollBack();

            // Delete uploaded files if error occurs
            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()->withInput()->with('error', 'Gagal mencatat meeting: ' . $th->getMessage());
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('sdm.meeting.index')) {
            return $denied;
        }

        $meeting = Meeting::with(['uploader'])->findOrFail($id);
        return view('sdm::meeting.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('sdm.meeting.update')) {
            return $denied;
        }

        $meeting = Meeting::findOrFail($id);
        return view('sdm::meeting.edit', compact('meeting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('sdm.meeting.update')) {
            return $denied;
        }

        $meeting = Meeting::findOrFail($id);

        $request->validate([
            'tanggal_meeting' => 'required|date',
            'peserta'         => 'required|string',
            'hasil_meeting'   => 'required|string',
            'jenis_meeting'   => 'required|in:Weekly,Monthly,Quartal,Yearly',
            'foto'            => 'nullable|array',
            'foto.*'          => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $uploadedPaths = $meeting->foto ?? [];
        try {
            DB::beginTransaction();

            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $filename        = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path            = $file->storeAs('sdm/meeting', $filename, 'public');
                    $uploadedPaths[] = $path;
                }
            }

            // Handle deleted photos if implemented in UI (omitted for now)

            $peserta = array_map('trim', explode(',', $request->peserta));

            $meeting->update([
                'tanggal_meeting' => $request->tanggal_meeting,
                'peserta'         => $peserta,
                'foto'            => $uploadedPaths,
                'hasil_meeting'   => $request->hasil_meeting,
                'jenis_meeting'   => $request->jenis_meeting,
            ]);

            DB::commit();
            return redirect()->route('meeting.index')->with('success', 'Data meeting berhasil diperbarui.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui meeting: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('sdm.meeting.delete')) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        }

        try {
            DB::beginTransaction();

            $meeting = Meeting::findOrFail($id);

            // Photos are kept if using soft deletes, but usually we delete if it's a permanent delete
            // For now, let's keep them and rely on soft deletes.

            $meeting->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data meeting berhasil dihapus.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus meeting: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = Meeting::with(['uploader'])->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal_meeting', function ($row) {
                return $row->tanggal_meeting->format('d/m/Y');
            })
            ->editColumn('peserta', function ($row) {
                $peserta = (array) $row->peserta;
                return Str::limit(implode(', ', $peserta), 50);
            })
            ->editColumn('jenis_meeting', function ($row) {
                return '<span class="badge ' . $row->getTypeBadgeClass() . '">' . $row->jenis_meeting . '</span>';
            })
            ->editColumn('uploader', function ($row) {
                $name = $row->uploader->nm_user ?? '-';
                $date = $row->created_at->format('d/m/Y H:i');
                return '<div>' . $name . '</div><small class="text-muted">' . $date . '</small>';
            })
            ->addColumn('action', function ($row) {
                $show = route('meeting.show', $row->id);
                $edit = route('meeting.edit', $row->id);

                return '
                    <div class="dropdown text-center">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa fa-eye text-primary"></i> Lihat</a></li>
                            <li><a class="dropdown-item" href="' . $edit . '"><i class="fa fa-edit text-warning"></i> Edit</a></li>
                            <li><button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i> Hapus</button></li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['jenis_meeting', 'uploader', 'action'])
            ->make(true);
    }
}
