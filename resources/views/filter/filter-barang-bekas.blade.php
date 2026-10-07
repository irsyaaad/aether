<div class="row">
    <div class="col-md-6">
        <label style="font-weight: bold;">
            Dari Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="tanggal_dari" id="tanggal_dari"
            value="{{ $filter['tanggal_dari'] ?? '' }}">
    </div>

    <div class="col-md-6">
        <label style="font-weight: bold;">
            Sampai Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="tanggal_sampai" id="tanggal_sampai"
            value="{{ $filter['tanggal_sampai'] ?? '' }}">
    </div>
</div>
