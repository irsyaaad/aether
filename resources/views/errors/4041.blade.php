@extends('template.root')
@section('page-name', Request::segment(1))
@section('title-page', 'Tidak Punya Akses')
@section('content')
    <center>
        <div class="notfound">
            <div class="notfound-404">
                <h1>4 <span>0</span> 4</h1>
            </div>
            <h2>Oops! Anda tidak punya akses</h2>
            <p>Maaf, Anda tidak memiliki akses untuk mengakses Halaman Ini, Hubungi Administrator untuk informasi lebih
                lanjut</p>
            <a href="{{ url('dashboard') }}">Dashboard</a>
        </div>
    </center>
@endsection
