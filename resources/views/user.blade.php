@extends('layouts.app')

@section('title', 'Manajemen User - ' . config('app.name'))
@section('page_title', 'User')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    #users-table_wrapper { width: 100% !important; }
    #users-table_wrapper .dt-layout-row { width: 100% !important; }
    #users-table_wrapper .dt-layout-cell { width: 100% !important; padding: 0 !important; }
    #users-table_wrapper .dt-layout-table { width: 100% !important; }
    #users-table_wrapper table { width: 100% !important; border-collapse: collapse !important; }
    .dataTables_empty { padding: 2.5rem !important; text-align: center; color: #6b7280; font-weight: 400; font-size: 0.875rem; }
    #users-table_wrapper .dt-layout-row:last-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        font-size: 0.875rem;
        color: #6b7280;
        gap: 1rem;
        flex-wrap: wrap;
        background-color: #ffffff;
    }
    #users-table_wrapper .dt-layout-cell.dt-start { order: 1; display: flex; align-items: center; gap: 1rem; }
    #users-table_wrapper .dt-layout-cell.dt-end { order: 2; margin-left: auto; display: flex; justify-content: flex-end; }
    #users-table_wrapper .dt-paging nav { display: flex; gap: 0.25rem; align-items: center; }
    #users-table_wrapper .dt-paging button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        background: #ffffff;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        color: #374151;
        transition: all 0.15s ease-in-out;
    }
    #users-table_wrapper .dt-paging button:hover:not(.current):not(:disabled) {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    #users-table_wrapper .dt-paging button.current {
        background: #e11d48;
        color: #ffffff;
        border-color: #e11d48;
    }
    #users-table_wrapper .dt-paging button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    #users-table_wrapper .dt-length select {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.875rem;
        color: #374151;
        background-color: #ffffff;
        outline: none;
    }
    #users-table th {
        padding: 1rem 1.5rem !important;
        font-weight: 500 !important;
        font-size: 0.875rem !important;
        text-align: left;
        color: #4b5563 !important;
        background-color: #f9fafb !important;
        border-bottom: 1px solid #e5e7eb !important;
    }
    #users-table td {
        padding: 1rem 1.5rem !important;
        vertical-align: middle;
        color: #111827;
        border-bottom: 1px solid #f3f4f6 !important;
    }
    #users-table tbody tr:hover {
        background-color: #f9fafb;
        transition: background-color 0.15s ease-in-out;
    }

    /* Select2 Tweaks */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        padding: 0 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 6px;
    }
    .select2-dropdown {
        border-radius: 0.375rem;
        border-color: #d1d5db;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 99999;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Toolbar / Filter & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-80">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                <input 
                    type="text" 
                    id="custom-search-input" 
                    placeholder="Cari Nama, Username, atau Email..." 
                    class="w-full rounded-md border border-gray-300 bg-white py-2 pl-9 pr-4 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                />
            </div>
            <select id="filter-role" class="rounded-md border border-gray-300 bg-white py-2 px-3 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-gray-700">
                <option value="">Semua Role</option>
                @foreach ($roleOptions as $appId => $roles)
                    <optgroup label="Aplikasi: {{ strtoupper($appId) }}">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id_role }}">{{ $role->nm_role }} ({{ strtoupper($role->app_id) }})</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <button 
                type="button" 
                class="btn-generate inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors shrink-0"
            >
                <i data-lucide="refresh-cw" class="mr-2 h-4 w-4 text-emerald-600"></i> Generate dari Karyawan
            </button>
            <button 
                type="button" 
                class="btn-add inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary/90 transition-colors shrink-0"
            >
                <i data-lucide="plus" class="mr-2 h-4 w-4"></i> Tambah User
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="rounded-md border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="users-table" style="width:100%">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap w-16 text-center">No</th>
                        <th class="px-6 py-4 whitespace-nowrap">Pengguna</th>
                        <th class="px-6 py-4 whitespace-nowrap">Role & Aplikasi</th>
                        <th class="px-6 py-4 whitespace-nowrap">Tautan Karyawan</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit User -->
