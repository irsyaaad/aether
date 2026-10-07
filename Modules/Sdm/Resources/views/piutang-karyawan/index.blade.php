@extends('template.root')
@section('page-name', 'piutang-karyawan')
@section('title-page', 'Piutang Karyawan')
@section('add-page')
    <a href="{{ route('piutang-karyawan.create') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> Tambah Piutang
    </a>
@endsection
@section('content')
    <!-- DataTable -->
    <div class="table-container">
        <table class="table table-bordered" id="piutang-karyawan-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Piutang</th>
                    <th>Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Jumlah Piutang</th>
                    <th>Sisa Piutang</th>
                    <th>Status</th>
                    <th width="50px">Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal Tambah Pembayaran -->
    <div class="modal fade" id="tambah-pembayaran-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pembayaran Piutang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-tambah-pembayaran" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sisa Piutang</label>
                                    <input type="text" id="sisa-piutang-display" class="form-control" readonly>
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
                                    <input type="number" name="jumlah_bayar" id="jumlah-bayar" class="form-control"
                                        min="1" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Metode Bayar <span class="text-danger">*</span></label>
                                    <select name="metode_bayar" class="form-control" required>
                                        <option value="">Pilih Metode</option>
                                        <option value="tunai">Tunai</option>
                                        <option value="transfer">Transfer</option>
                                        <option value="potong_gaji">Potong Gaji</option>
                                        <option value="lainnya">Lainnya</option>
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#piutang-karyawan-table').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('piutang-karyawan.get-data') }}",
                        data: function(d) {
                            d.dr_tgl = $('input[name="dr_tgl"]').val();
                            d.sp_tgl = $('input[name="sp_tgl"]').val();
                            d.status = $('select[name="status"]').val();
                            d.jenis_piutang = $('select[name="jenis_piutang"]').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nomor_piutang',
                            name: 'nomor_piutang'
                        },
                        {
                            data: 'karyawan',
                            name: 'karyawan.nama_lengkap'
                        },
                        {
                            data: 'tanggal_piutang',
                            name: 'tanggal_piutang'
                        },
                        {
                            data: 'jenis_piutang',
                            name: 'jenis_piutang'
                        },
                        {
                            data: 'jumlah_piutang',
                            name: 'jumlah_piutang'
                        },
                        {
                            data: 'sisa_piutang',
                            name: 'sisa_piutang'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    pageLength: 50,
                    responsive: true
                });

                // Bayar piutang functionality
                $(document).on('click', '.btn-bayar', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var sisa = $(this).data('sisa');

                    $('#form-tambah-pembayaran').attr('action', "{{ url('piutang-karyawan') }}/" + id +
                        "/bayar");
                    $('#sisa-piutang-display').val('Rp ' + new Intl.NumberFormat('id-ID').format(sisa));
                    $('#jumlah-bayar').attr('max', sisa);
                    $('#tambah-pembayaran-modal').modal('show');
                });

                // Delete functionality
                $(document).on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');

                    Swal.fire({
                        title: 'Hapus Piutang?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('piutang-karyawan') }}/" + id,
                                type: 'DELETE',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire('Berhasil!', response.message, 'success');
                                        table.ajax.reload();
                                    } else {
                                        Swal.fire('Error!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!',
                                        'Terjadi kesalahan saat menghapus data', 'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
