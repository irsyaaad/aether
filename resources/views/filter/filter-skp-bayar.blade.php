<div class="col-md-12 row">
    <div class="col-md-4">
        <label class="form-label" for="search-permohonan">Tanggal (dari)</label>
        <input type="date" data-ignore-limit class="form-control" name="dr_tgl" id="dr_tgl"
            value="{{ $filter['dr_tgl'] ?? '' }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tipe layanan">Tanggal (sampai)</label>
        <input type="date" data-ignore-limit class="form-control" name="sp_tgl" id="sp_tgl"
            value="{{ $filter['sp_tgl'] ?? '' }}">
    </div>
</div>