<div id="modal_create" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-xs hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 w-full max-w-lg overflow-hidden transform transition-all my-8">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2" id="modal_title">
                <i data-lucide="user-check" class="h-5 w-5 text-primary"></i>
                <span>Tambah User</span>
            </h3>
            <button type="button" class="btn-close-modal text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors" data-target="#modal_create">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <form action="{{ url('user') }}" method="POST" id="modal_create_form" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="_method" value="">
            <input type="hidden" name="id_ru" id="id_ru">

            <!-- Pilih Karyawan (Optional) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tautkan ke Karyawan (Opsional)</label>
                <select name="karyawan_id" id="karyawan_id" class="w-full" style="width: 100%"></select>
                <span class="text-xs text-gray-400 mt-1 block">Pilih karyawan jika user ini adalah staf/karyawan terdaftar.</span>
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5" for="full_name">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" id="full_name" required placeholder="Masukkan nama lengkap user"
                    class="w-full rounded-md border border-gray-300 px-3.5 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-all">
            </div>

            <!-- Email / Username -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5" for="email">Email / Username <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" required placeholder="user@contoh.com"
                    class="w-full rounded-md border border-gray-300 px-3.5 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-all">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5" for="password">Password <span id="password-required-star" class="text-red-500">*</span></label>
                <div class="relative flex items-center">
                    <input type="password" name="password" id="password" placeholder="Minimal 6 karakter"
                        class="w-full rounded-md border border-gray-300 px-3.5 py-2 pr-10 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-all">
                    <button type="button" id="togglePassword" class="absolute right-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i data-lucide="eye" id="togglePasswordIcon" class="h-4 w-4"></i>
                    </button>
                </div>
                <span id="password-hint" class="text-xs text-gray-400 mt-1 block hidden">Biarkan kosong jika tidak ingin mengubah password saat edit.</span>
            </div>

            <!-- Role User -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5" for="id_role">Role Utama <span class="text-red-500">*</span></label>
                <select name="id_role" id="id_role" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary bg-white transition-all">
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roleOptions as $appId => $roles)
                        <optgroup label="Aplikasi: {{ strtoupper($appId) }}">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id_role }}" data-app-id="{{ $role->app_id }}">
                                    {{ $role->nm_role }} ({{ strtoupper($role->app_id) }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" class="btn-close-modal rounded-md px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors" data-target="#modal_create">
                    Batal
                </button>
                <button type="submit" id="btn_submit_user" class="inline-flex items-center gap-2 rounded-md px-5 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 shadow-sm transition-colors">
                    <i data-lucide="check" class="h-4 w-4"></i>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Role Aplikasi -->
<div id="modal_add_role_app" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-xs hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 w-full max-w-md overflow-hidden transform transition-all my-8">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="shield-plus" class="h-5 w-5 text-primary"></i>
                <span>Tambah Role Aplikasi</span>
            </h3>
            <button type="button" class="btn-close-modal text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors" data-target="#modal_add_role_app">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <form id="modal_add_role_form" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="user_id" id="add_role_user_id">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Pengguna</label>
                <input type="text" id="add_role_user_name" readonly class="w-full rounded-md px-3.5 py-2 text-sm bg-gray-100 border border-gray-200 text-gray-700 font-medium">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Role yang Sudah Dimiliki</label>
                <div id="current_role_list" class="p-3 bg-gray-50 border border-gray-200 rounded-md min-h-[42px] flex flex-wrap items-center">
                    <span class="text-gray-400 text-xs">-</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5" for="add_role_id_role">Pilih Role Tambahan <span class="text-red-500">*</span></label>
                <select name="id_role" id="add_role_id_role" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary bg-white transition-all">
                    <option value="">-- Pilih Role Aplikasi Baru --</option>
                    @foreach ($roleOptions as $appId => $roles)
                        <optgroup label="Aplikasi: {{ strtoupper($appId) }}">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id_role }}" data-app-id="{{ $role->app_id }}">
                                    {{ $role->nm_role }} ({{ strtoupper($role->app_id) }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <span class="text-xs text-gray-400 mt-1 block">Aplikasi yang sudah dimiliki user akan dinonaktifkan secara otomatis.</span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" class="btn-close-modal rounded-md px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors" data-target="#modal_add_role_app">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-md px-5 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 shadow-sm transition-colors">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span>Tambahkan Role</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    var table;

    function openModal(modalId) {
        $(modalId).removeClass('hidden');
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function closeModal(modalId) {
        $(modalId).addClass('hidden');
    }

    $(document).ready(function() {
        // Modal close button bindings
        $('.btn-close-modal').on('click', function() {
            const target = $(this).data('target');
            closeModal(target);
        });

        // Initialize DataTable matching companies layout
        table = $('#users-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("user.get-data") }}',
                data: function(d) {
                    d.role_id = $('#filter-role').val();
                    d.search = $('#custom-search-input').val();
                }
            },
            pageLength: 10,
            order: [[1, 'asc']],
            language: {
                search: '',
                searchPlaceholder: 'Cari Pengguna...',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ Pengguna',
                infoEmpty: 'Menampilkan 0 Pengguna',
                infoFiltered: '(difilter dari _MAX_ total)',
                lengthMenu: 'Tampilkan _MENU_ data',
                loadingRecords: '&nbsp;',
                paginate: { first: '&laquo;', previous: '&lsaquo;', next: '&rsaquo;', last: '&raquo;' }
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['pageLength', 'info'],
                bottomEnd: 'paging'
            },
            columnDefs: [
                { orderable: false, targets: [0, 2, 3, 4] },
                { className: "align-middle font-medium text-sm text-gray-900", targets: "_all" }
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center text-gray-500 font-medium' },
                { data: 'user_info', name: 'nm_user' },
                { data: 'role_summary', name: 'role_summary' },
                { data: 'karyawan_info', name: 'karyawan.nama_lengkap', searchable: true, orderable: false },
                { data: 'action', searchable: false, orderable: false, className: 'text-right' }
            ],
            drawCallback: function(settings) {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });

        // Search and Filter triggers
        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#filter-role').on('change', function() {
            table.draw();
        });

        // Initialize Select2 for Karyawan inside modal
        $('#karyawan_id').select2({
            placeholder: 'Pilih / Cari Karyawan...',
            dropdownParent: $('#modal_create'),
            allowClear: true,
            ajax: {
                url: '/ajax/get-karyawan',
                dataType: 'json',
                delay: 250,
                processResults: data => ({
                    results: (data.results || []).map(item => ({
                        id: item.id,
                        text: item.text,
                    })),
                    pagination: data.pagination
                })
            }
        });

        // Autofill full_name when karyawan chosen
        $('#karyawan_id').on('select2:select', function(e) {
            var selectedData = e.params.data;
            if (!$('#full_name').val()) {
                $('#full_name').val(selectedData.text);
            }
        });

        // Password visibility toggle
        $('#togglePassword').on('click', function() {
            const field = $('#password');
            const icon = $('#togglePasswordIcon');
            if (field.attr('type') === 'password') {
                field.attr('type', 'text');
                icon.attr('data-lucide', 'eye-off');
            } else {
                field.attr('type', 'password');
                icon.attr('data-lucide', 'eye');
            }
            if (window.lucide) lucide.createIcons();
        });

        // Add user button click
        $('.btn-add').on('click', function() {
            $("#modal_create_form").attr("action", "{{ url('user') }}");
            $("#_method").val("");
            $("#full_name").val('');
            $("#email").val('');
            $("#password").val('').prop('required', true);
            $("#password-required-star").show();
            $("#password-hint").addClass('hidden');
            $("#id_role").val('');
            $("#id_ru").val('');
            $('#karyawan_id').val(null).trigger('change');

            $('#password').attr('type', 'password');
            $('#modal_title span').text('Tambah User');
            openModal('#modal_create');
        });

        // Edit user button click
        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            const urlnya = $(this).data('url');
            const data = $(this).data('temp');

            $("#modal_create_form").attr("action", urlnya);
            $("#_method").val("PUT");
            $("#full_name").val(data.nm_user || '');
            $("#email").val(data.email || '');

            // Password is not required when editing
            $("#password").val('').prop('required', false);
            $("#password-required-star").hide();
            $("#password-hint").removeClass('hidden');

            if (data.role_user && data.role_user.length > 0) {
                $("#id_role").val(data.role_user[0].id_role);
                $("#id_ru").val(data.role_user[0].id_ru);
            } else {
                $("#id_role").val('');
                $("#id_ru").val('');
            }

            if (data.karyawan_id && data.karyawan) {
                $('#karyawan_id').empty();
                const opt = new Option(data.karyawan.nama_lengkap, data.karyawan_id, true, true);
                $('#karyawan_id').append(opt).trigger('change');
            } else if (data.karyawan_id) {
                $('#karyawan_id').empty();
                const opt = new Option(data.nm_user, data.karyawan_id, true, true);
                $('#karyawan_id').append(opt).trigger('change');
            } else {
                $('#karyawan_id').val(null).trigger('change');
            }

            $('#modal_title span').text('Edit Pengguna');
            openModal('#modal_create');
        });

        // Form submit create / edit
        $('#modal_create_form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(this);
            const url = form.attr('action');
            const method = $('#_method').val() || 'POST';

            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            const submitBtn = $('#btn_submit_user');
            const origHtml = submitBtn.html();
            submitBtn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    closeModal('#modal_create');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Data user berhasil disimpan',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-md px-4 py-2 font-medium text-sm',
                            cancelButton: 'rounded-md px-4 py-2 font-medium text-sm'
                        }
                    });
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Gagal!',
                        html: msg,
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                        }
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(origHtml);
                    if (window.lucide) lucide.createIcons();
                }
            });
        });

        // Add role app modal click
        $(document).on('click', '.btn-add-role', function(e) {
            e.preventDefault();
            const data = $(this).data('temp');
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            const existingApps = [];
            const roleBadges = [];

            if (data.role_user && data.role_user.length > 0) {
                data.role_user.forEach(function(item) {
                    if (item.role) {
                        existingApps.push(item.role.app_id);
                        roleBadges.push(
                            '<span class="inline-flex items-center rounded border px-2 py-0.5 text-xs font-semibold bg-slate-100 text-slate-700 border-slate-200 mr-1 mb-1 font-mono">' +
                            (item.role.app_id || '').toUpperCase() + ' &bull; ' + item.role.nm_role +
                            '</span>'
                        );
                    }
                });
            }

            $('#add_role_user_id').val(userId);
            $('#add_role_user_name').val(userName);
            $('#current_role_list').html(roleBadges.length ? roleBadges.join('') : '<span class="text-gray-400 text-xs">-</span>');
            $('#modal_add_role_form').attr('action', "{{ url('user') }}/" + userId + "/roles");
            $('#add_role_id_role').val('');

            $('#add_role_id_role option').each(function() {
                const optionAppId = $(this).data('app-id');
                const shouldDisable = optionAppId && existingApps.includes(optionAppId);
                $(this).prop('disabled', shouldDisable);
            });

            openModal('#modal_add_role_app');
        });

        // Add role form submit
        $('#modal_add_role_form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const origText = submitBtn.html();
            submitBtn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    closeModal('#modal_add_role_app');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                        }
                    });
                },
                error: function(xhr) {
                    let msg = 'Gagal menambahkan role aplikasi.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Gagal!',
                        html: msg,
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                        }
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(origText);
                    if (window.lucide) lucide.createIcons();
                }
            });
        });

        // Delete user (matching deleteCompany style)
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const urlnya = $(this).data('url');
            const namaData = $(this).data('name') || $(this).closest('tr').find('td:eq(1)').text().trim();

            Swal.fire({
                title: 'Hapus Pengguna?',
                text: 'Pengguna "' + namaData + '" akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-md px-4 py-2 font-medium text-sm',
                    cancelButton: 'rounded-md px-4 py-2 font-medium text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: urlnya,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: data.message || "User berhasil dihapus.",
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'rounded-xl',
                                    confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: xhr.responseJSON?.message || "Gagal menghapus user.",
                                customClass: {
                                    popup: 'rounded-xl',
                                    confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                }
                            });
                        }
                    });
                }
            });
        });

        // Generate User from Karyawan
        $('.btn-generate').on('click', function() {
            Swal.fire({
                title: 'Generate User Otomatis',
                text: 'Sistem akan membuat akun user untuk seluruh karyawan aktif yang belum memiliki user (username & password default adalah nomor HP). Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Generate!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-md px-4 py-2 font-medium text-sm',
                    cancelButton: 'rounded-md px-4 py-2 font-medium text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang men-generate akun pengguna...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('user.generate') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                let message = response.message;
                                if (response.errors && response.errors.length > 0) {
                                    message += '<br><br>Errors:<br>' + response.errors.join('<br>');
                                }
                                Swal.fire({
                                    title: 'Berhasil!',
                                    html: message,
                                    icon: 'success',
                                    customClass: {
                                        popup: 'rounded-xl',
                                        confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                    }
                                }).then(() => {
                                    table.ajax.reload(null, false);
                                });
                            } else {
                                Swal.fire({
                                    title: 'Perhatian',
                                    text: response.message,
                                    icon: 'info',
                                    customClass: {
                                        popup: 'rounded-xl',
                                        confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat generate users.',
                                icon: 'error',
                                customClass: {
                                    popup: 'rounded-xl',
                                    confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                }
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
