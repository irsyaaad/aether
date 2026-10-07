<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karir | PAP Hardware - PT Putera Adhi Perkasa</title>
    <link rel="icon" href="{{ asset('images/logo_persada_duta.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('images/logo_persada_duta.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_persada_duta.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/feather-icon.css') }}">

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/bootstrap.css') }}">

    <style>
        :root {
            --primary-color: #7366ff;
            --secondary-color: #f73164;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            font-family: 'Rubik', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
            color: #2b2b2b;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            overflow: hidden;
            width: 100%;
            /* max-width: 1000px; */
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-section {
            background: linear-gradient(to right, #7366ff, #a166ff);
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .logo-img {
            width: 80px;
            background: white;
            padding: 10px;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }

        .form-section {
            padding: 40px;
        }

        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 3px rgba(115, 102, 255, 0.1);
            border-color: var(--primary-color);
            background-color: white;
        }

        .btn-submit {
            background: linear-gradient(to right, #7366ff, #5e50ee);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            box-shadow: 0 5px 15px rgba(115, 102, 255, 0.3);
            transition: all 0.3s;
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(115, 102, 255, 0.4);
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .preview-image {
            max-width: 150px;
            border-radius: 10px;
            margin-top: 10px;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .job-history-item {
            position: relative;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.3s;
        }

        .job-history-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-color);
        }

        .btn-add-job {
            background: #e8e6ff;
            color: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-add-job:hover {
            background: #d4d0ff;
        }

        .btn-remove-job {
            color: var(--secondary-color);
            background: #ffe6eb;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 15px;
            right: 15px;
            transition: all 0.3s;
        }

        .btn-remove-job:hover {
            background: var(--secondary-color);
            color: white;
        }
    </style>
</head>

<body>

    <div class="container-fluid px-4">
        <div class="glass-card">
            <div class="header-section">
                <img src="{{ asset('images/logo_persada_duta.png') }}" alt="Logo" class="logo-img" style="max-height: 55px; width: auto; object-fit: contain;">
                <h2 class="fw-bold mb-1">Bergabung Bersama Kami</h2>
                <p class="mb-0 opacity-75">Isi formulir di bawah ini untuk menjadi bagian dari PT Putera Adhi Perkasa (PAP Hardware)</p>
            </div>

            <div class="form-section">
                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('recruitment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- DATA PRIBADI -->
                    <h5 class="section-title"><i class="fa fa-user"></i> Data Pribadi</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_lengkap"
                                value="{{ old('nama_lengkap') }}" required placeholder="Sesuai KTP">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nama Panggilan</label>
                            <input type="text" class="form-control" name="nama_panggilan"
                                value="{{ old('nama_panggilan') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">NIK (KTP) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="nik_ktp" value="{{ old('nik_ktp') }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Agama</label>
                            <select class="form-select" name="agama">
                                <option value="" selected disabled>Pilih...</option>
                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen
                                </option>
                                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik
                                </option>
                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Tempat Lahir</label>
                            <input type="text" class="form-control" name="tempat_lahir"
                                value="{{ old('tempat_lahir') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Jenis Kelamin</label>
                            <select class="form-select" name="jenis_kelamin">
                                <option value="" selected disabled>Pilih...</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Status Pernikahan</label>
                            <select class="form-select" name="status_pernikahan">
                                <option value="" selected disabled>Pilih...</option>
                                <option value="Belum Menikah"
                                    {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah
                                </option>
                                <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>
                                    Menikah</option>
                                <option value="Cerai" {{ old('status_pernikahan') == 'Cerai' ? 'selected' : '' }}>
                                    Cerai</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nomor SIM</label>
                            <input type="text" class="form-control" name="nomor_sim"
                                value="{{ old('nomor_sim') }}">
                        </div>
                    </div>

                    <!-- ALAMAT & KONTAK -->
                    <h5 class="section-title mt-5"><i class="fa fa-map-marker-alt"></i> Alamat & Kontak</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Alamat KTP <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="alamat_ktp" rows="2" required>{{ old('alamat_ktp') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Alamat Domisili</label>
                            <textarea class="form-control" name="alamat_domisili" rows="2">{{ old('alamat_domisili') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nomor HP / WhatsApp <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nomor_hp"
                                value="{{ old('nomor_hp') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                required>
                        </div>
                    </div>

                    <!-- DOKUMEN IDENTITAS TAMBAHAN -->
                    <h5 class="section-title mt-5"><i class="fa fa-id-card"></i> Identitas Tambahan</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Nomor NPWP</label>
                            <input type="text" class="form-control" name="nomor_npwp"
                                value="{{ old('nomor_npwp') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">BPJS Kesehatan</label>
                            <input type="text" class="form-control" name="nomor_bpjs_kesehatan"
                                value="{{ old('nomor_bpjs_kesehatan') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">BPJS Ketenagakerjaan</label>
                            <input type="text" class="form-control" name="nomor_bpjs_ketenagakerjaan"
                                value="{{ old('nomor_bpjs_ketenagakerjaan') }}">
                        </div>
                    </div>

                    <!-- DATA PERBANKAN -->
                    <h5 class="section-title mt-5"><i class="fa fa-university"></i> Data Perbankan</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nama Bank</label>
                            <input type="text" class="form-control" name="nama_bank"
                                value="{{ old('nama_bank') }}" placeholder="Contoh: BCA, BRI">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nomor Rekening</label>
                            <input type="text" class="form-control" name="nomor_rekening"
                                value="{{ old('nomor_rekening') }}">
                        </div>
                    </div>

                    <!-- KONTAK DARURAT -->
                    <h5 class="section-title mt-5"><i class="fa fa-phone-alt"></i> Kontak Darurat</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Nama Kontak</label>
                            <input type="text" class="form-control" name="kontak_darurat_nama"
                                value="{{ old('kontak_darurat_nama') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Nomor Kontak</label>
                            <input type="text" class="form-control" name="kontak_darurat_nomor"
                                value="{{ old('kontak_darurat_nomor') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Hubungan</label>
                            <input type="text" class="form-control" name="kontak_darurat_hubungan"
                                value="{{ old('kontak_darurat_hubungan') }}" placeholder="Contoh: Orang Tua, Istri">
                        </div>
                    </div>

                    <!-- RIWAYAT PEKERJAAN (DYNAMIC) -->
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-5 border-bottom pb-2">
                        <h5 class="section-title mb-0 border-0 pb-0"><i class="fa fa-history"></i> Riwayat Pekerjaan
                        </h5>
                        <button type="button" class="btn-add-job" id="addJobHistory">
                            <i class="fa fa-plus me-1"></i> Tambah Riwayat
                        </button>
                    </div>

                    <div id="jobHistoryWrapper">
                        <!-- Rows will be added here -->
                    </div>

                    <!-- HIDDEN TEMPLATE FOR JS -->
                    <div id="jobHistoryTemplate" style="display: none;">
                        <div class="job-history-item">
                            <button type="button" class="btn-remove-job removeJobHistory">
                                <i class="fa fa-times"></i>
                            </button>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Nama Perusahaan /
                                        Jabatan</label>
                                    <input type="text" name="job_history[jabatan][]" class="form-control" required
                                        placeholder="Contoh: PT. ABC - Supir">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Divisi</label>
                                    <input type="text" name="job_history[divisi][]" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Lokasi Kerja</label>
                                    <input type="text" name="job_history[lokasi_kerja][]" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Status</label>
                                    <select name="job_history[status_kerja][]" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option>Tetap</option>
                                        <option>Kontrak</option>
                                        <option>Harian</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Tanggal Mulai</label>
                                    <input type="date" name="job_history[tanggal_mulai][]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Tanggal Selesai</label>
                                    <input type="date" name="job_history[tanggal_selesai][]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Atasan Langsung</label>
                                    <input type="text" name="job_history[atasan_langsung][]" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">Catatan / Alasan Keluar</label>
                                    <textarea name="job_history[catatan][]" rows="1" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- DOKUMEN -->
                    <h5 class="section-title mt-5"><i class="fa fa-file-upload"></i> Upload Dokumen</h5>
                    <div class="alert alert-info py-2 small">
                        <i class="fa fa-info-circle me-1"></i> Format diizinkan: JPG, PNG, PDF. Maksimal 5MB per file.
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm bg-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="fw-bold mb-3">Pas Foto <span class="text-danger">*</span></h6>
                                    <input type="file" class="form-control form-control-sm" name="foto_diri"
                                        required accept="image/*" onchange="previewImage(this)">
                                    <img src="" class="preview-image mx-auto">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm bg-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="fw-bold mb-3">Scan KTP</h6>
                                    <input type="file" class="form-control form-control-sm" name="dokumen_ktp">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm bg-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="fw-bold mb-3">Scan SIM</h6>
                                    <input type="file" class="form-control form-control-sm" name="dokumen_sim">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm bg-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="fw-bold mb-3">Scan Kartu Keluarga</h6>
                                    <input type="file" class="form-control form-control-sm" name="dokumen_kk">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm bg-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="fw-bold mb-3">Ijazah Terakhir</h6>
                                    <input type="file" class="form-control form-control-sm" name="dokumen_ijazah">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm bg-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="fw-bold mb-3">Scan NPWP</h6>
                                    <input type="file" class="form-control form-control-sm" name="dokumen_npwp">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-submit">
                            <i class="fa fa-paper-plane me-2"></i> Kirim Lamaran
                        </button>
                        <div class="text-center mt-3">
                            <a href="{{ url('/') }}" class="text-decoration-none text-muted small">
                                <i class="fa fa-arrow-left me-1"></i> Kembali ke Halaman Utama
                            </a>
                        </div>
                    </div>

                </form>
            </div>

            <div class="text-center py-3 bg-light border-top small text-muted">
                &copy; {{ date('Y') }} PT Putera Adhi Perkasa. All rights reserved.
            </div>
        </div>
    </div>

    <script>
        // Preview Image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = input.nextElementSibling;
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Dynamic Job History
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('jobHistoryWrapper');
            const addButton = document.getElementById('addJobHistory');
            const template = document.getElementById('jobHistoryTemplate').innerHTML;

            // Add row
            addButton.addEventListener('click', function() {
                const div = document.createElement('div');
                div.innerHTML = template;
                wrapper.appendChild(div);

                // Optional: Animation for new row
                const newItem = div.querySelector('.job-history-item');
                newItem.style.opacity = 0;
                setTimeout(() => {
                    newItem.style.transition = 'opacity 0.3s';
                    newItem.style.opacity = 1;
                }, 10);
            });

            // Remove row using delegation
            wrapper.addEventListener('click', function(e) {
                if (e.target.closest('.removeJobHistory')) {
                    const item = e.target.closest('.job-history-item');
                    item.style.opacity = 0;
                    setTimeout(() => {
                        item.parentElement.remove();
                    }, 300);
                }
            });

            // Add one initial row if empty (Optional)
            // addButton.click();
        });
    </script>

</body>

</html>
