@extends('layouts.app')

@section('title', 'Master Kelas & Workshop - ' . config('app.name'))
@section('page_title', 'Master Kelas & Workshop')

@push('styles')
<style>
    /* DataTables Custom Polish */
    #kelas-table_wrapper .dataTables_length {
        font-size: 0.75rem;
        color: #4B5563;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    #kelas-table_wrapper .dataTables_length select {
        padding: 0.35rem 2rem 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
        background-color: #F9FAFB;
        outline: none;
    }
    #kelas-table_wrapper .dataTables_length select:focus {
        border-color: #1768B5;
        background-color: #FFFFFF;
    }
    #kelas-table_wrapper .dataTables_filter {
        display: none; /* We use custom search input with debounce */
    }
    #kelas-table_wrapper .dataTables_info {
        font-size: 0.75rem;
        color: #6B7280;
        font-weight: 500;
        padding-top: 1rem;
    }
    #kelas-table_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.25rem;
    }
    #kelas-table_wrapper .dataTables_paginate .paginate_button {
        padding: 0.35rem 0.75rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        border-radius: 0.625rem !important;
        border: 1px solid #E5E7EB !important;
        background: #FFFFFF !important;
        color: #374151 !important;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    #kelas-table_wrapper .dataTables_paginate .paginate_button:hover {
        background: #F3F4F6 !important;
        color: #111827 !important;
        border-color: #D1D5DB !important;
    }
    #kelas-table_wrapper .dataTables_paginate .paginate_button.current,
    #kelas-table_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #1768B5 !important;
        color: #FFFFFF !important;
        border-color: #1768B5 !important;
    }
    #kelas-table_wrapper .dataTables_paginate .paginate_button.disabled,
    #kelas-table_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.5;
        cursor: not-allowed;
        background: #F9FAFB !important;
        color: #9CA3AF !important;
        border-color: #E5E7EB !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Header Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold mb-1.5 border border-purple-100">
                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                Katalog Master Kelas &amp; Workshop Riset
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Katalog &amp; Manajemen Kelas Riset</h1>
            <p class="text-xs text-gray-500">Kelola daftar bootcamp riset, instruktur pengampu, kuota pendaftaran, foto thumbnail, dan jadwal pelaksanaan.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('pelatihan.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-[#1768B5] bg-sky-50 hover:bg-sky-100 border border-sky-200 transition">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Halaman Katalog Publik
            </a>
            <a href="{{ route('master.kelas.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Kelas Baru</span>
            </a>
        </div>
    </div>

    <!-- Summary Metric Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat: Total Kelas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                <i data-lucide="presentation" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Total Kelas</span>
                <span class="text-xl font-black text-gray-900">{{ number_format($stats['total'] ?? 0) }}</span>
                <span class="text-[10px] text-gray-400 block">Pelatihan &amp; bootcamp</span>
            </div>
        </div>

        <!-- Stat: Pendaftaran Dibuka -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <i data-lucide="play-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Pendaftaran Dibuka</span>
                <span class="text-xl font-black text-emerald-600">{{ number_format($stats['dibuka'] ?? 0) }}</span>
                <span class="text-[10px] text-emerald-600 font-semibold block">Siap dipesan mahasiswa</span>
            </div>
        </div>

        <!-- Stat: Sedang Berlangsung -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Sedang Berlangsung</span>
                <span class="text-xl font-black text-[#1768B5]">{{ number_format($stats['berlangsung'] ?? 0) }}</span>
                <span class="text-[10px] text-sky-600 font-semibold block">Sesi materi aktif</span>
            </div>
        </div>

        <!-- Stat: Selesai & Ditutup -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center shrink-0 border border-slate-200">
                <i data-lucide="archive" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Selesai / Ditutup</span>
                <span class="text-xl font-black text-gray-700">{{ number_format(($stats['selesai'] ?? 0) + ($stats['ditutup'] ?? 0)) }}</span>
                <span class="text-[10px] text-gray-400 block">{{ $stats['selesai'] ?? 0 }} selesai &bull; {{ $stats['ditutup'] ?? 0 }} ditutup</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- Tab View Switcher (Tabel vs Katalog Visual) -->
    <div class="flex border-b border-gray-200">
        <button id="tab-table-btn" onclick="switchTab('table')" class="px-5 py-3 text-xs font-bold border-b-2 border-[#1768B5] text-[#1768B5] flex items-center gap-2 transition">
            <i data-lucide="table" class="w-4 h-4"></i>
            <span>Tabel Kelola Kelas</span>
        </button>
        <button id="tab-showcase-btn" onclick="switchTab('showcase')" class="px-5 py-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2 transition">
            <i data-lucide="layout-grid" class="w-4 h-4"></i>
            <span>Etalase Visual Workshop (Preview)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">{{ count($showcaseKelas ?? []) }}</span>
        </button>
    </div>

    <!-- VIEW 1: DATA TABLE VIEW -->
    <div id="view-table" class="space-y-4">
        <!-- Filter Card -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-center">
                <!-- Search Input with icon -->
                <div class="md:col-span-2 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" id="custom-search" placeholder="Cari nama kelas, mentor, kode kelas, kategori..." class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5] transition">
                </div>

                <!-- Filter Level -->
                <div class="relative">
                    <select id="filter-level" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 bg-gray-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5] transition font-medium text-gray-700">
                        <option value="">Semua Tingkat (Level)</option>
                        <option value="Pemula">Pemula</option>
                        <option value="Intermediate">Intermediate / Menengah</option>
                        <option value="Advanced">Advanced / Mahir</option>
                        <option value="Semua Level">Semua Level</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="flex items-center gap-2">
                    <select id="filter-status" class="w-full text-xs rounded-xl border border-gray-200 p-2.5 bg-gray-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5] transition font-medium text-gray-700">
                        <option value="">Semua Status</option>
                        <option value="Dibuka">Dibuka</option>
                        <option value="Berlangsung">Berlangsung</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Ditutup">Ditutup</option>
                    </select>
                    <button type="button" onclick="refreshTable()" class="p-2.5 rounded-xl border border-gray-200 hover:bg-gray-100 text-gray-500 transition shrink-0" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- DataTable Container -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden p-5">
            <div class="overflow-x-auto">
                <table id="kelas-table" class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50/75 text-[11px] uppercase tracking-wider text-gray-500 font-bold border-y border-gray-200">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 min-w-[280px]">Kelas &amp; Instruktur</th>
                            <th class="py-3.5 px-4 min-w-[140px]">Tingkat &amp; Kategori</th>
                            <th class="py-3.5 px-4 min-w-[130px]">Biaya &amp; Kuota</th>
                            <th class="py-3.5 px-4 min-w-[160px]">Jadwal &amp; Tipe</th>
                            <th class="py-3.5 px-4 text-center min-w-[110px]">Status</th>
                            <th class="py-3.5 px-4 text-right min-w-[160px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- VIEW 2: SHOWCASE / CARD GRID VIEW -->
    <div id="view-showcase" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($showcaseKelas as $item)
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-lg transition duration-200 group">
                <div>
                    <!-- Thumbnail Header -->
                    <div class="h-44 w-full bg-gray-100 relative overflow-hidden shrink-0">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-950/80 via-transparent to-transparent"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#102654] via-[#1768B5] to-[#2B8BD1] flex items-center justify-between p-5 relative overflow-hidden">
                                <i data-lucide="award" class="w-24 h-24 text-white/10 absolute -right-3 -bottom-3"></i>
                                <div class="z-10">
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-white/20 text-white backdrop-blur-xs">
                                        {{ $item->tipe_kelas }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Top Badges -->
                        <div class="absolute top-3 left-3 flex items-center gap-1.5 z-10">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-black/60 text-white backdrop-blur-xs">
                                {{ $item->level ?? 'Semua Level' }}
                            </span>
                        </div>

                        @if($item->badge)
                            <div class="absolute top-3 right-3 z-10">
                                <span class="bg-gradient-to-l from-amber-500 to-amber-400 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
                                    ⭐ {{ $item->badge }}
                                </span>
                            </div>
                        @endif

                        <!-- Bottom Tag in Banner -->
                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white z-10">
                            <span class="text-[11px] font-mono font-bold bg-black/40 px-2 py-0.5 rounded backdrop-blur-xs">
                                {{ $item->kode_kelas }}
                            </span>
                            @if($item->status === 'Dibuka')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Dibuka
                                </span>
                            @elseif($item->status === 'Berlangsung')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500 text-white">
                                    Berlangsung
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-800 text-gray-200">
                                    {{ $item->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 space-y-3">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100">{{ $item->kategori ?? 'Riset Umum' }}</span>
                            <span>&bull;</span>
                            <span class="truncate">{{ $item->tipe_kelas }}</span>
                        </div>

                        <h3 class="font-black text-gray-900 text-sm leading-snug line-clamp-2 group-hover:text-[#1768B5] transition">
                            <a href="{{ route('master.kelas.show', $item->id) }}">{{ $item->judul }}</a>
                        </h3>

                        <div class="flex items-center gap-2 text-xs text-gray-600 pt-1">
                            <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="font-medium truncate">
                                {{ $item->mentor ? $item->mentor->nama_lengkap : 'Mentor belum ditentukan' }}
                            </span>
                        </div>

                        @if($item->silabus_singkat)
                            <p class="text-[11px] text-gray-500 line-clamp-2 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                <i data-lucide="check" class="w-3 h-3 text-emerald-600 inline mr-1"></i>
                                {{ $item->silabus_singkat }}
                            </p>
                        @endif

                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-gray-400 block uppercase font-bold">Biaya Pendaftaran</span>
                                <span class="text-sm font-black font-mono text-emerald-700">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-gray-400 block uppercase font-bold">Kuota Maksimal</span>
                                <span class="text-xs font-bold text-gray-700">{{ $item->kuota_maksimal }} peserta</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="px-5 py-3.5 bg-gray-50/75 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('master.kelas.show', $item->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#1768B5] hover:underline">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        Lihat Detail
                    </a>
                    <a href="{{ route('master.kelas.edit', $item->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:underline">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        Edit Kelas
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-3 bg-white p-12 rounded-2xl border border-gray-200 text-center">
                <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <h4 class="font-bold text-gray-700 text-sm">Belum ada kelas yang terdaftar</h4>
                <p class="text-xs text-gray-400 mt-1 mb-4">Mulai dengan menambahkan kelas &amp; workshop baru melalui tombol di atas.</p>
                <a href="{{ route('master.kelas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5]">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Kelas Baru
                </a>
            </div>
            @endforelse
        </div>
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
            language: {
                search: "",
                searchPlaceholder: "Cari data...",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data kelas",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(disaring dari _MAX_ total kelas)",
                zeroRecords: "Tidak ada data kelas yang cocok dengan pencarian",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut &rarr;",
                    previous: "&larr; Kembali"
                }
            },
            drawCallback: function() {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });

        // Custom search with debounce
        var searchTimer;
        $('#custom-search').on('keyup', function() {
            clearTimeout(searchTimer);
            var val = this.value;
            searchTimer = setTimeout(function() {
                table.search(val).draw();
            }, 300);
        });

        $('#filter-level, #filter-status').on('change', function() {
            table.draw();
        });
    });

    function refreshTable() {
        $('#custom-search').val('');
        $('#filter-level').val('');
        $('#filter-status').val('');
        table.search('').draw();
    }

    function switchTab(mode) {
        if (mode === 'table') {
            $('#view-table').removeClass('hidden');
            $('#view-showcase').addClass('hidden');
            $('#tab-table-btn').addClass('border-[#1768B5] text-[#1768B5]').removeClass('border-transparent text-gray-500');
            $('#tab-showcase-btn').removeClass('border-[#1768B5] text-[#1768B5]').addClass('border-transparent text-gray-500');
            table.columns.adjust().draw();
        } else {
            $('#view-table').addClass('hidden');
            $('#view-showcase').removeClass('hidden');
            $('#tab-showcase-btn').addClass('border-[#1768B5] text-[#1768B5]').removeClass('border-transparent text-gray-500');
            $('#tab-table-btn').removeClass('border-[#1768B5] text-[#1768B5]').addClass('border-transparent text-gray-500');
        }
        if (window.lucide) { lucide.createIcons(); }
    }

    function ubahStatusKelas(id, newStatus, title) {
        let actionWord = 'mengubah status';
        let confirmColor = '#1768B5';
        let actionDesc = 'Status kelas akan diubah menjadi ' + newStatus + '.';

        if (newStatus === 'Ditutup') {
            actionWord = 'menutup kelas';
            confirmColor = '#e11d48';
            actionDesc = 'Pendaftaran untuk kelas ini akan ditutup dan tidak dapat dipesan oleh mahasiswa.';
        } else if (newStatus === 'Selesai') {
            actionWord = 'menselesaikan kelas';
            confirmColor = '#4f46e5';
            actionDesc = 'Kelas akan ditandai selesai. Pelaksanaan workshop dianggap telah rampung.';
        } else if (newStatus === 'Dibuka') {
            actionWord = 'membuka pendaftaran kelas';
            confirmColor = '#059669';
            actionDesc = 'Pendaftaran kelas ini akan dibuka kembali untuk mahasiswa.';
        }

        Swal.fire({
            title: 'Konfirmasi ' + actionWord.charAt(0).toUpperCase() + actionWord.slice(1) + '?',
            text: 'Apakah Anda yakin ingin ' + actionWord + ' "' + title + '"? ' + actionDesc,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            confirmButtonText: 'Ya, ' + (newStatus === 'Ditutup' ? 'Tutup Kelas' : (newStatus === 'Selesai' ? 'Selesaikan Kelas' : 'Buka Kelas')),
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/kelas") }}/' + id + '/update-status',
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        table.draw(false);
                    },
                    error: function(err) {
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Gagal memperbarui status kelas.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    }
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
