@extends('template.root')
@section('page-name', 'perusahaan')
@php
    $isEdit = isset($perusahaan);
@endphp
@section('title-page', $isEdit ? 'Edit Perusahaan' : 'Create Perusahaan')
@section('content')
    <form action="{{ $isEdit ? route('perusahaan.update', $perusahaan->id) : route('perusahaan.store') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nama Perusahaan *</label>
                    <input type="text" name="nama_perusahaan" class="form-control"
                        value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan ?? '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Pemilik</label>
                    <input type="text" name="pemilik" class="form-control"
                        value="{{ old('pemilik', $perusahaan->pemilik ?? '') }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ old('alamat', $perusahaan->alamat ?? '') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kota</label>
                    <input type="text" name="kota" class="form-control"
                        value="{{ old('kota', $perusahaan->kota ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telp" class="form-control"
                        value="{{ old('telp', $perusahaan->telp ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $perusahaan->email ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">NPWP</label>
                    <input type="text" name="npwp" class="form-control"
                        value="{{ old('npwp', $perusahaan->npwp ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $perusahaan->status ?? '') == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive"
                            {{ old('status', $perusahaan->status ?? '') == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control">{{ old('keterangan', $perusahaan->keterangan ?? '') }}</textarea>
                </div>

                <!-- FILE UPLOADS -->
                <div class="col-md-12 mt-4">
                    <div class="row g-3">
                        @foreach (['company_profile' => 'Company Profile', 'akta_perusahaan' => 'Akta Perusahaan', 'visi_misi' => 'Visi Misi'] as $field => $label)
                            <div class="col-md-6">
                                <label class="form-label">{{ $label }}</label>
                                <div class="upload-area p-4 border rounded text-center" id="dropzone-{{ $field }}"
                                    style="border-style: dashed !important; cursor: pointer; background: #f8f9fa; transition: 0.3s;">
                                    <div class="upload-icon mb-2">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                    </div>
                                    <p class="mb-1 text-muted">Click or drag & drop to upload {{ $label }}</p>
                                    <small class="text-secondary d-block mb-2">PDF, DOCX, JPG, PNG (Max 10MB)</small>
                                    <input type="file" name="{{ $field }}" id="input-{{ $field }}"
                                        class="d-none" accept=".pdf,.docx,.jpg,.jpeg,.png">
                                    <div id="file-info-{{ $field }}" class="file-info mt-2 d-none">
                                        <span class="badge bg-success text-white"><i class="fas fa-file me-1"></i> <span
                                                id="filename-{{ $field }}"></span></span>
                                    </div>
                                    @if ($isEdit && $perusahaan->$field)
                                        <div class="mt-2">
                                            <a href="{{ Storage::url($perusahaan->$field) }}" target="_blank"
                                                class="btn btn-xs btn-outline-info">
                                                <i class="fas fa-eye"></i> View Current
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

        <div class="col-md-12 text-end">
            <a href="{{ url(Request::segment(1)) }}" class="btn btn-danger mt-3">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Update Perusahaan' : 'Simpan Perusahaan' }}
            </button>
        </div>

    </form>

    <style>
        .upload-area.dragover {
            background-color: #e9ecef !important;
            border-color: #7366ff !important;
            transform: scale(1.02);
        }
    </style>

@section('script')
    <script>
        document.querySelectorAll('.upload-area').forEach(area => {
            const id = area.id.split('-')[1];
            const input = document.getElementById('input-' + id);
            const info = document.getElementById('file-info-' + id);
            const filename = document.getElementById('filename-' + id);

            area.addEventListener('click', () => input.click());

            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('dragover');
            });

            area.addEventListener('dragleave', () => {
                area.classList.remove('dragover');
            });

            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    updateFileInfo(id, e.dataTransfer.files[0].name);
                }
            });

            input.addEventListener('change', () => {
                if (input.files.length) {
                    updateFileInfo(id, input.files[0].name);
                }
            });

            function updateFileInfo(field, name) {
                info.classList.remove('d-none');
                filename.textContent = name;
            }
        });
    </script>
@endsection
@endsection
