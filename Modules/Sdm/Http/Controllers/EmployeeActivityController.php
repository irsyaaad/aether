<?php
namespace Modules\Sdm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Keuangan\Entities\Invoice;
use Modules\Keuangan\Entities\KasBankKeluar;
use Modules\Keuangan\Entities\KasBankMasuk;
use Modules\Operasional\Entities\Armada;
use Modules\Operasional\Entities\DoModel;
use Modules\Sdm\Entities\DailyPlan;
use Modules\Sdm\Entities\EmployeeActivity;
use Yajra\DataTables\Facades\DataTables;

class EmployeeActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('sdm::activity.index');
    }

    /**
     * Master Report based on Role/Jabatan
     */
    public function masterReport(Request $request)
    {
        $user     = auth()->user();
        $karyawan = $user->karyawan;

        // Default to 'STAFF' if no jabatan found
        $jabatan = $karyawan && $karyawan->jabatanRelation ? $karyawan->jabatanRelation->nama_jabatan : ($karyawan->jabatan ?? 'STAFF');
        $jabatan = strtolower($jabatan);

        $date = $request->date ?? date('Y-m-d');
        $data = [
            'jabatan' => $jabatan,
            'date'    => $date,
        ];

        // Specific data fetching based on jabatan
        if (str_contains($jabatan, 'operasional')) {
            // Staff Ops & PIC Ops share DO data
            $data['dos'] = DoModel::with(['customer', 'trayek', 'armada'])
                ->where('tanggal_do', $date)
                ->get();

            $data['daily_plan'] = DailyPlan::where('user_id', $user->id_user)
                ->where('plan_date', $date)
                ->first();
        } elseif (str_contains($jabatan, 'keuangan') || str_contains($jabatan, 'admin')) {
            $data['kas_masuk']  = KasBankMasuk::where('tanggal', $date)->get();
            $data['kas_keluar'] = KasBankKeluar::where('tanggal', $date)->get();
            $data['invoices']   = Invoice::with('customer')->where('tanggal_tagihan', $date)->get();
        }

        // For managers/directors/hrd, they might want to see everyone's summaries (optional for now)
        if (in_array($jabatan, ['direktur', 'manager', 'hrd'])) {
            $data['all_activities'] = EmployeeActivity::with('user.karyawan')
                ->where('activity_date', $date)
                ->get();
        }

        return view('sdm::activity.master-report', $data);
    }

    public function fixDoAjax(Request $request)
    {
        try {
            DB::beginTransaction();
            $do = DoModel::findOrFail($request->id);
            $do->update([
                'is_fixed' => true,
                'fixed_at' => now(),
            ]);
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'DO berhasil ditandai Fixed.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function approveDoAjax(Request $request)
    {
        try {
            DB::beginTransaction();
            $do = DoModel::findOrFail($request->id);
            $do->update([
                'is_approved_ops' => true,
                'approved_ops_at' => now(),
            ]);
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'DO berhasil di-Approve.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function saveDailyPlanAjax(Request $request)
    {
        try {
            DB::beginTransaction();
            DailyPlan::updateOrCreate(
                [
                    'user_id'   => auth()->id(),
                    'plan_date' => $request->date ?? date('Y-m-d'),
                ],
                [
                    'tomorrow_plan'   => $request->tomorrow_plan,
                    'truck_positions' => $request->truck_positions,
                ]
            );
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Laporan berhasil disimpan.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function getDoTodayCountAjax(Request $request)
    {
        $user    = auth()->user();
        $date    = $request->date ?? date('Y-m-d');
        $jabatan = strtolower($request->jabatan ?? '');
        $count   = 0;

        if (str_contains($jabatan, 'staff') && str_contains($jabatan, 'operasional')) {
            $count = DoModel::where('created_by', $user->id_user)
                ->where('tanggal_do', $date)
                ->count();
        } elseif (str_contains($jabatan, 'pic') && str_contains($jabatan, 'operasional')) {
            $count = DoModel::where('tanggal_do', $date)->count();
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        $user     = auth()->user();
        $karyawan = $user->karyawan;
        $role     = session('role');
        $is_admin = $role && (str_contains(strtolower($role['nm_role']), 'admin') || str_contains(strtolower($role['nm_role']), 'administrator'));

        $jabatan = $request->jabatan ?? ($karyawan && $karyawan->jabatanRelation ? $karyawan->jabatanRelation->nama_jabatan : ($karyawan->jabatan ?? 'STAFF'));
        $jabatan = strtolower($jabatan);

        $do_today_count = 0;
        if (str_contains($jabatan, 'staff') && str_contains($jabatan, 'operasional')) {
            $do_today_count = DoModel::where('created_by', $user->id_user)
                ->where('tanggal_do', date('Y-m-d'))
                ->count();
        } elseif (str_contains($jabatan, 'pic') && str_contains($jabatan, 'operasional')) {
            $do_today_count = DoModel::where('tanggal_do', date('Y-m-d'))->count();
        }

        $armadas = Armada::orderBy('nopol')->get();

        return view('sdm::activity.create', compact('jabatan', 'do_today_count', 'is_admin', 'armadas'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'activity_date'   => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'required|after:start_time',
            'activity'        => 'required',
            'do_today'        => 'nullable|numeric',
            'do_tomorrow'     => 'nullable|numeric',
            'truck_positions' => 'nullable',
            'photo'           => 'nullable|image|max:2048',
        ]);

        try {
            $validate['user_id'] = auth()->id();

            if ($request->hasFile('photo')) {
                $validate['photo'] = $request->file('photo')
                    ->store('activities', 'public');
            }

            DB::beginTransaction();
            EmployeeActivity::create([
                'user_id'         => $validate['user_id'],
                'activity_date'   => $validate['activity_date'],
                'start_time'      => $validate['start_time'],
                'end_time'        => $validate['end_time'],
                'activity'        => $validate['activity'],
                'do_today'        => $validate['do_today'] ?? null,
                'do_tomorrow'     => $validate['do_tomorrow'] ?? null,
                'truck_positions' => $request->truck_positions ? json_encode($request->truck_positions) : null,
                'photo'           => $validate['photo'] ?? null,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors(['error' => $th->getMessage()]);
        }

        return redirect()->route('employee-activities.index')->with('success', 'Aktivitas berhasil ditambahkan.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $activity        = EmployeeActivity::with('user')->findOrFail($id);
        $truck_locations = $activity->truck_positions ? json_decode($activity->truck_positions, true) : [];

        return view('sdm::activity.show', compact('activity', 'truck_locations'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $activity = EmployeeActivity::findOrFail($id);
        $user     = auth()->user();
        $karyawan = $user->karyawan;
        $jabatan  = $karyawan && $karyawan->jabatanRelation ? $karyawan->jabatanRelation->nama_jabatan : ($karyawan->jabatan ?? 'STAFF');
        $jabatan  = strtolower($jabatan);

        $do_today_count = 0;
        if (str_contains($jabatan, 'staff') && str_contains($jabatan, 'operasional')) {
            $do_today_count = DoModel::where('created_by', $user->id_user)
                ->where('tanggal_do', $activity->activity_date->format('Y-m-d'))
                ->count();
        } elseif (str_contains($jabatan, 'pic') && str_contains($jabatan, 'operasional')) {
            $do_today_count = DoModel::where('tanggal_do', $activity->activity_date->format('Y-m-d'))->count();
        }

        $armadas = Armada::orderBy('nopol')->get();
        // Decode truck_positions if it's stored as JSON
        $truck_locations = $activity->truck_positions ? json_decode($activity->truck_positions, true) : [];

        $role     = session('role');
        $is_admin = $role && (str_contains(strtolower($role['nm_role']), 'admin') || str_contains(strtolower($role['nm_role']), 'administrator'));

        return view('sdm::activity.create', compact('activity', 'jabatan', 'do_today_count', 'is_admin', 'armadas', 'truck_locations'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'activity_date'   => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'required|after:start_time',
            'activity'        => 'required',
            'do_today'        => 'nullable|numeric',
            'do_tomorrow'     => 'nullable|numeric',
            'truck_positions' => 'nullable',
            'photo'           => 'nullable|image|max:2048',
        ]);

        try {
            $validate['user_id'] = auth()->id();
            $employeeActivity    = EmployeeActivity::findOrFail($id);

            if ($request->hasFile('photo')) {
                if ($employeeActivity->photo) {
                    Storage::disk('public')->delete($employeeActivity->photo);
                }
                $validate['photo'] = $request->file('photo')->store('activities', 'public');
            }

            DB::beginTransaction();
            $employeeActivity->update([
                'user_id'         => $validate['user_id'],
                'activity_date'   => $validate['activity_date'],
                'start_time'      => $validate['start_time'],
                'end_time'        => $validate['end_time'],
                'activity'        => $validate['activity'],
                'do_today'        => $validate['do_today'] ?? null,
                'do_tomorrow'     => $validate['do_tomorrow'] ?? null,
                'truck_positions' => $request->truck_positions ? json_encode($request->truck_positions) : null,
                'photo'           => $validate['photo'] ?? $employeeActivity->photo,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withErrors(['error' => $th->getMessage()]);
        }

        return redirect()->route('employee-activities.index')->with('success', 'Aktivitas berhasil ditambahkan.');
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
            $employeeActivity = EmployeeActivity::findOrFail($id);
            $employeeActivity->delete();
            DB::commit();
            DB::disconnect();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::disconnect();
            return response()->json([
                'status'  => 'error',
                'message' => 'Data gagal dihapus.',
            ]);
        }
    }

    public function get_data(Request $request)
    {
        $query = EmployeeActivity::with('user')->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('activity_date', function ($row) {
                return $row->activity_date->format('d-m-Y');
            })
            ->addColumn('karyawan', fn($row) => $row->user->nm_user ?? '-')
            ->addColumn('jam', fn($row) => $row->time_range)
            ->addColumn('dibuat_oleh', function ($row) {
                $createdBy = $row->user ? $row->user->nm_user : 'System';
                $createdAt = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $createdBy . '</span><br><small class="text-muted">' . $createdAt . '</small></div>';
            })
            ->addColumn('foto', function ($row) {
                if (! $row->photo) {
                    return '-';
                }

                return '<img src="' . asset('storage/' . $row->photo) . '" width="60">';
            })
            ->addColumn('action', function ($row) {
                $show = route('employee-activities.detail', $row->id);
                $edit = route('employee-activities.edit', $row->id);

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
            ->addColumn('summary', function ($row) {
                $html = '<div class="small">';
                if ($row->do_today !== null) {
                    $html .= '<div>DO Hari Ini: <span class="badge bg-primary">' . $row->do_today . '</span></div>';
                }

                if ($row->do_tomorrow !== null) {
                    $html .= '<div>DO Besok: <span class="badge bg-info">' . $row->do_tomorrow . '</span></div>';
                }

                if ($row->truck_positions) {
                    $html .= '<div class="mt-1 d-block text-truncate" style="max-width: 200px;" title="' . $row->truck_positions . '">Posisi: ' . $row->truck_positions . '</div>';
                }

                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['foto', 'action', 'dibuat_oleh', 'summary'])
            ->make(true);
    }
}
