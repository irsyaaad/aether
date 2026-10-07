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
    <div class="col-md-4">
        <label class="form-label" for="tipe layanan">Tipe</label>
        <select name="type_mutasi" id="type_mutasi_f" class="form-control">
            <option value="">Pilih Tipe Mutasi</option>
            @foreach ($tipeMutasi as $item)
                <option value="{{ $item->id }}"
                    {{ isset($filter['type_mutasi']) && $filter['type_mutasi'] == $item->id ? 'selected' : '' }}>
                    {{ $item->nama }}</option>
            @endforeach
        </select>
    </div>
</div>
