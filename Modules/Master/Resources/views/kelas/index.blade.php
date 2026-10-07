@extends('layouts.app')

@section('title', 'Master Kelas & Workshop - ' . config('app.name'))
@section('page_title', 'Master Kelas & Workshop')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 bg-purple-50 text-purple-700 rounded-xl">
                    <i data-lucide="award" class="w-5 h-5"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Katalog Master Kelas &amp; Workshop Riset</h2>
                    <p class="text-xs text-gray-500">Kelola daftar bootcamp, workshop statistik, jadwal webinar, kuota, dan harga pendaftaran.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Kelas Baru</span>
            </button>
        </div>
    </div>

    <!-- Filters & DataTable Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-6 space-y-4">
        <!-- Filter Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Filter Tingkat (Level)</label>
                <select id="filter-level" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                    <option value="">Semua Level</option>
                    <option value="Pemula">Pemula</option>
                    <option value="Intermediate">Intermediate / Menengah</option>
                    <option value="Advanced">Advanced / Mahir</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Filter Status Pendaftaran</label>
                <select id="filter-status" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                    <option value="">Semua Status</option>
                    <option value="Dibuka">Dibuka</option>
                    <option value="Berlangsung">Berlangsung</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Ditutup">Ditutup</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" onclick="refreshTable()" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-1.5 transition">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Muat Ulang Data</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto pt-2">
            <table id="kelas-table" class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500 font-bold border-y border-gray-200">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Judul Kelas &amp; Mentor</th>
                        <th class="py-3.5 px-4">Level &amp; Kategori</th>
                        <th class="py-3.5 px-4">Biaya &amp; Kuota</th>
                        <th class="py-3.5 px-4">Jadwal Pelaksanaan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Create / Edit Kelas -->
<div id="modal-kelas" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 id="modal-title" class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4 text-[#1768B5]"></i>
                <span>Tambah Kelas Baru</span>
            </h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="form-kelas" onsubmit="submitForm(event)" class="p-6 space-y-4">
            <input type="hidden" id="kelas_id" name="id">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" id="kode_kelas" name="kode_kelas" required placeholder="Contoh: KLS-PLS-01" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 font-mono uppercase focus:ring-2 focus:ring-[#1768B5]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Badge Promosi</label>
                    <input type="text" id="badge" name="badge" placeholder="Contoh: Terpopuler, Best Seller, Baru" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Judul Kelas / Pelatihan <span class="text-rose-500">*</span></label>
                <input type="text" id="judul" name="judul" required placeholder="Contoh: Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Bab 4" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Instruktur / Mentor</label>
                    <select id="mentor_id" name="mentor_id" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="">-- Pilih Mentor Pengampu --</option>
                        @foreach($mentors as $mentor)
                            <option value="{{ $mentor->id }}">{{ $mentor->nama_lengkap }} ({{ $mentor->spesialisasi }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Program Terkait</label>
                    <select id="program_id" name="program_id" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="">-- Hubungkan dengan Program --</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->nama_program }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tingkat (Level)</label>
                    <select id="level" name="level" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="Pemula">Pemula</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Semua Level">Semua Level</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tipe Pelaksanaan</label>
                    <select id="tipe_kelas" name="tipe_kelas" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="Bootcamp Intensif">Bootcamp Intensif</option>
                        <option value="Online Webinar">Online Webinar</option>
                        <option value="Self-Paced Video">Self-Paced Video</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status Pendaftaran</label>
                    <select id="status" name="status" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                        <option value="Dibuka">Dibuka</option>
                        <option value="Berlangsung">Berlangsung</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Ditutup">Ditutup</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Biaya Pendaftaran (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="harga" name="harga" required min="0" placeholder="250000" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 font-mono focus:ring-2 focus:ring-[#1768B5]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kuota Maksimal Peserta <span class="text-rose-500">*</span></label>
                    <input type="number" id="kuota_maksimal" name="kuota_maksimal" required min="1" placeholder="100" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 font-mono focus:ring-2 focus:ring-[#1768B5]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Link Pertemuan (Zoom / GMeet / LMS)</label>
                <input type="url" id="link_meeting" name="link_meeting" placeholder="https://meet.google.com/..." class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi &amp; Cakupan Materi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan tujuan pembelajaran, software yang digunakan, dan output bagi peserta..." class="w-full text-xs rounded-xl border border-gray-200 p-2.5 focus:ring-2 focus:ring-[#1768B5]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Batal</button>
                <button type="submit" id="btn-save" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var table;
    $(document).ready(function() {
        table = $('#kelas-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("master.kelas.get-data") }}',
                data: function(d) {
                    d.level = $('#filter-level').val();
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-bold text-gray-400' },
                { data: 'judul_info', name: 'judul' },
                { data: 'level_kategori', name: 'level' },
                { data: 'harga_formatted', name: 'harga' },
                { data: 'jadwal_tipe', name: 'tanggal_mulai' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            drawCallback: function() {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });

        $('#filter-level, #filter-status').on('change', function() {
            table.draw();
        });
    });

    function refreshTable() {
        $('#filter-level').val('');
        $('#filter-status').val('');
        table.draw();
    }

    function openCreateModal() {
        $('#modal-title span').text('Tambah Kelas Baru');
        $('#form-kelas')[0].reset();
        $('#kelas_id').val('');
        $('#modal-kelas').removeClass('hidden');
        if (window.lucide) { lucide.createIcons(); }
    }

    function closeModal() {
        $('#modal-kelas').addClass('hidden');
    }

    function editKelas(row) {
        $('#modal-title span').text('Edit Kelas: ' + row.kode_kelas);
        $('#kelas_id').val(row.id);
        $('#kode_kelas').val(row.kode_kelas);
        $('#badge').val(row.badge);
        $('#judul').val(row.judul);
        $('#mentor_id').val(row.mentor_id);
        $('#program_id').val(row.program_id);
        $('#level').val(row.level);
        $('#tipe_kelas').val(row.tipe_kelas);
        $('#status').val(row.status);
        $('#harga').val(row.harga);
        $('#kuota_maksimal').val(row.kuota_maksimal);
        $('#tanggal_mulai').val(row.tanggal_mulai ? row.tanggal_mulai.substring(0, 10) : '');
        $('#tanggal_selesai').val(row.tanggal_selesai ? row.tanggal_selesai.substring(0, 10) : '');
        $('#link_meeting').val(row.link_meeting);
        $('#deskripsi').val(row.deskripsi);
        $('#modal-kelas').removeClass('hidden');
        if (window.lucide) { lucide.createIcons(); }
    }

    function submitForm(e) {
        e.preventDefault();
        let id = $('#kelas_id').val();
        let isEdit = id ? true : false;
        let url = isEdit ? '{{ url("master/kelas") }}/' + id : '{{ route("master.kelas.store") }}';
        let method = isEdit ? 'PUT' : 'POST';

        let formData = $('#form-kelas').serialize();
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
                $('#btn-save').prop('disabled', false).text('Simpan Kelas');
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
                $('#btn-save').prop('disabled', false).text('Simpan Kelas');
                let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Terjadi kesalahan sistem';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg
                });
            }
        });
    }

    function deleteKelas(id, title) {
        Swal.fire({
            title: 'Hapus Kelas?',
            text: 'Kelas "' + title + '" beserta seluruh silabusnya akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/kelas") }}/' + id,
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
                            text: 'Gagal menghapus kelas.'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
