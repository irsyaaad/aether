@php
    $isEdit = isset($feeOmset);
@endphp

@extends('layouts.app')

@section('title', ($isEdit ? 'Edit Setting Fee Omset' : 'Tambah Setting Fee Omset') . ' - ' . config('app.name'))
@section('page_title', $isEdit ? 'Edit Setting Fee Omset' : 'Tambah Setting Fee Omset')

@section('toolbar_actions')
    <a href="{{ route('setting-fee-omset.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-xs hover:bg-gray-50 transition-colors">
        <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i> Kembali
    </a>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-xs">
            <div class="flex items-center gap-2 font-medium mb-1">
                <i data-lucide="alert-circle" class="h-5 w-5 text-red-600 shrink-0"></i>
                <span>Terdapat beberapa kesalahan pengisian form:</span>
            </div>
            <ul class="list-disc ps-7 space-y-0.5 text-xs text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form 
        action="{{ $isEdit ? route('setting-fee-omset.update', $feeOmset->id) : route('setting-fee-omset.store') }}" 
        method="POST" 
        id="form-fee-omset"
    >
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="rounded-xl border border-gray-200 bg-white shadow-xs overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $isEdit ? 'Ubah Setting Fee Omset Platform' : 'Informasi Setting Fee Platform Baru' }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Tarif fee ini akan otomatis digunakan untuk menghitung potongan nominal omset menjadi <strong>Omset Bersih</strong> pada menu Input Omset.
                </p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Platform -->
                    <div>
                        <label for="platform" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Platform E-Commerce <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="platform" 
                            id="platform" 
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-gray-900"
                            required
                        >
                            <option value="">-- Pilih Platform --</option>
                            @foreach($platforms as $key => $label)
                                <option value="{{ $key }}" {{ old('platform', $feeOmset->platform ?? '') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[11px] text-gray-400 mt-1 block">Platform penjualan e-commerce atau marketplace terkait.</span>
                    </div>

                    <!-- Prosentase Fee -->
                    <div>
                        <label for="fee_percentage" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Prosentase Fee (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="fee_percentage" 
                                id="fee_percentage" 
                                step="0.01" 
                                min="0" 
                                max="100" 
                                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 pr-10 text-sm font-mono font-medium shadow-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-gray-900" 
                                placeholder="Contoh: 5.50" 
                                value="{{ old('fee_percentage', isset($feeOmset) ? $feeOmset->fee_percentage : '') }}" 
                                required
                            >
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 pointer-events-none">%</span>
                        </div>
                        <span class="text-[11px] text-gray-400 mt-1 block">Persentase potongan fee yang dikenakan oleh platform (0 s/d 100).</span>
                    </div>
                </div>

                <!-- Keterangan / Catatan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Keterangan / Catatan (Opsional)
                    </label>
                    <textarea 
                        name="keterangan" 
                        id="keterangan" 
                        rows="3" 
                        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-gray-900" 
                        placeholder="Contoh: Fee admin marketplace gratis ongkir ekstra + fee transaksi reguler"
                    >{{ old('keterangan', $feeOmset->keterangan ?? '') }}</textarea>
                </div>

                <!-- Status Aktif -->
                <div class="pt-2 border-t border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1" 
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" 
                            {{ old('is_active', $feeOmset->is_active ?? true) ? 'checked' : '' }}
                        >
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-800">Status Aktif</span>
                            <span class="text-xs text-gray-400">Jika dinonaktifkan, platform ini tidak akan mengenakan potongan fee otomatis.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Footer Tombol Aksi -->
            <div class="p-6 bg-gray-50/70 border-t border-gray-200 flex items-center justify-end gap-3">
                <a 
                    href="{{ route('setting-fee-omset.index') }}" 
                    class="px-5 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-2 rounded-md bg-primary text-sm font-medium text-white shadow-xs hover:bg-primary/90 transition-colors"
                >
                    <i data-lucide="save" class="h-4 w-4"></i>
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Setting Fee' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
