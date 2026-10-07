<?php

namespace Modules\Master\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Master\Entities\Layanan;

class LayananController extends Controller
{
    use HasAccessControl;

    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('layanan.index')) {
            return $denied;
        }

        $query = Layanan::withTrashed();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_layanan', 'like', "%{$search}%")
                  ->orWhere('kode_layanan', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'active') {
                $query->whereNull('deleted_at');
            } else {
                $query->whereNotNull('deleted_at');
            }
        }

        $sort = $request->input('sort', 'name_asc');
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'older':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('nama_layanan', 'asc');
                break;
        }

        $layanan = $query->paginate(10)->withQueryString();

        return Inertia::render('Master/Layanan/Index', [
            'layanan' => $layanan,
            'filters' => (object) $request->only(['search', 'status', 'sort'])
        ]);
    }

    public function create()
    {
        if ($denied = $this->requireAccess('layanan.create')) {
            return $denied;
        }

        return Inertia::render('Master/Layanan/Form', [
            'layanan' => null
        ]);
    }

    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('layanan.create')) {
            return $denied;
        }

        $request->validate([
            'kode_layanan' => 'required|string|max:20|unique:layanan,kode_layanan',
            'nama_layanan' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();
            $layanan = Layanan::create([
                'kode_layanan' => $request->kode_layanan,
                'nama_layanan' => $request->nama_layanan,
            ]);
            
            if ($request->status == 'inactive') {
                $layanan->delete();
            }
            DB::commit();

            return redirect()->route('layanan.index')->with('success', 'Data Layanan berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        if ($denied = $this->requireAccess('layanan.update')) {
            return $denied;
        }

        $layanan = Layanan::withTrashed()->findOrFail($id);
        
        // Add status attribute dynamically for the React Form
        $layanan->status = $layanan->trashed() ? 'inactive' : 'active';
        
        return Inertia::render('Master/Layanan/Form', [
            'layanan' => $layanan
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('layanan.update')) {
            return $denied;
        }

        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();
            $layanan = Layanan::withTrashed()->findOrFail($id);
            $layanan->update([
                'nama_layanan' => $request->nama_layanan,
            ]);
            
            if ($request->status == 'active' && $layanan->trashed()) {
                $layanan->restore();
            } elseif ($request->status == 'inactive' && !$layanan->trashed()) {
                $layanan->delete();
            }
            DB::commit();

            return redirect()->route('layanan.index')->with('success', 'Data Layanan berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($denied = $this->requireAccess('layanan.delete')) {
            return $denied;
        }

        try {
            DB::beginTransaction();
            $layanan = Layanan::findOrFail($id);
            $layanan->delete();
            DB::commit();

            return redirect()->back()->with('success', 'Data Layanan berhasil dihapus (nonaktif)');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }
}
