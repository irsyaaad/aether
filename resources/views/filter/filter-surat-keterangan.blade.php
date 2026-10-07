<div class="col-md-12 row">
    <div class="col-md-4">
        <label class="form-label" for="search-permohonan">No Surat</label>
        <select class="form-control search-surat" style="width: 100%;" name="id_surat">
            @isset($filter['id_surat'])
                <option value="{{ $filter['id_surat']->id }}" selected>{{ $filter['id_surat']->nomer_surat }}</option>
            @endisset
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="search-permohonan">No Permohonan</label>
        <select class="form-control search-permohonan" style="width: 100%;" name="id_permohonan">
            @isset($filter['id_permohonan'])
                <option value="{{ $filter['id_permohonan']->id }}" selected>{{ $filter['id_permohonan']->no_permohonan }}
                </option>
            @endisset
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="search-permohonan">Nama Pemohon</label>
        <select class="form-control search-nm-permohonan" style="width: 100%;" name="nm_pemohon">
            @isset($filter['nm_pemohon'])
                <option value="{{ $filter['nm_pemohon'] }}" selected>{{ $filter['nm_pemohon'] }}</option>
            @endisset
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="search-permohonan">Persil</label>
        <select class="form-control search-alamat-persil" style="width: 100%;" name="alamat_persil">
            @isset($filter['alamat_persil'])
                <option value="{{ $filter['alamat_persil'] }}" selected>{{ $filter['alamat_persil'] }}</option>
            @endisset
        </select>
    </div>
</div>
