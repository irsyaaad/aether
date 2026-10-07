@extends('layouts.app')

@section('title', 'Master Materi & Silabus - ' . config('app.name'))
@section('page_title', 'Master Materi & Silabus')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 bg-sky-50 text-[#1768B5] rounded-xl">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Kurikulum &amp; Silabus Modul Pembelajaran</h2>
                    <p class="text-xs text-gray-500">Kelola urutan materi, video tutorial, modul PDF, dan file dataset latihan per kelas pelatihan.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('master.kelas.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Kelas</span>
            </a>
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Materi Baru</span>
            </button>
        </div>
    </div>

    <!-- Pilih Kelas & Filter -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div class="flex-1 max-w-md">
                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Kelas / Kursus:</label>
                <select id="select-kelas" onchange="changeKelas(this.value)" class="w-full text-xs font-medium rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5] bg-sky-50/50 text-[#102654]">
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ ($selectedKelas && $selectedKelas->id == $k->id) ? 'selected' : '' }}>
                            [{{ $k->kode_kelas }}] {{ $k->judul }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($selectedKelas)
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="inline-flex items-center gap-1 bg-gray-100 px-2.5 py-1 rounded-lg">
                        <i data-lucide="award" class="w-3.5 h-3.5 text-[#1768B5]"></i>
                        Level: <strong class="text-gray-800">{{ $selectedKelas->level }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded-lg">
                        <i data-lucide="tag" class="w-3.5 h-3.5 text-emerald-600"></i>
                        Status: <strong class="text-emerald-700">{{ $selectedKelas->status }}</strong>
                    </span>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto pt-2">
            <table id="materi-table" class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500 font-bold border-y border-gray-200">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">Urutan</th>
                        <th class="py-3.5 px-4">Judul Modul / Materi</th>
                        <th class="py-3.5 px-4">Tipe Konten</th>
                        <th class="py-3.5 px-4">Durasi Sesi</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Create / Edit Materi -->
<div id="modal-materi" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 id="modal-title" class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <i data-lucide="book-open" class="w-4 h-4 text-[#1768B5]"></i>
                <span>Tambah Materi Baru</span>
            </h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="form-materi" onsubmit="submitForm(event)" class="p-6 space-y-4">
            <input type="hidden" id="materi_id" name="id">
            <input type="hidden" id="form_kelas_id" name="kelas_id" value="{{ $selectedKelas ? $selectedKelas->id : '' }}">
            
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Urutan <span class="text-rose-500">*</span></label>
                    <input type="number" id="urutan" name="urutan" required min="1" value="1" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 font-mono focus:ring-2 focus:ring-[#1768B5]">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tipe Konten <span class="text-rose-500">*</span></label>
                    <select id="tipe_materi" name="tipe_materi" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="Video">Video Pembelajaran</option>
                        <option value="Dokumen PDF">Dokumen PDF / PPT</option>
                        <option value="Dataset Praktik">Dataset Praktik (.sav/.csv)</option>
                        <option value="Kuis/Tugas">Latihan Mandiri / Tugas</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Judul Materi / Modul <span class="text-rose-500">*</span></label>
                <input type="text" id="judul_materi" name="judul_materi" required placeholder="Contoh: Pengujian Hipotesis Inner Model & Mediasi" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Estimasi Durasi (Menit)</label>
                    <input type="number" id="durasi_menit" name="durasi_menit" min="0" placeholder="45" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 font-mono focus:ring-2 focus:ring-[#1768B5]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Akses Preview</label>
                    <select id="is_free_preview" name="is_free_preview" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="0">Terkunci (Member Berbayar)</option>
                        <option value="1">Free Preview (Bisa Ditonton Gratis)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Link URL Video / Embed (Opsional)</label>
                <input type="url" id="link_video" name="link_video" placeholder="https://youtube.com/... atau https://vimeo.com/..." class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama File Dokumen / Dataset (Opsional)</label>
                <input type="text" id="file_materi" name="file_materi" placeholder="template_laporan_bab4.docx atau data_150n.csv" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi Singkat Silabus</label>
                <textarea id="deskripsi" name="deskripsi" rows="2" placeholder="Poin-poin bahasan pada materi ini..." class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Batal</button>
                <button type="submit" id="btn-save" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">Simpan Materi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var table;
    $(document).ready(function() {
        initTable();
    });

    function initTable() {
        let currentKelasId = $('#select-kelas').val();
        table = $('#materi-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("master.materi.get-data") }}',
                data: function(d) {
                    d.kelas_id = $('#select-kelas').val();
                }
            },
            columns: [
                { data: 'urutan_badge', name: 'urutan', className: 'text-center' },
                { data: 'materi_info', name: 'judul_materi' },
                { data: 'tipe_badge', name: 'tipe_materi' },
                { data: 'durasi_info', name: 'durasi_menit' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            drawCallback: function() {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });
    }

    function changeKelas(kelasId) {
        $('#form_kelas_id').val(kelasId);
        table.draw();
    }

    function openCreateModal() {
        $('#modal-title span').text('Tambah Materi Baru');
        $('#form-materi')[0].reset();
        $('#materi_id').val('');
        $('#form_kelas_id').val($('#select-kelas').val());
        $('#modal-materi').removeClass('hidden');
        if (window.lucide) { lucide.createIcons(); }
    }

    function closeModal() {
        $('#modal-materi').addClass('hidden');
    }

    function editMateri(row) {
        $('#modal-title span').text('Edit Materi: #' + row.urutan);
        $('#materi_id').val(row.id);
        $('#form_kelas_id').val(row.kelas_id);
        $('#urutan').val(row.urutan);
        $('#tipe_materi').val(row.tipe_materi);
        $('#judul_materi').val(row.judul_materi);
        $('#durasi_menit').val(row.durasi_menit);
        $('#is_free_preview').val(row.is_free_preview ? '1' : '0');
        $('#link_video').val(row.link_video);
        $('#file_materi').val(row.file_materi);
        $('#deskripsi').val(row.deskripsi);
        $('#modal-materi').removeClass('hidden');
        if (window.lucide) { lucide.createIcons(); }
    }

    function submitForm(e) {
        e.preventDefault();
        let id = $('#materi_id').val();
        let isEdit = id ? true : false;
        let url = isEdit ? '{{ url("master/materi") }}/' + id : '{{ route("master.materi.store") }}';
        let method = isEdit ? 'PUT' : 'POST';

        let formData = $('#form-materi').serialize();
        if (isEdit) {
            formData += '&_method=PUT';
        }

        $('#btn-save').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(res) {
                $('#btn-save').prop('disabled', false).text('Simpan Materi');
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                table.draw(false);
            },
            error: function(err) {
                $('#btn-save').prop('disabled', false).text('Simpan Materi');
                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Terjadi kesalahan';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg
                });
            }
        });
    }

    function deleteMateri(id, title) {
        Swal.fire({
            title: 'Hapus Materi?',
            text: 'Materi "' + title + '" akan dihapus dari silabus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/materi") }}/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        table.draw(false);
                    },
                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal menghapus materi.'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
