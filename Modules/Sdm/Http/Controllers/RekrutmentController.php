<?php

namespace Modules\Sdm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoleMenu;
use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Sdm\Entities\Kandidat;
use Modules\Sdm\Entities\Ads;

class RekrutmentController extends Controller
{
    use HasAccessControl;

    /**
     * Display a kanban view of recruitment candidates.
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('rekrutment.index')) {
            return $denied;
        }

        // Define the status columns for kanban
        $statuses = [
            'open' => 'Open',
            'interview' => 'Interview',
            'tes' => 'Tes',
            'dipanggil' => 'Dipanggil',
            'ditolak' => 'Ditolak',
        ];

        // Get all active advertisements
        $ads = Ads::where('status', 'active')
            ->orderBy('title', 'ASC')
            ->get();

        $selectedAdsId = $request->ads_id;

        // Filter ads based on selection
        if ($selectedAdsId && $selectedAdsId !== 'all') {
            $filteredAds = $ads->where('id', $selectedAdsId);
        } else {
            $filteredAds = $ads;
        }

        // Get candidates grouped by ads and status
        $kandidats = Kandidat::select([
            'id',
            'ads_id',
            'nama_lengkap',
            'nama_panggilan',
            'nik_ktp',
            'nomor_hp',
            'email',
            'status_seleksi',
            'created_at',
            'updated_at',
        ])
            ->whereNotNull('status_seleksi')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Organize data for each ads (board)
        $boards = [];
        foreach ($filteredAds as $ad) {
            $kandidatsByStatus = $kandidats->where('ads_id', $ad->id)->groupBy('status_seleksi');

            $columns = [];
            foreach ($statuses as $key => $label) {
                $columns[$key] = [
                    'id' => $key,
                    'name' => $label,
                    'kandidats' => $kandidatsByStatus->get($key, collect()),
                ];
            }

            $boards[] = [
                'ads' => $ad,
                'columns' => $columns,
            ];
        }

        return view('sdm::rekrutment.index', compact('boards', 'statuses', 'ads', 'selectedAdsId'));
    }

    /**
     * Store a newly created candidate in storage.
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('rekrutment.create')) {
            return $denied;
        }

        $validated = $request->validate([
            'ads_id' => 'required|exists:ads,id',
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:100',
            'nik_ktp' => 'required|string|max:20|unique:kandidat,nik_ktp',
            'nomor_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status_seleksi' => 'nullable|in:open,interview,tes,dipanggil,ditolak',
        ]);

        // Set default status to 'open' if not provided
        $validated['status_seleksi'] = $validated['status_seleksi'] ?? 'open';

        $kandidat = Kandidat::create($validated);

        return response()->json([
            'message' => 'Kandidat berhasil ditambahkan.',
            'kandidat' => $kandidat,
        ], 201);
    }

    /**
     * Update the specified candidate in storage.
     */
    public function update(Request $request, int $id)
    {
        if ($denied = $this->requireAccess('rekrutment.update')) {
            return $denied;
        }

        $kandidat = Kandidat::findOrFail($id);

        $validated = $request->validate([
            'ads_id' => 'required|exists:ads,id',
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'nullable|string|max:100',
            'nik_ktp' => 'required|string|max:20|unique:kandidat,nik_ktp,' . $id,
            'nomor_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status_seleksi' => 'nullable|in:open,interview,tes,dipanggil,ditolak',
        ]);

        $kandidat->update($validated);

        return response()->json([
            'message' => 'Kandidat berhasil diperbarui.',
            'kandidat' => $kandidat,
        ]);
    }

    /**
     * Update the specified candidate status in storage.
     */
    public function updateStatus(Request $request, int $id)
    {
        if ($denied = $this->requireAccess('rekrutment.update')) {
            return $denied;
        }

        $validated = $request->validate([
            'status_seleksi' => 'required|in:open,interview,tes,dipanggil,ditolak',
        ]);

        $kandidat = Kandidat::findOrFail($id);
        $kandidat->status_seleksi = $validated['status_seleksi'];
        $kandidat->save();

        return response()->json([
            'message' => 'Status kandidat berhasil diperbarui.',
            'kandidat' => $kandidat,
        ]);
    }

    /**
     * Move candidate to a different status (for drag and drop).
     */
    public function move(Request $request, int $id)
    {
        if (!RoleMenu::checkAccess('rekrutment.update')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status_seleksi' => 'required|in:open,interview,tes,dipanggil,ditolak',
        ]);

        $kandidat = Kandidat::findOrFail($id);
        $kandidat->status_seleksi = $validated['status_seleksi'];
        $kandidat->save();

        return response()->json([
            'message' => 'Kandidat berhasil dipindahkan.',
            'kandidat' => $kandidat,
        ]);
    }

    /**
     * Remove the specified candidate from storage.
     */
    public function destroy(int $id)
    {
        if ($denied = $this->requireAccess('rekrutment.delete')) {
            return $denied;
        }

        $kandidat = Kandidat::findOrFail($id);
        $kandidat->delete();

        return response()->json([
            'message' => 'Kandidat berhasil dihapus.',
        ]);
    }

    /**
     * Get candidate details for modal.
     */
    public function show(int $id)
    {
        if ($denied = $this->requireAccess('rekrutment.index')) {
            return $denied;
        }

        $kandidat = Kandidat::findOrFail($id);

        return response()->json([
            'kandidat' => $kandidat,
        ]);
    }
}
