@extends('layouts.app')

@section('title', 'Master Program & Paket Akademik - ' . config('app.name'))
@section('page_title', 'Master Program & Paket Akademik')

@push('styles')
<style>
    #programs-table_wrapper .dataTables_length select {
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #programs-table_wrapper .dataTables_filter input {
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        outline: none;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Top Action & Overview -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-1.5 border border-sky-100">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                Data Master Terpadu
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Master Program &amp; Layanan Akademik</h1>
            <p class="text-xs text-gray-500">Kelola daftar program bimbingan, paket olah data, jenjang pendidikan, serta tarif sesi secara dinamis.</p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Program Baru
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
        <div class="overflow-x-auto">
            <table id="programs-table" class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Program &amp; Kode</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Jenjang</th>
                        <th class="py-3 px-4">Biaya Standar</th>
                        <th class="py-3 px-4">Durasi Sesi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Create / Edit -->
<div id="program-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 id="modal-title" class="text-base font-black text-gray-900">Tambah Program Baru</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="program-form" onsubmit="saveProgram(event)" class="p-6 space-y-4">
            <input type="hidden" id="program-id" name="id">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kode Program *</label>
                    <input type="text" id="kode_program" name="kode_program" required placeholder="Contoh: PRG-SKR-02" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kategori *</label>
                    <select id="kategori" name="kategori" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="Bimbingan Skripsi">Bimbingan Skripsi</option>
                        <option value="Bimbingan Tesis">Bimbingan Tesis</option>
                        <option value="Olah Data">Olah Data Riset</option>
                        <option value="Pelatihan">Pelatihan &amp; Workshop</option>
                        <option value="Publikasi Jurnal">Publikasi Jurnal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Program *</label>
                <input type="text" id="nama_program" name="nama_program" required placeholder="Contoh: Bimbingan Skripsi Full Bab 1-5" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Jenjang Pendidikan</label>
                    <select id="jenjang" name="jenjang" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="S1 (Sarjana)">S1 (Sarjana)</option>
                        <option value="S2 (Magister)">S2 (Magister)</option>
                        <option value="S3 (Doktor)">S3 (Doktor)</option>
                        <option value="Semua Jenjang">Semua Jenjang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Durasi Sesi (Menit)</label>
                    <input type="number" id="durasi_sesi" name="durasi_sesi" value="60" min="15" step="15" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Biaya Standar (Rp) *</label>
                <input type="number" id="biaya_standar" name="biaya_standar" required placeholder="500000" min="0" step="10000" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Deskripsi Singkat</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Rincian materi, fasilitas, dan cakupan layanan..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <label for="is_active" class="text-xs font-medium text-gray-700 cursor-pointer">Program Aktif &amp; Dapat Dipilih Klien</label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" id="save-btn" class="px-5 py-2 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition shadow-xs">
                    Simpan Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let programTable;

    $(document).ready(function() {
        programTable = $('#programs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master.programs.get-data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_program', name: 'nama_program' },
                { data: 'kategori', name: 'kategori' },
                { data: 'jenjang', name: 'jenjang' },
                { data: 'biaya_standar', name: 'biaya_standar' },
                { data: 'durasi_sesi', name: 'durasi_sesi', render: data => data + ' Menit' },
                { data: 'is_active', name: 'is_active' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-right' }
            ],
            drawCallback: function() {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });
    });

    function openCreateModal() {
        $('#program-form')[0].reset();
        $('#program-id').val('');
        $('#modal-title').text('Tambah Program Baru');
        $('#is_active').prop('checked', true);
        $('#program-modal').removeClass('hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeModal() {
        $('#program-modal').addClass('hidden');
    }

    function editProgram(id) {
        $.get("{{ url('master/programs') }}/" + id, function(data) {
            $('#program-id').val(data.id);
            $('#kode_program').val(data.kode_program);
            $('#nama_program').val(data.nama_program);
            $('#kategori').val(data.kategori);
            $('#jenjang').val(data.jenjang);
            $('#biaya_standar').val(Math.round(data.biaya_standar));
            $('#durasi_sesi').val(data.durasi_sesi);
            $('#deskripsi').val(data.deskripsi);
            $('#is_active').prop('checked', data.is_active == 1);

            $('#modal-title').text('Edit Program: ' + data.nama_program);
            $('#program-modal').removeClass('hidden');
            if (window.lucide) lucide.createIcons();
        });
    }

    function saveProgram(e) {
        e.preventDefault();
        const id = $('#program-id').val();
        const url = id ? "{{ url('master/programs') }}/" + id : "{{ route('master.programs.store') }}";
        const method = id ? 'PUT' : 'POST';

        const formData = $('#program-form').serializeArray();
        let payload = {};
        formData.forEach(item => payload[item.name] = item.value);
        if (!$('#is_active').is(':checked')) {
            delete payload['is_active'];
        }

        $.ajax({
            url: url,
            type: method,
            data: payload,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                closeModal();
                programTable.ajax.reload();
                alert(res.message);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                if (errors) {
                    let msg = '';
                    for (let k in errors) msg += errors[k].join('\n') + '\n';
                    alert(msg);
                } else {
                    alert('Gagal menyimpan program.');
                }
            }
        });
    }

    function deleteProgram(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus program ini?')) return;

        $.ajax({
            url: "{{ url('master/programs') }}/" + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                programTable.ajax.reload();
                alert(res.message);
            },
            error: function() {
                alert('Gagal menghapus program.');
            }
        });
    }
</script>
@endpush
