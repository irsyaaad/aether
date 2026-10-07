<div class="col-md-12 text-center">
    <h5>Silahkan Pilih Permohonan yang akan anda buat</h5>
    <br>
    <hr>
</div>
<div class="row">
    @foreach ($layanan['surat-keterangan'] as $item)
        <div class="col-xl-4 col-hr-6 col-sm-6">
            <a href="{{ url('permohonan') . '/create' }}?tipe={{ $item->id_layanan }}">
                <div class="card widget-11 widget-hover">
                    <div class="card-body">
                        <div class="common-align justify-content-start">
                            <div class="analytics-tread bg-light-primary">
                                <svg class="fill-primary">
                                    <use href="{{ asset('cuba/svg/icon-sprite.svg#c-invoice') }}"></use>
                                </svg>
                            </div>
                            <div>
                                <h6>{{ ucwords(strtolower($item->nm_layanan)) }}</h6>
                                <span class="text-muted">{{ $item->keterangan ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
<hr>
<div class="row">
    @foreach ($layanan['ipt'] as $item)
        <div class="col-xl-4 col-hr-6 col-sm-6">
            <a href="{{ url('ipt-pengurangan') . '/create' }}?tipe={{ $item->id_layanan }}">
                <div class="card widget-11 widget-hover">
                    <div class="card-body">
                        <div class="common-align justify-content-start">
                            <div class="analytics-tread bg-light-success">
                                <svg class="fill-success">
                                    <use href="{{ asset('cuba/svg/icon-sprite.svg#c-invoice') }}"></use>
                                </svg>
                            </div>
                            <div>
                                <h6>{{ ucwords(strtolower($item->nm_layanan)) }}</h6>
                                <span class="text-muted">{{ $item->keterangan ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
