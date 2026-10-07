@extends('template.root')

@section('title', isset($jobdesc) ? 'Edit Job Description' : 'Tambah Job Description')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-file-text text-primary me-2"></i>
                                {{ isset($jobdesc) ? 'Edit Job Description' : 'Tambah Job Description' }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('job-description.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="jobdesc-form" enctype="multipart/form-data">
                        @csrf
                        @if(isset($jobdesc))
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
                                                {{ (isset($jobdesc) && $jobdesc->jabatan_id == $item->id) ? 'selected' : '' }}>
                                                {{ $item->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="judul_jobdesc" class="form-label">Judul Job Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="judul_jobdesc" name="judul_jobdesc" 
                                           value="{{ isset($jobdesc) ? $jobdesc->judul_jobdesc : '' }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="versi" class="form-label">Versi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="versi" name="versi" 
                                           value="{{ isset($jobdesc) ? $jobdesc->versi : '1.0' }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="draft" {{ (isset($jobdesc) && $jobdesc->status == 'draft') ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ (isset($jobdesc) && $jobdesc->status == 'active') ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ (isset($jobdesc) && $jobdesc->status == 'inactive') ? 'selected' : '' }}>Inactive</option>
                                        <option value="archived" {{ (isset($jobdesc) && $jobdesc->status == 'archived') ? 'selected' : '' }}>Archived</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_berlaku" name="tanggal_berlaku" 
                                           value="{{ (isset($jobdesc) && $jobdesc->tanggal_berlaku) ? $jobdesc->tanggal_berlaku->format('Y-m-d') : '' }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_review" class="form-label">Tanggal Review</label>
                                    <input type="date" class="form-control" id="tanggal_review" name="tanggal_review" 
                                           value="{{ (isset($jobdesc) && $jobdesc->tanggal_review) ? $jobdesc->tanggal_review->format('Y-m-d') : '' }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ isset($jobdesc) ? $jobdesc->deskripsi : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="tugas_utama" class="form-label">Tugas Utama <span class="text-danger">*</span></label>
                            <textarea class="form-control ckeditor" id="tugas_utama" name="tugas_utama" rows="5">{{ isset($jobdesc) ? $jobdesc->tugas_utama : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="tanggung_jawab" class="form-label">Tanggung Jawab <span class="text-danger">*</span></label>
                            <textarea class="form-control ckeditor" id="tanggung_jawab" name="tanggung_jawab" rows="5">{{ isset($jobdesc) ? $jobdesc->tanggung_jawab : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="kualifikasi" class="form-label">Kualifikasi</label>
                            <textarea class="form-control ckeditor" id="kualifikasi" name="kualifikasi" rows="4">{{ isset($jobdesc) ? $jobdesc->kualifikasi : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="kompetensi" class="form-label">Kompetensi</label>
                            <textarea class="form-control ckeditor" id="kompetensi" name="kompetensi" rows="4">{{ isset($jobdesc) ? $jobdesc->kompetensi : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="dokumen_file" class="form-label">Dokumen File</label>
                            <input type="file" class="form-control" id="dokumen_file" name="dokumen_file" accept=".pdf,.doc,.docx">
                            <div class="form-text">Format: PDF, DOC, DOCX. Maksimal 5MB.</div>
                            @if(isset($jobdesc) && $jobdesc->dokumen_file)
                                <div class="mt-2">
                                    <small class="text-muted">File saat ini: </small>
                                    <a href="{{ asset('storage/' . $jobdesc->dokumen_file) }}" target="_blank" class="badge bg-info">
                                        <i class="fa fa-file"></i> Lihat File
                                    </a>
                                </div>
                            @endif
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('job-description.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fa-solid fa-save me-1"></i>
                                <span class="btn-text">{{ isset($jobdesc) ? 'Update' : 'Simpan' }}</span>
                                <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                <span class="loading-text d-none">Processing...</span>
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
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor 5 for all ckeditor textareas
    $('.ckeditor').each(function() {
        const editorId = this.id;
        ClassicEditor
            .create(document.querySelector('#' + editorId), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'link', '|',
                    'undo', 'redo'
                ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .then(editor => {
                // Set editor height to 500px
                editor.editing.view.change(writer => {
                    writer.setStyle('height', '300px', editor.editing.view.document.getRoot());
                });
                
                // Store editor instance for form submission
                window['editor_' + editorId] = editor;
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });
    });

    // Initialize Select2
    $('#jabatan_id').select2({
        placeholder: 'Pilih Jabatan',
        allowClear: true
    });

    // Form submission
    $('#jobdesc-form').on('submit', function(e) {
        e.preventDefault();
        
        // Update CKEditor 5 instances and validate required fields
        var isValid = true;
        var requiredEditors = ['tugas_utama', 'tanggung_jawab']; // Required CKEditor fields
        
        $('.ckeditor').each(function() {
            const editorId = this.id;
            const editor = window['editor_' + editorId];
            if (editor) {
                const content = editor.getData().trim();
                this.value = content;
                
                // Check if required field is empty
                if (requiredEditors.includes(editorId) && content === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                    $(this).siblings('.invalid-feedback').text('Field ini wajib diisi.');
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').text('');
                }
            }
        });
        
        // If validation fails, stop submission
        if (!isValid) {
            Swal.fire('Error!', 'Mohon lengkapi semua field yang wajib diisi.', 'error');
            return;
        }
        
        // Show loading state
        var submitBtn = $('#submit-btn');
        submitBtn.prop('disabled', true);
        submitBtn.find('.btn-text').addClass('d-none');
        submitBtn.find('.fa-save').addClass('d-none');
        submitBtn.find('.spinner-border').removeClass('d-none');
        submitBtn.find('.loading-text').removeClass('d-none');
        
        var formData = new FormData(this);
        var url = @if(isset($jobdesc))
            "{{ route('job-description.update', $jobdesc->id) }}"
        @else
            "{{ route('job-description.store') }}"
        @endif;
        
        // Clear previous validation errors
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('job-description.index') }}";
                        }
                    });
                } else {
                    // Reset button state on error
                    resetSubmitButton();
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                // Reset button state on error
                resetSubmitButton();
                
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
    
    // Function to reset submit button state
    function resetSubmitButton() {
        var submitBtn = $('#submit-btn');
        submitBtn.prop('disabled', false);
        submitBtn.find('.btn-text').removeClass('d-none');
        submitBtn.find('.fa-save').removeClass('d-none');
        submitBtn.find('.spinner-border').addClass('d-none');
        submitBtn.find('.loading-text').addClass('d-none');
    }
});
</script>
@endpush