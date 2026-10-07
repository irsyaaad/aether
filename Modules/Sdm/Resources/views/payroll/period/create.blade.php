@extends('template.root')
@section('page-name', 'payroll-period')
@section('title-page', isset($period) ? 'Edit Periode Payroll' : 'Buat Periode Payroll')
@section('content')
    @php
        $isEdit = isset($period);
    @endphp
    <div class="col-md-12">
        <form action="{{ $isEdit ? route('payroll-period.update', $period->id) : route('payroll-period.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- CARD DATA PERIODE -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{ $isEdit ? 'Edit Data Periode' : 'Data Periode Payroll' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                            <input type="text" name="nama_periode" 
                                   class="form-control @error('nama_periode') is-invalid @enderror"
                                   value="{{ old('nama_periode', $period->nama_periode ?? '') }}" 
                                   placeholder="Contoh: Gaji Bulan Januari 2024"
                                   required>
                            @error('nama_periode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" 
                                   class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                   value="{{ old('tanggal_mulai', isset($period) ? $period->tanggal_mulai->format('Y-m-d') : '') }}" 
                                   required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" 
                                   class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                   value="{{ old('tanggal_selesai', isset($period) ? $period->tanggal_selesai->format('Y-m-d') : '') }}" 
                                   required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Gajian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_gajian" 
                                   class="form-control @error('tanggal_gajian') is-invalid @enderror"
                                   value="{{ old('tanggal_gajian', isset($period) ? $period->tanggal_gajian->format('Y-m-d') : '') }}" 
                                   required>
                            @error('tanggal_gajian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" rows="3" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $period->keterangan ?? '') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payroll-period.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{ $isEdit ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Auto set end date when start date changes (set to end of month)
            $('input[name="tanggal_mulai"]').on('change', function() {
                var startDate = new Date($(this).val());
                if (startDate) {
                    // Set end date to last day of the same month
                    var endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
                    $('input[name="tanggal_selesai"]').val(endDate.toISOString().split('T')[0]);
                    
                    // Set payroll date to 5th of next month
                    var payrollDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 5);
                    $('input[name="tanggal_gajian"]').val(payrollDate.toISOString().split('T')[0]);
                }
            });
        });
    </script>
    @endpush
@endsection