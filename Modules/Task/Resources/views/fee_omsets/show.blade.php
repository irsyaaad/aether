@extends('layouts.app')

@section('title', 'Detail Setting Fee - ' . config('app.name'))
@section('page_title', 'Detail Setting Fee Omset')

@section('toolbar_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('setting-fee-omset.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-xs hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i> Kembali
        </a>
        <a href="{{ route('setting-fee-omset.edit', $feeOmset->id) }}" class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white shadow-xs hover:bg-primary/90 transition-colors">
            <i data-lucide="edit-3" class="mr-2 h-4 w-4"></i> Edit Setting
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-gray-100">
            <div>
                <span class="text-xs font-medium text-primary uppercase tracking-wider">Setting Fee Platform</span>
                <h2 class="text-2xl font-bold text-gray-900 mt-1 flex items-center gap-3">
                    {{ $feeOmset->platform }}
                    @if($feeOmset->is_active)
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border-emerald-200">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-red-50 text-red-700 border-red-200">
                            Nonaktif
                        </span>
                    @endif
                </h2>
                <div class="flex items-center gap-4 text-xs text-gray-500 mt-2">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="h-3.5 w-3.5 text-gray-400"></i>
                        Dibuat: {{ $feeOmset->created_at->format('d/m/Y H:i') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="clock" class="h-3.5 w-3.5 text-gray-400"></i>
                        Terakhir diubah: {{ $feeOmset->updated_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>

            <div class="rounded-xl bg-primary/5 border border-primary/20 p-4 text-right">
                <span class="text-xs font-medium text-gray-500 block">Prosentase Potongan</span>
                <span class="text-3xl font-extrabold text-primary font-mono mt-0.5 block">
                    {{ number_format($feeOmset->fee_percentage, 2, ',', '.') }}%
                </span>
            </div>
        </div>

        <div class="py-6 space-y-4">
            <div>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block mb-1">Simulasi Pemotongan Omset</span>
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-sm">
                    <p class="text-gray-700">
                        Jika penjualan omset di platform <strong>{{ $feeOmset->platform }}</strong> sebesar <strong>Rp 10.000.000</strong>:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3 pt-3 border-t border-gray-200 text-xs">
                        <div>
                            <span class="text-gray-400 block">Omset Kotor:</span>
                            <span class="font-bold text-gray-800 text-sm">Rp 10.000.000</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Potongan Fee ({{ $feeOmset->fee_percentage }}%):</span>
                            <span class="font-bold text-rose-600 text-sm">
                                Rp {{ number_format(10000000 * ($feeOmset->fee_percentage / 100), 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Omset Bersih:</span>
                            <span class="font-bold text-emerald-600 text-sm">
                                Rp {{ number_format(10000000 * (1 - ($feeOmset->fee_percentage / 100)), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($feeOmset->keterangan)
            <div class="pt-4 border-t border-gray-100">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block mb-1">Keterangan / Catatan</span>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $feeOmset->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
