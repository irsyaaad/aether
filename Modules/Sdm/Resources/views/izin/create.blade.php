@extends('template.root')
@section('page-name', 'izin')
@section('title-page', isset($izin) ? 'Edit Izin' : 'Buat Izin')
@section('content')
    @php
        $isEdit = isset($izin);
    @endphp
    <div class="col-md-12">
        <form action="{{ $isEdit ? route('izin.update', $izin->id) : route('izin.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- CARD DATA IZIN -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{ $isEdit ? 'Edit Data Izin' : 'Data Izin' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                            <select name="karyawan_id" id="karyawan_select" class="form-select @error('karyawan_id') is-invalid @enderror" required>
                                @if(isset($izin) && $izin->karyawan)
                                    <option value="{{ $izin->karyawan->id }}" selected>
                                        {{ $izin->karyawan->nama_lengkap }} - {{ $izin->karyawan->jabatan ?? 'Tidak ada jabatan' }}
                                    </option>
                                @endif
                            </select>
                            @error('karyawan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                            <select name="jenis_izin" class="form-select @error('jenis_izin') is-invalid @enderror" required>
                                <option value="">Pilih Jenis Izin</option>
                                <option value="sakit" {{ old('jenis_izin', $izin->jenis_izin ?? '') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="cuti" {{ old('jenis_izin', $izin->jenis_izin ?? '') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="izin_khusus" {{ old('jenis_izin', $izin->jenis_izin ?? '') == 'izin_khusus' ? 'selected' : '' }}>Izin Khusus</option>
                                <option value="dinas_luar" {{ old('jenis_izin', $izin->jenis_izin ?? '') == 'dinas_luar' ? 'selected' : '' }}>Dinas Luar</option>
                            </select>
                            @error('jenis_izin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" 
                                   class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                   value="{{ old('tanggal_mulai', isset($izin) ? $izin->tanggal_mulai : '') }}" 
                                   required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" 
                                   class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                   value="{{ old('tanggal_selesai', isset($izin) ? $izin->tanggal_selesai : '') }}" 
                                   required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alasan <span class="text-danger">*</span></label>
                            <textarea name="alasan" rows="4" 
                                      class="form-control @error('alasan') is-invalid @enderror" 
                                      placeholder="Masukkan alasan izin..." required>{{ old('alasan', $izin->alasan ?? '') }}</textarea>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Dokumen Pendukung</label>
                            <input type="file" name="dokumen_pendukung" 
                                   class="form-control @error('dokumen_pendukung') is-invalid @enderror"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB</small>
                            @if(isset($izin) && $izin->dokumen_pendukung)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($izin->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i> Lihat Dokumen
                                    </a>
                                </div>
                            @endif
                            @error('dokumen_pendukung')
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
                        <a href="{{ route('izin.index') }}" class="btn btn-secondary">
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
            // Initialize Select2 for Karyawan
            $('#karyawan_select').select2({
                placeholder: 'Pilih Karyawan',
                allowClear: true,
                ajax: {
                    url: "{{ url('ajax/get-karyawan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
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
                minimumInputLength: 0,
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    return $('<span>' + data.text + '</span>');
                },
                templateSelection: function(data) {
                    return data.text || data.id;
                }
            });

            // Auto calculate days when dates change
            function calculateDays() {
                var startDate = $('input[name="tanggal_mulai"]').val();
                var endDate = $('input[name="tanggal_selesai"]').val();
                
                if (startDate && endDate) {
                    var start = new Date(startDate);
                    var end = new Date(endDate);
                    var timeDiff = end.getTime() - start.getTime();
                    var dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                    
                    if (dayDiff > 0) {
                        // You can display the calculated days somewhere if needed
                        console.log('Jumlah hari: ' + dayDiff);
                    }
                }
            }

            $('input[name="tanggal_mulai"], input[name="tanggal_selesai"]').on('change', calculateDays);
        });
    </script>
    @endpush
@endsection