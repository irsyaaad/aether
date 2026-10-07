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

    <div class="col-md-3">
        <label style="font-weight: bold;">Status</label>
        <div class="form-group" style="margin-bottom: 5px;">
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="berangkat" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('berangkat', $filter['status']) ? 'checked' : '' }}>
                Berangkat
            </label>
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="muat" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('muat', $filter['status']) ? 'checked' : '' }}>
                Muat
            </label>
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="bongkar" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('bongkar', $filter['status']) ? 'checked' : '' }}>
                Bongkar
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <label style="font-weight: bold;">Status</label>
        <div class="form-group" style="margin-bottom: 5px;">
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="Sudah di terima operasional" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('Sudah di terima operasional', $filter['status']) ? 'checked' : '' }}>
                Sudah di terima operasional
            </label>
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="Sudah di terima admin keuangan" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('Sudah di terima admin keuangan', $filter['status']) ? 'checked' : '' }}>
                Sudah di terima admin keuangan
            </label>
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="Sudah di tagihkan" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('Sudah di tagihkan', $filter['status']) ? 'checked' : '' }}>
                Sudah di tagihkan
            </label>
        </div>
    </div>
</div>
