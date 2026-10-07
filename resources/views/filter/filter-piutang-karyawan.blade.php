<div class="row">
    <div class="col-md-3">
        <label class="form-label">Dari Tanggal</label>
        <input type="date" data-ignore-limit name="dr_tgl" class="form-control" value="{{ $filter['dr_tgl'] }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" data-ignore-limit name="sp_tgl" class="form-control" value="{{ $filter['sp_tgl'] }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="aktif" {{ $filter['status'] == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="lunas" {{ $filter['status'] == 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="dihapuskan" {{ $filter['status'] == 'dihapuskan' ? 'selected' : '' }}>Dihapuskan</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jenis Piutang</label>
        <select name="jenis_piutang" class="form-select">
            <option value="">Semua Jenis</option>
            <option value="kasbon" {{ $filter['jenis_piutang'] == 'kasbon' ? 'selected' : '' }}>Kasbon</option>
            <option value="pinjaman" {{ $filter['jenis_piutang'] == 'pinjaman' ? 'selected' : '' }}>Pinjaman</option>
            <option value="lainnya" {{ $filter['jenis_piutang'] == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
    </div>
</div>
