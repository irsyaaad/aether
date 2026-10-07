@extends('template.root')
@section('page-name', 'payroll-component')
@section('title-page', isset($component) ? 'Edit Komponen Payroll' : 'Buat Komponen Payroll')
@section('content')
    @php
        $isEdit = isset($component);
    @endphp
    <div class="col-md-12">
        <form action="{{ $isEdit ? route('payroll-component.update', $component->id) : route('payroll-component.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- CARD DATA KOMPONEN -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{ $isEdit ? 'Edit Data Komponen' : 'Data Komponen Payroll' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Komponen <span class="text-danger">*</span></label>
                            <input type="text" name="kode_komponen" 
                                   class="form-control @error('kode_komponen') is-invalid @enderror"
                                   value="{{ old('kode_komponen', $component->kode_komponen ?? '') }}" 
                                   placeholder="Contoh: TJ001, POT001"
                                   style="text-transform: uppercase;"
                                   required>
                            @error('kode_komponen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Komponen <span class="text-danger">*</span></label>
                            <input type="text" name="nama_komponen" 
                                   class="form-control @error('nama_komponen') is-invalid @enderror"
                                   value="{{ old('nama_komponen', $component->nama_komponen ?? '') }}" 
                                   placeholder="Contoh: Tunjangan Transport, Potongan BPJS"
                                   required>
                            @error('nama_komponen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jenis Komponen <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="">Pilih Jenis</option>
                                <option value="allowance" {{ old('jenis', $component->jenis ?? '') == 'allowance' ? 'selected' : '' }}>
                                    Tunjangan (Allowance)
                                </option>
                                <option value="deduction" {{ old('jenis', $component->jenis ?? '') == 'deduction' ? 'selected' : '' }}>
                                    Potongan (Deduction)
                                </option>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipe Perhitungan <span class="text-danger">*</span></label>
                            <select name="tipe_perhitungan" class="form-select @error('tipe_perhitungan') is-invalid @enderror" required>
                                <option value="">Pilih Tipe</option>
                                <option value="fixed" {{ old('tipe_perhitungan', $component->tipe_perhitungan ?? '') == 'fixed' ? 'selected' : '' }}>
                                    Tetap (Fixed)
                                </option>
                                <option value="percentage" {{ old('tipe_perhitungan', $component->tipe_perhitungan ?? '') == 'percentage' ? 'selected' : '' }}>
                                    Persentase (Percentage)
                                </option>
                                <option value="formula" {{ old('tipe_perhitungan', $component->tipe_perhitungan ?? '') == 'formula' ? 'selected' : '' }}>
                                    Formula
                                </option>
                            </select>
                            @error('tipe_perhitungan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nilai Default <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="currency-symbol">Rp</span>
                                <input type="number" name="nilai_default" 
                                       class="form-control @error('nilai_default') is-invalid @enderror"
                                       value="{{ old('nilai_default', $component->nilai_default ?? '') }}" 
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>
                            <small class="text-muted">Untuk persentase, masukkan nilai tanpa simbol % (contoh: 5 untuk 5%)</small>
                            @error('nilai_default')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pengaturan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_taxable" value="1" 
                                       {{ old('is_taxable', $component->is_taxable ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Kena Pajak
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', $component->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Aktif
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" rows="3" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      placeholder="Keterangan tambahan tentang komponen ini">{{ old('keterangan', $component->keterangan ?? '') }}</textarea>
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
                        <a href="{{ route('payroll-component.index') }}" class="btn btn-secondary">
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
            // Update currency symbol based on calculation type
            $('select[name="tipe_perhitungan"]').on('change', function() {
                var type = $(this).val();
                var symbol = $('#currency-symbol');
                
                if (type === 'percentage') {
                    symbol.text('%');
                } else {
                    symbol.text('Rp');
                }
            });

            // Trigger change on page load
            $('select[name="tipe_perhitungan"]').trigger('change');

            // Auto uppercase kode komponen
            $('input[name="kode_komponen"]').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
    </script>
    @endpush
@endsection