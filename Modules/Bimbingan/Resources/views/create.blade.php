@extends('layouts.app')

@section('title', 'Ajukan Sesi Bimbingan - Aether')
@section('page_title', 'Form Pengajuan Bimbingan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-gray-200 shadow-xs p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-1 border border-sky-100">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    Bimbingan 1-on-1 Intensif
                </div>
                <h2 class="text-xl font-black text-gray-900">Form Pengajuan Sesi Bimbingan Skripsi</h2>
                <p class="text-xs text-gray-500 mt-1">Pilih paket program bimbingan sesuai kebutuhan Anda dan tentukan jadwal preferensi sesi.</p>
            </div>
            <a href="{{ route('bimbingan.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800 flex items-center gap-1">
                <span>← Kembali</span>
            </a>
        </div>

        <form action="{{ route('bimbingan.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. PILIH PROGRAM MASTER (FITUR UTAMA) -->
            <div class="bg-gradient-to-br from-sky-50/50 to-indigo-50/30 rounded-2xl p-5 border border-sky-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#1768B5]">1. Pilih Paket Program Bimbingan *</label>
                        <p class="text-xs text-gray-500 mt-0.5">Tersedia berbagai pilihan paket skripsi dan bimbingan terpadu.</p>
                    </div>
                    <a href="{{ route('bimbingan.programs') }}" target="_blank" class="text-xs font-bold text-[#1768B5] hover:underline flex items-center gap-1">
                        <span>Lihat Rincian di Katalog</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <select id="program_id" name="program_id" onchange="updateProgramDetails()" class="w-full px-4 py-3 rounded-xl border border-sky-200 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1768B5] bg-white shadow-xs">
                    <option value="">-- Pilih Program Bimbingan Sesuai Kebutuhan --</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" 
                            data-nama="{{ $prog->nama_program }}"
                            data-biaya="{{ $prog->formatted_biaya }}"
                            data-rawbiaya="{{ $prog->biaya_standar }}"
                            data-sesi="{{ $prog->jumlah_sesi ?: 1 }}"
                            data-durasi="{{ $prog->durasi_sesi ?: 60 }}"
                            data-target="{{ $prog->target_bab ?: 'Bimbingan Skripsi' }}"
                            data-kategori="{{ $prog->kategori }}"
                            data-badge="{{ $prog->badge }}"
                            data-deskripsi="{{ $prog->deskripsi }}"
                            data-fasilitas="{{ json_encode($prog->fasilitas_array) }}"
                            {{ (old('program_id', $selectedProgram ? $selectedProgram->id : '') == $prog->id) ? 'selected' : '' }}>
                            {{ $prog->nama_program }} — {{ $prog->formatted_biaya }} ({{ $prog->jumlah_sesi ?: 1 }} Sesi)
                        </option>
                    @endforeach
                </select>

                <!-- Dynamic Preview Card for Selected Program -->
                <div id="program-preview-box" class="bg-white rounded-xl p-4 border border-sky-200 shadow-xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span id="preview-kategori" class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-sky-100 text-[#1768B5]">Bimbingan</span>
                                <span id="preview-badge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800 hidden">Populer</span>
                            </div>
                            <h4 id="preview-nama" class="text-sm font-black text-gray-900 mt-1">Nama Program</h4>
                            <p id="preview-target" class="text-xs text-sky-700 font-medium">Cakupan Bab</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-gray-400 font-semibold block">Total Biaya Paket:</span>
                            <span id="preview-biaya" class="text-xl font-black text-[#1768B5]">Rp 0</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">
                        <span class="inline-flex items-center gap-1 font-medium">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#1768B5]"></i>
                            <span id="preview-sesi">0 Sesi Pertemuan</span>
                        </span>
                        <span class="inline-flex items-center gap-1 font-medium">
                            <i data-lucide="clock" class="w-4 h-4 text-[#1768B5]"></i>
                            <span id="preview-durasi">60 Menit per Sesi</span>
                        </span>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Fasilitas Termasuk:</span>
                        <ul id="preview-fasilitas" class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-xs text-gray-700">
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 2. DETAIL TUGAS AKHIR / SKRIPSI -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b border-gray-100 pb-2">
                    2. Rincian Topik &amp; Tugas Akhir
                </h3>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Judul Skripsi / Tesis / Riset *</label>
                    <input type="text" name="judul_penelitian" value="{{ old('judul_penelitian') }}" required placeholder="Contoh: Pengaruh E-WOM dan Brand Trust Terhadap Keputusan Pembelian..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Fokus Bab yang Dibahas *</label>
                        <select name="topik_bab" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                            <option value="Full Bab 1 s/d Bab 5">Full Bab 1 s/d Bab 5 (Paket Komprehensif)</option>
                            <option value="Bab 1 - Latar Belakang & Rumusan Masalah">Bab 1 - Latar Belakang &amp; Rumusan Masalah</option>
                            <option value="Bab 2 - Kajian Pustaka & Hipotesis">Bab 2 - Kajian Pustaka &amp; Hipotesis</option>
                            <option value="Bab 3 - Metodologi Riset & Instrumen">Bab 3 - Metodologi Riset &amp; Instrumen</option>
                            <option value="Bab 4 - Hasil Olah Data & Pembahasan" selected>Bab 4 - Hasil Olah Data &amp; Pembahasan</option>
                            <option value="Bab 5 - Kesimpulan & Rekomendasi">Bab 5 - Kesimpulan &amp; Rekomendasi</option>
                            <option value="Simulasi Tanya Jawab Sidang">Simulasi Tanya Jawab Ujian Sidang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Pilih Mentor Spesialis *</label>
                        <select name="mentor" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                            @foreach($mentors as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tanggal &amp; Waktu Preferensi *</label>
                        <input type="datetime-local" name="tanggal_preferensi" value="{{ old('tanggal_preferensi', now()->addDays(2)->format('Y-m-d\T14:00')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Media Pertemuan *</label>
                        <select name="media" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                            <option value="Google Meet">Google Meet (Online Video &amp; Screen Share)</option>
                            <option value="Zoom Meeting">Zoom Meeting</option>
                            <option value="Offline Studio Aether">Tatap Muka Offline (Studio Riset Aether)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Catatan Khusus / Masukan dari Dosen Pembimbing</label>
                    <textarea name="catatan" rows="3" placeholder="Tuliskan catatan revisi dari dosen atau kendala utama analisis yang dihadapi..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-gray-100">
                <a href="{{ route('bimbingan.programs') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100">
                    Batal &amp; Lihat Katalog
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-black shadow-md flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Kirim Pengajuan Bimbingan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateProgramDetails() {
        const select = document.getElementById('program_id');
        const selected = select.options[select.selectedIndex];
        const previewBox = document.getElementById('program-preview-box');

        if (!selected || !selected.value) {
            previewBox.classList.add('hidden');
            return;
        }

        previewBox.classList.remove('hidden');

        document.getElementById('preview-nama').textContent = selected.getAttribute('data-nama') || '-';
        document.getElementById('preview-biaya').textContent = selected.getAttribute('data-biaya') || 'Rp 0';
        document.getElementById('preview-kategori').textContent = selected.getAttribute('data-kategori') || 'Bimbingan';
        document.getElementById('preview-target').textContent = 'Fokus: ' + (selected.getAttribute('data-target') || 'Bimbingan Skripsi');
        document.getElementById('preview-sesi').textContent = (selected.getAttribute('data-sesi') || 1) + 'x Sesi Pertemuan';
        document.getElementById('preview-durasi').textContent = '@' + (selected.getAttribute('data-durasi') || 60) + ' Menit/Sesi';

        const badge = selected.getAttribute('data-badge');
        const badgeEl = document.getElementById('preview-badge');
        if (badge) {
            badgeEl.textContent = '⭐ ' + badge;
            badgeEl.classList.remove('hidden');
        } else {
            badgeEl.classList.add('hidden');
        }

        const rawFasilitas = selected.getAttribute('data-fasilitas');
        const fasList = document.getElementById('preview-fasilitas');
        fasList.innerHTML = '';
        try {
            const arr = JSON.parse(rawFasilitas);
            if (arr && arr.length > 0) {
                arr.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center gap-1.5';
                    li.innerHTML = '<svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>' + item + '</span>';
                    fasList.appendChild(li);
                });
            } else {
                fasList.innerHTML = '<li class="text-gray-400 italic">Konsultasi 1-on-1 intensif</li>';
            }
        } catch(e) {
            fasList.innerHTML = '<li class="text-gray-400 italic">Konsultasi 1-on-1 intensif</li>';
        }

        if (window.lucide) {
            lucide.createIcons();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateProgramDetails();
    });
</script>
@endpush
