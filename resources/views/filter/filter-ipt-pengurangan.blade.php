<div class="col-md-12 row">
    <div class="col-md-4">
        <label class="form-label" for="search-permohonan">No Permohonan</label>
        <select class="form-control search-permohonan" style="width: 100%;" name="id_permohonan">
            @if ($filter['permohonan'] != null)
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
</div>
