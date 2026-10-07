@extends('layouts.app')

@section('title', 'Prioritas Tugas - ' . config('app.name'))
@section('page_title', 'Master Prioritas Tugas')

@section('toolbar_actions')
<div class="flex items-center gap-2">
    <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
        Kembali ke Tugas
    </a>
    <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Prioritas
    </button>
</div>
@endsection

@push('styles')
<style>
    #priorities-table_wrapper { width: 100% !important; }
    #priorities-table_wrapper .dt-layout-row { width: 100% !important; }
    #priorities-table_wrapper .dt-layout-cell { width: 100% !important; padding: 0 !important; }
    #priorities-table_wrapper .dt-layout-table { width: 100% !important; }
    #priorities-table_wrapper table { width: 100% !important; border-collapse: collapse !important; }
    
    #priorities-table_wrapper .dt-layout-row:last-child {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 1rem !important;
        padding: 1rem 1.5rem !important;
        border-top: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
    }

    #priorities-table th {
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-weight: 600 !important;
        color: #4b5563 !important;
        background-color: #f9fafb !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding: 0.875rem 1.25rem !important;
    }
    #priorities-table td {
        padding: 0.875rem 1.25rem !important;
        font-size: 0.875rem !important;
        border-bottom: 1px solid #f3f4f6 !important;
        color: #1f2937 !important;
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center gap-3">
        <div class="w-full sm:w-64">
            <select id="filter-company" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                <option value="">Semua Perusahaan</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-48">
            <select id="filter-status" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table id="priorities-table" class="w-full text-left">
            <thead>
                <tr>
                    <th class="w-12 text-center">#</th>
                    <th>Nama & Kode</th>
                    <th>Perusahaan</th>
                    <th>Level</th>
                    <th>Jumlah Tugas</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<!-- Modal Form -->
<div id="priority-modal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900" id="modal-title">Tambah Prioritas Tugas</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="priority-form" onsubmit="savePriority(event)">
            <input type="hidden" id="priority-id" value="">
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Perusahaan <span class="text-red-500">*</span></label>
                    <select id="modal-company-id" required class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Prioritas <span class="text-red-500">*</span></label>
                    <input type="text" id="modal-name" required placeholder="Contoh: Urgent" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kode Prioritas <span class="text-red-500">*</span></label>
                    <input type="text" id="modal-code" required placeholder="Contoh: URGENT" class="w-full py-2 px-3 border border-gray-300 rounded-lg uppercase focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Level (Tingkat Urgensi 1-10) <span class="text-red-500">*</span></label>
                    <input type="number" id="modal-level" min="1" max="10" required placeholder="1 = Rendah, 4 = Urgent" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea id="modal-description" rows="3" placeholder="Keterangan singkat..." class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Status</label>
                    <select id="modal-is-active" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" id="btn-save-pri" class="px-4 py-1.5 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let priorityTable;

    $(document).ready(function() {
        priorityTable = $('#priorities-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('task-priorities.get-data') }}",
                data: function(d) {
                    d.company_id = $('#filter-company').val();
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name', name: 'name' },
                { data: 'company_name', name: 'company.name' },
                { data: 'level', name: 'level' },
                { data: 'tasks_count', name: 'tasks_count', searchable: false },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[3, 'asc']], // Order by level asc
            drawCallback: function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });

        $('#filter-company, #filter-status').on('change', function() {
            priorityTable.draw();
        });
    });

    function openCreateModal() {
        $('#priority-id').val('');
        $('#modal-title').text('Tambah Prioritas Tugas');
        $('#modal-name').val('');
        $('#modal-code').val('');
        $('#modal-level').val('1');
        $('#modal-description').val('');
        $('#modal-is-active').val('1');
        $('#priority-modal').removeClass('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function editPriority(p) {
        $('#priority-id').val(p.id);
        $('#modal-title').text('Edit Prioritas Tugas');
        $('#modal-company-id').val(p.company_id);
        $('#modal-name').val(p.name);
        $('#modal-code').val(p.code);
        $('#modal-level').val(p.level);
        $('#modal-description').val(p.description);
        $('#modal-is-active').val(p.is_active);
        $('#priority-modal').removeClass('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeModal() {
        $('#priority-modal').addClass('hidden');
    }

    function savePriority(e) {
        e.preventDefault();
        const id = $('#priority-id').val();
        const url = id ? `{{ url('task-priorities') }}/${id}` : `{{ route('task-priorities.store') }}`;
        const method = id ? 'PUT' : 'POST';

        $('#btn-save-pri').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                company_id: $('#modal-company-id').val(),
                name: $('#modal-name').val(),
                code: $('#modal-code').val(),
                level: $('#modal-level').val(),
                description: $('#modal-description').val(),
                is_active: $('#modal-is-active').val()
            },
            success: function(res) {
                $('#btn-save-pri').prop('disabled', false).text('Simpan');
                closeModal();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1200, showConfirmButton: false });
                priorityTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                $('#btn-save-pri').prop('disabled', false).text('Simpan');
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    function deletePriority(id, name) {
        Swal.fire({
            title: 'Hapus Prioritas?',
            text: `Yakin ingin menghapus prioritas "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('task-priorities') }}/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1200, showConfirmButton: false });
                        priorityTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
