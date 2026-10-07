@extends('template.root')
@section('page-name', 'jabatan')
@section('title-page', isset($jabatan) ? 'Edit Jabatan' : 'Tambah Jabatan')
@section('add-page')
    <a href="{{ route('jabatan.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ isset($jabatan) ? 'Edit' : 'Tambah' }} Jabatan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($jabatan) ? route('jabatan.update', $jabatan->id) : route('jabatan.store') }}" 
                          method="POST" id="jabatan-form">
                        @csrf
                        @if(isset($jabatan))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_jabatan" class="form-label">Kode Jabatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kode_jabatan') is-invalid @enderror" 
                                           id="kode_jabatan" name="kode_jabatan" 
                                           value="{{ old('kode_jabatan', $jabatan->kode_jabatan ?? '') }}" 
                                           placeholder="Contoh: MGR001" required>
                                    @error('kode_jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_jabatan" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_jabatan') is-invalid @enderror" 
                                           id="nama_jabatan" name="nama_jabatan" 
                                           value="{{ old('nama_jabatan', $jabatan->nama_jabatan ?? '') }}" 
                                           placeholder="Contoh: Manager Operasional" required>
                                    @error('nama_jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select class="form-control @error('level') is-invalid @enderror" id="level" name="level">
                                        <option value="">Pilih Level</option>
                                        <option value="Staff" {{ old('level', $jabatan->level ?? '') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="Senior Staff" {{ old('level', $jabatan->level ?? '') == 'Senior Staff' ? 'selected' : '' }}>Senior Staff</option>
                                        <option value="Supervisor" {{ old('level', $jabatan->level ?? '') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="Assistant Manager" {{ old('level', $jabatan->level ?? '') == 'Assistant Manager' ? 'selected' : '' }}>Assistant Manager</option>
                                        <option value="Manager" {{ old('level', $jabatan->level ?? '') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="General Manager" {{ old('level', $jabatan->level ?? '') == 'General Manager' ? 'selected' : '' }}>General Manager</option>
                                        <option value="Director" {{ old('level', $jabatan->level ?? '') == 'Director' ? 'selected' : '' }}>Director</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departemen" class="form-label">Departemen</label>
                                    <input type="text" class="form-control @error('departemen') is-invalid @enderror" 
                                           id="departemen" name="departemen" 
                                           value="{{ old('departemen', $jabatan->departemen ?? '') }}" 
                                           placeholder="Contoh: Operasional">
                                    @error('departemen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="4" 
                                      placeholder="Deskripsi jabatan...">{{ old('deskripsi', $jabatan->deskripsi ?? '') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $jabatan->is_active ?? true) ? 'checked' : '' }}>
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
                                <i class="fa fa-save"></i> {{ isset($jabatan) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#jabatan-form').on('submit', function(e) {
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
                                window.location.href = "{{ route('jabatan.index') }}";
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