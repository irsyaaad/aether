<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Sdm\Entities\Absensi;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{
    use HasAccessControl;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('absensi.index')) {
            return $denied;
        }

        $data['filter'] = [
            'dr_tgl' => request('dr_tgl', date('Y-m-d')),
            'sp_tgl' => request('sp_tgl', date('Y-m-d')),
        ];

        return view('sdm::absensi.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('sdm::absensi.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'      => 'required|in:masuk,pulang',
            'photo'     => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'nullable|numeric|max:1000000',
        ]);

        if (! Auth::check()) {
            return response()->json([
                'message' => 'User belum login',
            ], 401);
        }

        DB::beginTransaction();

        try {
            $photoPath = $request->file('photo')->store('absensi', 'public');

            $absensi = Absensi::create([
                'user_id'   => Auth::id(),
                'type'      => $request->type,
                'photo'     => $photoPath,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy'  => $request->accuracy,
                'absen_at'  => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Absensi berhasil disimpan',
                'data'    => $absensi,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ABSENSI ERROR', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal menyimpan absensi',
            ], 500);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('sdm::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('sdm::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $absensi = Absensi::findOrFail($id);

            // Cek apakah user dapat menghapus data ini
            // Admin (role 1) dapat menghapus semua data, user lain hanya data sendiri
            if (session('role')['id_role'] != 1 && $absensi->user_id != Auth::id()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk menghapus data absensi ini.',
                ], 403);
            }

            $absensi->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus data absensi: ' . $th->getMessage(),
            ], 500);
        }
        return response()->json([
            'message' => 'Data absensi berhasil dihapus.',
        ]);
    }

    public function get_data(Request $request)
    {
        // Group absensi by user and date
        $absensis = Absensi::with('user')
            ->select('user_id',
                DB::raw('DATE(absen_at) as tanggal'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "masuk" THEN TIME(absen_at) END) as jam_masuk'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "pulang" THEN TIME(absen_at) END) as jam_pulang'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "masuk" THEN latitude END) as lat_masuk'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "masuk" THEN longitude END) as lng_masuk'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "pulang" THEN latitude END) as lat_pulang'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "pulang" THEN longitude END) as lng_pulang'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "masuk" THEN photo END) as foto_masuk'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "pulang" THEN photo END) as foto_pulang'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "masuk" THEN id END) as id_masuk'),
                DB::raw('GROUP_CONCAT(CASE WHEN type = "pulang" THEN id END) as id_pulang')
            );

        if ($request->dr_tgl && $request->sp_tgl) {
            $absensis->whereBetween('absen_at', [$request->dr_tgl . ' 00:00:00', $request->sp_tgl . ' 23:59:59']);
        }

        // Filter berdasarkan role: jika bukan admin (role 1), hanya tampilkan data user sendiri
        if (session('role')['id_role'] != 1) {
            $absensis->where('user_id', Auth::id());
        }

        $absensis = $absensis->groupBy('user_id', DB::raw('DATE(absen_at)'))
            ->orderBy('tanggal', 'desc')
            ->orderBy('user_id');

        return DataTables::of($absensis)
            ->addIndexColumn()

            ->addColumn('nama', fn($q) => $q->user->nm_user ?? '-')

            ->addColumn('tanggal', fn($q) => date('d-m-Y', strtotime($q->tanggal)))

            ->addColumn('masuk', function ($q) {
                $html = '<div class="text-center">';
                if ($q->jam_masuk) {
                    $html .= '<div class="fw-bold text-success mb-1">Masuk</div>';
                    $html .= '<div class="small text-muted">' . date('H:i', strtotime($q->jam_masuk)) . '</div>';
                } else {
                    $html .= '<div class="text-muted">-</div>';
                }
                $html .= '</div>';
                return $html;
            })

            ->addColumn('pulang', function ($q) {
                $html = '<div class="text-center">';
                if ($q->jam_pulang) {
                    $html .= '<div class="fw-bold text-danger mb-1">Pulang</div>';
                    $html .= '<div class="small text-muted">' . date('H:i', strtotime($q->jam_pulang)) . '</div>';
                } else {
                    $html .= '<div class="text-muted">-</div>';
                }
                $html .= '</div>';
                return $html;
            })

            ->addColumn('lokasi', function ($q) {
                $html = '<div class="text-center">';

                // Lokasi masuk
                if ($q->lat_masuk && $q->lng_masuk) {
                    $url  = "https://www.google.com/maps?q={$q->lat_masuk},{$q->lng_masuk}";
                    $html .= '<div class="mb-1"><a href="' . $url . '" target="_blank" class="btn btn-xs btn-outline-success" title="Lokasi Masuk">📍 Masuk</a></div>';
                }

                // Lokasi pulang
                if ($q->lat_pulang && $q->lng_pulang) {
                    $url  = "https://www.google.com/maps?q={$q->lat_pulang},{$q->lng_pulang}";
                    $html .= '<div><a href="' . $url . '" target="_blank" class="btn btn-xs btn-outline-danger" title="Lokasi Pulang">📍 Pulang</a></div>';
                }

                if (! $q->lat_masuk && ! $q->lat_pulang) {
                    $html .= '<div class="text-muted">-</div>';
                }

                $html .= '</div>';
                return $html;
            })

            ->addColumn('foto', function ($q) {
                $html = '<div class="text-center">';

                // Foto masuk
                if ($q->foto_masuk) {
                    $html .= '<div class="mb-2">';
                    $html .= '<div class="small text-success mb-1 fw-bold">Masuk</div>';
                    $html .= '<img src="' . asset('storage/' . $q->foto_masuk) . '" class="img-thumbnail" width="50" style="cursor:pointer" onclick="window.open(this.src)">';
                    $html .= '</div>';
                }

                // Foto pulang
                if ($q->foto_pulang) {
                    $html .= '<div>';
                    $html .= '<div class="small text-danger mb-1 fw-bold">Pulang</div>';
                    $html .= '<img src="' . asset('storage/' . $q->foto_pulang) . '" class="img-thumbnail" width="50" style="cursor:pointer" onclick="window.open(this.src)">';
                    $html .= '</div>';
                }

                if (! $q->foto_masuk && ! $q->foto_pulang) {
                    $html .= '<div class="text-muted">-</div>';
                }

                $html .= '</div>';
                return $html;
            })

            ->addColumn('aksi', function ($q) {
                // Cek apakah user dapat menghapus data ini
                $canDelete = session('role')['id_role'] == 1 || $q->user_id == Auth::id();

                if (! $canDelete) {
                    return '<div class="text-center text-muted">-</div>';
                }

                $buttons  = '<div class="dropdown">';
                $buttons .= '<button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">';
                $buttons .= '<i class="fa fa-bars"></i>';
                $buttons .= '</button>';
                $buttons .= '<ul class="dropdown-menu">';

                // Hapus masuk jika ada
                if ($q->id_masuk) {
                    $buttons .= '<li><button class="dropdown-item text-danger" onclick="hapus(' . $q->id_masuk . ')"><i class="fa fa-trash me-2"></i>Hapus Masuk</button></li>';
                }

                // Hapus pulang jika ada
                if ($q->id_pulang) {
                    $buttons .= '<li><button class="dropdown-item text-danger" onclick="hapus(' . $q->id_pulang . ')"><i class="fa fa-trash me-2"></i>Hapus Pulang</button></li>';
                }

                // Jika tidak ada data masuk atau pulang
                if (! $q->id_masuk && ! $q->id_pulang) {
                    $buttons .= '<li><span class="dropdown-item text-muted">Tidak ada aksi</span></li>';
                }

                $buttons .= '</ul>';
                $buttons .= '</div>';

                return $buttons;
            })

            ->rawColumns(['masuk', 'pulang', 'lokasi', 'foto', 'aksi'])
            ->make(true);
    }

    public function index_laporan_absensi(Request $request)
    {
        $data['filter'] = [
            'nm_filter'   => 'filter-laporan-armada',
            'dr_tgl'      => $request->dr_tgl ?? date('Y-01-01'),
            'sp_tgl'      => $request->sp_tgl ?? date('Y-m-d'),
            'cetak_pdf'   => true,
            'cetak_excel' => true,
        ];
        return view('sdm::laporan.absensi', $data);
    }

    public function get_data_laporan_absensi(Request $request)
    {
        $dr_tgl = $request->dr_tgl ?? date('Y-01-01');
        $sp_tgl = $request->sp_tgl ?? date('Y-m-d');

        $data = Absensi::with('user')
            ->whereBetween('absen_at', [$dr_tgl . ' 00:00:00', $sp_tgl . ' 23:59:59']);

        // Filter berdasarkan role: jika bukan admin (role 1), hanya tampilkan data user sendiri
        if (session('role')['id_role'] != 1) {
            $data->where('user_id', Auth::id());
        }

        $data = $data->orderBy('absen_at', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tanggal', fn($row) => $row->absen_at->format('d/m/Y H:i'))
            ->addColumn('karyawan', fn($row) => $row->user->nm_user ?? '-')
            ->addColumn('tipe', function ($row) {
                return $row->type == 'masuk'
                    ? '<span class="badge bg-success">Masuk</span>'
                    : '<span class="badge bg-danger">Pulang</span>';
            })
            ->addColumn('foto', function ($row) {
                if ($row->photo) {
                    return '<img src="' . asset('storage/' . $row->photo) . '" class="img-thumbnail" width="60" style="cursor:pointer" onclick="window.open(this.src)">';
                }
                return '-';
            })
            ->addColumn('lokasi', function ($row) {
                if ($row->latitude && $row->longitude) {
                    return '<a href="https://www.google.com/maps?q=' . $row->latitude . ',' . $row->longitude . '" target="_blank" class="btn btn-xs btn-outline-primary"><i class="fa fa-map-marker"></i> Lihat Lokasi</a>';
                }
                return '-';
            })
            ->rawColumns(['tipe', 'foto', 'lokasi'])
            ->make(true);
    }
}
