<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Sdm\Entities\Karyawan;
use Modules\Sdm\Entities\Pkwt;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class PkwtController
 * @package Modules\Sdm\Http\Controllers
 */
class PkwtController extends Controller
{
    use HasAccessControl;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        // Calculate statistics
        $stats = [
            'total'    => Pkwt::count(),
            'aktif'    => Pkwt::aktif()->count(),
            'expiring' => Pkwt::expiringSoon(30)->count(),
            'expired'  => Pkwt::expired()->count(),
        ];

        $filter = [
            'nm_filter' => 'no-filter',
            'dr_tgl'    => request('dr_tgl', date('Y-m-01')),
            'sp_tgl'    => request('sp_tgl', date('Y-m-t')),
        ];

        return view('sdm::pkwt.index', compact('stats', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        return view('sdm::pkwt.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        $validator = Validator::make($request->all(), [
            'karyawan_id'      => 'required|exists:karyawan,id',
            'nomor_pkwt'       => 'nullable|string|max:255|unique:pkwt,nomor_pkwt',
            'judul_pkwt'       => 'required|string|max:255',
            'jenis_kontrak'    => 'required|in:pkwt,pkwtt,magang,freelance,konsultan',
            'tanggal_mulai'    => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'jabatan'          => 'nullable|string|max:255',
            'gaji_pokok'       => 'nullable|numeric|min:0',
            'file_pkwt'        => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'deskripsi'        => 'nullable|string',
            'ketentuan_khusus' => 'nullable|string',
            'auto_reminder'    => 'boolean',
            'reminder_days'    => 'nullable|integer|min:1|max:365',
            'catatan'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Auto-generate nomor PKWT if empty
            $nomorPkwt = $request->nomor_pkwt;
            if (empty($nomorPkwt)) {
                $year     = date('Y');
                $month    = date('m');
                $lastPkwt = Pkwt::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();

                $sequence  = $lastPkwt ? (intval(substr($lastPkwt->nomor_pkwt, -4)) + 1) : 1;
                $nomorPkwt = sprintf('PKWT/%s/%s/%04d', $year, $month, $sequence);
            }

            // Handle file upload
            $file     = $request->file('file_pkwt');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pkwt', $fileName, 'public');

            // Create PKWT record
            $pkwt = Pkwt::create([
                'karyawan_id'      => $request->karyawan_id,
                'nomor_pkwt'       => $nomorPkwt,
                'judul_pkwt'       => $request->judul_pkwt,
                'jenis_kontrak'    => $request->jenis_kontrak,
                'tanggal_mulai'    => $request->tanggal_mulai,
                'tanggal_berakhir' => $request->tanggal_berakhir,
                'jabatan'          => $request->jabatan,
                'gaji_pokok'       => $request->gaji_pokok,
                'deskripsi'        => $request->deskripsi,
                'ketentuan_khusus' => $request->ketentuan_khusus,
                'nama_file'        => $file->getClientOriginalName(),
                'path_file'        => $filePath,
                'ukuran_file'      => $file->getSize(),
                'format_file'      => $file->getClientOriginalExtension(),
                'status_kontrak'   => 'aktif',
                'auto_reminder'    => $request->boolean('auto_reminder'),
                'reminder_days'    => $request->reminder_days,
                'tanggal_reminder' => $request->auto_reminder && $request->reminder_days
                    ? now()->parse($request->tanggal_berakhir)->subDays($request->reminder_days)
                    : null,
                'catatan'          => $request->catatan,
            ]);

            return redirect()->route('pkwt.index')
                ->with('success', 'PKWT berhasil diupload.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        $pkwt = Pkwt::with(['karyawan', 'uploader', 'updater'])->findOrFail($id);
        return view('sdm::pkwt.show', compact('pkwt'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        $pkwt = Pkwt::findOrFail($id);
        return view('sdm::pkwt.edit', compact('pkwt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        $pkwt = Pkwt::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'karyawan_id'      => 'required|exists:karyawan,id',
            'nomor_pkwt'       => 'required|string|max:255|unique:pkwt,nomor_pkwt,' . $id,
            'judul_pkwt'       => 'required|string|max:255',
            'jenis_kontrak'    => 'required|in:pkwt,pkwtt,magang,freelance,konsultan',
            'tanggal_mulai'    => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'jabatan'          => 'nullable|string|max:255',
            'gaji_pokok'       => 'nullable|numeric|min:0',
            'file_pkwt'        => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'deskripsi'        => 'nullable|string',
            'ketentuan_khusus' => 'nullable|string',
            'status_kontrak'   => 'required|in:aktif,berakhir,diperpanjang,dibatalkan',
            'auto_reminder'    => 'boolean',
            'reminder_days'    => 'nullable|integer|min:1|max:365',
            'catatan'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'karyawan_id'      => $request->karyawan_id,
                'nomor_pkwt'       => $request->nomor_pkwt,
                'judul_pkwt'       => $request->judul_pkwt,
                'jenis_kontrak'    => $request->jenis_kontrak,
                'tanggal_mulai'    => $request->tanggal_mulai,
                'tanggal_berakhir' => $request->tanggal_berakhir,
                'jabatan'          => $request->jabatan,
                'gaji_pokok'       => $request->gaji_pokok,
                'deskripsi'        => $request->deskripsi,
                'ketentuan_khusus' => $request->ketentuan_khusus,
                'status_kontrak'   => $request->status_kontrak,
                'auto_reminder'    => $request->boolean('auto_reminder'),
                'reminder_days'    => $request->reminder_days,
                'tanggal_reminder' => $request->auto_reminder && $request->reminder_days
                    ? now()->parse($request->tanggal_berakhir)->subDays($request->reminder_days)
                    : null,
                'catatan'          => $request->catatan,
            ];

            // Handle file upload if new file is provided
            if ($request->hasFile('file_pkwt')) {
                // Delete old file
                if ($pkwt->path_file && Storage::disk('public')->exists($pkwt->path_file)) {
                    Storage::disk('public')->delete($pkwt->path_file);
                }

                // Upload new file
                $file     = $request->file('file_pkwt');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('pkwt', $fileName, 'public');

                $updateData['nama_file']   = $file->getClientOriginalName();
                $updateData['path_file']   = $filePath;
                $updateData['ukuran_file'] = $file->getSize();
                $updateData['format_file'] = $file->getClientOriginalExtension();
            }

            $pkwt->update($updateData);

            return redirect()->route('pkwt.index')
                ->with('success', 'PKWT berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        try {
            $pkwt = Pkwt::findOrFail($id);

            // Delete file from storage
            if ($pkwt->path_file && Storage::disk('public')->exists($pkwt->path_file)) {
                Storage::disk('public')->delete($pkwt->path_file);
            }

            $pkwt->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'PKWT berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download PKWT file.
     */
    public function download($id)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        try {
            $pkwt = Pkwt::findOrFail($id);

            if (! $pkwt->path_file || ! Storage::disk('public')->exists($pkwt->path_file)) {
                return back()->with('error', 'File tidak ditemukan.');
            }

            return Storage::disk('public')->download($pkwt->path_file, $pkwt->nama_file);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        $query = Pkwt::with(['karyawan'])->select('pkwt.*');

        // Apply filters
        if ($request->filled('dr_tgl') && $request->filled('sp_tgl')) {
            $query->whereBetween('tanggal_mulai', [$request->dr_tgl, $request->sp_tgl]);
        }

        if ($request->filled('status')) {
            $query->where('status_kontrak', $request->status);
        }

        if ($request->filled('jenis_kontrak')) {
            $query->where('jenis_kontrak', $request->jenis_kontrak);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('karyawan_nama', function ($row) {
                return $row->karyawan ? $row->karyawan->nama_lengkap : '-';
            })
            ->addColumn('jenis_badge', function ($row) {
                $class = $row->getJenisPkwtBadgeClass();
                return '<span class="badge ' . $class . '">' . strtoupper($row->jenis_kontrak) . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $class = $row->getStatusBadgeClass();
                return '<span class="badge ' . $class . '">' . ucfirst($row->status_kontrak) . '</span>';
            })
            ->addColumn('periode', function ($row) {
                $mulai    = $row->tanggal_mulai ? $row->tanggal_mulai->format('d/m/Y') : '-';
                $berakhir = $row->tanggal_berakhir ? $row->tanggal_berakhir->format('d/m/Y') : '-';
                return $mulai . ' - ' . $berakhir;
            })
            ->addColumn('sisa_hari', function ($row) {
                $sisaHari = $row->sisa_hari;
                if ($sisaHari <= 0) {
                    return '<span class="text-danger">Berakhir</span>';
                } elseif ($sisaHari <= 30) {
                    return '<span class="text-warning">' . $sisaHari . ' hari</span>';
                } else {
                    return '<span class="text-success">' . $sisaHari . ' hari</span>';
                }
            })
            ->addColumn('gaji_formatted', function ($row) {
                return $row->formatted_gaji;
            })
            ->addColumn('file_info', function ($row) {
                return '<small>' . $row->nama_file . '<br>(' . $row->formatted_size . ')</small>';
            })
            ->addColumn('action', function ($row) {
                $actions  = '<div class="dropdown">';
                $actions .= '<button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">';
                $actions .= '<i class="fa fa-cog"></i> Aksi';
                $actions .= '</button>';
                $actions .= '<ul class="dropdown-menu">';
                $actions .= '<li><a class="dropdown-item" href="' . route('pkwt.show', $row->id) . '"><i class="fa fa-eye text-info"></i> Detail</a></li>';
                $actions .= '<li><a class="dropdown-item" href="' . route('pkwt.download', $row->id) . '"><i class="fa fa-download text-success"></i> Download</a></li>';
                $actions .= '<li><a class="dropdown-item" href="' . route('pkwt.edit', $row->id) . '"><i class="fa fa-edit text-warning"></i> Edit</a></li>';
                $actions .= '<li><hr class="dropdown-divider"></li>';
                $actions .= '<li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deletePkwt(' . $row->id . ')"><i class="fa fa-trash"></i> Hapus</a></li>';
                $actions .= '</ul>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['jenis_badge', 'status_badge', 'periode', 'sisa_hari', 'file_info', 'action'])
            ->make(true);
    }

    /**
     * Get expiring contracts for notifications.
     */
    public function getExpiringContracts(Request $request)
    {
        if ($denied = $this->requireAccess('pkwt')) {
            return $denied;
        }

        $days      = $request->get('days', 30);
        $contracts = Pkwt::with(['karyawan'])
            ->expiringSoon($days)
            ->orderBy('tanggal_berakhir')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $contracts->map(function ($pkwt) {
                return [
                    'id'               => $pkwt->id,
                    'nomor_pkwt'       => $pkwt->nomor_pkwt,
                    'karyawan'         => $pkwt->karyawan->nama_lengkap ?? '-',
                    'tanggal_berakhir' => $pkwt->tanggal_berakhir->format('d/m/Y'),
                    'sisa_hari'        => $pkwt->sisa_hari,
                ];
            }),
        ]);
    }
}
