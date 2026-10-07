@extends('template.root')

@section('title-page')
    Detail Kontrak Karyawan
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Nama Karyawan</label>
            <p>{{ $kontrak->karyawan->nama_lengkap ?? '-' }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Jenis Kontrak</label>
            <p class="text-uppercase">{{ $kontrak->jenis_kontrak }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Tanggal Mulai</label>
            <p>{{ $kontrak->tanggal_mulai }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Tanggal Selesai</label>
            <p>{{ $kontrak->tanggal_selesai }}</p>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label font-weight-bold">File PKWT</label>
            @if ($kontrak->file_pkwt)
                <div><a href="{{ asset('storage/' . $kontrak->file_pkwt) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">Lihat PKWT</a></div>
            @else
                <p>-</p>
            @endif
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label font-weight-bold">File PKWTT</label>
            @if ($kontrak->file_pkwtt)
                <div><a href="{{ asset('storage/' . $kontrak->file_pkwtt) }}" target="_blank"
                        class="btn btn-sm btn-outline-success">Lihat PKWTT</a></div>
            @else
                <p>-</p>
            @endif
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label font-weight-bold">File KHL</label>
            @if ($kontrak->file_khl)
                <div><a href="{{ asset('storage/' . $kontrak->file_khl) }}" target="_blank"
                        class="btn btn-sm btn-outline-info">Lihat KHL</a></div>
            @else
                <p>-</p>
            @endif
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label font-weight-bold">Keterangan</label>
            <p>{{ $kontrak->keterangan ?? '-' }}</p>
        </div>

        <div class="col-12 mt-3">
            <a href="{{ route('kontrak-karyawan.edit', $kontrak->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('kontrak-karyawan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
