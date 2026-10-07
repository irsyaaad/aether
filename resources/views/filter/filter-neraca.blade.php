<div class="row">
    <div class="col-md-3">
        <label style="font-weight: bold;">
            Dari Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="dr_tgl" id="dr_tgl"
            value="{{ $filter['dr_tgl'] ?? '' }}">
    </div>

    <div class="col-md-3">
        <label style="font-weight: bold;">
            Sampai Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="sp_tgl" id="sp_tgl"
            value="{{ $filter['sp_tgl'] ?? '' }}">
    </div>
    @isset($filter['id_ac'])
        <input type="hidden" name="id_ac" value="{{ $filter['id_ac'] }}">
    @endisset
</div>
