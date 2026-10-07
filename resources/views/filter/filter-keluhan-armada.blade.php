<div class="row">
    <div class="col-md-4">
        <label style="font-weight: bold;">
            Dari Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="dr_tgl" id="dr_tgl"
            value="{{ $filter['dr_tgl'] ?? '' }}">
    </div>

    <div class="col-md-4">
        <label style="font-weight: bold;">
            Sampai Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="sp_tgl" id="sp_tgl"
            value="{{ $filter['sp_tgl'] ?? '' }}">
    </div>

    <div class="col-md-4">
        <label style="font-weight: bold;">
            Status
        </label>
        <select name="status" id="status" class="form-control">
            <option value="">Semua</option>
            <option value="open" {{ isset($filter['status']) && $filter['status'] == 'open' ? 'selected' : '' }}>
                Open</option>
            <option value="dijadwalkan"
                {{ isset($filter['status']) && $filter['status'] == 'dijadwalkan' ? 'selected' : '' }}>
                Dijadwalkan</option>
            <option value="diproses"
                {{ isset($filter['status']) && $filter['status'] == 'diproses' ? 'selected' : '' }}>
                Diproses</option>
            <option value="closed" {{ isset($filter['status']) && $filter['status'] == 'closed' ? 'selected' : '' }}>
                Closed</option>
        </select>
    </div>
</div>
