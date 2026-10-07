@extends('layouts.app')

@section('title', 'Master Mentor & Konsultan Riset - ' . config('app.name'))
@section('page_title', 'Master Mentor & Konsultan Riset')

@push('styles')
<style>
    #mentors-table_wrapper .dataTables_length select {
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #mentors-table_wrapper .dataTables_filter input {
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold mb-1.5 border border-purple-100">
                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                Tenaga Ahli &amp; Konsultan Metodologi
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Master Mentor &amp; Analis Data</h1>
            <p class="text-xs text-gray-500">Kelola profil mentor bimbingan, spesialisasi metode statistik, software yang dikuasai, dan status ketersediaan.</p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] shadow-sm transition">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Tambah Mentor Baru
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
        <div class="overflow-x-auto">
            <table id="mentors-table" class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Mentor &amp; Spesialisasi</th>
                        <th class="py-3 px-4">Software Dikuasai</th>
                        <th class="py-3 px-4">Almamater / Institusi</th>
                        <th class="py-3 px-4">Tarif Sesi</th>
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
<div id="mentor-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 id="modal-title" class="text-base font-black text-gray-900">Tambah Mentor Baru</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="mentor-form" onsubmit="saveMentor(event)" class="p-6 space-y-4">
            <input type="hidden" id="mentor-id" name="id">

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Contoh: Nur Atika Rochmah" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Gelar Akademik</label>
                    <input type="text" id="gelar" name="gelar" placeholder="S.Stat., M.Si." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Spesialisasi Metodologi *</label>
                <input type="text" id="spesialisasi" name="spesialisasi" required placeholder="Contoh: Structural Equation Modeling & Kuantitatif" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" placeholder="mentor@aether.test" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">No. WhatsApp</label>
                    <input type="text" id="no_hp" name="no_hp" placeholder="081234567890" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Pendidikan Terakhir</label>
                    <input type="text" id="pendidikan_terakhir" name="pendidikan_terakhir" placeholder="S2 Statistika Terapan" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Almamater / Kampus</label>
                    <input type="text" id="institusi" name="institusi" placeholder="Institut Teknologi Sepuluh Nopember" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Keahlian Software (Pisahkan koma)</label>
                <input type="text" id="keahlian_software" name="keahlian_software" placeholder="SmartPLS, AMOS, SPSS, EViews, R" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Tarif Bimbingan per Sesi (Rp) *</label>
                <input type="number" id="tarif_per_sesi" name="tarif_per_sesi" required placeholder="150000" min="0" step="5000" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <label for="is_active" class="text-xs font-medium text-gray-700 cursor-pointer">Mentor Aktif &amp; Siap Menerima Jadwal Sesi</label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition shadow-xs">
                    Simpan Data Mentor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let mentorTable;

    $(document).ready(function() {
        mentorTable = $('#mentors-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master.mentors.get-data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_lengkap', name: 'nama_lengkap' },
                { data: 'keahlian_software', name: 'keahlian_software' },
                { data: 'institusi', name: 'institusi', defaultContent: '-' },
                { data: 'tarif_per_sesi', name: 'tarif_per_sesi' },
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
        $('#mentor-form')[0].reset();
        $('#mentor-id').val('');
        $('#modal-title').text('Tambah Mentor Baru');
        $('#is_active').prop('checked', true);
        $('#mentor-modal').removeClass('hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeModal() {
        $('#mentor-modal').addClass('hidden');
    }

    function editMentor(id) {
        $.get("{{ url('master/mentors') }}/" + id, function(data) {
            $('#mentor-id').val(data.id);
            $('#nama_lengkap').val(data.nama_lengkap);
            $('#gelar').val(data.gelar);
            $('#spesialisasi').val(data.spesialisasi);
            $('#email').val(data.email);
            $('#no_hp').val(data.no_hp);
            $('#pendidikan_terakhir').val(data.pendidikan_terakhir);
            $('#institusi').val(data.institusi);
            $('#keahlian_software').val(data.keahlian_software);
            $('#tarif_per_sesi').val(Math.round(data.tarif_per_sesi));
            $('#is_active').prop('checked', data.is_active == 1);

            $('#modal-title').text('Edit Data: ' + data.nama_lengkap);
            $('#mentor-modal').removeClass('hidden');
            if (window.lucide) lucide.createIcons();
        });
    }

    function saveMentor(e) {
        e.preventDefault();
        const id = $('#mentor-id').val();
        const url = id ? "{{ url('master/mentors') }}/" + id : "{{ route('master.mentors.store') }}";
        const method = id ? 'PUT' : 'POST';

        const formData = $('#mentor-form').serializeArray();
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
                mentorTable.ajax.reload();
                alert(res.message);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                if (errors) {
                    let msg = '';
                    for (let k in errors) msg += errors[k].join('\n') + '\n';
                    alert(msg);
                } else {
                    alert('Gagal menyimpan data mentor.');
                }
            }
        });
    }

    function deleteMentor(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus mentor ini?')) return;

        $.ajax({
            url: "{{ url('master/mentors') }}/" + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                mentorTable.ajax.reload();
                alert(res.message);
            },
            error: function() {
                alert('Gagal menghapus data mentor.');
            }
        });
    }
</script>
@endpush
