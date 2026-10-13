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
            <p class="text-xs text-gray-500">Kelola daftar penawaran paket skripsi, harga/tarif per paket, jumlah pertemuan, serta fasilitas yang didapatkan mahasiswa.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('bimbingan.programs') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-[#1768B5] bg-sky-50 hover:bg-sky-100 border border-sky-200 transition">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Lihat Halaman Klien
            </a>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Program Baru
            </button>
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
                        <th class="py-3 px-4">Nama Program &amp; Cakupan</th>
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
            <div class="bg-white rounded-2xl border {{ $program->is_populer ? 'border-amber-400 ring-2 ring-amber-400/20 shadow-md' : 'border-gray-200 shadow-xs' }} p-6 flex flex-col justify-between transition hover:shadow-lg relative overflow-hidden">
                @if($program->is_populer)
                <div class="absolute top-0 right-0">
                    <div class="bg-gradient-to-l from-amber-500 to-amber-400 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-xs">
                        ⭐ {{ $program->badge ?: 'Paling Populer' }}
                    </div>
                </div>
                @elseif($program->badge)
                <div class="absolute top-0 right-0">
                    <div class="bg-sky-500 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-xs">
                        {{ $program->badge }}
                    </div>
                </div>
                @endif

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

                    <p class="text-xs text-gray-500 leading-relaxed mb-4">
                        {{ $program->deskripsi }}
                    </p>

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
                    <button onclick="editProgram({{ $program->id }})" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition text-center">
                        Edit Program
                    </button>
                    <a href="{{ route('bimbingan.create', ['program_id' => $program->id]) }}" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-bold transition text-center shadow-xs">
                        Pilih Program Ini →
                    </a>
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

<!-- Modal Form Create / Edit -->
<div id="program-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all my-8">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h3 id="modal-title" class="text-base font-black text-gray-900">Tambah Program Bimbingan</h3>
                <p class="text-xs text-gray-500">Konfigurasi paket bimbingan skripsi, tarif, dan fasilitas untuk ditawarkan ke klien.</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="program-form" onsubmit="saveProgram(event)" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            <input type="hidden" id="program-id" name="id">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kode Program *</label>
                    <input type="text" id="kode_program" name="kode_program" required placeholder="Contoh: PRG-SKR-FULL" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kategori Program *</label>
                    <select id="kategori" name="kategori" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="Bimbingan Skripsi">Bimbingan Skripsi Full</option>
                        <option value="Bimbingan Proposal">Bimbingan Proposal (Bab 1-3)</option>
                        <option value="Bimbingan Hasil">Bimbingan Hasil &amp; Bab 4-5</option>
                        <option value="Simulasi Sidang">Simulasi &amp; Defense Sidang</option>
                        <option value="Bimbingan Tesis">Bimbingan Tesis (S2)</option>
                        <option value="Olah Data">Olah Data Riset</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Paket Program *</label>
                <input type="text" id="nama_program" name="nama_program" required placeholder="Contoh: Paket Bimbingan Skripsi Komprehensif (Bab 1 - 5)" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Jenjang Pendidikan</label>
                    <select id="jenjang" name="jenjang" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="S1 (Sarjana)">S1 (Sarjana)</option>
                        <option value="S2 (Magister)">S2 (Magister)</option>
                        <option value="S3 (Doktor)">S3 (Doktor)</option>
                        <option value="S1 / S2">S1 / S2</option>
                        <option value="Semua Jenjang">Semua Jenjang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Target Bab / Cakupan</label>
                    <input type="text" id="target_bab" name="target_bab" placeholder="Contoh: Full Bab 1 s/d Bab 5 &amp; Sidang" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Biaya / Harga (Rp) *</label>
                    <input type="number" id="biaya_standar" name="biaya_standar" required placeholder="1500000" min="0" step="10000" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Jumlah Sesi</label>
                    <input type="number" id="jumlah_sesi" name="jumlah_sesi" value="4" min="1" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Durasi per Sesi (Menit)</label>
                    <input type="number" id="durasi_sesi" name="durasi_sesi" value="90" min="15" step="15" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Badge / Label Penawaran</label>
                <input type="text" id="badge" name="badge" placeholder="Contoh: Paling Populer, Best Value, Rekomendasi, Hemat" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Fasilitas yang Didapat (Pisahkan tiap baris)</label>
                <textarea id="fasilitas" name="fasilitas" rows="4" placeholder="8x Sesi Bimbingan Intensif 1-on-1&#10;Pendampingan Bab 1 s.d. Bab 5 hingga ACC Dosen&#10;Free pengecekan Turnitin&#10;Simulasi tanya-jawab ujian sidang" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
                <p class="text-[11px] text-gray-400 mt-1">Setiap baris akan ditampilkan sebagai poin fasilitas dengan checklist hijau pada penawaran klien.</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Deskripsi Singkat Program</label>
                <textarea id="deskripsi" name="deskripsi" rows="2" placeholder="Penjelasan umum mengenai paket bimbingan dan hasil yang dicapai..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
            </div>

            <div class="flex items-center gap-6 pt-1">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_populer" name="is_populer" value="1" class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    <label for="is_populer" class="text-xs font-medium text-gray-700 cursor-pointer">Tandai sebagai Paket Populer (Highlight)</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <label for="is_active" class="text-xs font-medium text-gray-700 cursor-pointer">Status Aktif &amp; Ditampilkan</label>
                </div>
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

    function openCreateModal() {
        $('#program-form')[0].reset();
        $('#program-id').val('');
        $('#modal-title').text('Tambah Program Bimbingan');
        $('#is_active').prop('checked', true);
        $('#is_populer').prop('checked', false);
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
            $('#target_bab').val(data.target_bab);
            $('#biaya_standar').val(Math.round(data.biaya_standar));
            $('#jumlah_sesi').val(data.jumlah_sesi || 1);
            $('#durasi_sesi').val(data.durasi_sesi || 60);
            $('#badge').val(data.badge);
            $('#fasilitas').val(data.fasilitas);
            $('#deskripsi').val(data.deskripsi);
            $('#is_populer').prop('checked', data.is_populer == 1);
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
            payload['is_active'] = 0;
        }
        if (!$('#is_populer').is(':checked')) {
            payload['is_populer'] = 0;
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
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    alert(res.message);
                    location.reload();
                }
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
        if (!confirm('Apakah Anda yakin ingin menghapus program bimbingan ini?')) return;

        $.ajax({
            url: "{{ url('master/programs') }}/" + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                programTable.ajax.reload();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Terhapus', text: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert('Gagal menghapus program.');
            }
        });
    }
</script>
@endpush
