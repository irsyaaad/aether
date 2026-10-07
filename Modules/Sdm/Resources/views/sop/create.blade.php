@extends('template.root')
@section('page-name', 'sop')
@section('title-page', isset($sop) ? 'Edit SOP' : 'Tambah SOP')
@section('add-page')
    <a href="{{ route('sop.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ isset($sop) ? 'Edit' : 'Tambah' }} SOP</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($sop) ? route('sop.update', $sop->id) : route('sop.store') }}" 
                          method="POST" id="sop-form" enctype="multipart/form-data">
                        @csrf
                        @if(isset($sop))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jabatan_id" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jabatan_id') is-invalid @enderror" 
                                            id="jabatan_id" name="jabatan_id" required>
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($jabatan as $item)
                                            <option value="{{ $item->id }}" 
                                                {{ old('jabatan_id', $sop->jabatan_id ?? request('jabatan_id')) == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama_jabatan }} ({{ $item->kode_jabatan }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jabatan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="judul_sop" class="form-label">Judul SOP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('judul_sop') is-invalid @enderror" 
                                           id="judul_sop" name="judul_sop" 
                                           value="{{ old('judul_sop', $sop->judul_sop ?? '') }}" 
                                           placeholder="Contoh: Prosedur Penanganan Keluhan Pelanggan" required>
                                    @error('judul_sop')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="versi" class="form-label">Versi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('versi') is-invalid @enderror" 
                                           id="versi" name="versi" 
                                           value="{{ old('versi', $sop->versi ?? '1.0') }}" 
                                           placeholder="Contoh: 1.0" required>
                                    @error('versi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_berlaku') is-invalid @enderror" 
                                           id="tanggal_berlaku" name="tanggal_berlaku" 
                                           value="{{ old('tanggal_berlaku', (isset($sop) && $sop->tanggal_berlaku) ? $sop->tanggal_berlaku->format('Y-m-d') : '') }}" 
                                           required>
                                    @error('tanggal_berlaku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tanggal_review" class="form-label">Tanggal Review</label>
                                    <input type="date" class="form-control @error('tanggal_review') is-invalid @enderror" 
                                           id="tanggal_review" name="tanggal_review" 
                                           value="{{ old('tanggal_review', (isset($sop) && $sop->tanggal_review) ? $sop->tanggal_review->format('Y-m-d') : '') }}">
                                    @error('tanggal_review')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="draft" {{ old('status', $sop->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ old('status', $sop->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $sop->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="archived" {{ old('status', $sop->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dokumen_file" class="form-label">Dokumen File</label>
                                    <input type="file" class="form-control @error('dokumen_file') is-invalid @enderror" 
                                           id="dokumen_file" name="dokumen_file" accept=".pdf,.doc,.docx">
                                    <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maksimal 5MB</small>
                                    @if(isset($sop) && $sop->dokumen_file)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $sop->dokumen_file) }}" target="_blank" class="badge bg-info">
                                                <i class="fa fa-file"></i> File Saat Ini
                                            </a>
                                        </div>
                                    @endif
                                    @error('dokumen_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="3" 
                                      placeholder="Deskripsi singkat tentang SOP ini...">{{ old('deskripsi', $sop->deskripsi ?? '') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="prosedur" class="form-label">Prosedur <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('prosedur') is-invalid @enderror" 
                                      id="prosedur" name="prosedur" rows="10" 
                                      placeholder="Tuliskan langkah-langkah prosedur secara detail..." required>{{ old('prosedur', $sop->prosedur ?? '') }}</textarea>
                            @error('prosedur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                <i class="fa fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> {{ isset($sop) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for jabatan
            $('#jabatan_id').select2({
                placeholder: 'Pilih Jabatan',
                allowClear: true
            });

            // Initialize CKEditor for prosedur
            if (typeof ClassicEditor !== 'undefined') {
                ClassicEditor
                    .create(document.querySelector('#prosedur'))
                    .catch(error => {
                        console.error(error);
                    });
            }

            $('#sop-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                var url = $(this).attr('action');
                
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
                                window.location.href = "{{ route('sop.index') }}";
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
                                input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
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