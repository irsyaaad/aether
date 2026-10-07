<?php

namespace Modules\Sdm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Modules\Sdm\Entities\Ads;

class AdsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('sdm::ads.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('sdm::ads.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
            'remarks' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('ads', 'public');
        }

        try {
            DB::beginTransaction();
            $ads = Ads::create($validated);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Pembuatan Iklan gagal');
        }

        return redirect()->route('ads.detail', $ads->id)->with('success', 'Iklan berhasil dibuat');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data['ads'] = Ads::findOrFail($id);

        // Check for flash data from create
        if (session()->has('recruitment_url')) {
            $data['recruitment_url'] = session('recruitment_url');
        }

        return view('sdm::ads.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data['ads'] = Ads::findOrFail($id);
        return view('sdm::ads.create', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $ads = Ads::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
            'remarks' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('ads', 'public');
        }

        try {
            DB::beginTransaction();
            $ads->update($validated);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Pembuatan Iklan gagal');
        }

        return redirect()->route('ads.detail', $ads->id)->with('success', 'Iklan berhasil diperbarui');
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
            $ads = Ads::findOrFail($id);
            $ads->delete();
            DB::commit();
            DB::disconnect();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Iklan berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::disconnect();
            return response()->json([
                'status' => 'error',
                'message' => 'Data Iklan gagal dihapus.',
            ]);
        }
    }

    public function get_data(Request $request)
    {
        $ads = Ads::with('user')->withCount('histories')->select('ads.*');

        return datatables()->of($ads)
            ->addIndexColumn()

        // Kolom gambar
            ->addColumn('image', function ($row) {
                $url = asset('storage/' . $row->image);
                return '<img src="' . $url . '" width="60" class="rounded">';
            })

        // Kolom status badge
            ->addColumn('status', function ($row) {
                $colors = [
                    'draft' => 'secondary',
                    'published' => 'success',
                    'expired' => 'danger',
                ];

                $color = $colors[$row->status] ?? 'secondary';

                return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })

        // Kolom tanggal
            ->editColumn('start_date', fn($row) => $row->start_date ? $row->start_date : '-')
            ->editColumn('end_date', fn($row) => $row->end_date ? $row->end_date : '-')

        // Jumlah history
            ->addColumn('history', fn($row) => $row->histories_count . ' log')

        // Kolom dibuat oleh
            ->addColumn('dibuat_oleh', function($row) {
                $createdBy = $row->user ? $row->user->name : 'System';
                $createdAt = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $createdBy . '</span><br><small class="text-muted">' . $createdAt . '</small></div>';
            })

        // Tombol action
            ->addColumn('action', function ($row) {

                $show = route('ads.detail', $row->id);
                $edit = route('ads.edit', $row->id);

                return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="' . $show . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Show
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="' . $edit . '">
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
            ->rawColumns(['image', 'status', 'action', 'dibuat_oleh'])
            ->make(true);
    }
}
