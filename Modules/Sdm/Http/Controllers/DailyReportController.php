<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Operasional\Entities\Armada;
use Modules\Operasional\Entities\DoCheckpoint;
use Modules\Operasional\Entities\DoModel;
use Modules\Sdm\Entities\DailyReport;
use Modules\Sdm\Entities\DailyReportItem;
use Yajra\DataTables\Facades\DataTables;

class DailyReportController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('daily-report.index')) {
            return $denied;
        }

        $data['filter'] = [
            'nm_filter' => 'filter-do',
            'dr_tgl' => date('Y-m-d'),
            'sp_tgl' => date('Y-m-d')
        ];

        return view('sdm::daily-report.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        // if ($denied = $this->requireAccess('daily-report.create')) {
        //     return $denied;
        // }

        $user = Auth::user();
        $role = session('role');

        // Check if user is admin/super admin
        $isAdmin = $this->isAdminUser($role);

        $roleType = $this->determineRoleType($role);

        $data = [
            'role_type' => $roleType,
            'date' => date('Y-m-d'),
            'is_admin' => $isAdmin,
            'isEdit' => false,
            'items_map' => collect(),
            'report' => null,
        ];


        // Always load armada data for all users
        $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')->get();

        // Get data based on role type
        switch ($roleType) {
            case 'operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', date('Y-m-d'))
                    ->get();
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')->get();
                break;

            case 'pic_operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', date('Y-m-d'))
                    ->where('status', 'dibuat')
                    ->get();
                $data['historicalDos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', '<', date('Y-m-d'))
                    ->where('status', 'dibuat')
                    ->orderBy('tanggal_do', 'desc')
                    ->limit(50)
                    ->get();
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')->get();
                break;

            case 'staff_operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', now()->addDay())
                    ->get();
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')->get();
                break;

            case 'keuangan':
                // Get kas/bank data for current user's role
                $data['kas_banks'] = \App\Helpers\KasBankHelper::getKasBankOptions();
                break;

            default:
                // General staff
                break;
        }

        return view('sdm::daily-report.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // if ($denied = $this->requireAccess('daily-report.create')) {
        //     return $denied;
        // }

        $validated = $request->validate([
            'report_date' => 'required|date',
            'role_type' => 'required|string',
            'summary' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.type' => 'nullable|string',
            'items.*.reference_id' => 'nullable|integer',
            'items.*.description' => 'nullable|string',
            'items.*.status' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
            'items.*.customer' => 'nullable|string',
            'items.*.rute' => 'nullable|string',
            'items.*.do_id' => 'nullable',
            'items.*.tujuan' => 'nullable|string',
            'items.*.qty' => 'nullable',
            'items.*.status_muat' => 'nullable|string',
            'items.*.kode_do' => 'nullable|string',
            'items.*.nominal_sangu' => 'nullable|string',
            'items.*.kendaraan' => 'nullable|string',
            'items.*.alasan' => 'nullable|string',
            'items.*.solusi' => 'nullable|string',
            'items.*.bbm' => 'nullable|string',
            'items.*.sangu' => 'nullable|string',
            'items.*.total' => 'nullable|string',
            'items.*.driver' => 'nullable|string',
            'items.*.driver_presence' => 'nullable|string',
            'items.*.driver_name' => 'nullable|string|max:255',
            'items.*.problem' => 'nullable|string',
            'items.*.solusi' => 'nullable|string',
            'items.*.kerusakan' => 'nullable|string',
            'items.*.pic_bengkel' => 'nullable|string',
            'items.*.estimasi' => 'nullable|string',
            'items.*.nama' => 'nullable|string',
            'items.*.no_telp' => 'nullable|string',
            'items.*.bidang_usaha' => 'nullable|string',
            'items.*.nama_sopir' => 'nullable|string',
            'items.*.nopol' => 'nullable|string',
            'items.*.uraian' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Check if report already exists for today
            $existingReport = DailyReport::where('user_id', Auth::id())
                ->whereDate('report_date', $validated['report_date'])
                ->where('role_type', $validated['role_type'])
                ->first();

            if ($existingReport) {
                return redirect()->back()->with('error', 'Laporan untuk hari ini sudah dibuat. Silakan edit laporan yang sudah ada.');
            }

            $report = DailyReport::create([
                'user_id' => Auth::id(),
                'report_date' => $validated['report_date'],
                'role_type' => $validated['role_type'],
                'summary' => $validated['summary'],
            ]);

            // Save report items
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $key => $item) {
                    // Skip empty items
                    if (empty($item['type']) && empty($item['description'])) {
                        continue;
                    }

                    // For DO plan items, combine customer and rute into notes
                    $notes = $item['notes'] ?? null;
                    if (($item['type'] ?? '') === 'do_plan' && (isset($item['customer']) || isset($item['rute']))) {
                        $notes = 'Customer: ' . ($item['customer'] ?? '-') . ' | Rute: ' . ($item['rute'] ?? '-');
                    }

                    if (($item['type'] ?? '') === 'armada') {
                        $notes = $this->normalizeArmadaNotes($item);
                    }

                    // Handle PIC Operasional Detail DO items with nested fields
                    if (str_starts_with($key, 'pic_detail_do_') && isset($item['do_id'])) {
                        $notes = json_encode([
                            'do_id' => $item['do_id'] ?? null,
                            'reference_id' => $item['reference_id'] ?? null,
                            'customer' => $item['customer'] ?? '',
                            'tujuan' => $item['tujuan'] ?? '',
                            'qty' => $item['qty'] ?? '',
                            'status_muat' => $item['status_muat'] ?? '',
                            'kendaraan' => $item['kendaraan'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle PIC Operasional DO Tidak Termuat items with nested fields
                    if (str_starts_with($key, 'pic_do_tidak_termuat_') && isset($item['do_id'])) {
                        $notes = json_encode([
                            'do_id' => $item['do_id'] ?? null,
                            'reference_id' => $item['reference_id'] ?? null,
                            'alasan' => $item['alasan'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle PIC Keuangan Operasional items with nested fields
                    if (str_starts_with($key, 'pic_keuangan_')) {
                        $notes = json_encode([
                            'kode_do' => $item['kode_do'] ?? '',
                            'tujuan' => $item['tujuan'] ?? '',
                            'nominal_sangu' => $item['nominal_sangu'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle Problem & Solusi items
                    if (in_array($key, ['problem_do', 'problem_bengkel', 'problem_legalitas', 'problem_customer'])) {
                        $notes = json_encode([
                            'problem' => $item['problem'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle PIC Operasional Armada Status items
                    if (str_starts_with($key, 'pic_armada_status_')) {
                        $notes = $item['status'] ?? 'stand_by';
                    }

                    // Handle PIC Operasional DO Status items
                    if (str_starts_with($key, 'pic_do_status_')) {
                        $notes = $item['status'] ?? '';
                    }

                    // Handle Armada Rusak Detail items
                    if (str_starts_with($key, 'armada_rusak_')) {
                        $notes = json_encode([
                            'kerusakan' => $item['kerusakan'] ?? '',
                            'pic_bengkel' => $item['pic_bengkel'] ?? '',
                            'estimasi' => $item['estimasi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle Customer Baru Detail items
                    if (str_starts_with($key, 'pic_customer_baru_detail_')) {
                        $notes = json_encode([
                            'nama' => $item['nama'] ?? '',
                            'no_telp' => $item['no_telp'] ?? '',
                            'bidang_usaha' => $item['bidang_usaha'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    if (str_starts_with($key, 'pic_driver_ijin_detail_')) {
                        $notes = json_encode([
                            'nama_sopir' => $item['nama_sopir'] ?? '',
                            'nopol' => $item['nopol'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle Komplain Detail items
                    if (str_starts_with($key, 'pic_komplain_detail_')) {
                        $notes = json_encode([
                            'uraian' => $item['uraian'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    DailyReportItem::create([
                        'daily_report_id' => $report->id,
                        'type' => $item['type'] ?? 'task',
                        'reference_id' => $item['reference_id'] ?? null,
                        'description' => $item['description'] ?? 'No description',
                        'status' => $item['status'] ?? null,
                        'notes' => $notes,
                    ]);

                    // Update DO status and create checkpoint for do_status items
                    if (str_starts_with($key, 'pic_do_status_') && !empty($item['status'])) {
                        $doModel = DoModel::find($item['reference_id']);
                        if ($doModel) {
                            $doModel->status = $item['status'];
                            $doModel->save();

                            DoCheckpoint::create([
                                'do_id' => $doModel->id,
                                'jenis_checkpoint' => $item['status'],
                                'keterangan' => 'Update dari Daily Report PIC Operasional',
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('do.index')->with('success', 'Laporan harian berhasil disimpan.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan laporan: ' . $th->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('daily-report.index')) {
            return $denied;
        }

        $report = DailyReport::with([
            'user',
            'items',
            'items.doReference:id,kode_do,customer_id,trayek_id',
            'items.doReference.customer:id,nama',
            'items.doReference.trayek:id,nama_trayek',
            'items.armadaReference:id,nopol,nama_armada'
        ])->findOrFail($id);

        return view('sdm::daily-report.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        // if ($denied = $this->requireAccess('daily-report.update')) {
        //     return $denied;
        // }

        $report = DailyReport::with('items')->findOrFail($id);

        // Check if user can edit this report
        if ($report->user_id !== Auth::id() && !$this->hasAccess('daily-report.admin')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit laporan ini.');
        }

        $data = [
            'report' => $report,
            'role_type' => $report->role_type,
            'date' => $report->report_date->format('Y-m-d'),
        ];

        // Get data based on role type
        switch ($report->role_type) {
            case 'operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', $report->report_date)
                    ->get();
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')->get();
                break;

            case 'pic_operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', $report->report_date)
                    ->get();
                $data['historicalDos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', '<', $report->report_date)
                    ->orderBy('tanggal_do', 'desc')
                    ->limit(50)
                    ->get();
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')->get();
                break;

            case 'staff_operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', $report->report_date->addDay())
                    ->get();
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')->get();
                break;

            case 'keuangan':
                $data['kas_banks'] = \App\Helpers\KasBankHelper::getKasBankOptions();
                break;
        }

        // Map items by description for easier retrieval in view
        $data['items_map'] = $report->items->groupBy('description');

        return view('sdm::daily-report.create', $data);

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // if ($denied = $this->requireAccess('daily-report.update')) {
        //     return $denied;
        // }

        $report = DailyReport::findOrFail($id);

        // Check if user can edit this report
        if ($report->user_id !== Auth::id() && !$this->hasAccess('daily-report.admin')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit laporan ini.');
        }

        // Base validation rules
        $rules = [
            'report_date' => 'required|date',
            'role_type' => 'required|string',
            'summary' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.type' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.reference_id' => 'nullable|integer',

            'items.*.status' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
            'items.*.customer' => 'nullable|string',
            'items.*.rute' => 'nullable|string',
            'items.*.do_id' => 'nullable',
            'items.*.tujuan' => 'nullable|string',
            'items.*.qty' => 'nullable',
            'items.*.status_muat' => 'nullable|string',
            'items.*.kode_do' => 'nullable|string',
            'items.*.nominal_sangu' => 'nullable|string',
            'items.*.kendaraan' => 'nullable|string',
            'items.*.alasan' => 'nullable|string',
            'items.*.solusi' => 'nullable|string',
            'items.*.bbm' => 'nullable|string',
            'items.*.sangu' => 'nullable|string',
            'items.*.total' => 'nullable|string',
            'items.*.driver' => 'nullable|string',
            'items.*.driver_presence' => 'nullable|string',
            'items.*.driver_name' => 'nullable|string|max:255',
            'items.*.problem' => 'nullable|string',
            'items.*.solusi' => 'nullable|string',
            'items.*.kerusakan' => 'nullable|string',
            'items.*.pic_bengkel' => 'nullable|string',
            'items.*.estimasi' => 'nullable|string',
            'items.*.nama' => 'nullable|string',
            'items.*.no_telp' => 'nullable|string',
            'items.*.bidang_usaha' => 'nullable|string',
            'items.*.nama_sopir' => 'nullable|string',
            'items.*.nopol' => 'nullable|string',
            'items.*.uraian' => 'nullable|string',
        ];

        $roleType = $request->input('role_type');
        if ($roleType === 'general') {
            $rules['items.main_task.description'] = 'required|string';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $report->update([
                'report_date' => $validated['report_date'],
                'role_type' => $validated['role_type'],
                'summary' => $validated['summary'],
            ]);

            // Delete existing items and create new ones
            $report->items()->delete();

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $key => $item) {
                    // Skip empty items
                    if (empty($item['type']) && empty($item['description']) && empty($item['notes'])) {
                        continue;
                    }

                    // For DO plan items, combine customer and rute into notes
                    $notes = $item['notes'] ?? null;
                    if ($item['type'] === 'do_plan' && (isset($item['customer']) || isset($item['rute']))) {
                        $notes = 'Customer: ' . ($item['customer'] ?? '-') . ' | Rute: ' . ($item['rute'] ?? '-');
                    }

                    if ($item['type'] === 'armada') {
                        $notes = $this->normalizeArmadaNotes($item);
                    }

                    // Handle PIC Operasional Detail DO items with nested fields
                    if (str_starts_with($key, 'pic_detail_do_') && isset($item['do_id'])) {
                        $notes = json_encode([
                            'do_id' => $item['do_id'] ?? null,
                            'reference_id' => $item['reference_id'] ?? null,
                            'customer' => $item['customer'] ?? '',
                            'tujuan' => $item['tujuan'] ?? '',
                            'qty' => $item['qty'] ?? '',
                            'status_muat' => $item['status_muat'] ?? '',
                            'kendaraan' => $item['kendaraan'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle PIC Operasional DO Tidak Termuat items with nested fields
                    if (str_starts_with($key, 'pic_do_tidak_termuat_') && isset($item['do_id'])) {
                        $notes = json_encode([
                            'do_id' => $item['do_id'] ?? null,
                            'reference_id' => $item['reference_id'] ?? null,
                            'alasan' => $item['alasan'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle PIC Keuangan Operasional items with nested fields
                    if (str_starts_with($key, 'pic_keuangan_')) {
                        $notes = json_encode([
                            'kode_do' => $item['kode_do'] ?? '',
                            'tujuan' => $item['tujuan'] ?? '',
                            'nominal_sangu' => $item['nominal_sangu'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle Problem & Solusi items
                    if (in_array($key, ['problem_do', 'problem_bengkel', 'problem_legalitas', 'problem_customer'])) {
                        $notes = json_encode([
                            'problem' => $item['problem'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle PIC Operasional Armada Status items
                    if (str_starts_with($key, 'pic_armada_status_')) {
                        $notes = $item['status'] ?? 'stand_by';
                    }

                    // Handle PIC Operasional DO Status items
                    if (str_starts_with($key, 'pic_do_status_')) {
                        $notes = $item['status'] ?? '';
                    }

                    // Handle Armada Rusak Detail items
                    if (str_starts_with($key, 'armada_rusak_')) {
                        $notes = json_encode([
                            'kerusakan' => $item['kerusakan'] ?? '',
                            'pic_bengkel' => $item['pic_bengkel'] ?? '',
                            'estimasi' => $item['estimasi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle Customer Baru Detail items
                    if (str_starts_with($key, 'pic_customer_baru_detail_')) {
                        $notes = json_encode([
                            'nama' => $item['nama'] ?? '',
                            'no_telp' => $item['no_telp'] ?? '',
                            'bidang_usaha' => $item['bidang_usaha'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    if (str_starts_with($key, 'pic_driver_ijin_detail_')) {
                        $notes = json_encode([
                            'nama_sopir' => $item['nama_sopir'] ?? '',
                            'nopol' => $item['nopol'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    // Handle Komplain Detail items
                    if (str_starts_with($key, 'pic_komplain_detail_')) {
                        $notes = json_encode([
                            'uraian' => $item['uraian'] ?? '',
                        ], JSON_UNESCAPED_UNICODE);
                    }

                    DailyReportItem::create([
                        'daily_report_id' => $report->id,
                        'type' => $item['type'] ?? 'task',
                        'reference_id' => $item['reference_id'] ?? null,
                        'description' => $item['description'] ?? 'No description',
                        'status' => $item['status'] ?? null,
                        'notes' => $notes,

]);

                    // Update DO status and create checkpoint for do_status items
                    if (str_starts_with($key, 'pic_do_status_') && !empty($item['status'])) {
                        $doModel = DoModel::find($item['reference_id']);
                        if ($doModel) {
                            $doModel->status = $item['status'];
                            $doModel->save();

                            DoCheckpoint::create([
                                'do_id' => $doModel->id,
                                'jenis_checkpoint' => $item['status'],
                                'keterangan' => 'Update dari Daily Report PIC Operasional',
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('daily-report.index')->with('success', 'Laporan harian berhasil diperbarui.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui laporan: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('daily-report.delete')) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        }

        try {
            DB::beginTransaction();

            $report = DailyReport::findOrFail($id);

            // Check if user can delete this report
            if ($report->user_id !== Auth::id() && !$this->hasAccess('daily-report.admin')) {
                return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk menghapus laporan ini.'], 403);
            }

            $report->items()->delete();
            $report->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Laporan berhasil dihapus.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus laporan: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display summary of all daily reports with full details
     * @return Renderable
     */
    public function summary()
    {
        if ($denied = $this->requireAccess('daily-report.index')) {
            return $denied;
        }

        // Get all reports with optimized eager loading
        $reports = DailyReport::with([
                'user:id_user,nm_user',
                'items' => function ($query) {
                    $query->select('daily_report_id', 'type', 'reference_id', 'description', 'status', 'notes');
                },
                'items.doReference:id,kode_do,customer_id', // Load DO references
                'items.doReference.customer:id,nama', // Load customer for DOs (correct column name)
                'items.armadaReference:id,nopol,nama_armada' // Load Armada references
            ])
            ->when(!$this->hasAccess('daily-report.admin'), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->select('id', 'user_id', 'report_date', 'role_type', 'summary', 'created_at', 'updated_at')
            ->latest('report_date')
            ->paginate(5); // Reduced to 5 per page for better performance

        return view('sdm::daily-report.summary', compact('reports'));
    }

    /**
     * Get summary data for DataTables
     */
    public function getSummaryData(Request $request)
    {
        $query = DailyReport::with(['user', 'items'])
            ->when(!$this->hasAccess('daily-report.admin'), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->when($request->filter_date, function ($q) use ($request) {
                return $q->whereDate('report_date', $request->filter_date);
            })
            ->when($request->filter_role, function ($q) use ($request) {
                return $q->where('role_type', $request->filter_role);
            })
            ->when($request->filter_employee, function ($q) use ($request) {
                return $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('nm_user', 'like', '%' . $request->filter_employee . '%');
                });
            })
            ->latest('report_date');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('report_date', fn($row) => $row->report_date->format('d-m-Y'))
            ->addColumn('karyawan', fn($row) => $row->user->nm_user ?? '-')
            ->editColumn('role_type', function ($row) {
                $badges = [
                    'operasional' => 'bg-primary',
                    'staff_operasional' => 'bg-info',
                    'pic_operasional' => 'bg-warning text-dark',
                    'keuangan' => 'bg-success',
                    'general' => 'bg-secondary',
                ];
                $badge = $badges[$row->role_type] ?? 'bg-secondary';
                return '<span class="badge ' . $badge . '">' . ucwords(str_replace('_', ' ', $row->role_type)) . '</span>';
            })
            ->addColumn('items_summary', function ($row) {
                $summary = '';

                if (in_array($row->role_type, ['operasional', 'staff_operasional'])) {
                    $doTomorrowItem = $row->items->where('type', 'task')->where('description', 'Rencana DO Besok')->first();
                    $doPlanItems = $row->items->where('type', 'do_plan');
                    $armadaItems = $row->items->where('type', 'armada');

                    $doCount = $doTomorrowItem ? $doTomorrowItem->notes : '0';
                    $summary = "Rencana DO: {$doCount} | Detail: {$doPlanItems->count()} | Armada: {$armadaItems->count()}";

                } elseif ($row->role_type === 'pic_operasional') {
                    $summary = 'PIC: ' . $row->items->count() . ' item laporan';

                } elseif ($row->role_type === 'keuangan') {
                    $bankItems = $row->items->whereIn('type', ['bank_transaksi', 'bank_nominal', 'bank_mutasi', 'bank_nominal_mutasi']);
                    $bankCount = $bankItems->groupBy('reference_id')->count();

                    $summary = "Bank: {$bankCount} | Total Item: {$bankItems->count()}";

                } elseif ($row->role_type === 'general') {
                    $mainTaskItem = $row->items->where('type', 'task')->first();
                    $summary = $mainTaskItem ? 'Kegiatan: ' . Str::limit($mainTaskItem->description, 50) : 'Tidak ada kegiatan';
                }

                return $summary ?: 'Tidak ada data';
            })
            ->addColumn('summary_text', fn($row) => $row->summary ? Str::limit($row->summary, 100) : '-')
            ->addColumn('action', function ($row) {
                $show = route('daily-report.show', $row->id);
                return '<a href="' . $show . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> Lihat</a>';
            })
            ->rawColumns(['role_type', 'action'])
            ->make(true);
    }
    public function get_data(Request $request)
    {
        $query = DailyReport::with(['user'])
            ->when(!$this->hasAccess('daily-report.admin'), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->latest('report_date');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('report_date', fn($row) => $row->report_date->format('d-m-Y'))
            ->addColumn('karyawan', fn($row) => $row->user->nm_user ?? '-')
            ->editColumn('role_type', function ($row) {
                $badges = [
                    'operasional' => 'bg-primary',
                    'staff_operasional' => 'bg-info',
                    'pic_operasional' => 'bg-warning text-dark',
                    'keuangan' => 'bg-success',
                    'general' => 'bg-secondary',
                ];
                $badge = $badges[$row->role_type] ?? 'bg-secondary';
                return '<span class="badge ' . $badge . '">' . ucwords(str_replace('_', ' ', $row->role_type)) . '</span>';
            })
            ->addColumn('items_count', fn($row) => $row->items()->count() . ' item')
            ->addColumn('action', function ($row) {
                $show = route('daily-report.show', $row->id);
                $edit = route('daily-report.edit', $row->id);

                $canEdit = $row->user_id === Auth::id() || $this->hasAccess('daily-report.admin');
                $canDelete = $row->user_id === Auth::id() || $this->hasAccess('daily-report.admin');

                $editBtn = $canEdit ? '<li><a class="dropdown-item" href="' . $edit . '"><i class="fa fa-edit text-warning"></i> Edit</a></li>' : '';
                $deleteBtn = $canDelete ? '<li><button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i> Hapus</button></li>' : '';

                return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="' . $show . '"><i class="fa fa-eye text-primary"></i> Lihat</a></li>
                            ' . $editBtn . '
                            ' . $deleteBtn . '
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['role_type', 'action'])
            ->make(true);
    }

    /**
     * Get armada location data for DataTables (AJAX)
     */
    public function getArmadaLocationData(Request $request, $reportId)
    {
        if ($denied = $this->requireAccess('daily-report.index')) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $report = DailyReport::findOrFail($reportId);

        // Check access - user can only see their own reports unless admin
        if ($report->user_id !== Auth::id() && !$this->hasAccess('daily-report.admin')) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $query = DailyReportItem::with(['armadaReference:id,nopol,nama_armada'])
            ->where('daily_report_id', $reportId)
            ->where('type', 'armada')
            ->select('id', 'reference_id', 'notes');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nopol', function ($row) {
                return $row->armadaReference->nopol ?? 'N/A';
            })
            ->addColumn('nama_armada', function ($row) {
                return $row->armadaReference->nama_armada ?? '-';
            })
            ->addColumn('lokasi', function ($row) {
                $parsed = $this->parseArmadaNotes($row->notes);
                return $parsed['lokasi'];
            })
            ->addColumn('status_armada', function ($row) {
                $parsed = $this->parseArmadaNotes($row->notes);
                return $parsed['status'];
            })
            ->addColumn('sopir', function ($row) {
                $parsed = $this->parseArmadaNotes($row->notes);
                return $parsed['sopir'];
            })
            ->rawColumns(['nopol'])
            ->make(true);
    }

    /**
     * Get statistics for summary dashboard
     */
    public function getStatistics()
    {
        $query = DailyReport::query()
            ->when(!$this->hasAccess('daily-report.admin'), function ($q) {
                return $q->where('user_id', Auth::id());
            });

        $total = $query->count();
        $today = $query->whereDate('report_date', today())->count();
        $thisWeek = $query->whereBetween('report_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        $thisMonth = $query->whereMonth('report_date', now()->month)
            ->whereYear('report_date', now()->year)
            ->count();

        return response()->json([
            'total' => $total,
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
        ]);
    }

    /**
     * Determine role type based on session role
     */
    private function determineRoleType($role)
    {
        if (!$role) return 'general';

        $roleName = strtolower($role['nm_role'] ?? '');

        if (str_contains($roleName, 'pic') && str_contains($roleName, 'operasional')) {
            return 'pic_operasional';
        } elseif (str_contains($roleName, 'operasional')) {
            return 'operasional';
        } elseif (str_contains($roleName, 'keuangan')) {
            return 'keuangan';
        }

        return 'general';
    }

    /**
     * Check if user is admin/super admin
     */
    private function isAdminUser($role)
    {
        if (!$role) return false;

        $roleName = strtolower($role['nm_role'] ?? '');
        $roleId = $role['id_role'] ?? 0;

        // Check if user is admin by role ID (1 is typically admin) or role name
        return $roleId == 1 ||
               str_contains($roleName, 'admin') ||
               str_contains($roleName, 'administrator') ||
               str_contains($roleName, 'super');
    }

    /**
     * AJAX endpoint to get data based on selected role type
     */
    public function getRoleData(Request $request)
    {
        $roleType = $request->role_type;
        $date = $request->date ?? date('Y-m-d');

        $data = [];

        switch ($roleType) {
            case 'operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', $date)
                    ->get()
                    ->map(function ($do) {
                        return [
                            'id' => $do->id,
                            'kode_do' => $do->kode_do,
                            'customer' => $do->customer->nama ?? '-',
                            'trayek' => $do->trayek->nama_trayek ?? '-',
                        ];
                    });
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')
                    ->get()
                    ->map(function ($armada) {
                        return [
                            'id' => $armada->id,
                            'nopol' => $armada->nopol,
                            'nama_armada' => $armada->nama_armada ?? '',
                        ];
                    });
                break;

            case 'pic_operasional':
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', $date)
                    ->where('status', 'dibuat')
                    ->get()
                    ->map(function ($do) {
                        return [
                            'id' => $do->id,
                            'kode_do' => $do->kode_do,
                            'customer' => $do->customer->nama ?? '-',
                            'trayek' => $do->trayek->nama_trayek ?? '-',
                            'status' => $do->status,
                        ];
                    });
                $data['historicalDos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', '<', $date)
                    ->where('status', 'dibuat')
                    ->orderBy('tanggal_do', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function ($do) {
                        return [
                            'id' => $do->id,
                            'kode_do' => $do->kode_do,
                            'tanggal_do' => $do->tanggal_do,
                            'customer' => $do->customer->nama ?? '-',
                            'trayek' => $do->trayek->nama_trayek ?? '-',
                            'status' => $do->status,
                        ];
                    });
                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')
                    ->get()
                    ->map(function ($armada) {
                        return [
                            'id' => $armada->id,
                            'nopol' => $armada->nopol,
                            'nama_armada' => $armada->nama_armada ?? '',
                        ];
                    });
                break;

            case 'staff_operasional':
                $nextDay = date('Y-m-d', strtotime($date . ' +1 day'));
                $data['dos'] = DoModel::with(['customer', 'trayek', 'armada', 'driver'])
                    ->whereDate('tanggal_do', $nextDay)
                    ->get()
                    ->map(function ($do) {
                        return [
                            'id' => $do->id,
                            'kode_do' => $do->kode_do,
                            'customer' => $do->customer->nama ?? '-',
                            'trayek' => $do->trayek->nama_trayek ?? '-',
                        ];
                    });

                $data['armadas'] = Armada::where('is_aset', true)->orderBy('nopol')
                    ->get()
                    ->map(function ($armada) {
                        return [
                            'id' => $armada->id,
                            'nopol' => $armada->nopol,
                            'nama_armada' => $armada->nama_armada ?? '',
                        ];
                    });
                break;

            case 'keuangan':
                // Get kas/bank data for current user's role
                $data['kas_banks'] = \App\Helpers\KasBankHelper::getKasBankOptions();
                $data['message'] = 'Data keuangan untuk tanggal ' . date('d-m-Y', strtotime($date));
                break;

            default:
                $data['message'] = 'Form laporan umum';
                break;
        }

        return response()->json($data);
    }

    public function getDoForSelect2(Request $request)
    {
        // Handle both old and new Select2 parameter formats
        $search = $request->q ?? $request->term ?? '';

        $excludedStatuses = ['Sudah di terima operasional', 'Sudah di terima admin keuangan', 'Sudah di tagihkan'];

        $dos = DoModel::with(['customer', 'trayek', 'armada'])
            ->whereNotIn('status', $excludedStatuses)
            ->when($search, function ($q) use ($search) {
                return $q->where('kode_do', 'like', '%' . $search . '%');
            })
            ->whereDate('tanggal_do', date('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($do) {
                return [
                    'id' => $do->kode_do,
                    'text' => $do->kode_do,
                    'customer' => $do->customer->nama ?? '',
                    'tujuan' => $do->trayek->nama_trayek ?? '',
                    'nopol' => $do->armada->nopol ?? '',
                    'armada_id' => $do->armada->id ?? null,
                ];
            });

        // If no results and has search, try without date filter
        if ($dos->isEmpty() && $search) {
            $dos = DoModel::with(['customer', 'trayek', 'armada'])
                ->whereNotIn('status', $excludedStatuses)
                ->where('kode_do', 'like', '%' . $search . '%')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($do) {
                    return [
                    'id' => $do->kode_do,
                    'text' => $do->kode_do,
                        'customer' => $do->customer->nama ?? '',
                        'tujuan' => $do->trayek->nama_trayek ?? '',
                        'nopol' => $do->armada->nopol ?? '',
                        'armada_id' => $do->armada->id ?? null,
                    ];
                });
        }

        // If still empty, show recent DO regardless of date
        if ($dos->isEmpty()) {
            $dos = DoModel::with(['customer', 'trayek', 'armada'])
                ->whereNotIn('status', $excludedStatuses)
                ->orderBy('tanggal_do', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($do) {
                    return [
                    'id' => $do->kode_do,
                    'text' => $do->kode_do,
                        'customer' => $do->customer->nama ?? '',
                        'tujuan' => $do->trayek->nama_trayek ?? '',
                        'nopol' => $do->armada->nopol ?? '',
                        'armada_id' => $do->armada->id ?? null,
                    ];
                });
        }

        return response()->json(['results' => $dos]);
    }

    private function normalizeArmadaNotes(array $item): string
    {
        $lokasi = trim((string) ($item['notes'] ?? ''));
        $status = trim((string) ($item['status'] ?? 'stand_by'));
        $sopir = trim((string) ($item['driver_presence'] ?? 'ada'));
        $driverName = trim((string) ($item['driver_name'] ?? ''));

        if ($lokasi === '' && $status === '' && $sopir === '' && $driverName === '') {
            return '';
        }

        return 'Lokasi: ' . ($lokasi !== '' ? $lokasi : '-')
            . ' | Status: ' . ($status !== '' ? $status : '-')
            . ' | Sopir: ' . ($sopir !== '' ? $sopir : '-')
            . ' | Nama Sopir: ' . ($driverName !== '' ? $driverName : '-');
    }

    private function parseArmadaNotes(?string $notes): array
    {
        $result = [
            'lokasi' => '-',
            'status' => '-',
            'sopir' => '-',
            'driver_name' => '-',
        ];

        if (!$notes) {
            return $result;
        }

        $parts = explode(' | ', $notes);
        foreach ($parts as $part) {
            if (str_starts_with($part, 'Lokasi: ')) {
                $result['lokasi'] = trim(str_replace('Lokasi: ', '', $part)) ?: '-';
            }
            if (str_starts_with($part, 'Status: ')) {
                $value = trim(str_replace('Status: ', '', $part));
                $result['status'] = $value !== '' ? ucfirst(str_replace('_', ' ', $value)) : '-';
            }
            if (str_starts_with($part, 'Sopir: ')) {
                $value = trim(str_replace('Sopir: ', '', $part));
                if ($value === 'ada') {
                    $result['sopir'] = 'Ada Sopir';
                } elseif ($value === 'tidak_ada') {
                    $result['sopir'] = 'Tidak Ada Sopir';
                } else {
                    $result['sopir'] = $value !== '' ? $value : '-';
                }
            }
            if (str_starts_with($part, 'Nama Sopir: ')) {
                $value = trim(str_replace('Nama Sopir: ', '', $part));
                $result['driver_name'] = $value !== '' && $value !== '-' ? $value : '-';
            }
        }

        return $result;
    }
}
