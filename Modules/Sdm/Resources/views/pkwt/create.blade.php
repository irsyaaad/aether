@extends('template.root')
@section('page-name', 'Upload PKWT')
@section('title-page', 'Upload PKWT Baru')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fa fa-upload text-success"></i> Upload PKWT Baru
            </h5>
            <a href="{{ route('pkwt.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('pkwt.store') }}" method="POST" enctype="multipart/form-data" id="pkwt-form">
                @csrf

                <div class="row">
                    {{-- Informasi Karyawan --}}
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fa fa-user"></i> Informasi Karyawan</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                    <select name="karyawan_id" id="karyawan_id"
                                        class="form-select @error('karyawan_id') is-invalid @enderror" required>
                                        <option value="">Pilih Karyawan</option>
                                    </select>
                                    @error('karyawan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nomor PKWT</label>
                                    <input type="text" name="nomor_pkwt"
                                        class="form-control @error('nomor_pkwt') is-invalid @enderror"
                                        value="{{ old('nomor_pkwt') }}" placeholder="Kosongkan untuk auto-generate">
                                    <div class="form-text">Kosongkan untuk generate otomatis</div>
                                    @error('nomor_pkwt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Judul PKWT <span class="text-danger">*</span></label>
                                    <input type="text" name="judul_pkwt"
                                        class="form-control @error('judul_pkwt') is-invalid @enderror"
                                        value="{{ old('judul_pkwt') }}" required
                                        placeholder="Contoh: Kontrak Kerja Staff IT">
                                    @error('judul_pkwt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jenis Kontrak <span class="text-danger">*</span></label>
                                    <select name="jenis_kontrak"
                                        class="form-select @error('jenis_kontrak') is-invalid @enderror" required>
                                        <option value="">Pilih Jenis Kontrak</option>
                                        <option value="pkwt" {{ old('jenis_kontrak') == 'pkwt' ? 'selected' : '' }}>PKWT
                                            (Perjanjian Kerja Waktu Tertentu)</option>
                                        <option value="pkwtt" {{ old('jenis_kontrak') == 'pkwtt' ? 'selected' : '' }}>
                                            PKWTT
                                            (Perjanjian Kerja Waktu Tidak Tertentu)</option>
                                        <option value="magang" {{ old('jenis_kontrak') == 'magang' ? 'selected' : '' }}>
                                            Magang
                                        </option>
                                        <option value="freelance"
                                            {{ old('jenis_kontrak') == 'freelance' ? 'selected' : '' }}>
                                            Freelance</option>
                                        <option value="konsultan"
                                            {{ old('jenis_kontrak') == 'konsultan' ? 'selected' : '' }}>
                                            Konsultan</option>
                                    </select>
                                    @error('jenis_kontrak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Kontrak --}}
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fa fa-calendar"></i> Detail Kontrak</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Mulai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="tanggal_mulai"
                                                class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                                value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                                            @error('tanggal_mulai')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Berakhir <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="tanggal_berakhir"
                                                class="form-control @error('tanggal_berakhir') is-invalid @enderror"
                                                value="{{ old('tanggal_berakhir') }}" required>
                                            @error('tanggal_berakhir')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Gaji Pokok</label>
                                            <input type="number" name="gaji_pokok"
                                                class="form-control @error('gaji_pokok') is-invalid @enderror"
                                                value="{{ old('gaji_pokok') }}" min="0" step="1000"
                                                placeholder="0">
                                            @error('gaji_pokok')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" name="jabatan"
                                        class="form-control @error('jabatan') is-invalid @enderror"
                                        value="{{ old('jabatan') }}" placeholder="Jabatan dalam kontrak">
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ketentuan Khusus</label>
                                    <textarea name="ketentuan_khusus" class="form-control @error('ketentuan_khusus') is-invalid @enderror" rows="3"
                                        placeholder="Ketentuan khusus dalam kontrak">{{ old('ketentuan_khusus') }}</textarea>
                                    @error('ketentuan_khusus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="auto_reminder" id="auto_reminder"
                                                    class="form-check-input" value="1"
                                                    {{ old('auto_reminder') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="auto_reminder">
                                                    Auto Reminder
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Reminder (Hari)</label>
                                            <input type="number" name="reminder_days"
                                                class="form-control @error('reminder_days') is-invalid @enderror"
                                                value="{{ old('reminder_days', 30) }}" min="1" max="365"
                                                placeholder="30">
                                            <div class="form-text">Berapa hari sebelum berakhir</div>
                                            @error('reminder_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- File Upload & Deskripsi --}}
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fa fa-file"></i> Upload File PKWT</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">File PKWT <span class="text-danger">*</span></label>
                                    <input type="file" name="file_pkwt" id="file_pkwt"
                                        class="form-control @error('file_pkwt') is-invalid @enderror" required
                                        accept=".pdf,.doc,.docx">
                                    <div class="form-text">Format: PDF, DOC, DOCX. Maksimal 10MB</div>
                                    @error('file_pkwt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- File Preview --}}
                                <div id="file-preview" class="mb-3" style="display: none;">
                                    <label class="form-label">Preview File</label>
                                    <div id="preview-container" class="border rounded p-3 text-center">
                                        <!-- Preview will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fa fa-edit"></i> Deskripsi & Catatan</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4"
                                        placeholder="Deskripsi singkat tentang kontrak ini">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="4"
                                        placeholder="Catatan tambahan atau ketentuan khusus">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Section --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pkwt.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-success" id="btn-submit">
                                <i class="fa fa-upload"></i> Upload PKWT
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for Karyawan with AJAX
            $('#karyawan_id').select2({
                placeholder: 'Pilih Karyawan',
                allowClear: true,
                ajax: {
                    url: "{{ route('ajax.get-karyawan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            // Handle file selection
            $('#file_pkwt').on('change', function() {
                const file = this.files[0];

                if (file) {
                    const previewContainer = $('#preview-container');
                    const filePreview = $('#file-preview');

                    previewContainer.html(`
                        <i class="fa fa-file-pdf fa-4x text-danger mb-2"></i>
                        <div>
                            <strong>File:</strong> ${file.name}<br>
                            <strong>Ukuran:</strong> ${formatFileSize(file.size)}<br>
                            <strong>Tipe:</strong> ${file.type || 'Unknown'}
                        </div>
                    `);

                    filePreview.show();
                } else {
                    $('#file-preview').hide();
                }
            });

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Form submission with loading state
            $('#pkwt-form').on('submit', function() {
                const submitBtn = $('#btn-submit');
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Mengupload...');
            });

            // Auto-generate title from karyawan selection
            $('#karyawan_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const judulInput = $('input[name="judul_pkwt"]');

                if (selectedOption.val() && !judulInput.val()) {
                    const karyawanName = selectedOption.text().split(' - ')[0];
                    const jenisKontrak = $('select[name="jenis_kontrak"]').val();

                    if (jenisKontrak) {
                        judulInput.val(`Kontrak ${jenisKontrak.toUpperCase()} - ${karyawanName}`);
                    }
                }
            });

            // Auto-generate title when jenis_kontrak changes
            $('select[name="jenis_kontrak"]').on('change', function() {
                const karyawanSelect = $('#karyawan_id');
                const judulInput = $('input[name="judul_pkwt"]');

                if (karyawanSelect.val() && !judulInput.val()) {
                    const karyawanName = karyawanSelect.find('option:selected').text().split(' - ')[0];
                    const jenisKontrak = $(this).val();

                    if (jenisKontrak) {
                        judulInput.val(`Kontrak ${jenisKontrak.toUpperCase()} - ${karyawanName}`);
                    }
                }
            });

            // Calculate duration when dates change
            $('input[name="tanggal_mulai"], input[name="tanggal_berakhir"]').on('change', function() {
                const mulai = $('input[name="tanggal_mulai"]').val();
                const berakhir = $('input[name="tanggal_berakhir"]').val();

                if (mulai && berakhir) {
                    const startDate = new Date(mulai);
                    const endDate = new Date(berakhir);
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    const diffMonths = Math.round(diffDays / 30);

                    // Show duration info (you can add a div to display this)
                    console.log(`Durasi: ${diffDays} hari (${diffMonths} bulan)`);
                }
            });
        });
    </script>
@endsection

@section('style')
    <style>
        .card-header h6 {
            margin: 0;
        }

        .form-text {
            font-size: 0.875em;
        }

        #preview-container {
            background-color: #f8f9fa;
        }

        .btn-submit:disabled {
            opacity: 0.6;
        }
    </style>
@endsection
