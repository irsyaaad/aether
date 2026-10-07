@php
    $isEdit = isset($layanan);
@endphp

@extends('layouts.app')

@section('title', ($isEdit ? 'Edit Layanan' : 'Tambah Layanan') . ' - ' . config('app.name'))
@section('page_title', $isEdit ? 'Edit Layanan' : 'Tambah Layanan')

@section('breadcrumb')
    <a class="text-secondary-foreground hover:text-primary" href="{{ url('/') }}">Home</a>
    <span class="text-muted-foreground text-sm">/</span>
    <a class="text-secondary-foreground hover:text-primary" href="{{ route('layanan.index') }}">Layanan</a>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-mono">{{ $isEdit ? 'Edit' : 'Buat Baru' }}</span>
@endsection

@section('toolbar_actions')
    <a class="kt-btn kt-btn-outline" href="{{ route('layanan.index') }}">
        <i class="ki-filled ki-arrow-left"></i> Kembali
    </a>
@endsection

@push('styles')
<style>
    .kt-card { border-radius: 10px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.04); border: 1px solid var(--border); }
    .kt-card-header { padding: 16px 24px !important; border-bottom: 1px solid var(--border); background: var(--background); }
    .kt-input { transition: border-color 0.2s ease; font-size: 0.875rem; border: 1px solid var(--border); height: 38px; padding: 0 12px; border-radius: 6px; background: var(--background); color: var(--foreground); }
    .kt-input:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 3px color-mix(in oklab, var(--primary) 10%, transparent) !important; outline: none !important; }
    .kt-table td { border-bottom: 1px solid color-mix(in oklab, var(--border) 40%, transparent); vertical-align: middle; }
    .kt-table tr:last-child td { border-bottom: none; }
    .kt-table td:first-child { font-weight: 500; color: var(--secondary-foreground); font-size: 0.8125rem; }
</style>
@endpush

@section('content')
@if (count($errors) > 0)
    <div class="kt-alert kt-alert-destructive mb-6">
        <div class="kt-alert-content">
            <div class="kt-alert-description">
                <ul class="list-disc ps-4 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ $isEdit ? route('layanan.update', $layanan->kode_layanan) : route('layanan.store') }}" method="POST">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="kt-container-fixed grid grid-cols-1 gap-5 lg:gap-7.5">
        <div class="flex flex-col gap-5 lg:gap-7.5">
            <div class="kt-card min-w-full">
                <div class="kt-card-header">
                    <h3 class="kt-card-title text-base font-bold">Informasi Layanan</h3>
                </div>
                <div class="kt-card-table kt-scrollable-x-auto pb-3">
                    <table class="kt-table align-middle text-sm text-muted-foreground w-full">
                        <tbody>
                            <tr>
                                <td class="w-40 text-secondary-foreground font-normal py-2.5 px-4">Kode Layanan *</td>
                                <td class="text-foreground font-normal py-2.5 px-4">
                                    <input type="text" name="kode_layanan" class="kt-input bg-muted/20 w-full" placeholder="Contoh: REG, EKS, ONS"
                                        value="{{ old('kode_layanan', $layanan->kode_layanan ?? '') }}" {{ $isEdit ? 'readonly' : 'required' }} maxlength="20">
                                </td>
                            </tr>
                            <tr>
                                <td class="w-40 text-secondary-foreground font-normal py-2.5 px-4">Nama Layanan *</td>
                                <td class="text-foreground font-normal py-2.5 px-4">
                                    <input type="text" name="nama_layanan" class="kt-input w-full" placeholder="Masukkan nama layanan"
                                        value="{{ old('nama_layanan', $layanan->nama_layanan ?? '') }}" required>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('layanan.index') }}" class="kt-btn kt-btn-outline grow md:grow-0 justify-center">Batal</a>
                <button type="submit" class="kt-btn kt-btn-primary grow md:grow-0 justify-center">
                    <i class="ki-filled ki-check text-sm"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
