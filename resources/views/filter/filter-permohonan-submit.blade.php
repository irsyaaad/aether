<div class="col-md-12 row">
    <div class="col-md-4">
        <label class="form-label" for="search-permohonan">No Permohonan</label>
        <select class="form-control search-permohonan" style="width: 100%;" name="id_permohonan" id="id_permohonan">
            @if($filter['permohonan'] != null)
                <option value="{{ $filter['permohonan']->id }}">{{ $filter['permohonan']->no_permohonan }}</option>
            @endif
        </select>
    </div>
    @if (Session('role')['id_role'] != 99)
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
    @endif
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
        <label class="form-label" for="tipe layanan">Status Permohonan</label>
        <select name="id_status" class="form-control" id="id_status">
            <option value="">-- Pilih Status Permohonan --</option>
            @foreach ($status as $item)
                <option value="{{ $item->id_status }}"
                    {{ isset($filter['id_status']) && $filter['id_status'] == $item->id_status ? 'selected' : '' }}>
                    {{ strtoupper($item->nama_status) }}</option>
            @endforeach
        </select>
    </div>
</div>
