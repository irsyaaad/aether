@extends('template.root')
@section('page-name', 'piutang-karyawan')
@section('title-page', 'Detail Piutang Karyawan')
@section('content')
    <div class="col-md-12">
        <div class="row">
            <!-- Informasi Piutang -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Informasi Piutang - {{ $piutang->nomor_piutang }}</h6>
                            <div>
                                @if ($piutang->canBeEdited())
                                    <a href="{{ route('piutang-karyawan.edit', $piutang->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @endif
                                @if ($piutang->status === 'aktif' && $piutang->sisa_piutang > 0)
                                    <button class="btn btn-success btn-sm btn-bayar" data-id="{{ $piutang->id }}"
                                        data-sisa="{{ $piutang->sisa_piutang }}">
                                        <i class="fa fa-money-bill"></i> Bayar
                                    </button>
                                @endif
                                <a href="{{ route('piutang-karyawan.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Nomor Piutang:</strong></div>
                            <div class="col-md-8">{{ $piutang->nomor_piutang }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Karyawan:</strong></div>
                            <div class="col-md-8">{{ $piutang->karyawan->nama_lengkap ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Jabatan:</strong></div>
                            <div class="col-md-8">{{ $piutang->karyawan->jabatan ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Tanggal Piutang:</strong></div>
                            <div class="col-md-8">{{ $piutang->tanggal_piutang->format('d F Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Jenis Piutang:</strong></div>
                            <div class="col-md-8">{!! $piutang->jenis_piutang_badge !!}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Jumlah Piutang:</strong></div>
                            <div class="col-md-8">
                                <span class="fw-bold text-primary">Rp
                                    {{ number_format($piutang->jumlah_piutang, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Jumlah Terbayar:</strong></div>
                            <div class="col-md-8">
                                <span class="fw-bold text-success">Rp
                                    {{ number_format($piutang->jumlah_terbayar, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Sisa Piutang:</strong></div>
                            <div class="col-md-8">
                                <span class="fw-bold {{ $piutang->sisa_piutang > 0 ? 'text-danger' : 'text-success' }}">
                                    Rp {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @if ($piutang->jatuh_tempo)
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Jatuh Tempo:</strong></div>
                                <div class="col-md-8">
                                    {{ $piutang->jatuh_tempo->format('d F Y') }}
                                    @if ($piutang->jatuh_tempo < now() && $piutang->sisa_piutang > 0)
                                        <span class="badge bg-danger ms-2">Terlambat</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Status:</strong></div>
                            <div class="col-md-8">{!! $piutang->status_badge !!}</div>
                        </div>
                        @if ($piutang->keterangan)
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Keterangan:</strong></div>
                                <div class="col-md-8">{{ $piutang->keterangan }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Tambahan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Dibuat oleh:</small><br>
                            {{ $piutang->creator->nm_user ?? '-' }}
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Tanggal dibuat:</small><br>
                            {{ $piutang->created_at->format('d F Y H:i') }}
                        </div>
                        @if ($piutang->updated_at != $piutang->created_at)
                            <div class="mb-3">
                                <small class="text-muted">Terakhir diupdate:</small><br>
                                {{ $piutang->updated_at->format('d F Y H:i') }}
                            </div>
                            @if ($piutang->updater)
                                <div class="mb-3">
                                    <small class="text-muted">Diupdate oleh:</small><br>
                                    {{ $piutang->updater->nm_user }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Progress Pembayaran -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Progress Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $persentase =
                                $piutang->jumlah_piutang > 0
                                    ? ($piutang->jumlah_terbayar / $piutang->jumlah_piutang) * 100
                                    : 0;
                        @endphp
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar {{ $persentase == 100 ? 'bg-success' : 'bg-primary' }}"
                                role="progressbar" style="width: {{ $persentase }}%">
                                {{ number_format($persentase, 1) }}%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Terbayar</small>
                            <small class="text-muted">{{ number_format($persentase, 1) }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Riwayat Pembayaran</h6>
                            @if ($piutang->status === 'aktif' && $piutang->sisa_piutang > 0)
                                <button class="btn btn-success btn-sm btn-tambah-pembayaran">
                                    <i class="fa fa-plus"></i> Tambah Pembayaran
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($piutang->pembayaran->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Pembayaran</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Metode</th>
                                            <th>Bukti</th>
                                            <th>Keterangan</th>
                                            <th>User</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($piutang->pembayaran as $index => $bayar)
                                            <tr class="{{ $bayar->trashed() ? 'table-danger' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    {{ $bayar->nomor_pembayaran }}
                                                    @if ($bayar->trashed())
                                                        <span class="badge bg-danger ms-1">Dihapus</span>
                                                    @endif
                                                </td>
                                                <td>{{ $bayar->tanggal_bayar->format('d M Y') }}</td>
                                                <td>Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                                <td>{{ $bayar->acKredit->nama ?? '-' }}</td>
                                                <td>
                                                    @if ($bayar->bukti_bayar)
                                                        <a href="{{ $bayar->bukti_bayar_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fa fa-image"></i> Lihat
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $bayar->keterangan ?? '-' }}</td>
                                                <td>{{ $bayar->creator->nm_user ?? '-' }}</td>
                                                <td>
                                                    @if (!$bayar->trashed())
                                                        <button class="btn btn-sm btn-warning btn-edit-pembayaran"
                                                            data-id="{{ $bayar->id }}"
                                                            data-pembayaran-id="{{ $bayar->id }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger btn-hapus-pembayaran"
                                                            data-id="{{ $bayar->id }}"
                                                            data-nomor="{{ $bayar->nomor_pembayaran }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada pembayaran</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pembayaran -->
    <div class="modal fade" id="tambah-pembayaran-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pembayaran Piutang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-tambah-pembayaran" method="POST"
                    action="{{ route('piutang-karyawan.bayar', $piutang->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sisa Piutang</label>
                                    <input type="text"
                                        value="Rp {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_bayar" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                    <input type="text" name="jumlah_bayar" class="form-control auto-number" min="1"
                                        step="0.01" max="{{ $piutang->sisa_piutang }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Metode Bayar <span class="text-danger">*</span></label>
                                    <select name="metode_bayar" class="form-control" required>
                                        <option value="">Pilih Kas / Bank</option>
                                        @foreach ($kasBank as $item)
                                            <option value="{{ $item->id_ac }}">{{ $item->nama    }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bukti Pembayaran</label>
                            <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan pembayaran (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pembayaran -->
    <div class="modal fade" id="edit-pembayaran-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pembayaran Piutang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-edit-pembayaran" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sisa Piutang</label>
                                    <input type="text" id="edit-sisa-piutang" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_bayar" id="edit-tanggal-bayar" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                    <input type="text" name="jumlah_bayar" id="edit-jumlah-bayar" class="form-control auto-number" min="1" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Metode Bayar <span class="text-danger">*</span></label>
                                    <select name="metode_bayar" id="edit-metode-bayar" class="form-control" required>
                                        <option value="">Pilih Kas / Bank</option>
                                        @foreach ($kasBank as $item)
                                            <option value="{{ $item->id_ac }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bukti Pembayaran</label>
                            <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB</small>
                            <div id="edit-bukti-lama" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="edit-keterangan" class="form-control" rows="3" placeholder="Keterangan pembayaran (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Tambah pembayaran functionality
                $('.btn-tambah-pembayaran').on('click', function(e) {
                    e.preventDefault();
                    $('#tambah-pembayaran-modal').modal('show');
                });

                // Hapus pembayaran functionality
                $('.btn-hapus-pembayaran').on('click', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var nomor = $(this).data('nomor');

                    Swal.fire({
                        title: 'Hapus Pembayaran?',
                        text: "Pembayaran " + nomor + " akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('piutang-karyawan/' . $piutang->id . '/pembayaran') }}/" +
                                    id,
                                type: 'DELETE',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire('Berhasil!', response.message, 'success')
                                            .then(() => {
                                                location.reload();
                                            });
                                    } else {
                                        Swal.fire('Error!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!',
                                        'Terjadi kesalahan saat menghapus pembayaran',
                                        'error');
                                }
                            });
                        }
                    });
                });

                // Update button bayar functionality for compatibility
                $('.btn-bayar').on('click', function(e) {
                    e.preventDefault();
                    $('#tambah-pembayaran-modal').modal('show');
                });

                // Edit pembayaran functionality
                $('.btn-edit-pembayaran').on('click', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var piutangId = '{{ $piutang->id }}';
                    var url = "{{ url('piutang-karyawan') }}/" + piutangId + "/pembayaran/" + id + "/edit";
                    console.log('Edit URL:', url);
                    
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            if (response.status === 'success') {
                                var pembayaran = response.pembayaran;
                                var piutang = response.piutang;
                                var kasBank = response.kasBank;

                                $('#edit-sisa-piutang').val('Rp ' + piutang.sisa_piutang.toLocaleString('id-ID'));
                                $('#edit-tanggal-bayar').val(pembayaran.tanggal_bayar);
                                $('#edit-jumlah-bayar').val(pembayaran.jumlah_bayar);
                                $('#edit-keterangan').val(pembayaran.keterangan || '');

                                $('#edit-metode-bayar').empty().append('<option value="">Pilih Kas / Bank</option>');
                                $.each(kasBank, function(index, item) {
                                    var selected = item.selected ? 'selected' : '';
                                    $('#edit-metode-bayar').append('<option value="' + item.id_ac + '" ' + selected + '>' + item.nama + '</option>');
                                });

                                $('#edit-jumlah-bayar').attr('max', piutang.sisa_piutang);

                                if (pembayaran.bukti_bayar) {
                                    $('#edit-bukti-lama').html('<a href="' + pembayaran.bukti_bayar_url + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-image"></i> Lihat Bukti Lama</a>');
                                } else {
                                    $('#edit-bukti-lama').html('');
                                }

                                var actionUrl = "{{ url('piutang-karyawan') }}/" + piutangId + "/pembayaran/" + id;
                                $('#form-edit-pembayaran').attr('action', actionUrl);

                                $('#edit-pembayaran-modal').modal('show');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            console.log('Error response:', xhr.responseText);
                            Swal.fire('Error!', 'Terjadi kesalahan saat memuat data pembayaran: ' + xhr.statusText, 'error');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
