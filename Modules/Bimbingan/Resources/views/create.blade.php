@extends('layouts.app')

@section('title', 'Ajukan Sesi Bimbingan - Aether')
@section('page_title', 'Form Pengajuan Bimbingan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6">
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black text-gray-900">Form Pengajuan Sesi Bimbingan</h2>
                <p class="text-xs text-gray-500 mt-1">Lengkapi rincian tugas akhir agar mentor dapat menyiapkan telaah materi sebelum sesi dimulai.</p>
            </div>
            <a href="{{ route('bimbingan.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">
                ← Kembali
            </a>
        </div>

        <form action="{{ route('bimbingan.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Judul Skripsi / Tesis / Riset *</label>
                <input type="text" name="judul_penelitian" required placeholder="Contoh: Pengaruh Kepemimpinan Transformasional Terhadap Kinerja Guru..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Bagian / Bab yang Ingin Dibahas *</label>
                    <select name="topik_bab" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                        <option value="Bab 1 - Latar Belakang & Rumusan Masalah">Bab 1 - Latar Belakang &amp; Rumusan Masalah</option>
                        <option value="Bab 2 - Kajian Pustaka & Hipotesis">Bab 2 - Kajian Pustaka &amp; Hipotesis</option>
                        <option value="Bab 3 - Metodologi Riset & Instrumen">Bab 3 - Metodologi Riset &amp; Instrumen</option>
                        <option value="Bab 4 - Hasil Olah Data & Pembahasan" selected>Bab 4 - Hasil Olah Data &amp; Pembahasan</option>
                        <option value="Bab 5 - Kesimpulan & Rekomendasi">Bab 5 - Kesimpulan &amp; Rekomendasi</option>
                        <option value="Simulasi Tanya Jawab Sidang">Simulasi Tanya Jawab Sidang Ujian</option>
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tanggal & Waktu Preferensi *</label>
                    <input type="datetime-local" name="tanggal_preferensi" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Media Pertemuan *</label>
                    <select name="media" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                        <option value="Google Meet">Google Meet (Online Video & Screen Share)</option>
                        <option value="Zoom Meeting">Zoom Meeting</option>
                        <option value="Offline Studio Aether">Tatap Muka Offline (Studio Riset Aether)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Catatan Khusus / Masukan dari Dosen Pembimbing</label>
                <textarea name="catatan" rows="4" placeholder="Tuliskan catatan revisi dari dosen atau kendala utama yang dihadapi..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('bimbingan.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-bold shadow-md">
                    Kirim Pengajuan Bimbingan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
