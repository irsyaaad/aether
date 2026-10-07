@extends('layouts.app')

@section('title', 'Hak Akses & Permission - ' . config('app.name'))
@section('page_title', 'Hak Akses & Permission')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Daftar Hak Akses (Permissions)</h2>
            <p class="text-sm text-gray-500">Pemetaan izin dan hak akses modul sistem yang diberikan kepada masing-masing role.</p>
        </div>
        <div>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-rose-700 transition-colors shadow-sm">
                <i data-lucide="shield-check" class="h-4 w-4"></i>
                <span>Atur Hak Akses per Role</span>
            </a>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-sm flex items-start gap-3">
        <i data-lucide="info" class="h-5 w-5 text-blue-600 shrink-0 mt-0.5"></i>
        <div>
            <span class="font-semibold">Informasi Pengaturan Permission:</span>
            Untuk menambah atau mengubah centang hak akses suatu role secara menyeluruh (matrix per modul), klik tombol 
            <span class="font-medium underline">Atur Hak Akses</span> pada role yang bersangkutan di halaman 
            <a href="{{ route('roles.index') }}" class="font-semibold text-blue-700 hover:underline">Manajemen Role</a>.
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
        <form method="GET" action="{{ route('role-menu.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Filter Berdasarkan Role</label>
                <select name="role_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white transition-all" onchange="this.form.submit()">
                    <option value="">-- Semua Role --</option>
                    @foreach ($roles ?? [] as $role)
                        <option value="{{ $role->id_role }}" {{ request('role_id') == $role->id_role ? 'selected' : '' }}>
                            {{ $role->nm_role }} ({{ strtoupper($role->app_id ?? 'ERP') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-6">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Cari Permission / Role</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama permission (misal: user.index, armada.create)..."
                        class="w-full px-3.5 py-2 pl-9 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"></i>
                </div>
            </div>

            <div class="sm:col-span-2 flex items-end gap-2 pt-5">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors shadow-sm">
                    <i data-lucide="filter" class="h-4 w-4"></i>
                    <span>Filter</span>
                </button>
                @if(request('role_id') || request('search'))
                    <a href="{{ route('role-menu.index') }}" class="p-2 rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600 transition-colors" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" style="width: 100%">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase text-gray-600">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Role</th>
                        <th class="py-3 px-4">Permission Key</th>
                        <th class="py-3 px-4">Modul</th>
                        <th class="py-3 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse ($data as $key => $item)
                        @php
                            $parts = explode('.', (string) $item->permission);
                            $module = $parts[0] ?? '-';
                            $action = $parts[1] ?? '-';
                        @endphp
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="py-3 px-4 text-center text-gray-500 font-medium">
                                {{ $data->firstItem() + $key }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900">{{ $item->role->nm_role ?? 'Role #' . $item->id_role }}</span>
                                    @if(isset($item->role->app_id))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ strtoupper($item->role->app_id) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 border border-slate-200">
                                    <i data-lucide="key" class="h-3 w-3 text-gray-500"></i>
                                    {{ $item->permission }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 uppercase tracking-wider font-mono">
                                    {{ $module }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('roles.show', $item->id_role) }}" 
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-primary hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-all">
                                    <i data-lucide="sliders" class="h-3.5 w-3.5"></i>
                                    <span>Konfigurasi</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="shield-alert" class="h-8 w-8 text-gray-300"></i>
                                    <p class="text-sm">Tidak ada data permission ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($data->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50/50">
                {{ $data->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
