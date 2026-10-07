<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\PayrollComponent;
use Yajra\DataTables\Facades\DataTables;

class PayrollComponentController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('payroll-component.index')) return $denied;
        
        return view('sdm::payroll.component.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('payroll-component.create')) return $denied;
        
        return view('sdm::payroll.component.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('payroll-component.create')) return $denied;

        $validated = $request->validate([
            'kode_komponen' => 'required|string|max:50|unique:payroll_components,kode_komponen',
            'nama_komponen' => 'required|string|max:255',
            'jenis' => 'required|in:allowance,deduction',
            'tipe_perhitungan' => 'required|in:fixed,percentage,formula',
            'nilai_default' => 'required|numeric|min:0',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            PayrollComponent::create([
                'kode_komponen' => strtoupper($validated['kode_komponen']),
                'nama_komponen' => $validated['nama_komponen'],
                'jenis' => $validated['jenis'],
                'tipe_perhitungan' => $validated['tipe_perhitungan'],
                'nilai_default' => $validated['nilai_default'],
                'is_taxable' => $request->boolean('is_taxable'),
                'is_active' => $request->boolean('is_active', true),
                'keterangan' => $validated['keterangan'],
            ]);

            DB::commit();
            return redirect()->route('payroll-component.index')->with('success', 'Komponen payroll berhasil dibuat');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat komponen: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('payroll-component.index')) return $denied;
        
        $data['component'] = PayrollComponent::with(['creator', 'payrollSlipDetails.payrollSlip.karyawan'])->findOrFail($id);
        return view('sdm::payroll.component.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('payroll-component.edit')) return $denied;
        
        $data['component'] = PayrollComponent::findOrFail($id);
        return view('sdm::payroll.component.create', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('payroll-component.edit')) return $denied;

        $component = PayrollComponent::findOrFail($id);

        $validated = $request->validate([
            'kode_komponen' => 'required|string|max:50|unique:payroll_components,kode_komponen,' . $id,
            'nama_komponen' => 'required|string|max:255',
            'jenis' => 'required|in:allowance,deduction',
            'tipe_perhitungan' => 'required|in:fixed,percentage,formula',
            'nilai_default' => 'required|numeric|min:0',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $component->update([
                'kode_komponen' => strtoupper($validated['kode_komponen']),
                'nama_komponen' => $validated['nama_komponen'],
                'jenis' => $validated['jenis'],
                'tipe_perhitungan' => $validated['tipe_perhitungan'],
                'nilai_default' => $validated['nilai_default'],
                'is_taxable' => $request->boolean('is_taxable'),
                'is_active' => $request->boolean('is_active'),
                'keterangan' => $validated['keterangan'],
            ]);

            DB::commit();
            return redirect()->route('payroll-component.show', $component->id)->with('success', 'Komponen payroll berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui komponen: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('payroll-component.delete')) return $denied;

        try {
            DB::beginTransaction();
            $component = PayrollComponent::findOrFail($id);
            
            // Check if component is used in any payroll slips
            if ($component->payrollSlipDetails()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Komponen ini tidak dapat dihapus karena sudah digunakan dalam slip gaji',
                ]);
            }
            
            $component->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Komponen payroll berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Komponen payroll gagal dihapus: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        if ($denied = $this->requireAccess('payroll-component.edit')) return $denied;

        try {
            DB::beginTransaction();
            $component = PayrollComponent::findOrFail($id);
            
            $component->update([
                'is_active' => !$component->is_active
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Status komponen berhasil diubah',
                'is_active' => $component->is_active
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah status: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = PayrollComponent::with(['creator'])
            ->select('payroll_components.*');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('kode_komponen', function ($component) {
                return '<a href="' . route('payroll-component.show', $component->id) . '" class="text-primary fw-bold">' . $component->kode_komponen . '</a>';
            })
            ->editColumn('jenis', function ($component) {
                return $component->jenis_badge;
            })
            ->editColumn('tipe_perhitungan', function ($component) {
                return $component->tipe_perhitungan_label;
            })
            ->editColumn('nilai_default', function ($component) {
                if ($component->tipe_perhitungan === 'percentage') {
                    return number_format($component->nilai_default, 2) . '%';
                }
                return 'Rp ' . number_format($component->nilai_default, 0, ',', '.');
            })
            ->editColumn('is_taxable', function ($component) {
                return $component->is_taxable ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>';
            })
            ->editColumn('is_active', function ($component) {
                $checked = $component->is_active ? 'checked' : '';
                return '<div class="form-check form-switch">
                    <input class="form-check-input toggle-status" type="checkbox" data-id="' . $component->id . '" ' . $checked . '>
                </div>';
            })
            ->addColumn('action', function ($component) {
                $show = route('payroll-component.show', $component->id);
                $edit = route('payroll-component.edit', $component->id);

                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa-solid fa-eye text-primary"></i> Detail</a></li>
                            <li><a class="dropdown-item" href="' . $edit . '"><i class="fa-solid fa-pen text-warning"></i> Edit</a></li>
                            <li><button class="dropdown-item text-danger btn-delete" data-id="' . $component->id . '"><i class="fa-solid fa-trash"></i> Hapus</button></li>
                        </ul>
                    </div>';

                return $actions;
            })
            ->rawColumns(['kode_komponen', 'jenis', 'is_taxable', 'is_active', 'action'])
            ->make(true);
    }
}