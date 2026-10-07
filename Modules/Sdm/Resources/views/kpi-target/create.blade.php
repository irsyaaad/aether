@extends('template.root')

@section('title', isset($target) ? 'Edit Target KPI' : 'Tambah Target KPI')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-bullseye text-primary me-2"></i>
                                {{ isset($target) ? 'Edit Target KPI' : 'Tambah Target KPI' }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('kpi-target.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="kpi-target-form">
                        @csrf
                        @if(isset($target))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kpi_id" class="form-label">KPI <span class="text-danger">*</span></label>
                                    <select class="form-select" id="kpi_id" name="kpi_id" required>
                                        <option value="">Pilih KPI</option>
                                        @foreach($kpi as $item)
                                            <option value="{{ $item->id }}" 
                                                data-satuan="{{ $item->satuan }}"
                                                data-tipe="{{ $item->tipe_target }}"
                                                {{ (isset($target) && $target->kpi_id == $item->id) ? 'selected' : '' }}>
                                                {{ $item->jabatan->nama_jabatan ?? '' }} - {{ $item->nama_kpi }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tahun" name="tahun" required>
                                        <option value="">Pilih Tahun</option>
                                        @for($year = date('Y') - 1; $year <= date('Y') + 5; $year++)
                                            <option value="{{ $year }}" 
                                                {{ (isset($target) && $target->tahun == $year) ? 'selected' : (($year == date('Y')) ? 'selected' : '') }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="periode" class="form-label">Periode <span class="text-danger">*</span></label>
                                    <select class="form-select" id="periode" name="periode" required>
                                        <option value="">Pilih Periode</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_value" class="form-label">Target Value <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="target_value" name="target_value" 
                                               value="{{ isset($target) ? $target->target_value : '' }}" min="0" step="0.01" required>
                                        <span class="input-group-text" id="satuan-text">-</span>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                      placeholder="Keterangan tambahan tentang target ini">{{ isset($target) ? $target->keterangan : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kpi-target.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i>
                                {{ isset($target) ? 'Update' : 'Simpan' }}
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
    $('#kpi_id').select2({
        placeholder: 'Pilih KPI',
        allowClear: true
    });

    // Update periode options based on KPI tipe_target
    function updatePeriodeOptions() {
        var selectedOption = $('#kpi_id option:selected');
        var tipeTarget = selectedOption.data('tipe');
        var satuan = selectedOption.data('satuan');
        
        // Update satuan text
        $('#satuan-text').text(satuan || '-');
        
        // Clear periode options
        $('#periode').empty().append('<option value="">Pilih Periode</option>');
        
        if (tipeTarget) {
            var options = [];
            
            switch(tipeTarget) {
                case 'bulanan':
                    options = [
                        {value: 1, text: 'Januari'}, {value: 2, text: 'Februari'}, {value: 3, text: 'Maret'},
                        {value: 4, text: 'April'}, {value: 5, text: 'Mei'}, {value: 6, text: 'Juni'},
                        {value: 7, text: 'Juli'}, {value: 8, text: 'Agustus'}, {value: 9, text: 'September'},
                        {value: 10, text: 'Oktober'}, {value: 11, text: 'November'}, {value: 12, text: 'Desember'}
                    ];
                    break;
                case 'triwulan':
                    options = [
                        {value: 1, text: 'Triwulan 1'}, {value: 2, text: 'Triwulan 2'},
                        {value: 3, text: 'Triwulan 3'}, {value: 4, text: 'Triwulan 4'}
                    ];
                    break;
                case 'semester':
                    options = [
                        {value: 1, text: 'Semester 1'}, {value: 2, text: 'Semester 2'}
                    ];
                    break;
                case 'tahunan':
                    options = [{value: 1, text: 'Tahunan'}];
                    break;
            }
            
            $.each(options, function(index, option) {
                $('#periode').append('<option value="' + option.value + '">' + option.text + '</option>');
            });
            
            // Set selected value if editing
            @if(isset($target))
                $('#periode').val({{ $target->periode }});
            @endif
        }
    }

    // Handle KPI change
    $('#kpi_id').on('change', updatePeriodeOptions);
    
    // Initialize on page load
    updatePeriodeOptions();

    // Form submission
    $('#kpi-target-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = @if(isset($target))
            "{{ route('kpi-target.update', $target->id) }}"
        @else
            "{{ route('kpi-target.store') }}"
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
                            window.location.href = "{{ route('kpi-target.index') }}";
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