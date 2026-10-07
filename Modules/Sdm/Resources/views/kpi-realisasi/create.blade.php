@extends('template.root')

@section('title', isset($realisasi) ? 'Edit Realisasi KPI' : 'Tambah Realisasi KPI')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-chart-bar text-primary me-2"></i>
                                {{ isset($realisasi) ? 'Edit Realisasi KPI' : 'Tambah Realisasi KPI' }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('kpi-realisasi.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="kpi-realisasi-form">
                        @csrf
                        @if(isset($realisasi))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kpi_target_id" class="form-label">Target KPI <span class="text-danger">*</span></label>
                                    <select class="form-select" id="kpi_target_id" name="kpi_target_id" required>
                                        <option value="">Pilih Target KPI</option>
                                        @foreach($kpiTargets as $target)
                                            <option value="{{ $target->id }}" 
                                                data-target="{{ $target->target_value }}"
                                                data-satuan="{{ $target->kpi->satuan ?? '' }}"
                                                {{ (isset($realisasi) && $realisasi->kpi_target_id == $target->id) ? 'selected' : '' }}>
                                                {{ $target->kpi->jabatan->nama_jabatan ?? '' }} - {{ $target->kpi->nama_kpi ?? '' }} 
                                                ({{ $target->tahun }} - {{ $target->nama_periode }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="karyawan_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="karyawan_id" name="karyawan_id" required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($karyawan as $item)
                                            <option value="{{ $item->id }}" 
                                                {{ (isset($realisasi) && $realisasi->karyawan_id == $item->id) ? 'selected' : '' }}>
                                                {{ $item->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="realisasi_value" class="form-label">Realisasi Value <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="realisasi_value" name="realisasi_value" 
                                               value="{{ isset($realisasi) ? $realisasi->realisasi_value : '' }}" min="0" step="0.01" required>
                                        <span class="input-group-text" id="satuan-text">-</span>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tanggal_input" class="form-label">Tanggal Input <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_input" name="tanggal_input" 
                                           value="{{ (isset($realisasi) && $realisasi->tanggal_input) ? $realisasi->tanggal_input->format('Y-m-d') : date('Y-m-d') }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="draft" {{ (isset($realisasi) && $realisasi->status == 'draft') ? 'selected' : '' }}>Draft</option>
                                        <option value="submitted" {{ (isset($realisasi) && $realisasi->status == 'submitted') ? 'selected' : '' }}>Submitted</option>
                                        <option value="approved" {{ (isset($realisasi) && $realisasi->status == 'approved') ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ (isset($realisasi) && $realisasi->status == 'rejected') ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Target Info Display -->
                        <div class="row" id="target-info" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fa-solid fa-info-circle me-2"></i>Informasi Target</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Target:</strong> <span id="target-value">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Realisasi:</strong> <span id="realisasi-display">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Pencapaian:</strong> <span id="pencapaian-display">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Status:</strong> <span id="status-display">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                      placeholder="Catatan tambahan tentang realisasi ini">{{ isset($realisasi) ? $realisasi->catatan : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kpi-realisasi.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i>
                                {{ isset($realisasi) ? 'Update' : 'Simpan' }}
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
    $('#kpi_target_id, #karyawan_id').select2({
        placeholder: function() {
            return $(this).data('placeholder') || 'Pilih...';
        },
        allowClear: true
    });

    // Update target info and calculate achievement
    function updateTargetInfo() {
        var selectedOption = $('#kpi_target_id option:selected');
        var targetValue = parseFloat(selectedOption.data('target')) || 0;
        var satuan = selectedOption.data('satuan') || '';
        var realisasiValue = parseFloat($('#realisasi_value').val()) || 0;
        
        // Update satuan text
        $('#satuan-text').text(satuan || '-');
        
        if (targetValue > 0) {
            $('#target-info').show();
            $('#target-value').text(targetValue.toFixed(2) + ' ' + satuan);
            
            if (realisasiValue > 0) {
                var pencapaian = (realisasiValue / targetValue) * 100;
                var status = '';
                var statusClass = '';
                
                if (pencapaian >= 100) {
                    status = 'Tercapai';
                    statusClass = 'text-success';
                } else if (pencapaian >= 80) {
                    status = 'Hampir Tercapai';
                    statusClass = 'text-warning';
                } else {
                    status = 'Belum Tercapai';
                    statusClass = 'text-danger';
                }
                
                $('#realisasi-display').text(realisasiValue.toFixed(2) + ' ' + satuan);
                $('#pencapaian-display').text(pencapaian.toFixed(2) + '%');
                $('#status-display').html('<span class="' + statusClass + '">' + status + '</span>');
            } else {
                $('#realisasi-display').text('-');
                $('#pencapaian-display').text('-');
                $('#status-display').text('-');
            }
        } else {
            $('#target-info').hide();
        }
    }

    // Handle target KPI change
    $('#kpi_target_id').on('change', updateTargetInfo);
    
    // Handle realisasi value change
    $('#realisasi_value').on('input', updateTargetInfo);
    
    // Initialize on page load
    updateTargetInfo();

    // Form submission
    $('#kpi-realisasi-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = @if(isset($realisasi))
            "{{ route('kpi-realisasi.update', $realisasi->id) }}"
        @else
            "{{ route('kpi-realisasi.store') }}"
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
                            window.location.href = "{{ route('kpi-realisasi.index') }}";
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