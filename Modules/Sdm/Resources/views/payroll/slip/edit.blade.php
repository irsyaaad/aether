@extends('template.root')
@section('page-name', 'payroll-slip')
@section('title-page', 'Edit Slip Gaji')
@section('content')
    <div class="col-md-12">
        <form action="{{ route('payroll-slip.update', $slip->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- CARD INFORMASI DASAR -->
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Edit Slip Gaji - {{ $slip->nomor_slip }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Informasi Karyawan (Read Only) -->
                        <div class="col-md-6">
                            <label class="form-label">Karyawan</label>
                            <input type="text" class="form-control" value="{{ $slip->karyawan->nama_lengkap ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode</label>
                            <input type="text" class="form-control" value="{{ $slip->payrollPeriod->nama_periode ?? '-' }}" readonly>
                        </div>

                        <!-- Gaji Pokok -->
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                            <input type="number" name="gaji_pokok" 
                                   class="form-control @error('gaji_pokok') is-invalid @enderror"
                                   value="{{ old('gaji_pokok', $slip->gaji_pokok) }}" 
                                   min="0" step="1000" required>
                            @error('gaji_pokok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kehadiran -->
                        <div class="col-md-3">
                            <label class="form-label">Hari Kerja <span class="text-danger">*</span></label>
                            <input type="number" name="hari_kerja" 
                                   class="form-control @error('hari_kerja') is-invalid @enderror"
                                   value="{{ old('hari_kerja', $slip->hari_kerja) }}" 
                                   min="0" max="31" required>
                            @error('hari_kerja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Hari Hadir <span class="text-danger">*</span></label>
                            <input type="number" name="hari_hadir" 
                                   class="form-control @error('hari_hadir') is-invalid @enderror"
                                   value="{{ old('hari_hadir', $slip->hari_hadir) }}" 
                                   min="0" max="31" required>
                            @error('hari_hadir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Hari Alpha</label>
                            <input type="number" name="hari_alpha" 
                                   class="form-control @error('hari_alpha') is-invalid @enderror"
                                   value="{{ old('hari_alpha', $slip->hari_alpha) }}" 
                                   min="0" max="31">
                            @error('hari_alpha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Jam Lembur</label>
                            <input type="number" name="jam_lembur" 
                                   class="form-control @error('jam_lembur') is-invalid @enderror"
                                   value="{{ old('jam_lembur', $slip->jam_lembur) }}" 
                                   min="0" step="0.5">
                            @error('jam_lembur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" rows="3" 
                                      class="form-control @error('catatan') is-invalid @enderror" 
                                      placeholder="Catatan tambahan">{{ old('catatan', $slip->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD KOMPONEN GAJI -->
            <div class="card mt-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Komponen Gaji</h5>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addComponentModal">
                            <i class="fa fa-plus"></i> Tambah Komponen
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="components-container">
                        @foreach($slip->details as $index => $detail)
                            <div class="component-row mb-3 p-3 border rounded" data-index="{{ $index }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Komponen</label>
                                        <select name="components[{{ $index }}][component_id]" class="form-select" required>
                                            @foreach($components as $component)
                                                <option value="{{ $component->id }}" 
                                                    {{ $detail->payroll_component_id == $component->id ? 'selected' : '' }}>
                                                    {{ $component->nama_komponen }} 
                                                    ({{ $component->jenis === 'allowance' ? 'Tunjangan' : 'Potongan' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nilai</label>
                                        <input type="number" name="components[{{ $index }}][nilai]" 
                                               class="form-control" 
                                               value="{{ $detail->nilai }}" 
                                               min="0" step="1000" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" name="components[{{ $index }}][keterangan]" 
                                               class="form-control" 
                                               value="{{ $detail->keterangan }}" 
                                               placeholder="Keterangan (opsional)">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-component">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payroll-slip.show', $slip->id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Slip
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal Tambah Komponen -->
    <div class="modal fade" id="addComponentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Komponen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Komponen</label>
                        <select id="modal-component" class="form-select">
                            <option value="">Pilih Komponen</option>
                            @foreach($components as $component)
                                <option value="{{ $component->id }}" data-name="{{ $component->nama_komponen }}" data-jenis="{{ $component->jenis }}">
                                    {{ $component->nama_komponen }} 
                                    ({{ $component->jenis === 'allowance' ? 'Tunjangan' : 'Potongan' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai</label>
                        <input type="number" id="modal-nilai" class="form-control" min="0" step="1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" id="modal-keterangan" class="form-control" placeholder="Keterangan (opsional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="add-component-btn">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            let componentIndex = {{ $slip->details->count() }};

            // Add component functionality
            $('#add-component-btn').on('click', function() {
                const componentId = $('#modal-component').val();
                const componentName = $('#modal-component option:selected').data('name');
                const componentJenis = $('#modal-component option:selected').data('jenis');
                const nilai = $('#modal-nilai').val();
                const keterangan = $('#modal-keterangan').val();

                if (!componentId || !nilai) {
                    alert('Komponen dan nilai harus diisi');
                    return;
                }

                const jenisLabel = componentJenis === 'allowance' ? 'Tunjangan' : 'Potongan';
                
                const componentHtml = `
                    <div class="component-row mb-3 p-3 border rounded" data-index="${componentIndex}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Komponen</label>
                                <select name="components[${componentIndex}][component_id]" class="form-select" required>
                                    @foreach($components as $component)
                                        <option value="{{ $component->id }}" ${componentId == '{{ $component->id }}' ? 'selected' : ''}>
                                            {{ $component->nama_komponen }} 
                                            ({{ $component->jenis === 'allowance' ? 'Tunjangan' : 'Potongan' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nilai</label>
                                <input type="number" name="components[${componentIndex}][nilai]" 
                                       class="form-control" 
                                       value="${nilai}" 
                                       min="0" step="1000" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="components[${componentIndex}][keterangan]" 
                                       class="form-control" 
                                       value="${keterangan}" 
                                       placeholder="Keterangan (opsional)">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-component">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                $('#components-container').append(componentHtml);
                componentIndex++;

                // Reset modal
                $('#modal-component').val('');
                $('#modal-nilai').val('');
                $('#modal-keterangan').val('');
                $('#addComponentModal').modal('hide');
            });

            // Remove component functionality
            $(document).on('click', '.remove-component', function() {
                $(this).closest('.component-row').remove();
            });

            // Auto calculate alpha days
            $('input[name="hari_kerja"], input[name="hari_hadir"]').on('input', function() {
                const hariKerja = parseInt($('input[name="hari_kerja"]').val()) || 0;
                const hariHadir = parseInt($('input[name="hari_hadir"]').val()) || 0;
                const hariAlpha = Math.max(0, hariKerja - hariHadir);
                $('input[name="hari_alpha"]').val(hariAlpha);
            });
        });
    </script>
    @endpush
@endsection