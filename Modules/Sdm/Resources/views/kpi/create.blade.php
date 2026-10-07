@extends('template.root')

@section('title', isset($kpi) ? 'Edit KPI' : 'Tambah KPI')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-chart-line text-primary me-2"></i>
                                {{ isset($kpi) ? 'Edit KPI' : 'Tambah KPI' }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('kpi.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="kpi-form">
                        @csrf
                        @if(isset($kpi))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jabatan_id" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jabatan_id" name="jabatan_id" required>
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($jabatan as $item)
                                            <option value="{{ $item->id }}" 
                                                {{ (isset($kpi) && $kpi->jabatan_id == $item->id) ? 'selected' : '' }}>
                                                {{ $item->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_kpi" class="form-label">Nama KPI <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_kpi" name="nama_kpi" 
                                           value="{{ isset($kpi) ? $kpi->nama_kpi : '' }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bobot" class="form-label">Bobot (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="bobot" name="bobot" 
                                           value="{{ isset($kpi) ? $kpi->bobot : '' }}" min="0" max="100" step="0.01" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="satuan" name="satuan" 
                                           value="{{ isset($kpi) ? $kpi->satuan : '' }}" placeholder="contoh: unit, %, jam" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tipe_target" class="form-label">Tipe Target <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tipe_target" name="tipe_target" required>
                                        <option value="">Pilih Tipe Target</option>
                                        <option value="bulanan" {{ (isset($kpi) && $kpi->tipe_target == 'bulanan') ? 'selected' : '' }}>Bulanan</option>
                                        <option value="triwulan" {{ (isset($kpi) && $kpi->tipe_target == 'triwulan') ? 'selected' : '' }}>Triwulan</option>
                                        <option value="semester" {{ (isset($kpi) && $kpi->tipe_target == 'semester') ? 'selected' : '' }}>Semester</option>
                                        <option value="tahunan" {{ (isset($kpi) && $kpi->tipe_target == 'tahunan') ? 'selected' : '' }}>Tahunan</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" {{ (isset($kpi) && $kpi->status == 'active') ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ (isset($kpi) && $kpi->status == 'inactive') ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" 
                                      placeholder="Deskripsi detail tentang KPI ini">{{ isset($kpi) ? $kpi->deskripsi : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kpi.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i>
                                {{ isset($kpi) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#jabatan_id').select2({
        placeholder: 'Pilih Jabatan',
        allowClear: true
    });

    // Form submission
    $('#kpi-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = @if(isset($kpi))
            "{{ route('kpi.update', $kpi->id) }}"
        @else
            "{{ route('kpi.store') }}"
        @endif;
        
        // Clear previous validation errors
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('kpi.index') }}";
                        }
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(value[0]);
                    });
                } else {
                    var response = xhr.responseJSON;
                    Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                }
            }
        });
    });
});
</script>
@endpush