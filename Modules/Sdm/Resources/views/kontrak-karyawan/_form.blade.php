<div class="row">
    <div class="col-md-6 mb-3">
        <label for="id_karyawan" class="form-label">Nama Karyawan</label>
        <select class="form-control select2" id="id_karyawan" name="id_karyawan" required>
            <option value="">-- Pilih Karyawan --</option>
            @foreach ($karyawan as $k)
                <option value="{{ $k->id }}"
                    {{ isset($kontrak) && $kontrak->id_karyawan == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="jenis_kontrak" class="form-label">Jenis Kontrak</label>
        <select class="form-control" id="jenis_kontrak" name="jenis_kontrak" required>
            <option value="pkwt" {{ isset($kontrak) && $kontrak->jenis_kontrak == 'pkwt' ? 'selected' : '' }}>PKWT
            </option>
            <option value="tetap" {{ isset($kontrak) && $kontrak->jenis_kontrak == 'tetap' ? 'selected' : '' }}>Tetap
            </option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
            value="{{ isset($kontrak) ? $kontrak->tanggal_mulai : '' }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
            value="{{ isset($kontrak) ? $kontrak->tanggal_selesai : '' }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label for="file_pkwt" class="form-label">Upload PKWT (PDF/Image)</label>
        @if (isset($kontrak) && $kontrak->file_pkwt)
            <div class="mb-1"><a href="{{ asset('storage/' . $kontrak->file_pkwt) }}" target="_blank"
                    class="badge badge-primary">Lihat File</a></div>
        @endif
        <input type="file" class="form-control" id="file_pkwt" name="file_pkwt">
        @if (isset($kontrak))
            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah.</small>
        @endif
    </div>
    <div class="col-md-4 mb-3">
        <label for="file_pkwtt" class="form-label">Upload PKWTT (PDF/Image)</label>
        @if (isset($kontrak) && $kontrak->file_pkwtt)
            <div class="mb-1"><a href="{{ asset('storage/' . $kontrak->file_pkwtt) }}" target="_blank"
                    class="badge badge-success">Lihat File</a></div>
        @endif
        <input type="file" class="form-control" id="file_pkwtt" name="file_pkwtt">
        @if (isset($kontrak))
            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah.</small>
        @endif
    </div>
    <div class="col-md-4 mb-3">
        <label for="file_khl" class="form-label">Upload KHL (PDF/Image)</label>
        @if (isset($kontrak) && $kontrak->file_khl)
            <div class="mb-1"><a href="{{ asset('storage/' . $kontrak->file_khl) }}" target="_blank"
                    class="badge badge-info">Lihat File</a></div>
        @endif
        <input type="file" class="form-control" id="file_khl" name="file_khl">
        @if (isset($kontrak))
            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah.</small>
        @endif
    </div>

    <div class="col-md-12 mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ isset($kontrak) ? $kontrak->keterangan : '' }}</textarea>
    </div>
</div>

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
