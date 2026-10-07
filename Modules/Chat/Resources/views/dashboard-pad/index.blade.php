@extends('template.root')
@section('page-name', Request::segment(1))
@section('content')
    <div class="row">
        <div class="col-md-6">
            <a href="{{ url('pad') }}">
                <div class="card widget-1">
                    <div class="card-body">
                        <div class="widget-content">
                            <div class="widget-round primary">
                                <div class="bg-round">
                                    <svg class="fill-primary">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#c-invoice') }}"> </use>
                                    </svg>
                                    <svg class="half-circle svg-fill">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#halfcircle') }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="counter" data-target="0" id="total_ipt">0</h4><span class="f-light">Total
                                    Pembayaran IPT Per Range Tgl</span>
                            </div>
                        </div>
                        <div class="font-primary f-w-500"><i class="bookmark-search me-1"
                                data-feather="trending-up"></i><span class="txt-primary">+50%</span></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ url('pad') }}">
                <div class="card widget-1">
                    <div class="card-body">
                        <div class="widget-content">
                            <div class="widget-round warning">
                                <div class="bg-round">
                                    <svg class="fill-warning">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#c-invoice') }}"> </use>
                                    </svg>
                                    <svg class="half-circle svg-fill">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#halfcircle') }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="counter" data-target="0" id="total_ipr">0</h4><span class="f-light">Total IPR
                                    Per Range Tgl</span>
                            </div>
                        </div>
                        <div class="font-warning f-w-500"><i class="bookmark-search me-1"
                                data-feather="trending-up"></i><span class="txt-warning">+50%</span></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ url('pad') }}">
                <div class="card widget-1">
                    <div class="card-body">
                        <div class="widget-content">
                            <div class="widget-round success">
                                <div class="bg-round">
                                    <svg class="fill-success">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#c-invoice') }}"> </use>
                                    </svg>
                                    <svg class="half-circle svg-fill">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#halfcircle') }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="counter" data-target="0" id="total_gedung">0</h4><span class="f-light">Total Gedung
                                    Per Range Tgl</span>
                            </div>
                        </div>
                        <div class="font-success f-w-500"><i class="bookmark-search me-1"
                                data-feather="trending-up"></i><span class="txt-success">+50%</span></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ url('pad') }}">
                <div class="card widget-1">
                    <div class="card-body">
                        <div class="widget-content">
                            <div class="widget-round primary">
                                <div class="bg-round">
                                    <svg class="fill-primary">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#c-invoice') }}"> </use>
                                    </svg>
                                    <svg class="half-circle svg-fill">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#halfcircle') }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="counter" data-target="0" id="total_lain">0</h4><span class="f-light">Total
                                    Pemasukan Lain-lain</span>
                            </div>
                        </div>
                        <div class="font-primary f-w-500"><i class="bookmark-search me-1"
                                data-feather="trending-up"></i><span class="txt-primary">+50%</span></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-12 mb-5">
            <a href="{{ url('pad') }}">
                <div class="card widget-1 bg-success">
                    <div class="card-body">
                        <div class="widget-content">
                            <div class="widget-round success">
                                <div class="bg-round">
                                    <svg class="fill-success">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#c-invoice') }}"> </use>
                                    </svg>
                                    <svg class="half-circle svg-fill">
                                        <use href="{{ asset('cuba/svg/icon-sprite.svg#halfcircle') }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="counter" data-target="0" id="total_semua">0</h4><span class="f-light">Total
                                    Semua Pemasukan</span>
                            </div>
                        </div>
                        <div class="font-success f-w-500"><i class="bookmark-search me-1"
                                data-feather="trending-up"></i><span class="txt-success">+50%</span></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="#">
                <div class="card widget-1">
                    <div class="card-body">
                        <canvas id="iptChart" width="600" height="400"></canvas>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="#">
                <div class="card widget-1">
                    <div class="card-body">
                        <canvas id="donutChart" width="100" height="400"></canvas>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <a href="#">
                <div class="card widget-1">
                    <div class="card-body">
                        <canvas id="bulanChart" width="600" height="200"></canvas>
                    </div>
                </div>
            </a>
        </div>
    </div>
@include('chat::dashboard-pad.js-index')
@endsection
