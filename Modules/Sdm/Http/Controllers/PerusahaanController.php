<?php
namespace Modules\Sdm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\Perusahaan;
use Yajra\DataTables\Facades\DataTables;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('sdm::perusahaan.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('sdm::perusahaan.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "nama_perusahaan" => "required",
            "pemilik"         => "required",
            "alamat"          => "required",
            "kota"            => "required",
            "telp"            => "required",
            "email"           => "required",
            "npwp"            => "required",
            "status"          => "required",
            "keterangan"      => "nullable",
            "company_profile" => "nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:10000",
            "akta_perusahaan" => "nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:10000",
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('company_profile')) {
                $validated['company_profile'] = $request->file('company_profile')->store('perusahaan', 'public');
            }
            if ($request->hasFile('akta_perusahaan')) {
                $validated['akta_perusahaan'] = $request->file('akta_perusahaan')->store('perusahaan', 'public');
            }
            if ($request->hasFile('visi_misi')) {
                $validated['visi_misi'] = $request->file('visi_misi')->store('perusahaan', 'public');
            }

            $perusahaan = Perusahaan::create($validated);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('perusahaan.index')->with('error', 'Data gagal disimpan: ' . $th->getMessage());
        }
        return redirect()->route('perusahaan.detail', $perusahaan->id)->with('success', 'Data berhasil disimpan');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $perusahaan = Perusahaan::find($id);
        return view('sdm::perusahaan.show', compact('perusahaan'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $perusahaan = Perusahaan::find($id);
        return view('sdm::perusahaan.create', compact('perusahaan'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "nama_perusahaan" => "required",
            "pemilik"         => "required",
            "alamat"          => "required",
            "kota"            => "required",
            "telp"            => "required",
            "email"           => "required",
            "npwp"            => "required",
            "status"          => "required",
            "keterangan"      => "nullable",
            "company_profile" => "nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:10000",
            "akta_perusahaan" => "nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:10000",
        ]);

        try {
            DB::beginTransaction();
            $perusahaan = Perusahaan::find($id);

            if ($request->hasFile('company_profile')) {
                if ($perusahaan->company_profile) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($perusahaan->company_profile);
                }
                $validated['company_profile'] = $request->file('company_profile')->store('perusahaan', 'public');
            }

            if ($request->hasFile('akta_perusahaan')) {
                if ($perusahaan->akta_perusahaan) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($perusahaan->akta_perusahaan);
                }
                $validated['akta_perusahaan'] = $request->file('akta_perusahaan')->store('perusahaan', 'public');
            }

            if ($request->hasFile('visi_misi')) {
                if ($perusahaan->visi_misi) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($perusahaan->visi_misi);
                }
                $validated['visi_misi'] = $request->file('visi_misi')->store('perusahaan', 'public');
            }

            $perusahaan->update($validated);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('perusahaan.edit', $perusahaan->id)->with('error', 'Data gagal diupdate: ' . $th->getMessage());
        }
        return redirect()->route('perusahaan.detail', $perusahaan->id)->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $perusahaan = Perusahaan::findOrFail($id);
            $perusahaan->delete();
            DB::commit();
            DB::disconnect();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data perusahaan berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::disconnect();
            return response()->json([
                'status'  => 'error',
                'message' => 'Data perusahaan gagal dihapus.',
            ]);
        }
    }

    public function get_data()
    {
        $query = Perusahaan::with('user');

        return DataTables::of($query)
            ->addColumn('status', function ($row) {
                return $row->status == 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })

            ->addColumn('dibuat_oleh', function ($row) {
                $createdBy = $row->user ? $row->user->name : 'System';
                $createdAt = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $createdBy . '</span><br><small class="text-muted">' . $createdAt . '</small></div>';
            })

            ->addColumn('action', function ($row) {
                $show = route('perusahaan.detail', $row->id);
                $edit = route('perusahaan.edit', $row->id);

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

            ->rawColumns(['status', 'action', 'dibuat_oleh'])
            ->make(true);
    }
}
