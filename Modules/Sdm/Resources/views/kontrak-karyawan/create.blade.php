@extends('template.root')

@section('title-page')
    Tambah Kontrak Karyawan
@endsection

@section('content')
    <form action="{{ route('kontrak-karyawan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('sdm::kontrak-karyawan._form')
        <div class="row">
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('kontrak-karyawan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
@endsection
