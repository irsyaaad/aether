@extends('template.root')
@section('page-name', 'meeting')
@section('title-page', 'Catat Meeting')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fa fa-plus-circle text-primary"></i> Catat Meeting Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('meeting.store') }}" method="POST" enctype="multipart/form-data" id="order-form">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Meeting <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_meeting"
                                class="form-control @error('tanggal_meeting') is-invalid @enderror"
                                value="{{ old('tanggal_meeting', date('Y-m-d')) }}" required>
                            @error('tanggal_meeting')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Meeting <span class="text-danger">*</span></label>
                            <select name="jenis_meeting" class="form-select @error('jenis_meeting') is-invalid @enderror"
                                required>
                                <option value="">Pilih Jenis</option>
                                <option value="Weekly" {{ old('jenis_meeting') == 'Weekly' ? 'selected' : '' }}>Weekly
                                </option>
                                <option value="Monthly" {{ old('jenis_meeting') == 'Monthly' ? 'selected' : '' }}>Monthly
                                </option>
                                <option value="Quartal" {{ old('jenis_meeting') == 'Quartal' ? 'selected' : '' }}>Quartal
                                </option>
                                <option value="Yearly" {{ old('jenis_meeting') == 'Yearly' ? 'selected' : '' }}>Yearly
                                </option>
                            </select>
                            @error('jenis_meeting')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Peserta <span class="text-danger">*</span></label>
                            <textarea name="peserta" class="form-control @error('peserta') is-invalid @enderror" rows="3"
                                placeholder="Masukkan nama peserta, pisahkan dengan koma" required>{{ old('peserta') }}</textarea>
                            <div class="form-text">Contoh: Budi, Iwan, Sari</div>
                            @error('peserta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Hasil Meeting <span class="text-danger">*</span></label>
                            <textarea name="hasil_meeting" id="hasil_meeting" class="form-control @error('hasil_meeting') is-invalid @enderror"
                                rows="10" placeholder="Tuliskan ringkasan hasil meeting...">{{ old('hasil_meeting') }}</textarea>
                            @error('hasil_meeting')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fa fa-camera"></i> Foto Meeting</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Pilih Foto (Bisa pilih berkali-kali)</label>
                                    <input type="file" id="foto_input" class="form-control" accept="image/*" multiple>
                                    <input type="file" name="foto[]" id="foto_hidden" class="d-none" multiple>
                                    <div class="form-text">Format: JPG, PNG, WEBP. Maks: 5MB per file.</div>
                                </div>

                                <div id="preview-section" class="row g-2 mt-2">
                                    <!-- Previews will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('meeting.index') }}" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="fa fa-save"></i> Simpan Record
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .preview-container {
            position: relative;
            height: 120px;
        }

        .preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .btn-remove {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            cursor: pointer;
            border: 1px solid white;
            z-index: 10;
        }
    </style>
@endsection

@section('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <script>
        let editorInstance;
        ClassicEditor
            .create(document.querySelector('#hasil_meeting'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', '|',
                    'bulletedList', 'numberedList', '|',
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                editorInstance = editor;
                editor.editing.view.change(writer => {
                    writer.setStyle('height', '300px', editor.editing.view.document.getRoot());
                });
            })
            .catch(error => {
                console.error(error);
            });

        const fotoInput = document.getElementById('foto_input');
        const fotoHidden = document.getElementById('foto_hidden');
        const previewSection = document.getElementById('preview-section');
        const dataTransfer = new DataTransfer();

        fotoInput.addEventListener('change', function(e) {
            const files = e.target.files;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (!file.type.startsWith('image/')) continue;

                // Add to DataTransfer
                dataTransfer.items.add(file);

                // Create Preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    const col = document.createElement('div');
                    col.className = 'col-md-2 col-4 mb-3';
                    col.innerHTML = `
                        <div class="preview-container">
                            <img src="${event.target.result}">
                            <div class="btn-remove" data-name="${file.name}"><i class="fa fa-times"></i></div>
                        </div>
                    `;
                    previewSection.appendChild(col);
                }
                reader.readAsDataURL(file);
            }

            // Sync with hidden input
            fotoHidden.files = dataTransfer.files;
            // Clear visible input to allow re-selection
            fotoInput.value = '';
        });

        $(document).on('click', '.btn-remove', function() {
            const fileName = $(this).data('name');
            const container = $(this).closest('.col-md-2');

            // Remove from DataTransfer
            for (let i = 0; i < dataTransfer.files.length; i++) {
                if (dataTransfer.files[i].name === fileName) {
                    dataTransfer.items.remove(i);
                    break;
                }
            }

            // Sync with hidden input
            fotoHidden.files = dataTransfer.files;
            // Remove from UI
            container.remove();
        });

        $('#order-form').on('submit', function(e) {
            // Sync CKEditor
            let hasContent = false;
            if (editorInstance) {
                const data = editorInstance.getData();
                document.getElementById('hasil_meeting').value = data;
                if (data.trim() !== '') {
                    hasContent = true;
                }
            }

            if (!hasContent) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Hasil meeting tidak boleh kosong!',
                });
                return false;
            }

            $('#btn-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        });
    </script>
@endsection
