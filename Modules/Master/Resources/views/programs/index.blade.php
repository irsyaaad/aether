@extends('layouts.app')

@section('title', 'Master Program Bimbingan Skripsi - ' . config('app.name'))
@section('page_title', 'Master Program Bimbingan Skripsi')

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
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-1.5 border border-sky-100">
                <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                Master Program &amp; Paket Bimbingan Skripsi
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Katalog &amp; Penawaran Program Bimbingan</h1>
            <p class="text-xs text-gray-500">Kelola daftar penawaran paket skripsi, banner promosi, harga/tarif per paket, serta fasilitas yang didapatkan mahasiswa.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('bimbingan.programs') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-[#1768B5] bg-sky-50 hover:bg-sky-100 border border-sky-200 transition">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Lihat Halaman Klien
            </a>
            <a href="{{ route('master.programs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Program Baru
            </a>
        </div>
    </div>

    <!-- Tab View Switcher -->
    <div class="flex border-b border-gray-200">
        <button id="tab-table-btn" onclick="switchTab('table')" class="px-5 py-3 text-xs font-bold border-b-2 border-[#1768B5] text-[#1768B5] flex items-center gap-2 transition">
            <i data-lucide="table" class="w-4 h-4"></i>
            Tabel Kelola Program
        </button>
        <button id="tab-showcase-btn" onclick="switchTab('showcase')" class="px-5 py-3 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 flex items-center gap-2 transition">
            <i data-lucide="layout-grid" class="w-4 h-4"></i>
            Katalog Penawaran Klien (Preview Live)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-[#1768B5]">{{ count($showcasePrograms) }}</span>
        </button>
    </div>

    <!-- VIEW 1: DATA TABLE CARD -->
    <div id="view-table" class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
        <div class="overflow-x-auto">
            <table id="programs-table" class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Program &amp; Banner</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Jenjang</th>
                        <th class="py-3 px-4">Biaya / Paket</th>
                        <th class="py-3 px-4">Sesi &amp; Durasi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                </tbody>
            </table>
        </div>
    </div>

    <!-- VIEW 2: SHOWCASE / PRICING CARDS -->
    <div id="view-showcase" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($showcasePrograms as $program)
            <div class="bg-white rounded-2xl border {{ $program->is_populer ? 'border-amber-400 ring-2 ring-amber-400/20 shadow-md' : 'border-gray-200 shadow-xs' }} flex flex-col justify-between transition hover:shadow-lg relative overflow-hidden group">
                <!-- Banner Image or Themed Gradient Header -->
                @if($program->banner_url)
                <div class="h-44 w-full overflow-hidden bg-gray-100 relative shrink-0">
                    <img src="{{ $program->banner_url }}" alt="{{ $program->nama_program }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                    @if($program->is_populer)
                    <div class="absolute top-3 right-3">
                        <div class="bg-gradient-to-l from-amber-500 to-amber-400 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
                            ⭐ {{ $program->badge ?: 'Paling Populer' }}
                        </div>
                    </div>
                    @elseif($program->badge)
                    <div class="absolute top-3 right-3">
                        <div class="bg-sky-500 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
                            {{ $program->badge }}
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="h-32 w-full bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] relative flex items-center justify-between p-5 overflow-hidden shrink-0">
                    <i data-lucide="graduation-cap" class="w-24 h-24 text-white/10 absolute -right-4 -bottom-4"></i>
                    <div class="text-white z-10">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-white/20 text-white border border-white/20">
                            {{ $program->kategori }}
                        </span>
                    </div>
                    @if($program->is_populer)
                    <div class="z-10">
                        <div class="bg-gradient-to-l from-amber-500 to-amber-400 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
                            ⭐ {{ $program->badge ?: 'Paling Populer' }}
                        </div>
                    </div>
                    @elseif($program->badge)
                    <div class="z-10">
                        <div class="bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full border border-white/30">
                            {{ $program->badge }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-[#1768B5] border border-sky-100">
                                {{ $program->kategori }}
                            </span>
                            <span class="text-[11px] font-bold text-gray-400">{{ $program->jenjang }}</span>
                        </div>

                        <h3 class="text-base font-black text-gray-900 leading-snug mb-1">
                            {{ $program->nama_program }}
                        </h3>

                        @if($program->target_bab)
                        <div class="inline-flex items-center gap-1.5 text-xs text-sky-700 font-semibold mb-3">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-sky-600"></i>
                            <span>Fokus: {{ $program->target_bab }}</span>
                        </div>
                        @endif

                        <div class="text-xs text-gray-500 leading-relaxed mb-4 prose prose-xs max-w-none">
                            {!! $program->deskripsi !!}
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-5">
                            <div class="flex items-baseline gap-1">
                                <span class="text-xs text-gray-500 font-semibold">Biaya Investasi:</span>
                            </div>
                            <div class="flex items-baseline gap-1.5 mt-0.5">
                                <span class="text-2xl font-black text-gray-900">{{ $program->formatted_biaya }}</span>
                                <span class="text-xs text-gray-400 font-bold">/ paket</span>
                            </div>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-600 font-medium">
                                <span class="inline-flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-sky-600"></i>
                                    {{ $program->jumlah_sesi ?: 1 }}x Pertemuan
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-sky-600"></i>
                                    {{ $program->durasi_sesi ?: 60 }} Menit/Sesi
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2 mb-6">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Fasilitas yang Didapat:</h4>
                            <ul class="space-y-2 text-xs text-gray-700">
                                @foreach($program->fasilitas_array as $fasilitas)
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                                    <span>{{ $fasilitas }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center gap-2">
                        <a href="{{ route('master.programs.edit', $program->id) }}" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition text-center flex items-center justify-center gap-1.5">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            <span>Edit Program</span>
                        </a>
                        <a href="{{ route('bimbingan.payment', $program->id) }}" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-bold transition text-center shadow-xs">
                            Beli / Ikuti Pelatihan →
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-gray-200">
                <i data-lucide="folder-x" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <h4 class="text-sm font-bold text-gray-700">Belum ada program bimbingan yang aktif</h4>
                <p class="text-xs text-gray-400 mt-1">Tambahkan paket program bimbingan skripsi baru melalui tombol di atas.</p>
            </div>
            @endforelse
        </div>
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
                { data: 'durasi_sesi', name: 'durasi_sesi' },
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

    function switchTab(tab) {
        if (tab === 'table') {
            $('#view-table').removeClass('hidden');
            $('#view-showcase').addClass('hidden');
            $('#tab-table-btn').addClass('border-[#1768B5] text-[#1768B5]').removeClass('border-transparent text-gray-500');
            $('#tab-showcase-btn').removeClass('border-[#1768B5] text-[#1768B5]').addClass('border-transparent text-gray-500');
        } else {
            $('#view-table').addClass('hidden');
            $('#view-showcase').removeClass('hidden');
            $('#tab-showcase-btn').addClass('border-[#1768B5] text-[#1768B5]').removeClass('border-transparent text-gray-500');
            $('#tab-table-btn').removeClass('border-[#1768B5] text-[#1768B5]').addClass('border-transparent text-gray-500');
        }
        if (window.lucide) lucide.createIcons();
    }

    function toggleProgramStatus(id, newStatus) {
        let actionWord = newStatus ? 'mengaktifkan kembali' : 'menonaktifkan';
        if (!confirm('Apakah Anda yakin ingin ' + actionWord + ' program bimbingan ini?')) return;

        $.ajax({
            url: "{{ url('master/programs') }}/" + id + "/toggle-status",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                programTable.ajax.reload();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert('Gagal mengubah status program bimbingan.');
            }
        });
    }
</script>
@endpush
