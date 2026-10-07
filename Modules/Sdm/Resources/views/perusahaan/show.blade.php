@extends('template.root')
@section('page-name', 'perusahaan')
@section('title-page', 'Detail Perusahaan')
@section('add-page')
    <a href="{{ route('perusahaan.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
@endsection
@section('content')
    <div>
        <ul class="nav nav-tabs" id="icon-tab" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link txt-secondary active" id="icon-home-tab"
                    data-bs-toggle="tab" href="#icon-home" role="tab" aria-controls="icon-home" aria-selected="true"> <i
                        class="icofont icofont-man-in-glasses"></i>Data Perusahaan</a></li>
        </ul>
        <div class="tab-content" id="icon-tabContent">
            <div class="tab-pane fade active show" id="icon-home" role="tabpanel" aria-labelledby="icon-home-tab">
                @include('sdm::perusahaan.segment.informasi')
            </div>
        </div>
    </div>
@endsection
