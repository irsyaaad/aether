@extends('template.root')
@section('page-name', 'piutang-karyawan')
@section('title-page', isset($piutang) ? 'Edit Piutang Karyawan' : 'Tambah Piutang Karyawan')
@section('content')
    <div class="col-md-12">
        <form
            action="{{ isset($piutang) ? route('piutang-karyawan.update', $piutang->id) : route('piutang-karyawan.store') }}"
            method="POST">
            @csrf
            @if (isset($piutang))
                @method('PUT')
            @endif

            <div class="card">
                <div class="card-header">
                    <h5>{{ isset($piutang) ? 'Edit Piutang Karyawan' : 'Tambah Piutang Karyawan' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Karyawan -->
                        <div class="col-md-6">
                            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                            <select name="karyawan_id"
                                class="form-select select2 @error('karyawan_id') is-invalid @enderror" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach ($karyawan as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('karyawan_id', $piutang->karyawan_id ?? '') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_lengkap }} - {{ $k->jabatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('karyawan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Piutang -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Piutang <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_piutang"
                                class="form-control @error('tanggal_piutang') is-invalid @enderror"
                                value="{{ old('tanggal_piutang', isset($piutang) ? $piutang->tanggal_piutang->format('Y-m-d') : date('Y-m-d')) }}"
                                required>
                            @error('tanggal_piutang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jenis Piutang -->
                        <div class="col-md-6">
                            <label class="form-label">Jenis Piutang <span class="text-danger">*</span></label>
                            <select name="jenis_piutang" class="form-select @error('jenis_piutang') is-invalid @enderror"
                                required>
                                <option value="">Pilih Jenis Piutang</option>
                                <option value="kasbon"
                                    {{ old('jenis_piutang', $piutang->jenis_piutang ?? '') == 'kasbon' ? 'selected' : '' }}>
                                    Kasbon
                                </option>
                                <option value="pinjaman"
                                    {{ old('jenis_piutang', $piutang->jenis_piutang ?? '') == 'pinjaman' ? 'selected' : '' }}>
                                    Pinjaman
                                </option>
                                <option value="lainnya"
                                    {{ old('jenis_piutang', $piutang->jenis_piutang ?? '') == 'lainnya' ? 'selected' : '' }}>
                                    Lainnya
                                </option>
                            </select>
                            @error('jenis_piutang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jumlah Piutang -->
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Piutang <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah_piutang"
                                class="form-control auto-number @error('jumlah_piutang') is-invalid @enderror"
                                value="{{ old('jumlah_piutang', $piutang->jumlah_piutang ?? '') }}" min="1"
                                step="0.01" required>
                            @error('jumlah_piutang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jatuh Tempo -->
                        <div class="col-md-6">
                            <label class="form-label">Jatuh Tempo (Opsional)</label>
                            <input type="date" name="jatuh_tempo"
                                class="form-control @error('jatuh_tempo') is-invalid @enderror"
                                value="{{ old('jatuh_tempo', isset($piutang) && $piutang->jatuh_tempo ? $piutang->jatuh_tempo->format('Y-m-d') : '') }}">
                            @error('jatuh_tempo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Di Transfer Akun? <span class="text-danger">*</span></label>
                            <select name="kas_bank_id"
                                class="form-select select2 @error('kas_bank_id') is-invalid @enderror" required>
                                <option value="">Pilih Kas / Bank</option>
                                @foreach ($kasBank as $item)
                                    <option value="{{ $item->id_ac }}"
                                        {{ old('kas_bank_id', $piutang->kas_bank_id ?? '') == $item->id_ac ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kas_bank_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" rows="4" class="form-control @error('keterangan') is-invalid @enderror"
                                placeholder="Keterangan piutang (opsional)">{{ old('keterangan', $piutang->keterangan ?? '') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('piutang-karyawan.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{ isset($piutang) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize Select2
                $('.select2').select2({
                    placeholder: 'Pilih',
                    allowClear: true
                });
            });
        </script>
    @endpush
@endsection
