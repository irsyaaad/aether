<div class="col-md-12 row mb-3">
    <div class="col-md-4">
        <label class="form-label" for="tipe layanan">Tipe Layanan</label>
        <select name="id_layanan" class="form-control" id="id_layanan">
            <option value="">-- Pilih Layanan --</option>
            @foreach ($layanan as $item)
                <option value="{{ $item->id_layanan }}"
                    {{ isset($filter['id_layanan']) && $filter['id_layanan'] == $item->id_layanan ? 'selected' : '' }}>
                    {{ strtoupper($item->nm_layanan) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tipe layanan">Tgl Pengajuan (Dari)</label>
        <input type="date" data-ignore-limit class="form form-control" name="dr_tgl" id="dr_tgl"
            value="{{ $filter['dr_tgl'] ?? '' }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tipe layanan">Tgl Pengajuan (Sampai)</label>
        <input type="date" data-ignore-limit class="form form-control" name="sp_tgl" id="sp_tgl"
            value="{{ $filter['sp_tgl'] ?? '' }}">
    </div>
    <div class="col-md-12">
        <label class="form-label" for="search-permohonan">Search</label>
        <input type="text" class="form-control" name="key" id="key" value="{{ $filter['key'] ?? '' }}"
            placeholder="Masukkan No Permohonan atau Nama Pemohon , dll ...">
    </div>
    @if (Request::segment(1) == 'permohonan-survey')
        <div class="col-md-4">
            <label class="form-label" for="tipe layanan">Tanggal Survey</label>
            <input type="date" data-ignore-limit class="form form-control" name="tgl_survey" id="tgl_survey">
        </div>
    @endif
</div>
