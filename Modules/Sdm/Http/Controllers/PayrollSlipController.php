<?php

namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Sdm\Entities\PayrollSlip;
use Modules\Sdm\Entities\PayrollSlipDetail;
use Modules\Sdm\Entities\PayrollComponent;
use Modules\Sdm\Entities\PayrollPeriod;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollSlipController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ($denied = $this->requireAccess('payroll-slip.index')) return $denied;
        
        $data['periods'] = PayrollPeriod::orderBy('tanggal_mulai', 'desc')->get();
        $data['filter'] = [
            'period_id' => $request->period_id ?? '',
            'status' => $request->status ?? '',
        ];
        return view('sdm::payroll.slip.index', $data);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('payroll-slip.index')) return $denied;
        
        $data['slip'] = PayrollSlip::with([
            'payrollPeriod', 
            'karyawan', 
            'details.component', 
            'approver', 
            'creator'
        ])->findOrFail($id);
        
        $data['components'] = PayrollComponent::active()->get();
        
        return view('sdm::payroll.slip.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('payroll-slip.edit')) return $denied;
        
        $data['slip'] = PayrollSlip::with(['payrollPeriod', 'karyawan', 'details.component'])->findOrFail($id);
        
        if (!$data['slip']->canBeEdited()) {
            return redirect()->back()->with('error', 'Slip gaji ini tidak dapat diedit');
        }
        
        $data['components'] = PayrollComponent::active()->get();
        
        return view('sdm::payroll.slip.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('payroll-slip.edit')) return $denied;

        $slip = PayrollSlip::findOrFail($id);
        
        if (!$slip->canBeEdited()) {
            return redirect()->back()->with('error', 'Slip gaji ini tidak dapat diedit');
        }

        $validated = $request->validate([
            'gaji_pokok' => 'required|numeric|min:0',
            'hari_kerja' => 'required|integer|min:0|max:31',
            'hari_hadir' => 'required|integer|min:0|max:31',
            'hari_alpha' => 'required|integer|min:0|max:31',
            'jam_lembur' => 'required|numeric|min:0',
            'catatan' => 'nullable|string|max:1000',
            'components' => 'array',
            'components.*.component_id' => 'required|exists:payroll_components,id',
            'components.*.nilai' => 'required|numeric',
            'components.*.keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Update slip basic data
            $slip->update([
                'gaji_pokok' => $validated['gaji_pokok'],
                'hari_kerja' => $validated['hari_kerja'],
                'hari_hadir' => $validated['hari_hadir'],
                'hari_alpha' => $validated['hari_alpha'],
                'jam_lembur' => $validated['jam_lembur'],
                'catatan' => $validated['catatan'],
            ]);

            // Delete existing details
            $slip->details()->delete();

            // Add new details
            if (isset($validated['components'])) {
                foreach ($validated['components'] as $componentData) {
                    if ($componentData['nilai'] > 0) {
                        PayrollSlipDetail::create([
                            'payroll_slip_id' => $slip->id,
                            'payroll_component_id' => $componentData['component_id'],
                            'nilai' => $componentData['nilai'],
                            'keterangan' => $componentData['keterangan'] ?? null,
                        ]);
                    }
                }
            }

            // Recalculate totals
            $slip->calculateTotals();

            DB::commit();
            return redirect()->route('payroll-slip.show', $slip->id)->with('success', 'Slip gaji berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui slip gaji: ' . $e->getMessage());
        }
    }

    /**
     * Approve payroll slip
     */
    public function approve($id)
    {
        if ($denied = $this->requireAccess('payroll-slip.approve')) return $denied;

        try {
            DB::beginTransaction();
            
            $slip = PayrollSlip::findOrFail($id);
            
            if ($slip->status !== 'draft') {
                return redirect()->back()->with('error', 'Slip gaji ini tidak dapat disetujui');
            }

            $slip->update([
                'status' => 'approved',
                'approved_by' => Auth::user()->id_user,
                'approved_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Slip gaji berhasil disetujui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyetujui slip gaji: ' . $e->getMessage());
        }
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($id)
    {
        if ($denied = $this->requireAccess('payroll-slip.pay')) return $denied;

        try {
            DB::beginTransaction();
            
            $slip = PayrollSlip::findOrFail($id);
            
            if ($slip->status !== 'approved') {
                return redirect()->back()->with('error', 'Slip gaji harus disetujui terlebih dahulu');
            }

            $slip->update([
                'status' => 'paid',
                'tanggal_bayar' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Slip gaji berhasil ditandai sebagai dibayar');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menandai sebagai dibayar: ' . $e->getMessage());
        }
    }

    /**
     * Print payroll slip
     */
    public function print($id)
    {
        if ($denied = $this->requireAccess('payroll-slip.index')) return $denied;
        
        $slip = PayrollSlip::with([
            'payrollPeriod', 
            'karyawan', 
            'details.component'
        ])->findOrFail($id);

        $pdf = PDF::loadView('sdm::payroll.slip.print', compact('slip'))
            ->setPaper('A4', 'portrait');
            
        return $pdf->stream('Slip-Gaji-' . $slip->nomor_slip . '.pdf');
    }

    /**
     * Add component to slip
     */
    public function addComponent(Request $request, $id)
    {
        if ($denied = $this->requireAccess('payroll-slip.edit')) return $denied;

        $validated = $request->validate([
            'component_id' => 'required|exists:payroll_components,id',
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            $slip = PayrollSlip::findOrFail($id);
            
            if (!$slip->canBeEdited()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Slip gaji ini tidak dapat diedit'
                ]);
            }

            // Check if component already exists
            $existing = PayrollSlipDetail::where('payroll_slip_id', $slip->id)
                ->where('payroll_component_id', $validated['component_id'])
                ->first();

            if ($existing) {
                $existing->update([
                    'nilai' => $validated['nilai'],
                    'keterangan' => $validated['keterangan'],
                ]);
            } else {
                PayrollSlipDetail::create([
                    'payroll_slip_id' => $slip->id,
                    'payroll_component_id' => $validated['component_id'],
                    'nilai' => $validated['nilai'],
                    'keterangan' => $validated['keterangan'],
                ]);
            }

            // Recalculate totals
            $slip->calculateTotals();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Komponen berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan komponen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove component from slip
     */
    public function removeComponent($slipId, $detailId)
    {
        if ($denied = $this->requireAccess('payroll-slip.edit')) return $denied;

        try {
            DB::beginTransaction();
            
            $slip = PayrollSlip::findOrFail($slipId);
            
            if (!$slip->canBeEdited()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Slip gaji ini tidak dapat diedit'
                ]);
            }

            $detail = PayrollSlipDetail::where('payroll_slip_id', $slipId)
                ->where('id', $detailId)
                ->firstOrFail();

            $detail->delete();

            // Recalculate totals
            $slip->calculateTotals();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Komponen berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus komponen: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            $query = PayrollSlip::with(['payrollPeriod', 'karyawan', 'approver'])
                ->select('payroll_slips.*');

            if ($request->period_id) {
                $query->where('payroll_period_id', $request->period_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('nomor_slip', function ($slip) {
                    return '<a href="' . route('payroll-slip.show', $slip->id) . '" class="text-primary fw-bold">' . $slip->nomor_slip . '</a>';
                })
                ->editColumn('karyawan', function ($slip) {
                    return $slip->karyawan->nama_lengkap ?? '-';
                })
                ->editColumn('periode', function ($slip) {
                    return $slip->payrollPeriod->nama_periode ?? '-';
                })
                ->editColumn('gaji_bersih', function ($slip) {
                    return 'Rp ' . number_format($slip->gaji_bersih, 0, ',', '.');
                })
                ->editColumn('status', function ($slip) {
                    return $slip->status_badge;
                })
                ->addColumn('action', function ($slip) {
                    $show = route('payroll-slip.show', $slip->id);
                    $edit = route('payroll-slip.edit', $slip->id);
                    $print = route('payroll-slip.print', $slip->id);

                    $actions = '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . $show . '"><i class="fa-solid fa-eye text-primary"></i> Detail</a></li>
                                <li><a class="dropdown-item" href="' . $print . '" target="_blank"><i class="fa-solid fa-print text-info"></i> Print</a></li>';
                    
                    if ($slip->canBeEdited()) {
                        $actions .= '<li><a class="dropdown-item" href="' . $edit . '"><i class="fa-solid fa-pen text-warning"></i> Edit</a></li>';
                    }

                    if ($slip->status === 'draft') {
                        $actions .= '<li><a class="dropdown-item btn-approve" href="#" data-id="' . $slip->id . '"><i class="fa-solid fa-check text-success"></i> Approve</a></li>';
                    }

                    if ($slip->status === 'approved') {
                        $actions .= '<li><a class="dropdown-item btn-pay" href="#" data-id="' . $slip->id . '"><i class="fa-solid fa-money-bill text-success"></i> Mark as Paid</a></li>';
                    }
                    
                    $actions .= '</ul></div>';

                    return $actions;
                })
                ->rawColumns(['nomor_slip', 'status', 'action'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }
}