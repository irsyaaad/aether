<div class="row mb-3">
    <div class="col-md-2">
        <label class="form-label">Tipe File</label>
        <select id="filter_tipe" class="form-select form-select-sm">
            <option value="">Semua Tipe</option>
            <option value="foto">Foto</option>
            <option value="video">Video</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Kategori</label>
        <select id="filter_kategori" class="form-select form-select-sm">
            <option value="">Semua Kategori</option>
            <option value="kegiatan">Kegiatan</option>
            <option value="fasilitas">Fasilitas</option>
            <option value="karyawan">Karyawan</option>
            <option value="produk">Produk</option>
            <option value="acara">Acara</option>
            <option value="lainnya">Lainnya</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Status</label>
        <select id="filter_status" class="form-select form-select-sm">
            <option value="">Semua Status</option>
            <option value="aktif" selected>Aktif</option>
            <option value="arsip">Arsip</option>
            <option value="dihapus">Dihapus</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Dari Tanggal</label>
        <input type="date" id="dr_tgl" class="form-control form-control-sm" value="{{ $filter['dr_tgl'] }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" id="sp_tgl" class="form-control form-control-sm" value="{{ $filter['sp_tgl'] }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <div class="d-grid">
            <button type="button" id="btn-filter" class="btn btn-primary btn-sm">
                <i class="fa fa-search"></i> Filter
            </button>
        </div>
    </div>
</div>
