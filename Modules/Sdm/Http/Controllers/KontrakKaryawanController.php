<?php
namespace Modules\Sdm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Sdm\Entities\Karyawan;
use Modules\Sdm\Entities\KontrakKaryawan;
use Yajra\DataTables\Facades\DataTables;

class KontrakKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = KontrakKaryawan::with('karyawan')->select('kontrak_karyawan.*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_karyawan', function ($row) {
                    return $row->karyawan ? $row->karyawan->nama_lengkap : '-';
                })
                ->addColumn('action', function ($row) {
                    $show = route('kontrak-karyawan.show', $row->id);
                    $edit = route('kontrak-karyawan.edit', $row->id);

                    return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa-solid fa-eye text-primary"></i> Show</a></li>
                            <li><a class="dropdown-item" href="' . $edit . '"><i class="fa-solid fa-pen text-warning"></i> Edit</a></li>
                            <li><button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '"><i class="fa-solid fa-trash"></i> Hapus</button></li>
                        </ul>
                    </div>
                ';
                })
                ->addColumn('files', function ($row) {
                    $files = '';
                    if ($row->file_pkwt) {
                        $files .= '<a href="' . asset('storage/' . $row->file_pkwt) . '" target="_blank" class="badge badge-primary mr-1">PKWT</a>';
                    }

                    if ($row->file_pkwtt) {
                        $files .= '<a href="' . asset('storage/' . $row->file_pkwtt) . '" target="_blank" class="badge badge-success mr-1">PKWTT</a>';
                    }

                    if ($row->file_khl) {
                        $files .= '<a href="' . asset('storage/' . $row->file_khl) . '" target="_blank" class="badge badge-info mr-1">KHL</a>';
                    }

                    return $files;
                })
                ->rawColumns(['action', 'files'])
                ->make(true);
        }
        return view('sdm::kontrak-karyawan.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $karyawan = Karyawan::all();
        return view('sdm::kontrak-karyawan.create', compact('karyawan'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan'     => 'required|exists:karyawan,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_kontrak'   => 'required|in:pkwt,tetap',
            'file_pkwt'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_pkwtt'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_khl'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['file_pkwt', 'file_pkwtt', 'file_khl']);

        if ($request->hasFile('file_pkwt')) {
            $data['file_pkwt'] = $request->file('file_pkwt')->store('kontrak', 'public');
        }
        if ($request->hasFile('file_pkwtt')) {
            $data['file_pkwtt'] = $request->file('file_pkwtt')->store('kontrak', 'public');
        }
        if ($request->hasFile('file_khl')) {
            $data['file_khl'] = $request->file('file_khl')->store('kontrak', 'public');
        }

        KontrakKaryawan::create($data);

        return redirect()->route('kontrak-karyawan.index')->with('success', 'Kontrak karyawan berhasil ditambahkan.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $kontrak = KontrakKaryawan::with('karyawan')->findOrFail($id);
        return view('sdm::kontrak-karyawan.show', compact('kontrak'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $kontrak  = KontrakKaryawan::findOrFail($id);
        $karyawan = Karyawan::all();
        return view('sdm::kontrak-karyawan.edit', compact('kontrak', 'karyawan'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $kontrak = KontrakKaryawan::findOrFail($id);

        $request->validate([
            'id_karyawan'     => 'required|exists:karyawan,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_kontrak'   => 'required|in:pkwt,tetap',
            'file_pkwt'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_pkwtt'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_khl'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['file_pkwt', 'file_pkwtt', 'file_khl', '_token', '_method']);

        if ($request->hasFile('file_pkwt')) {
            if ($kontrak->file_pkwt) {
                Storage::disk('public')->delete($kontrak->file_pkwt);
            }
            $data['file_pkwt'] = $request->file('file_pkwt')->store('kontrak', 'public');
        }
        if ($request->hasFile('file_pkwtt')) {
            if ($kontrak->file_pkwtt) {
                Storage::disk('public')->delete($kontrak->file_pkwtt);
            }
            $data['file_pkwtt'] = $request->file('file_pkwtt')->store('kontrak', 'public');
        }
        if ($request->hasFile('file_khl')) {
            if ($kontrak->file_khl) {
                Storage::disk('public')->delete($kontrak->file_khl);
            }
            $data['file_khl'] = $request->file('file_khl')->store('kontrak', 'public');
        }

        $kontrak->update($data);

        return redirect()->route('kontrak-karyawan.index')->with('success', 'Kontrak karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $kontrak = KontrakKaryawan::findOrFail($id);

            if ($kontrak->file_pkwt) {
                Storage::disk('public')->delete($kontrak->file_pkwt);
            }
            if ($kontrak->file_pkwtt) {
                Storage::disk('public')->delete($kontrak->file_pkwtt);
            }
            if ($kontrak->file_khl) {
                Storage::disk('public')->delete($kontrak->file_khl);
            }

            $kontrak->delete();

            \Illuminate\Support\Facades\DB::commit();

            return response()->json(['success' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['error' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}
