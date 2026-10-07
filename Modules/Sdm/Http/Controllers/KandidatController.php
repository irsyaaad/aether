<?php
namespace Modules\Sdm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Sdm\Entities\Kandidat;
use Modules\Sdm\Entities\KandidatJob;
use Modules\Sdm\Entities\Ads;
use Yajra\DataTables\Facades\DataTables;

class KandidatController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('sdm::kandidat.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $ads = Ads::where('status', 'active')->orderBy('title', 'ASC')->get();
        return view('sdm::kandidat.create', compact('ads'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "ads_id"                    => "nullable|exists:ads,id",
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
            "status"                     => "nullable|in:aktif,nonaktif",
            'dokumen_ktp'                => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_kk'                 => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_npwp'               => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_ijazah'             => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'foto_diri'                  => 'nullable|mimes:pdf,jpg,jpeg|max:5120',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $kandidat                             = new Kandidat();
            $kandidat->ads_id                    = $request->ads_id;
            $kandidat->nama_lengkap               = $request->nama_lengkap;
            $kandidat->nama_panggilan             = $request->nama_panggilan;
            $kandidat->nik_ktp                    = $request->nik_ktp;
            $kandidat->tempat_lahir               = $request->tempat_lahir;
            $kandidat->tanggal_lahir              = $request->tanggal_lahir;
            $kandidat->jenis_kelamin              = $request->jenis_kelamin;
            $kandidat->agama                      = $request->agama;
            $kandidat->status_pernikahan          = $request->status_pernikahan;
            $kandidat->alamat_ktp                 = $request->alamat_ktp;
            $kandidat->alamat_domisili            = $request->alamat_domisili;
            $kandidat->nomor_hp                   = $request->nomor_hp;
            $kandidat->email                      = $request->email;
            $kandidat->nomor_npwp                 = $request->nomor_npwp;
            $kandidat->nomor_bpjs_kesehatan       = $request->nomor_bpjs_kesehatan;
            $kandidat->nomor_bpjs_ketenagakerjaan = $request->nomor_bpjs_ketenagakerjaan;
            $kandidat->nomor_sim                  = $request->nomor_sim;
            $kandidat->gaji_pokok                 = $request->gaji_pokok;
            $kandidat->tunjangan_jabatan          = $request->tunjangan_jabatan;
            $kandidat->tunjangan_makan            = $request->tunjangan_makan;
            $kandidat->tunjangan_transport        = $request->tunjangan_transport;
            $kandidat->nomor_rekening             = $request->nomor_rekening;
            $kandidat->nama_bank                  = $request->nama_bank;
            $kandidat->kontak_darurat_nama        = $request->kontak_darurat_nama;
            $kandidat->kontak_darurat_nomor       = $request->kontak_darurat_nomor;
            $kandidat->kontak_darurat_hubungan    = $request->kontak_darurat_hubungan;
            $kandidat->catatan_kinerja            = $request->catatan_kinerja;
            $kandidat->training                   = $request->training;
            $kandidat->status                     = $request->status ?? 'aktif';

            if (isset($request->dokumen_ktp) and $request->file('dokumen_ktp') != null) {
                $doc                   = $request->file('dokumen_ktp');
                $path_doc              = $doc->store('dokumen_ktp');
                $kandidat->dokumen_ktp = $path_doc;
            }
            if (isset($request->dokumen_kk) and $request->file('dokumen_kk') != null) {
                $doc                  = $request->file('dokumen_kk');
                $path_doc             = $doc->store('dokumen_kk');
                $kandidat->dokumen_kk = $path_doc;
            }
            if (isset($request->dokumen_npwp) and $request->file('dokumen_npwp') != null) {
                $doc                    = $request->file('dokumen_npwp');
                $path_doc               = $doc->store('dokumen_npwp');
                $kandidat->dokumen_npwp = $path_doc;
            }
            if (isset($request->dokumen_ijazah) and $request->file('dokumen_ijazah') != null) {
                $doc                      = $request->file('dokumen_ijazah');
                $path_doc                 = $doc->store('dokumen_ijazah');
                $kandidat->dokumen_ijazah = $path_doc;
            }
            if (isset($request->foto_diri) and $request->file('foto_diri') != null) {
                $doc                 = $request->file('foto_diri');
                $path_doc            = $doc->store('foto_diri');
                $kandidat->foto_diri = $path_doc;
            }
            $kandidat->save();
            // dd($kandidat, $request->all());
            $job = $request->job_history;

            // Hitung jumlah row berdasarkan salah satu array
            $count = count($job['jabatan']);

            for ($i = 0; $i < $count; $i++) {

                if ($job['jabatan'][$i] != null) {
                    $jobHistory                  = new KandidatJob();
                    $jobHistory->kandidat_id     = $kandidat->id;
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

        return redirect('kandidat/' . $kandidat->id . '/detail')->with('success', 'Pembuatan Karyawan berhasil');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data['karyawan'] = Kandidat::with('job_history')->find($id);
        // dd($data['karyawan']->job_history);
        return view('sdm::kandidat.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data['kandidat'] = Kandidat::with('job_history')->find($id);
        $data['ads'] = Ads::where('status', 'active')->orderBy('title', 'ASC')->get();
        return view('sdm::kandidat.create', $data);
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
            "ads_id"                    => "nullable|exists:ads,id",
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
            "status"                     => "nullable|in:aktif,nonaktif",
            'dokumen_ktp'                => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_kk'                 => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_npwp'               => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_ijazah'             => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'foto_diri'                  => 'nullable|mimes:pdf,jpg,jpeg|max:5120',

        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $kandidat                             = Kandidat::findOrfail($id);
            $kandidat->ads_id                    = $request->ads_id;
            $kandidat->nama_lengkap               = $request->nama_lengkap;
            $kandidat->nama_panggilan             = $request->nama_panggilan;
            $kandidat->nik_ktp                    = $request->nik_ktp;
            $kandidat->tempat_lahir               = $request->tempat_lahir;
            $kandidat->tanggal_lahir              = $request->tanggal_lahir;
            $kandidat->jenis_kelamin              = $request->jenis_kelamin;
            $kandidat->agama                      = $request->agama;
            $kandidat->status_pernikahan          = $request->status_pernikahan;
            $kandidat->alamat_ktp                 = $request->alamat_ktp;
            $kandidat->alamat_domisili            = $request->alamat_domisili;
            $kandidat->nomor_hp                   = $request->nomor_hp;
            $kandidat->email                      = $request->email;
            $kandidat->nomor_npwp                 = $request->nomor_npwp;
            $kandidat->nomor_bpjs_kesehatan       = $request->nomor_bpjs_kesehatan;
            $kandidat->nomor_bpjs_ketenagakerjaan = $request->nomor_bpjs_ketenagakerjaan;
            $kandidat->nomor_sim                  = $request->nomor_sim;
            $kandidat->gaji_pokok                 = $request->gaji_pokok;
            $kandidat->tunjangan_jabatan          = $request->tunjangan_jabatan;
            $kandidat->tunjangan_makan            = $request->tunjangan_makan;
            $kandidat->tunjangan_transport        = $request->tunjangan_transport;
            $kandidat->nomor_rekening             = $request->nomor_rekening;
            $kandidat->nama_bank                  = $request->nama_bank;
            $kandidat->kontak_darurat_nama        = $request->kontak_darurat_nama;
            $kandidat->kontak_darurat_nomor       = $request->kontak_darurat_nomor;
            $kandidat->kontak_darurat_hubungan    = $request->kontak_darurat_hubungan;
            $kandidat->catatan_kinerja            = $request->catatan_kinerja;
            $kandidat->training                   = $request->training;
            $kandidat->status                     = $request->status ?? 'aktif';

            if (isset($request->dokumen_ktp) and $request->file('dokumen_ktp') != null) {
                $doc                   = $request->file('dokumen_ktp');
                $path_doc              = $doc->store('dokumen_ktp');
                $kandidat->dokumen_ktp = $path_doc;
            }
            if (isset($request->dokumen_kk) and $request->file('dokumen_kk') != null) {
                $doc                  = $request->file('dokumen_kk');
                $path_doc             = $doc->store('dokumen_kk');
                $kandidat->dokumen_kk = $path_doc;
            }
            if (isset($request->dokumen_npwp) and $request->file('dokumen_npwp') != null) {
                $doc                    = $request->file('dokumen_npwp');
                $path_doc               = $doc->store('dokumen_npwp');
                $kandidat->dokumen_npwp = $path_doc;
            }
            if (isset($request->dokumen_ijazah) and $request->file('dokumen_ijazah') != null) {
                $doc                      = $request->file('dokumen_ijazah');
                $path_doc                 = $doc->store('dokumen_ijazah');
                $kandidat->dokumen_ijazah = $path_doc;
            }
            if (isset($request->foto_diri) and $request->file('foto_diri') != null) {
                $doc                 = $request->file('foto_diri');
                $path_doc            = $doc->store('foto_diri');
                $kandidat->foto_diri = $path_doc;
            }
            $kandidat->save();
            // dd($kandidat, $request->all());
            KandidatJob::where('kandidat_id', $id)->delete();

            $job = $request->job_history;

            // Hitung jumlah row berdasarkan salah satu array
            $count = count($job['jabatan']);

            for ($i = 0; $i < $count; $i++) {

                if ($job['jabatan'][$i] != null) {
                    $jobHistory                  = new KandidatJob();
                    $jobHistory->kandidat_id     = $kandidat->id;
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
                ->with('error', 'Update Kandidat gagal' . $e->getMessage());
        }

        return redirect('kandidat/' . $kandidat->id . '/detail')->with('success', 'Update Kandidat berhasil');
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
            $kandidat = Kandidat::findOrFail($id);
            KandidatJob::where('kandidat_id', $id)->delete();
            $kandidat->delete();
            DB::commit();
            DB::disconnect();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data kandidat berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::disconnect();
            return response()->json([
                'status'  => 'error',
                'message' => 'Data kandidat gagal dihapus.',
            ]);
        }
    }

    public function get_data(Request $request)
    {
        $kandidat = Kandidat::with('creator');

        return DataTables::of($kandidat)

            ->addColumn('usia', function ($row) {
                if (! $row->tanggal_lahir) {
                    return '-';
                }

                return \Carbon\Carbon::parse($row->tanggal_lahir)->age . ' th';
            })

            ->addColumn('status_label', function ($row) {
                return $row->status == 'aktif'
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-danger">Tidak Aktif</span>';
            })

            ->addColumn('dibuat_oleh', function ($row) {
                $creator = $row->creator->nm_user ?? '-';
                $date    = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $creator . '</span><br><small class="text-muted">' . $date . '</small></div>';
            })

            ->addColumn('action', function ($row) {
                return '
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/kandidat/' . $row->id . '/detail">
                            <i class="fa-solid fa-eye text-primary"></i> Show
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="/kandidat/' . $row->id . '/edit">
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

            ->rawColumns(['dokumen_ktp', 'status_label', 'action', 'dibuat_oleh'])
            ->make(true);
    }

    public function public_create()
    {
        return view('sdm::kandidat.public-create');
    }

    public function public_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nama_lengkap"   => "required|string|max:255",
            "nik_ktp"        => "required|string|max:50",
            "alamat_ktp"     => "required|string",
            "nomor_hp"       => "required|string|max:20",
            "email"          => "required|email|max:255",
            "foto_diri"      => "required|image|max:5120",
            'dokumen_ktp'    => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_kk'     => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_sim'    => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_npwp'   => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
            'dokumen_ijazah' => 'nullable|mimes:pdf,jpg,jpeg|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $kandidat                             = new Kandidat();
            $kandidat->nama_lengkap               = $request->nama_lengkap;
            $kandidat->nama_panggilan             = $request->nama_panggilan;
            $kandidat->nik_ktp                    = $request->nik_ktp;
            $kandidat->tempat_lahir               = $request->tempat_lahir;
            $kandidat->tanggal_lahir              = $request->tanggal_lahir;
            $kandidat->jenis_kelamin              = $request->jenis_kelamin;
            $kandidat->agama                      = $request->agama;
            $kandidat->status_pernikahan          = $request->status_pernikahan;
            $kandidat->alamat_ktp                 = $request->alamat_ktp;
            $kandidat->alamat_domisili            = $request->alamat_domisili;
            $kandidat->nomor_hp                   = $request->nomor_hp;
            $kandidat->email                      = $request->email;
            $kandidat->nomor_sim                  = $request->nomor_sim;
            $kandidat->nomor_npwp                 = $request->nomor_npwp;
            $kandidat->nomor_bpjs_kesehatan       = $request->nomor_bpjs_kesehatan;
            $kandidat->nomor_bpjs_ketenagakerjaan = $request->nomor_bpjs_ketenagakerjaan;
            $kandidat->nama_bank                  = $request->nama_bank;
            $kandidat->nomor_rekening             = $request->nomor_rekening;
            $kandidat->kontak_darurat_nama        = $request->kontak_darurat_nama;
            $kandidat->kontak_darurat_nomor       = $request->kontak_darurat_nomor;
            $kandidat->kontak_darurat_hubungan    = $request->kontak_darurat_hubungan;
            $kandidat->status                     = 'aktif';

            $fileFields = ['dokumen_ktp', 'dokumen_kk', 'dokumen_sim', 'dokumen_npwp', 'dokumen_ijazah', 'foto_diri'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path             = $request->file($field)->store('kandidat', 'public');
                    $kandidat->$field = $path;
                }
            }

            $kandidat->save();

            if ($request->has('job_history')) {
                $job   = $request->job_history;
                $count = count($job['jabatan'] ?? []);

                for ($i = 0; $i < $count; $i++) {
                    if (! empty($job['jabatan'][$i])) {
                        $jobHistory                  = new KandidatJob();
                        $jobHistory->kandidat_id     = $kandidat->id;
                        $jobHistory->jabatan         = $job['jabatan'][$i];
                        $jobHistory->divisi          = $job['divisi'][$i] ?? null;
                        $jobHistory->lokasi_kerja    = $job['lokasi_kerja'][$i] ?? null;
                        $jobHistory->status_kerja    = $job['status_kerja'][$i] ?? null;
                        $jobHistory->tanggal_mulai   = $job['tanggal_mulai'][$i] ?? null;
                        $jobHistory->tanggal_selesai = $job['tanggal_selesai'][$i] ?? null;
                        $jobHistory->atasan_langsung = $job['atasan_langsung'][$i] ?? null;
                        $jobHistory->catatan         = $job['catatan'][$i] ?? null;
                        $jobHistory->save();
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Pendaftaran gagal: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pendaftaran berhasil! Kami akan menghubungi Anda segera.');
    }
}
