<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\PayrollPeriod;
use Modules\Sdm\Entities\PayrollSlip;
use Modules\Sdm\Entities\Karyawan;
use Yajra\DataTables\Facades\DataTables;

class PayrollPeriodController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('payroll.index')) return $denied;
        
        $data['filter'] = [
            'nm_filter' => 'filter-do',
            'dr_tgl' => $request->dr_tgl ?? date('Y-m-01'),
            'sp_tgl' => $request->sp_tgl ?? date('Y-m-d'),
        ];
        return view('sdm::payroll.period.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if ($denied = $this->requireAccess('payroll.create')) return $denied;
        
        return view('sdm::payroll.period.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('payroll.create')) return $denied;

        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_gajian' => 'required|date|after_or_equal:tanggal_selesai',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $period = PayrollPeriod::create([
                'kode_periode' => PayrollPeriod::generateKodePeriode(),
                'nama_periode' => $validated['nama_periode'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'tanggal_gajian' => $validated['tanggal_gajian'],
                'keterangan' => $validated['keterangan'],
                'status' => 'draft',
            ]);

            DB::commit();
            return redirect()->route('payroll-period.show', $period->id)->with('success', 'Periode payroll berhasil dibuat');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat periode: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('payroll.index')) return $denied;
        
        $data['period'] = PayrollPeriod::with(['payrollSlips.karyawan', 'creator'])->findOrFail($id);
        return view('sdm::payroll.period.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('payroll.edit')) return $denied;
        
        $data['period'] = PayrollPeriod::findOrFail($id);
        
        if (!$data['period']->canBeEdited()) {
            return redirect()->back()->with('error', 'Periode ini tidak dapat diedit');
        }
        
        return view('sdm::payroll.period.create', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('payroll.edit')) return $denied;

        $period = PayrollPeriod::findOrFail($id);
        
        if (!$period->canBeEdited()) {
            return redirect()->back()->with('error', 'Periode ini tidak dapat diedit');
        }

        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_gajian' => 'required|date|after_or_equal:tanggal_selesai',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $period->update($validated);

            DB::commit();
            return redirect()->route('payroll-period.show', $period->id)->with('success', 'Periode payroll berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui periode: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('payroll.delete')) return $denied;

        try {
            DB::beginTransaction();
            $period = PayrollPeriod::findOrFail($id);
            
            if (!$period->canBeEdited()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Periode ini tidak dapat dihapus',
                ]);
            }
            
            $period->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Periode payroll berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Periode payroll gagal dihapus: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate payroll slips for all employees
     */
    public function generateSlips($id)
    {
        if ($denied = $this->requireAccess('payroll.process')) return $denied;

        try {
            DB::beginTransaction();
            
            $period = PayrollPeriod::findOrFail($id);
            
            if (!$period->canBeProcessed()) {
                return redirect()->back()->with('error', 'Periode ini tidak dapat diproses');
            }

            // Get all active employees
            $karyawan = Karyawan::where('status', 'aktif')->get();
            
            foreach ($karyawan as $emp) {
                // Check if slip already exists
                $existingSlip = PayrollSlip::where('payroll_period_id', $period->id)
                    ->where('karyawan_id', $emp->id)
                    ->first();
                
                if (!$existingSlip) {
                    PayrollSlip::create([
                        'nomor_slip' => PayrollSlip::generateNomorSlip($period->id),
                        'payroll_period_id' => $period->id,
                        'karyawan_id' => $emp->id,
                        'gaji_pokok' => $emp->gaji_pokok ?? 0,
                        'hari_kerja' => 22, // Default working days
                        'hari_hadir' => 22, // Default attendance
                        'hari_alpha' => 0,
                        'jam_lembur' => 0,
                        'status' => 'draft',
                    ]);
                }
            }

            $period->update(['status' => 'processed']);

            DB::commit();
            return redirect()->back()->with('success', 'Slip gaji berhasil digenerate untuk semua karyawan');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal generate slip gaji: ' . $e->getMessage());
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = PayrollPeriod::with(['creator'])
            ->select('payroll_periods.*');

        if ($request->dr_tgl && $request->sp_tgl) {
            $query->whereBetween('tanggal_mulai', [$request->dr_tgl, $request->sp_tgl]);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('kode_periode', function ($period) {
                return '<a href="' . route('payroll-period.show', $period->id) . '" class="text-primary fw-bold">' . $period->kode_periode . '</a>';
            })
            ->editColumn('tanggal', function ($period) {
                return date('d M Y', strtotime($period->tanggal_mulai)) . ' - ' . date('d M Y', strtotime($period->tanggal_selesai));
            })
            ->editColumn('tanggal_gajian', function ($period) {
                return date('d M Y', strtotime($period->tanggal_gajian));
            })
            ->editColumn('status', function ($period) {
                return $period->status_badge;
            })
            ->addColumn('total_slip', function ($period) {
                return $period->payrollSlips()->count();
            })
            ->addColumn('action', function ($period) {
                $show = route('payroll-period.show', $period->id);
                $edit = route('payroll-period.edit', $period->id);

                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa-solid fa-eye text-primary"></i> Detail</a></li>';
                
                if ($period->canBeEdited()) {
                    $actions .= '<li><a class="dropdown-item" href="' . $edit . '"><i class="fa-solid fa-pen text-warning"></i> Edit</a></li>';
                }

                if ($period->canBeProcessed()) {
                    $actions .= '<li><a class="dropdown-item btn-generate" href="#" data-id="' . $period->id . '"><i class="fa-solid fa-cogs text-info"></i> Generate Slip</a></li>';
                }
                
                if ($period->canBeEdited()) {
                    $actions .= '<li><button class="dropdown-item text-danger btn-delete" data-id="' . $period->id . '"><i class="fa-solid fa-trash"></i> Hapus</button></li>';
                }
                
                $actions .= '</ul></div>';

                return $actions;
            })
            ->rawColumns(['kode_periode', 'status', 'action'])
            ->make(true);
    }
}