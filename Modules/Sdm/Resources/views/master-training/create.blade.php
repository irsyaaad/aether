@extends('template.root')
@section('page-name', 'master-training')
@section('title-page', isset($masterTraining) ? 'Edit Master Training' : 'Tambah Master Training')
@section('add-page')
    <a href="{{ route('master-training.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ isset($masterTraining) ? 'Edit' : 'Tambah' }} Master Training</h5>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($masterTraining) ? route('master-training.update', $masterTraining->id) : route('master-training.store') }}"
                        method="POST" id="master-training-form" enctype="multipart/form-data">
                        @csrf
                        @if (isset($masterTraining))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_training" class="form-label">Kode Training <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kode_training') is-invalid @enderror"
                                        id="kode_training" name="kode_training"
                                        value="{{ old('kode_training', $masterTraining->kode_training ?? '') }}"
                                        placeholder="Contoh: TRN001" required>
                                    @error('kode_training')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_training" class="form-label">Nama Training <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_training') is-invalid @enderror"
                                        id="nama_training" name="nama_training"
                                        value="{{ old('nama_training', $masterTraining->nama_training ?? '') }}"
                                        placeholder="Contoh: Pelatihan Safety Driving" required>
                                    @error('nama_training')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Kategori</label>
                                    <select class="form-control @error('kategori') is-invalid @enderror" id="kategori"
                                        name="kategori">
                                        <option value="">Pilih Kategori</option>
                                        <option value="Safety"
                                            {{ old('kategori', $masterTraining->kategori ?? '') == 'Safety' ? 'selected' : '' }}>
                                            Safety</option>
                                        <option value="Operasional"
                                            {{ old('kategori', $masterTraining->kategori ?? '') == 'Operasional' ? 'selected' : '' }}>
                                            Operasional</option>
                                        <option value="Softskill"
                                            {{ old('kategori', $masterTraining->kategori ?? '') == 'Softskill' ? 'selected' : '' }}>
                                            Softskill</option>
                                        <option value="Teknis"
                                            {{ old('kategori', $masterTraining->kategori ?? '') == 'Teknis' ? 'selected' : '' }}>
                                            Teknis</option>
                                        <option value="Leadership"
                                            {{ old('kategori', $masterTraining->kategori ?? '') == 'Leadership' ? 'selected' : '' }}>
                                            Leadership</option>
                                        <option value="Lainnya"
                                            {{ old('kategori', $masterTraining->kategori ?? '') == 'Lainnya' ? 'selected' : '' }}>
                                            Lainnya</option>
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="durasi_jam" class="form-label">Durasi (Jam)</label>
                                    <input type="number" class="form-control @error('durasi_jam') is-invalid @enderror"
                                        id="durasi_jam" name="durasi_jam"
                                        value="{{ old('durasi_jam', $masterTraining->durasi_jam ?? '') }}"
                                        placeholder="Contoh: 8" min="1">
                                    @error('durasi_jam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select class="form-control @error('level') is-invalid @enderror" id="level"
                                        name="level">
                                        <option value="">Pilih Level</option>
                                        <option value="Beginner"
                                            {{ old('level', $masterTraining->level ?? '') == 'Beginner' ? 'selected' : '' }}>
                                            Beginner</option>
                                        <option value="Intermediate"
                                            {{ old('level', $masterTraining->level ?? '') == 'Intermediate' ? 'selected' : '' }}>
                                            Intermediate</option>
                                        <option value="Advanced"
                                            {{ old('level', $masterTraining->level ?? '') == 'Advanced' ? 'selected' : '' }}>
                                            Advanced</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4"
                                placeholder="Deskripsi training...">{{ old('deskripsi', $masterTraining->deskripsi ?? '') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- File Materi Section --}}
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fa fa-file-alt me-2"></i>Materi Training</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="file_materi" class="form-label">Upload File Materi</label>
                                            <input type="file"
                                                class="form-control @error('file_materi') is-invalid @enderror"
                                                id="file_materi" name="file_materi"
                                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.zip">
                                            <small class="text-muted">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, MP4,
                                                ZIP (Max: 50MB)</small>
                                            @error('file_materi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if (isset($masterTraining) && $masterTraining->file_materi)
                                                <div class="mt-2">
                                                    <span class="badge bg-success">
                                                        <i class="fa fa-check-circle"></i> File sudah ada
                                                    </span>
                                                    <a href="{{ asset('storage/' . $masterTraining->file_materi) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                        <i class="fa fa-download"></i> Download
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="link_youtube" class="form-label">Link YouTube Materi</label>
                                            <input type="url"
                                                class="form-control @error('link_youtube') is-invalid @enderror"
                                                id="link_youtube" name="link_youtube"
                                                value="{{ old('link_youtube', $masterTraining->link_youtube ?? '') }}"
                                                placeholder="https://www.youtube.com/watch?v=...">
                                            <small class="text-muted">Contoh: https://www.youtube.com/watch?v=xxxxx</small>
                                            @error('link_youtube')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1"
                                    {{ old('is_active', $masterTraining->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                <i class="fa fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> {{ isset($masterTraining) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#master-training-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var url = $(this).attr('action');
                var method = $(this).find('input[name="_method"]').val() || 'POST';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href =
                                    "{{ route('master-training.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;
                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').remove();

                            $.each(errors, function(field, messages) {
                                var input = $('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.after('<div class="invalid-feedback">' + messages[
                                    0] + '</div>');
                            });
                        } else {
                            var message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire({
                                title: 'Error!',
                                text: message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
