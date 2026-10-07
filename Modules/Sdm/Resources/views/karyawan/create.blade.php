@extends('template.root')
@section('page-name', Request::segment(1))
@section('title-page', 'Buat Karyawan')
@section('content')
    @php
        $isEdit = isset($karyawan);
    @endphp
    <div class="col-md-12">
        <form action="{{ $isEdit ? route('karyawan.update', $karyawan->id) : route('karyawan.store') }}" method="POST"
            enctype="multipart/form-data">

            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- CARD DATA PRIBADI -->
            <div class="card">
                <div class="card-header pb-2">
                    <h5>Data Pribadi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach ($jabatan as $item)
                                    <option value="{{ $item }}"
                                        {{ old('jabatan', $karyawan->jabatan ?? '') == $item ? 'selected' : '' }}>
                                        {{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                value="{{ old('nama_lengkap', $karyawan->nama_lengkap ?? '') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" class="form-control"
                                value="{{ old('nama_panggilan', $karyawan->nama_panggilan ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIK KTP</label>
                            <input type="text" name="nik_ktp" class="form-control"
                                value="{{ old('nik_ktp', $karyawan->nik_ktp ?? '') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control"
                                value="{{ old('tempat_lahir', $karyawan->tempat_lahir ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ?? '') }}" data-ignore-limit>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Agama</label>
                            <input type="text" name="agama" class="form-control"
                                value="{{ old('agama', $karyawan->agama ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status Pernikahan</label>
                            <select name="status_pernikahan" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option
                                    {{ old('status_pernikahan', $karyawan->status_pernikahan ?? '') == 'Belum Menikah' ? 'selected' : '' }}>
                                    Belum Menikah</option>
                                <option
                                    {{ old('status_pernikahan', $karyawan->status_pernikahan ?? '') == 'Menikah' ? 'selected' : '' }}>
                                    Menikah</option>
                                <option
                                    {{ old('status_pernikahan', $karyawan->status_pernikahan ?? '') == 'Cerai' ? 'selected' : '' }}>
                                    Cerai</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alamat KTP</label>
                            <textarea name="alamat_ktp" class="form-control">{{ old('alamat_ktp', $karyawan->alamat_ktp ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alamat Domisili</label>
                            <textarea name="alamat_domisili" class="form-control">{{ old('alamat_domisili', $karyawan->alamat_domisili ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="nomor_hp" class="form-control"
                                value="{{ old('nomor_hp', $karyawan->nomor_hp ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $karyawan->email ?? '') }}">
                        </div>

                    </div>
                </div>
            </div>

            <!-- CARD DOKUMEN IDENTITAS -->
            <div class="card">
                <div class="card-header pb-2">
                    <h5>Dokumen Identitas</h5>
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Nomor NPWP</label>
                            <input type="text" name="nomor_npwp" class="form-control"
                                value="{{ old('nomor_npwp', $karyawan->nomor_npwp ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">BPJS Kesehatan</label>
                            <input type="text" name="nomor_bpjs_kesehatan" class="form-control"
                                value="{{ old('nomor_bpjs_kesehatan', $karyawan->nomor_bpjs_kesehatan ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">BPJS Ketenagakerjaan</label>
                            <input type="text" name="nomor_bpjs_ketenagakerjaan" class="form-control"
                                value="{{ old('nomor_bpjs_ketenagakerjaan', $karyawan->nomor_bpjs_ketenagakerjaan ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nomor SIM</label>
                            <input type="text" name="nomor_sim" class="form-control"
                                value="{{ old('nomor_sim', $karyawan->nomor_sim ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Jenis SIM</label>
                            <select name="jenis_sim" class="form-select select2" id="jenis_sim" style="width:100%">
                                <option value="">-- Pilih --</option>
                                <option value="SIM A (mobil kecil)" {{ old('jenis_sim', $karyawan->jenis_sim ?? '') == 'SIM A (mobil kecil)' ? 'selected' : '' }}>SIM A (mobil kecil)</option>
                                <option value="B1 & B2 (kendaraan berat/gandeng)" {{ old('jenis_sim', $karyawan->jenis_sim ?? '') == 'B1 & B2 (kendaraan berat/gandeng)' ? 'selected' : '' }}>B1 & B2 (kendaraan berat/gandeng)</option>
                                <option value="C (sepeda motor/3 kategori)" {{ old('jenis_sim', $karyawan->jenis_sim ?? '') == 'C (sepeda motor/3 kategori)' ? 'selected' : '' }}>C (sepeda motor/3 kategori)</option>
                                <option value="D (disabilitas)" {{ old('jenis_sim', $karyawan->jenis_sim ?? '') == 'D (disabilitas)' ? 'selected' : '' }}>D (disabilitas)</option>
                                <option value="SIM Internasional" {{ old('jenis_sim', $karyawan->jenis_sim ?? '') == 'SIM Internasional' ? 'selected' : '' }}>SIM Internasional</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">ID SPX</label>
                            <input type="text" name="id_spx" class="form-control"
                                value="{{ old('id_spx', $karyawan->id_spx ?? '') }}">
                        </div>
                    </div>

                </div>
            </div>

            <!-- CARD GAJI & PERBANKAN -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Gaji & Perbankan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        @foreach ([
            'gaji_pokok' => 'Gaji Pokok',
            'tunjangan_jabatan' => 'Tunjangan Jabatan',
            'tunjangan_makan' => 'Tunjangan Makan',
            'tunjangan_transport' => 'Tunjangan Transport',
        ] as $field => $label)
                            <div class="col-md-3">
                                <label class="form-label">{{ $label }}</label>
                                <input type="number" name="{{ $field }}" class="form-control"
                                    value="{{ old($field, $karyawan->$field ?? '') }}">
                            </div>
                        @endforeach

                        <div class="col-md-6">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" class="form-control"
                                value="{{ old('nomor_rekening', $karyawan->nomor_rekening ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Bank</label>
                            <input type="text" name="nama_bank" class="form-control"
                                value="{{ old('nama_bank', $karyawan->nama_bank ?? '') }}">
                        </div>

                    </div>
                </div>
            </div>

            <!-- CARD KONTAK DARURAT -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Kontak Darurat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        @foreach ([
            'kontak_darurat_nama' => 'Nama Kontak',
            'kontak_darurat_nomor' => 'Nomor Kontak',
            'kontak_darurat_hubungan' => 'Hubungan',
        ] as $field => $label)
                            <div class="col-md-4">
                                <label class="form-label">{{ $label }}</label>
                                <input type="text" name="{{ $field }}" class="form-control"
                                    value="{{ old($field, $karyawan->$field ?? '') }}">
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <!-- CARD UPLOAD DOKUMEN -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Upload Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        @foreach ([
            'dokumen_ktp' => 'KTP',
            'dokumen_kk' => 'KK',
            'dokumen_sim' => 'SIM',
            'dokumen_npwp' => 'NPWP',
            'dokumen_ijazah' => 'Ijazah',
            'foto_diri' => 'Foto Diri',
        ] as $field => $label)
                            <div class="col-md-4">
                                <label class="form-label">{{ $label }}</label>
                                <input type="file" name="{{ $field }}" class="form-control">

                                @if ($isEdit && $karyawan->$field)
                                    <small class="text-success">Sudah ada file (opsional upload baru)</small>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <!-- CARD CATATAN -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Catatan & Status</h5>
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Catatan Kinerja</label>
                            <textarea name="catatan_kinerja" class="form-control">{{ old('catatan_kinerja', $karyawan->catatan_kinerja ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Training</label>
                            <textarea name="training" class="form-control">{{ old('training', $karyawan->training ?? '') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="aktif"
                                    {{ old('status', $karyawan->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="tidak_aktif"
                                    {{ old('status', $karyawan->status ?? '') == 'tidak_aktif' ? 'selected' : '' }}>Tidak
                                    Aktif
                                </option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- JOB HISTORY -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Job History</h5>
                    <button type="button" id="addJobHistory" class="btn btn-sm btn-primary float-end">
                        <i class="fa fa-plus"></i> Tambah Riwayat
                    </button>
                </div>

                <div class="card-body">
                    <div id="jobHistoryWrapper">

                        @if ($isEdit && $karyawan->job_history)
                            @foreach (json_decode($karyawan->job_history, true) as $i => $item)
                                <div class="job-history-item border p-3 rounded mb-3 position-relative">
                                    <div class="col-md-12 text-end">
                                        <button type="button" class="btn btn-danger btn-sm removeJobHistory">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Jabatan</label>
                                            <input type="text" name="job_history[jabatan][]"
                                                class="form-control form-solid"
                                                value="{{ old('job_history.jabatan', $item['jabatan'] ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Divisi</label>
                                            <input type="text" name="job_history[divisi][]"
                                                class="form-control form-solid"
                                                value="{{ old('job_history.divisi', $item['divisi'] ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Lokasi Kerja</label>
                                            <input type="text" name="job_history[lokasi_kerja][]"
                                                class="form-control form-solid"
                                                value="{{ old('job_history.lokasi_kerja', $item['lokasi_kerja'] ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Status Kerja</label>
                                            <select name="job_history[status_kerja][]"
                                                class="form-control form-solid form-select">
                                                <option value="">-- Pilih --</option>
                                                <option
                                                    {{ old('job_history.status_kerja', $item['status_kerja'] ?? '') == 'Tetap' ? 'selected' : '' }}>
                                                    Tetap</option>
                                                <option
                                                    {{ old('job_history.status_kerja', $item['status_kerja'] ?? '') == 'Kontrak' ? 'selected' : '' }}>
                                                    Kontrak</option>
                                                <option
                                                    {{ old('job_history.status_kerja', $item['status_kerja'] ?? '') == 'Harian' ? 'selected' : '' }}>
                                                    Harian</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Mulai</label>
                                            <input type="date" name="job_history[tanggal_mulai][]"
                                                class="form-control form-solid"
                                                value="{{ old('job_history.tanggal_mulai', $item['tanggal_mulai'] ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Selesai</label>
                                            <input type="date" name="job_history[tanggal_selesai][]"
                                                class="form-control form-solid"
                                                value="{{ old('job_history.tanggal_selesai', $item['tanggal_selesai'] ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Atasan Langsung</label>
                                            <input type="text" name="job_history[atasan_langsung][]"
                                                class="form-control form-solid"
                                                value="{{ old('job_history.atasan_langsung', $item['atasan_langsung'] ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Shift Kerja</label>
                                            <input type="text" name="job_history[shift_kerja][]"
                                                class="form-control form-solid"
                                                value="{{ old('job_history.shift_kerja', $item['shift_kerja'] ?? '') }}">
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Catatan</label>
                                            <textarea name="job_history[catatan][]" rows="2" class="form-control form-solid">{{ old('job_history.catatan', $item['catatan'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    </div>
                    <div id="jobHistoryTemplate" class="d-none">
                        <div class="job-history-item border p-3 rounded mb-3 position-relative">
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-danger btn-sm removeJobHistory">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" name="job_history[jabatan][]" class="form-control form-solid">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Divisi</label>
                                    <input type="text" name="job_history[divisi][]" class="form-control form-solid">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Lokasi Kerja</label>
                                    <input type="text" name="job_history[lokasi_kerja][]"
                                        class="form-control form-solid">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Status Kerja</label>
                                    <select name="job_history[status_kerja][]"
                                        class="form-control form-solid form-select">
                                        <option value="">-- Pilih --</option>
                                        <option>Tetap</option>
                                        <option>Kontrak</option>
                                        <option>Harian</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="job_history[tanggal_mulai][]"
                                        class="form-control form-solid">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="job_history[tanggal_selesai][]"
                                        class="form-control form-solid">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Atasan Langsung</label>
                                    <input type="text" name="job_history[atasan_langsung][]"
                                        class="form-control form-solid">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Shift Kerja</label>
                                    <input type="text" name="job_history[shift_kerja][]"
                                        class="form-control form-solid">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="job_history[catatan][]" rows="2" class="form-control form-solid"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary mt-4">Simpan</button>
        </form>
    </div>

@section('script')
    <script>
        let jobIndex = 0;

        document.getElementById('addJobHistory').addEventListener('click', function() {
            let template = document.getElementById('jobHistoryTemplate').innerHTML;
            template = template.replace(/\[i\]/g, '[' + jobIndex + ']');

            const wrapper = document.getElementById('jobHistoryWrapper');
            const div = document.createElement('div');
            div.innerHTML = template;

            wrapper.appendChild(div);
            jobIndex++;
        });

        // Remove job history item
        document.addEventListener('click', function(e) {
            if (e.target.closest('.removeJobHistory')) {
                e.target.closest('.job-history-item').remove();
            }
        });
        // Initialize Select2 for jenis_sim if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('select.select2').select2({ width: '100%', placeholder: '-- Pilih --', allowClear: true });
        }
    </script>

@endsection
@endsection
