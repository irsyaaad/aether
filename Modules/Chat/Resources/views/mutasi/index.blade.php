@extends('template.root')
@section('page-name', 'permohonan')
@section('title-page', 'List Mutasi')
@section('add-page')
    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalCetak">
        <i class="fa fa-sync"></i> Request to Bank Jatim
    </button>
@endsection
@section('content')
    <div class="table-container">
        <table id="usersTable" class="table responsive table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Keterangan :</th>
                    <th>Jenis :</th>
                    <th>Nominal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align:right">Total:</th>
                    <th id="total_mutasi">Rp 0</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="modal fade" id="modalCetak" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Pilih Tanggal Mutasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('tes-api') }}" method="POST" enctype="multipart/form-data" id="form-add-mutasi">
                        @csrf
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="tgl">Dari Tanggal</label>
                            <input class="form-control" id="tgl" name="dr_tgl" type="date"
                                value="{{ date('Y-m-d') }}" placeholder="Tanggal Hari ini">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="tgl">Sampai Tanggal</label>
                            <input class="form-control" id="tgl" name="sp_tgl" type="date"
                                value="{{ date('Y-m-d') }}" placeholder="Tanggal Hari ini">
                        </div>
                        <div class="col-md-12 text-end">
                            <button class="btn btn-primary" type="submit"> <i class="fa fa-check"></i>
                                Proses</button>
                            <button class="btn btn-warning" type="button" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add-survey" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="bap-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <h4 class="text-center pb-2">Ganti Type Mutasi <span id="no_permohonan"></span>?</h4>
                        <form action="{{ url('list-mutasi/changeType') }}" method="POST" enctype="multipart/form-data"
                            id="form-add-survey">
                            @csrf
                            <input type="hidden" name="id_mutasi" id="mutasiId">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="tgl">Type Mutasi</label>
                                <select name="type" id="type" class="form-control">
                                    @foreach ($type_mutasi as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 text-end">
                                <button class="btn btn-primary" type="submit"> <i class="fa fa-check"></i>
                                    Proses</button>
                                <button class="btn btn-warning" type="button" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('list-mutasi.data') }}",
                    data: function(d) {
                        d.dr_tgl = $('#dr_tgl').val();
                        d.sp_tgl = $('#sp_tgl').val();
                        d.url = "{{ request()->segment(1) }}";
                    },
                    dataSrc: function(json) {
                        // Tampilkan total ke kolom footer
                        const total = Number(json.total_mutasi || 0);
                        $('#total_mutasi').html(
                            total.toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            })
                        );
                        return json.data;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jam',
                        name: 'jam'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'mutasi',
                        name: 'mutasi'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ]
            });

            const tableMonitoring = $('#usersTable');
            tableMonitoring.on('click', '.btn-change', function(e) {
                e.preventDefault();
                const parent = e.target.closest('tr');

                var mutasiId = $(this).data('id-mutasi');
                console.log(mutasiId);
                $('#mutasiId').val(mutasiId)

                $('#add-survey').modal('show');
            });

            document.getElementById('form-add-survey').addEventListener('submit', function(event) {
                event.preventDefault();

                const $form = $(this);
                const $submitButton = $form.find(":submit");
                const originalButtonText = '<i class="fa fa-check"></i> Proses';

                // Disable tombol dan tampilkan spinner
                $submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Loading...');

                const formData = new FormData(this);
                const actionUrl = this.action;

                fetch(actionUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Sukses:', data);
                        alert('Data berhasil dikirim!');
                        $('#usersTable').DataTable().ajax.reload(null, false);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mengirim data.');
                    })
                    .finally(() => {
                        // Enable kembali tombol submit dan kembalikan teksnya
                        $submitButton.prop('disabled', false).html(originalButtonText);
                    });
            });

        });
    </script>
@endsection
