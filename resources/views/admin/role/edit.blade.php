@extends('layouts.app')

@section('title', 'Hak Akses Role: ' . $role->nm_role . ' - ' . config('app.name'))
@section('page_title', 'Edit Role: ' . $role->nm_role)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold tracking-tight text-gray-900">{{ $role->nm_role }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-primary border border-rose-200 uppercase tracking-wider font-mono">
                    ID Role: {{ $role->id_role }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi nama role dan pembagian hak izin akses modul & fitur.</p>
        </div>
        <div>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="h-5 w-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
            <ul class="list-disc ps-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Edit Nama Role -->
    <div class="rounded-md border border-gray-200 bg-white shadow-sm p-6">
        <form action="{{ route('roles.update', $role->id_role) }}" method="POST">
            @csrf
            @method('PUT')

            <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                <i data-lucide="edit-3" class="h-5 w-5 text-primary"></i>
                <span>Identitas Role</span>
            </h3>

            <input type="hidden" name="app_id" id="app_id" value="{{ $role->app_id }}">

            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5" for="nm_role">
                        Nama Role <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nm_role" id="nm_role" value="{{ old('nm_role', $role->nm_role) }}" required
                        class="w-full rounded-md border border-gray-300 px-3.5 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-all">
                </div>

                <div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md px-5 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 shadow-sm transition-colors shrink-0">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        <span>Simpan Nama Role</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Permissions Container -->
    <div class="rounded-md border border-gray-200 bg-white shadow-sm p-6">
        <form action="{{ route('role-menu.update', $role->id_role) }}" method="POST" id="form-permissions">
            @csrf
            @method('PUT')

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <i data-lucide="key" class="h-5 w-5 text-primary"></i>
                        <span>Konfigurasi Hak Akses Fitur & Menu</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Tentukan wewenang create, read, update, dan delete untuk role ini sesuai menu sidebar.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" id="btn-check-all" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                        <i data-lucide="check-square" class="h-3.5 w-3.5 text-primary"></i>
                        <span>Pilih Semua</span>
                    </button>
                    <button type="button" id="btn-uncheck-all" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                        <i data-lucide="square" class="h-3.5 w-3.5 text-gray-400"></i>
                        <span>Batal Semua</span>
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md px-5 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 shadow-sm transition-colors shrink-0">
                        <i data-lucide="check" class="h-4 w-4"></i>
                        <span>Simpan Hak Akses</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-left border-collapse" style="width: 100%">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase text-gray-600">
                            <th class="py-3 px-4 w-72">Menu / Fitur</th>
                            <th class="py-3 px-3 text-center w-28">Lihat (Index)</th>
                            <th class="py-3 px-3 text-center w-28">Buat (Create)</th>
                            <th class="py-3 px-3 text-center w-28">Ubah (Update)</th>
                            <th class="py-3 px-3 text-center w-28">Hapus (Delete)</th>
                            <th class="py-3 px-3 text-center w-28">Pilih Semua</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @foreach ($permissions as $module => $submodules)
                            @php $moduleSlug = Str::slug($module); @endphp
                            <tr class="bg-gray-100/75 perm-row font-semibold text-gray-800" data-module="{{ $module }}">
                                <td colspan="5" class="py-2.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-gray-700">
                                        <i data-lucide="folder" class="h-4 w-4 text-primary"></i>
                                        MODUL: {{ $module }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center bg-gray-200/50">
                                    <button type="button" class="btn-check-module text-xs text-primary hover:underline font-semibold" data-module-slug="{{ $moduleSlug }}" title="Pilih Semua Modul {{ $module }}">
                                        Pilih Modul
                                    </button>
                                </td>
                            </tr>
                            @foreach ($submodules as $prefix => $routes)
                                @php $cleanPrefix = str_replace('.', '-', $prefix); @endphp
                                <tr class="perm-row hover:bg-gray-50 transition-colors" data-module="{{ $module }}">
                                    <td class="py-3 px-4 font-medium text-gray-800 pl-8">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-gray-400"></i>
                                            <span>{{ $routes['label'] }}</span>
                                        </div>
                                    </td>

                                    {{-- Actions Mapping --}}
                                    @foreach (['index', 'create', 'update', 'delete'] as $action)
                                        <td class="py-3 px-3 text-center">
                                            @if (isset($routes['actions'][$action]))
                                                @php $method = $prefix . '.' . $action; @endphp
                                                <input type="checkbox" name="permissions[]" value="{{ $method }}"
                                                    id="perm-{{ str_replace('.', '-', $method) }}"
                                                    {{ in_array($method, $rolePermissions) ? 'checked' : '' }}
                                                    class="permission-item mod-{{ $moduleSlug }} row-{{ $cleanPrefix }} h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="py-3 px-3 text-center bg-gray-50/50">
                                        <input type="checkbox" data-prefix="{{ $cleanPrefix }}" id="check-row-{{ $cleanPrefix }}"
                                            class="check-row h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" title="Pilih Semua Aksi">
                                    </td>
                                </tr>

                                {{-- Handle custom actions (if any) --}}
                                @php
                                    $customActions = array_diff(array_keys($routes['actions']), [
                                        'index',
                                        'create',
                                        'update',
                                        'delete',
                                    ]);
                                @endphp
                                @if (count($customActions) > 0)
                                    <tr class="perm-row bg-slate-50/60" data-module="{{ $module }}">
                                        <td class="py-2 px-4 text-xs font-medium text-gray-500 text-right pr-6 italic">Aksi Khusus:</td>
                                        <td colspan="5" class="py-2 px-3">
                                            <div class="flex flex-wrap items-center gap-4">
                                                @foreach ($customActions as $action)
                                                    @php $method = $prefix . '.' . $action; @endphp
                                                    <label class="inline-flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                                        <input type="checkbox" name="permissions[]" value="{{ $method }}"
                                                            id="perm-{{ str_replace('.', '-', $method) }}"
                                                            {{ in_array($method, $rolePermissions) ? 'checked' : '' }}
                                                            class="permission-item mod-{{ $moduleSlug }} row-{{ $cleanPrefix }} h-3.5 w-3.5 rounded border-gray-300 text-primary focus:ring-primary">
                                                        <span>{{ $routes['actions'][$action] }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200 mt-5">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md px-6 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary/90 shadow-sm transition-colors">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    <span>Simpan Hak Akses</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Check all for row
        $(document).on('change', '.check-row', function() {
            var prefix = $(this).data('prefix');
            $('.row-' + prefix).prop('checked', $(this).is(':checked'));
        });

        function syncCheckRows() {
            $('.check-row').each(function() {
                var prefix = $(this).data('prefix');
                var total = $('.row-' + prefix).length;
                var checked = $('.row-' + prefix + ':checked').length;
                $(this).prop('checked', total > 0 && total === checked);
            });
        }

        syncCheckRows();

        $(document).on('change', '.permission-item', function() {
            syncCheckRows();
        });

        // Check/Uncheck entire module
        $(document).on('click', '.btn-check-module', function() {
            var slug = $(this).data('module-slug');
            var items = $('.mod-' + slug);
            var allChecked = items.length > 0 && items.filter(':checked').length === items.length;
            items.prop('checked', !allChecked);
            syncCheckRows();
        });

        // Global Check all / Uncheck all
        $('#btn-check-all').on('click', function() {
            $('.permission-item').prop('checked', true);
            syncCheckRows();
        });

        $('#btn-uncheck-all').on('click', function() {
            $('.permission-item').prop('checked', false);
            syncCheckRows();
        });
    });
</script>
@endpush
