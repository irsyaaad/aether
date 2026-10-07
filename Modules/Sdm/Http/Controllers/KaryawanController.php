<?php
namespace Modules\Sdm\Http\Controllers;

use App\Traits\HasAccessControl;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Sdm\Entities\Karyawan;
use Modules\Sdm\Entities\KaryawanJob;
use Yajra\DataTables\Facades\DataTables;

class KaryawanController extends Controller
{
    use HasAccessControl;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if ($denied = $this->requireAccess('karyawan.index')) {
            return $denied;
        }

        return view('sdm::karyawan.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $data['jabatan'] = ['Direktur', 'Marketing', 'Staff Operasional', 'Staff Keuangan', 'Mekanik', 'Sopir', 'PIC Operasional'];
        return view('sdm::karyawan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "nama_lengkap"               => "nullable|string|max:255",
            "nama_panggilan"             => "nullable|string|max:255",
            "nik_ktp"                    => "nullable|string|max:50",
            "tempat_lahir"               => "nullable|string|max:255",
            "tanggal_lahir"              => "nullable|date|before:today",
            "jenis_kelamin"              => "nullable|in:Laki-laki,Perempuan",
            "agama"                      => "nullable|string|max:100",
            "status_pernikahan"          => "nullable|string|max:100",
            "alamat_ktp"                 => "nullable|string",
            "alamat_domisili"            => "nullable|string",
            "nomor_hp"                   => "nullable|string|max:20",
            "email"                      => "nullable|email|max:255",
            "nomor_npwp"                 => "nullable|string|max:20",
            "nomor_bpjs_kesehatan"       => "nullable|string|max:20",
            "nomor_bpjs_ketenagakerjaan" => "nullable|string|max:20",
            "nomor_sim"                  => "nullable|string|max:20",
            "jenis_sim"                  => "nullable|string",
            "gaji_pokok"                 => "nullable|numeric|min:0",
            "tunjangan_jabatan"          => "nullable|numeric|min:0",
            "tunjangan_makan"            => "nullable|numeric|min:0",
            "tunjangan_transport"        => "nullable|numeric|min:0",
            "nomor_rekening"             => "nullable|string|max:50",
            "nama_bank"                  => "nullable|string|max:100",
            "kontak_darurat_nama"        => "nullable|string|max:255",
            "kontak_darurat_nomor"       => "nullable|string|max:20",
            "kontak_darurat_hubungan"    => "nullable|string|max:100",
            "catatan_kinerja"            => "nullable|string",
            "training"                   => "nullable|string",
            "status"                     => "nullable|in:aktif,tidak_aktif",
            'dokumen_ktp'                => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_kk'                 => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_npwp'               => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_ijazah'             => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_diri'                  => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'jabatan'                    => 'nullable|in:Direktur,Marketing,Staff Operasional,Staff Keuangan,Mekanik,Sopir,PIC Operasional',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $karyawan                             = new Karyawan();
            $karyawan->nama_lengkap               = $request->nama_lengkap;
            $karyawan->nama_panggilan             = $request->nama_panggilan;
            $karyawan->nik_ktp                    = $request->nik_ktp;
            $karyawan->tempat_lahir               = $request->tempat_lahir;
            $karyawan->tanggal_lahir              = $request->tanggal_lahir;
            $karyawan->jenis_kelamin              = $request->jenis_kelamin;
            $karyawan->agama                      = $request->agama;
            $karyawan->status_pernikahan          = $request->status_pernikahan;
            $karyawan->alamat_ktp                 = $request->alamat_ktp;
            $karyawan->alamat_domisili            = $request->alamat_domisili;
            $karyawan->nomor_hp                   = $request->nomor_hp;
            $karyawan->email                      = $request->email;
            $karyawan->nomor_npwp                 = $request->nomor_npwp;
            $karyawan->nomor_bpjs_kesehatan       = $request->nomor_bpjs_kesehatan;
            $karyawan->nomor_bpjs_ketenagakerjaan = $request->nomor_bpjs_ketenagakerjaan;
            $karyawan->nomor_sim                  = $request->nomor_sim;
            $karyawan->jenis_sim                  = $request->jenis_sim;
            $karyawan->gaji_pokok                 = $request->gaji_pokok;
            $karyawan->tunjangan_jabatan          = $request->tunjangan_jabatan;
            $karyawan->tunjangan_makan            = $request->tunjangan_makan;
            $karyawan->tunjangan_transport        = $request->tunjangan_transport;
            $karyawan->nomor_rekening             = $request->nomor_rekening;
            $karyawan->nama_bank                  = $request->nama_bank;
            $karyawan->kontak_darurat_nama        = $request->kontak_darurat_nama;
            $karyawan->kontak_darurat_nomor       = $request->kontak_darurat_nomor;
            $karyawan->kontak_darurat_hubungan    = $request->kontak_darurat_hubungan;
            $karyawan->catatan_kinerja            = $request->catatan_kinerja;
            $karyawan->training                   = $request->training;
            $karyawan->status                     = $request->status ?? 'aktif';
            $karyawan->jabatan                    = $request->jabatan;
            $karyawan->id_spx                     = $request->id_spx;

            if (isset($request->dokumen_ktp) and $request->file('dokumen_ktp') != null) {
                $doc                   = $request->file('dokumen_ktp');
                $path_doc              = $doc->store('dokumen_ktp', 'public');
                $karyawan->dokumen_ktp = $path_doc;
            }
            if (isset($request->dokumen_kk) and $request->file('dokumen_kk') != null) {
                $doc                  = $request->file('dokumen_kk');
                $path_doc             = $doc->store('dokumen_kk', 'public');
                $karyawan->dokumen_kk = $path_doc;
            }
            if (isset($request->dokumen_npwp) and $request->file('dokumen_npwp') != null) {
                $doc                    = $request->file('dokumen_npwp');
                $path_doc               = $doc->store('dokumen_npwp', 'public');
                $karyawan->dokumen_npwp = $path_doc;
            }
            if (isset($request->dokumen_ijazah) and $request->file('dokumen_ijazah') != null) {
                $doc                      = $request->file('dokumen_ijazah');
                $path_doc                 = $doc->store('dokumen_ijazah', 'public');
                $karyawan->dokumen_ijazah = $path_doc;
            }
            if (isset($request->foto_diri) and $request->file('foto_diri') != null) {
                $doc                 = $request->file('foto_diri');
                $path_doc            = $doc->store('foto_diri', 'public');
                $karyawan->foto_diri = $path_doc;
            }
            $karyawan->save();
            // dd($karyawan, $request->all());
            $job = $request->job_history;

            // Hitung jumlah row berdasarkan salah satu array
            $count = count($job['jabatan']);

            for ($i = 0; $i < $count; $i++) {

                if ($job['jabatan'][$i] != null) {
                    $jobHistory                  = new KaryawanJob();
                    $jobHistory->karyawan_id     = $karyawan->id;
                    $jobHistory->jabatan         = $job['jabatan'][$i];
                    $jobHistory->divisi          = $job['divisi'][$i];
                    $jobHistory->lokasi_kerja    = $job['lokasi_kerja'][$i];
                    $jobHistory->status_kerja    = $job['status_kerja'][$i];
                    $jobHistory->tanggal_mulai   = $job['tanggal_mulai'][$i];
                    $jobHistory->tanggal_selesai = $job['tanggal_selesai'][$i];
                    $jobHistory->atasan_langsung = $job['atasan_langsung'][$i];
                    $jobHistory->shift_kerja     = $job['shift_kerja'][$i];
                    $jobHistory->catatan         = $job['catatan'][$i];
                    $jobHistory->save();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Pembuatan Karyawan gagal' . $e->getMessage());
        }

        return redirect('karyawan/' . $karyawan->id . '/detail')->with('success', 'Pembuatan Karyawan berhasil');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data['karyawan'] = Karyawan::with('job_history')->find($id);
        // dd($data['karyawan']->job_history);
        return view('sdm::karyawan.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data['karyawan'] = Karyawan::with('job_history')->find($id);
        $data['jabatan']  = ['Direktur', 'Marketing', 'Staff Operasional', 'Staff Keuangan', 'Mekanik', 'Sopir'];
        return view('sdm::karyawan.create', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "nama_lengkap"               => "nullable|string|max:255",
            "nama_panggilan"             => "nullable|string|max:255",
            "nik_ktp"                    => "nullable|string|max:50",
            "tempat_lahir"               => "nullable|string|max:255",
            "tanggal_lahir"              => "nullable|date|before:today",
            "jenis_kelamin"              => "nullable|in:Laki-laki,Perempuan",
            "agama"                      => "nullable|string|max:100",
            "status_pernikahan"          => "nullable|string|max:100",
            "alamat_ktp"                 => "nullable|string",
            "alamat_domisili"            => "nullable|string",
            "nomor_hp"                   => "nullable|string|max:20",
            "email"                      => "nullable|email|max:255",
            "nomor_npwp"                 => "nullable|string|max:20",
            "nomor_bpjs_kesehatan"       => "nullable|string|max:20",
            "nomor_bpjs_ketenagakerjaan" => "nullable|string|max:20",
            "nomor_sim"                  => "nullable|string|max:20",
            "jenis_sim"                  => "nullable|string",
            "gaji_pokok"                 => "nullable|numeric|min:0",
            "tunjangan_jabatan"          => "nullable|numeric|min:0",
            "tunjangan_makan"            => "nullable|numeric|min:0",
            "tunjangan_transport"        => "nullable|numeric|min:0",
            "nomor_rekening"             => "nullable|string|max:50",
            "nama_bank"                  => "nullable|string|max:100",
            "kontak_darurat_nama"        => "nullable|string|max:255",
            "kontak_darurat_nomor"       => "nullable|string|max:20",
            "kontak_darurat_hubungan"    => "nullable|string|max:100",
            "catatan_kinerja"            => "nullable|string",
            "training"                   => "nullable|string",
            "status"                     => "nullable|in:aktif,tidak_aktif",
            'dokumen_ktp'                => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_kk'                 => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_npwp'               => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_ijazah'             => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_diri'                  => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'jabatan'                    => 'nullable|in:Direktur,Marketing,Staff Operasional,Staff Keuangan,Mekanik,Sopir,PIC Operasional',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $karyawan                             = Karyawan::findOrfail($id);
            $karyawan->nama_lengkap               = $request->nama_lengkap;
            $karyawan->nama_panggilan             = $request->nama_panggilan;
            $karyawan->nik_ktp                    = $request->nik_ktp;
            $karyawan->tempat_lahir               = $request->tempat_lahir;
            $karyawan->tanggal_lahir              = $request->tanggal_lahir;
            $karyawan->jenis_kelamin              = $request->jenis_kelamin;
            $karyawan->agama                      = $request->agama;
            $karyawan->status_pernikahan          = $request->status_pernikahan;
            $karyawan->alamat_ktp                 = $request->alamat_ktp;
            $karyawan->alamat_domisili            = $request->alamat_domisili;
            $karyawan->nomor_hp                   = $request->nomor_hp;
            $karyawan->email                      = $request->email;
            $karyawan->nomor_npwp                 = $request->nomor_npwp;
            $karyawan->nomor_bpjs_kesehatan       = $request->nomor_bpjs_kesehatan;
            $karyawan->nomor_bpjs_ketenagakerjaan = $request->nomor_bpjs_ketenagakerjaan;
            $karyawan->nomor_sim                  = $request->nomor_sim;
            $karyawan->gaji_pokok                 = $request->gaji_pokok;
            $karyawan->tunjangan_jabatan          = $request->tunjangan_jabatan;
            $karyawan->tunjangan_makan            = $request->tunjangan_makan;
            $karyawan->tunjangan_transport        = $request->tunjangan_transport;
            $karyawan->nomor_rekening             = $request->nomor_rekening;
            $karyawan->nama_bank                  = $request->nama_bank;
            $karyawan->kontak_darurat_nama        = $request->kontak_darurat_nama;
            $karyawan->kontak_darurat_nomor       = $request->kontak_darurat_nomor;
            $karyawan->kontak_darurat_hubungan    = $request->kontak_darurat_hubungan;
            $karyawan->catatan_kinerja            = $request->catatan_kinerja;
            $karyawan->training                   = $request->training;
            $karyawan->status                     = $request->status ?? 'aktif';
            $karyawan->jabatan                    = $request->jabatan;
            $karyawan->id_spx                     = $request->id_spx;
            $karyawan->jenis_sim                  = $request->jenis_sim;

            if (isset($request->dokumen_ktp) and $request->file('dokumen_ktp') != null) {
                $doc                   = $request->file('dokumen_ktp');
                $path_doc              = $doc->store('dokumen_ktp', 'public');
                $karyawan->dokumen_ktp = $path_doc;
            }
            if (isset($request->dokumen_kk) and $request->file('dokumen_kk') != null) {
                $doc                  = $request->file('dokumen_kk');
                $path_doc             = $doc->store('dokumen_kk', 'public');
                $karyawan->dokumen_kk = $path_doc;
            }
            if (isset($request->dokumen_npwp) and $request->file('dokumen_npwp') != null) {
                $doc                    = $request->file('dokumen_npwp');
                $path_doc               = $doc->store('dokumen_npwp', 'public');
                $karyawan->dokumen_npwp = $path_doc;
            }
            if (isset($request->dokumen_ijazah) and $request->file('dokumen_ijazah') != null) {
                $doc                      = $request->file('dokumen_ijazah');
                $path_doc                 = $doc->store('dokumen_ijazah', 'public');
                $karyawan->dokumen_ijazah = $path_doc;
            }
            if (isset($request->foto_diri) and $request->file('foto_diri') != null) {
                $doc                 = $request->file('foto_diri');
                $path_doc            = $doc->store('foto_diri', 'public');
                $karyawan->foto_diri = $path_doc;
            }
            $karyawan->save();
            // dd($karyawan, $request->all());
            KaryawanJob::where('karyawan_id', $id)->delete();

            $job = $request->job_history;

            // Hitung jumlah row berdasarkan salah satu array
            $count = count($job['jabatan']);

            for ($i = 0; $i < $count; $i++) {

                if ($job['jabatan'][$i] != null) {
                    $jobHistory                  = new KaryawanJob();
                    $jobHistory->karyawan_id     = $karyawan->id;
                    $jobHistory->jabatan         = $job['jabatan'][$i];
                    $jobHistory->divisi          = $job['divisi'][$i];
                    $jobHistory->lokasi_kerja    = $job['lokasi_kerja'][$i];
                    $jobHistory->status_kerja    = $job['status_kerja'][$i];
                    $jobHistory->tanggal_mulai   = $job['tanggal_mulai'][$i];
                    $jobHistory->tanggal_selesai = $job['tanggal_selesai'][$i];
                    $jobHistory->atasan_langsung = $job['atasan_langsung'][$i];
                    $jobHistory->shift_kerja     = $job['shift_kerja'][$i];
                    $jobHistory->catatan         = $job['catatan'][$i];
                    $jobHistory->save();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Update Karyawan gagal' . $e->getMessage());
        }

        return redirect('karyawan/' . $karyawan->id . '/detail')->with('success', 'Update Karyawan berhasil');
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
            $karyawan = Karyawan::findOrFail($id);
            KaryawanJob::where('karyawan_id', $id)->delete();
            $karyawan->delete();
            DB::commit();
            DB::disconnect();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data karyawan berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::disconnect();
            return response()->json([
                'status'  => 'error',
                'message' => 'Data karyawan gagal dihapus.',
            ]);
        }
    }

    public function get_data(Request $request)
    {
        $karyawan = Karyawan::with('creator')->orderBy('nama_lengkap', 'asc');

        return DataTables::of($karyawan)
            ->addIndexColumn()
            ->editColumn('nama_lengkap', function ($row) {
                return '<a href="' . route('karyawan.detail', $row->id) . '">' . $row->nama_lengkap . '<br>
                <span class="text-muted">' . $row->nik_ktp . '</span></a>';
            })
            ->addColumn('usia', function ($row) {
                if (! $row->tanggal_lahir) {
                    return '-';
                }
                return \Carbon\Carbon::parse($row->tanggal_lahir)->age . ' th';
            })
            ->addColumn('dibuat_oleh', function ($row) {
                $creator = $row->creator->nm_user ?? '-';
                $date    = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $creator . '</span><br><small class="text-muted">' . $date . '</small></div>';
            })
            ->addColumn('dokumen_ktp', function ($row) {
                if (! $row->dokumen_ktp) {
                    return '<span class="badge bg-secondary">Tidak ada</span>';
                }

                $url = route('karyawan.get-doc', ['pdf' => $row->dokumen_ktp]);
                return '<a href="' . $url . '" target="_blank" class="badge bg-primary">Lihat</a>';
            })
            ->addColumn('status_label', function ($row) {
                return $row->status == 'aktif'
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-danger">Tidak Aktif</span>';
            })
            ->addColumn('action', function ($row) {
                return '
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/karyawan/' . $row->id . '/detail">
                            <i class="fa-solid fa-eye text-primary"></i> Show
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="/karyawan/' . $row->id . '/edit">
                            <i class="fa-solid fa-pen text-warning"></i> Edit
                        </a>
                    </li>

                    <li>
                        <button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </li>
                </ul>
            </div>';
            })

            ->addColumn('foto_diri', function ($row) {
                if ($row->foto_diri) {
                    $url = asset('storage/' . $row->foto_diri);
                    return '<img src="' . $url . '" class="img-fluid rounded" style="max-height: 50px; cursor: pointer;" onclick="window.open(\'' . $url . '\', \'_blank\')">';
                }
                return '<span class="text-muted">-</span>';
            })
            ->editColumn('jenis_sim', function ($row) {
                return $row->jenis_sim ?? '-';
            })
            ->rawColumns(['dokumen_ktp', 'status_label', 'action', 'nama_lengkap', 'dibuat_oleh', 'foto_diri'])
            ->make(true);
    }

    public function get_doc(Request $request)
    {
        $path = $request->pdf;
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path);
        }
    }

    public function get_sopir(Request $request)
    {
        $search   = $request->get('term');
        $karyawan = Karyawan::where('nama_lengkap', 'like', '%' . $search . '%')->where('jabatan', 'sopir')->orderBy('nama_lengkap', 'asc')->get();
        return response()->json($karyawan);
    }

    public function get_marketing(Request $request)
    {
        $search   = $request->get('term');
        $karyawan = Karyawan::where('nama_lengkap', 'like', '%' . $search . '%')->where('jabatan', 'marketing')->orderBy('nama_lengkap', 'asc')->get();
        return response()->json($karyawan);
    }

    public function get_karyawan(Request $request)
    {
        $search = $request->get('q', '');
        $page   = $request->get('page', 1);
        $limit  = 10;

        $query = Karyawan::select('id', 'nama_lengkap')
            ->where('nama_lengkap', 'like', '%' . $search . '%');

        $total    = $query->count();
        $karyawan = $query->offset(($page - 1) * $limit)
            ->limit($limit)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $results = [];
        foreach ($karyawan as $k) {
            $results[] = [
                'id'   => $k->id,
                'text' => $k->nama_lengkap,
            ];
        }

        return response()->json([
            'results'    => $results,
            'pagination' => [
                'more' => ($page * $limit) < $total,
            ],
        ]);
    }
}
