<div class="modal fade" id="add-survey" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="bap-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-toggle-wrapper">
                    <h4 class="text-center pb-2">Masukkan Survey <span id="no_permohonan"></span>?</h4>
                    <form action="{{ url('permohonan/add-tgl-survey') }}" method="POST" enctype="multipart/form-data"
                        id="form-add-survey">
                        @csrf
                        <input type="hidden" name="id_permohonan" id="permohonanId">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="tgl">Tgl Survey</label>
                            <input class="form-control" id="tgl" name="tgl" type="date"
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
</div>
<div>
    <ul class="nav nav-tabs" id="icon-tab" role="tablist">
        <li class="nav-item" role="presentation"><a class="nav-link txt-secondary active" id="icon-home-tab"
                data-bs-toggle="tab" href="#icon-home" role="tab" aria-controls="icon-home" aria-selected="true"> <i
                    class="icofont icofont-man-in-glasses"></i>Dashboard</a></li>
        <li class="nav-item" role="presentation"><a class="nav-link txt-secondary" id="data-syarat-tab"
                data-bs-toggle="tab" href="#data-syarat" role="tab" aria-controls="contact-icon"
                aria-selected="false" tabindex="-1"><i class="fa-solid fa-file-image"></i>Report per Actor</a></li>
        <li class="nav-item" role="presentation"><a class="nav-link txt-secondary" id="data-layanan-tab"
                data-bs-toggle="tab" href="#data-layanan" role="tab" aria-controls="contact-icon"
                aria-selected="false" tabindex="-1"><i class="fa-solid fa-file-image"></i>Report per Layanan</a></li>
    </ul>
    <div class="tab-content" id="icon-tabContent">
        <div class="tab-pane fade active show" id="icon-home" role="tabpanel" aria-labelledby="icon-home-tab">
            @include('dashboard.report-1')
        </div>
        <div class="tab-pane fade" id="data-syarat" role="tabpanel" aria-labelledby="data-syarat-tab">
            @include('dashboard.report-2')
        </div>
        <div class="tab-pane fade" id="data-layanan" role="tabpanel" aria-labelledby="data-layanan-tab">
            @include('dashboard.report-3')
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('monitoring-proses-data') }}",
                data: function(d) {
                    d.id_permohonan = $('#id_permohonan').val();
                    d.nm_pemohon = $('#nm_pemohon').val();
                    d.alamat_persil = $('#alamat_persil').val();
                    d.id_layanan = $('#id_layanan').val();
                    d.id_status = $('#id_status').val();
                    d.url = "{{ request()->segment(1) }}";
                }
            },
            columns: [{
                    data: null,
                    name: 'index',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'no_permohonan',
                    name: 'no_permohonan',
                    render: function(data, type, row) {
                        let html = '';
                        const url = '{{ request()->segment(1) }}';

                        if (url === 'dashboard') {
                            html +=
                                `<a href="/${row.url}/${row.id}/verifikasi">${dateIndo(row.tanggal_pengajuan)}<br>${row.no_permohonan}</a>`;
                        } else {
                            html +=
                                `${dateIndo(row.tanggal_pengajuan)}<br>${row.no_permohonan}`;
                        }

                        if (row.last_status == 99) {
                            html +=
                                ` <span class="badge bg-danger">Reject</span><br><span class="text-danger">Keterangan : ${row.last_keterangan}</span>`;
                        }

                        return html;
                    }
                },
                {
                    data: 'nama_pemohon',
                    name: 'nama_pemohon',
                    render: function(data, type, row) {
                        let html = `${row.nama_pemohon}<br>${row.alamat_persil}`;
                        if (row.tgl_survey) {
                            html +=
                                `<br><span class="badge bg-success">Tanggal Survey : ${dateIndo(row.tgl_survey)}</span>`;
                        }
                        return html;
                    }
                },
                {
                    data: 'nm_layanan',
                    name: 'nm_layanan'
                },
                {
                    data: null,
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let html = `<div class="d-flex">`;
                        const url = '{{ request()->segment(1) }}';

                        if (url !== 'dashboard') {
                            if (row.id_status != 99) {
                                html +=
                                    `<a href="/permohonan-proses/${row.id}/show" class="btn btn-sm btn-primary me-2"><i class="fa-solid fa-eye"></i></a>`;
                            } else {
                                html +=
                                    `<a href="/permohonan-proses/${row.id}/show" class="btn btn-sm btn-danger me-2"><i class="fa-solid fa-eye"></i></a>`;
                                html +=
                                    `<a href="/permohonan/${row.id}/detail" class="btn btn-sm btn-warning me-2"><i class="fa-solid fa-file"></i></a>`;
                            }
                        }

                        @if (check_access('upload-bap'))
                            if (url === 'permohonan-disposisi') {
                                html +=
                                    `<button class="btn btn-sm btn-warning btn-cetak" title="Cetak Dokumen" data-id-permohonan="${row.id}"><i class="fa-solid fa-print"></i></button>`;
                            }

                            if (url === 'permohonan-cetak') {
                                if (row.tgl_survey) {
                                    html +=
                                        `<button class="btn btn-sm btn-warning btn-survey" title="Masukkan Tanggal Survey" data-id-permohonan="${row.id}"><i class="fa-solid fa-pencil"></i></button>`;
                                } else {
                                    html +=
                                        `<button class="btn btn-sm btn-success btn-survey" title="Edit Tanggal Survey" data-id-permohonan="${row.id}"><i class="fa-solid fa-book"></i></button>`;
                                }
                            }
                        @endif

                        html += `</div>`;
                        return html;
                    }
                },
            ]
        });

        function dateIndo(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        function ucwords(str) {
            if (!str) return '-';
            return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
        }

        $('#reportTable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false, // <-- pindahkan ke sini
            pageLength: 10, // <-- juga di sini
            ajax: {
                url: "{{ route('get-report') }}",
                data: function(d) {
                    d.url = "{{ request()->segment(1) }}";
                    d.dr_tgl = $('#dr_tgl').val();
                    d.sp_tgl = $('#sp_tgl').val();
                },
                dataSrc: function(json) {
                    $('#total_masuk').html(Number(json.total_masuk || 0));
                    $('#total_proses').html(Number(json.total_proses || 0));
                    $('#total_selesai_r').html(Number(json.total_selesai || 0));
                    $('#total_tolak').html(Number(json.total_tolak || 0));
                    return json.data;
                }
            },
            columns: [{
                    data: null,
                    name: 'index',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nm_layanan',
                    name: 'nm_layanan',
                    // render: function(data, type, row) {
                    //     let url = "{{ url('permohonan/get-report-detail') }}" +
                    //         "?id_layanan=" + encodeURIComponent(row.id_layanan) +
                    //         "&dr_tgl=" + encodeURIComponent($('#dr_tgl').val()) +
                    //         "&sp_tgl=" + encodeURIComponent($('#sp_tgl').val());
                    //     return `<a href="${url}">${data}</a>`;
                    // }
                },
                {
                    data: 'masuk',
                    name: 'masuk'
                },
                {
                    data: 'proses',
                    name: 'proses'
                },
                {
                    data: 'selesai',
                    name: 'selesai'
                },
                {
                    data: 'tolak',
                    name: 'tolak'
                }
            ]
        });

        $('#reportPetugas').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false, // <-- pindahkan ke sini
            pageLength: 10, // <-- juga di sini
            ajax: {
                url: "{{ route('get-report-user') }}",
                data: function(d) {
                    d.url = "{{ request()->segment(1) }}";
                    d.dr_tgl = $('#dr_tgl').val();
                    d.sp_tgl = $('#sp_tgl').val();
                },
                dataSrc: function(json) {
                    $('#total_verifikasi').html(Number(json.total_verifikasi || 0));
                    return json.data;
                }
            },
            columns: [{
                    data: null,
                    name: 'index',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nm_user',
                    name: 'nm_user',
                    // render: function(data, type, row) {
                    //     let url = "{{ url('permohonan/get-report-detail') }}" +
                    //         "?id_layanan=" + encodeURIComponent(row.id_layanan) +
                    //         "&dr_tgl=" + encodeURIComponent($('#dr_tgl').val()) +
                    //         "&sp_tgl=" + encodeURIComponent($('#sp_tgl').val());
                    //     return `<a href="${url}">${data}</a>`;
                    // }
                },
                {
                    data: 'total_verifikasi',
                    name: 'total_verifikasi'
                }
            ]
        });
    });

    // $(document).ready(function() {
    //     $('#usersTable2').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         responsive: true,
    //         ajax: {
    //             url: "{{ route('ipt-pengurangan-data') }}",
    //             data: function(d) {
    //                 d.id_permohonan = $('#id_permohonan').val();
    //                 d.nm_pemohon = $('#nm_pemohon').val();
    //                 d.alamat_persil = $('#alamat_persil').val();
    //                 d.id_layanan = $('#id_layanan').val();
    //                 d.id_status = $('#id_status').val();
    //                 d.url = "{{ request()->segment(1) }}";
    //             }
    //         },
    //         columns: [{
    //                 data: null,
    //                 name: 'index',
    //                 orderable: false,
    //                 searchable: false,
    //                 render: function(data, type, row, meta) {
    //                     return meta.row + meta.settings._iDisplayStart + 1;
    //                 }
    //             },
    //             {
    //                 data: 'no_permohonan',
    //                 name: 'no_permohonan'
    //             },
    //             {
    //                 data: 'nama_pemohon',
    //                 name: 'nama_pemohon'
    //             },
    //             {
    //                 data: 'type',
    //                 name: 'type'
    //             },
    //         ]
    //     });
    // });

    document.addEventListener("DOMContentLoaded", function() {
        fetch('{{ url('permohonan/get-calculation') }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then(data => {
                // Asumsikan response berupa { total: 123 }
                console.log(data);
                const totalSemua = data.data.totalSemua;
                const totalFilter = data.data.totalFilter;
                const totalSelesai = data.data.totalSelesai;
                const totalArsip = data.data.totalArsip;
                const totalBap = data.data.totalBap;
                const totalKaban = data.data.totalKaban;
                const totalKabid = data.data.totalKabid;
                const totalKetua = data.data.totalKetua;
                const totalSekretaris = data.data.totalSekretaris;
                const totalSk = data.data.totalSk;
                const totalBo = data.data.totalBo;
                const totalPenyelia = data.data.totalPenyelia;
                const total_semua = document.getElementById('total_semua');
                const total_filter = document.getElementById('total_filter');
                const total_selesai = document.getElementById('total_selesai');
                const total_arsip = document.getElementById('total_arsip');
                const total_bap = document.getElementById('total_bap');
                const total_kabid = document.getElementById('total_kabid');
                const total_ketua = document.getElementById('total_ketua');
                const total_sekretaris = document.getElementById('total_sekretaris');
                const total_sk = document.getElementById('total_sk');
                const total_kaban = document.getElementById('total_kaban');
                const total_bo = document.getElementById('total_bo');
                const total_penyelia = document.getElementById('total_penyelia');
                if (total_arsip) {
                    animateCounter(total_arsip, totalArsip);
                }
                if (total_bap) {
                    animateCounter(total_bap, totalBap);
                }
                if (total_kabid) {
                    animateCounter(total_kabid, totalKabid);
                }
                if (total_ketua) {
                    animateCounter(total_ketua, totalKetua);
                }
                if (total_sekretaris) {
                    animateCounter(total_sekretaris, totalSekretaris);
                }
                if (total_sk) {
                    animateCounter(total_sk, totalSk);
                }
                if (total_kaban) {
                    animateCounter(total_kaban, totalKaban);
                }
                if (total_bo) {
                    animateCounter(total_bo, totalBo);
                }
                if (total_penyelia) {
                    animateCounter(total_penyelia, totalPenyelia);
                }

                if (total_semua) {
                    animateCounter(total_semua, totalSemua);
                }
                if (total_filter) {
                    animateCounter(total_filter, totalFilter);
                }
                if (total_selesai) {
                    animateCounter(total_selesai, totalSelesai);
                }
            })
            .catch(error => {
                console.error('Error fetching calculation data:', error);
            });

        // Fungsi animasi counter sederhana
        function animateCounter(element, target) {
            let current = 0;
            const increment = Math.ceil(target / 50);
            const interval = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(interval);
                }
                element.textContent = current;
            }, 20); // ubah kecepatan sesuai selera
        }
    });


    const tableMonitoring = $('#usersTable');
    tableMonitoring.on('click', '.btn-survey', function(e) {
        e.preventDefault();
        const parent = e.target.closest('tr');

        var permohonanId = $(this).data('id-permohonan');
        console.log(permohonanId);
        $('#permohonanId').val(permohonanId)

        $('#add-survey').modal('show');
    });

    document.getElementById('form-add-survey').addEventListener('submit', function(event) {
        event.preventDefault();

        const $form = $(this);
        const $submitButton = $form.find(":submit");
        const originalButtonText = $submitButton.html();

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
                $('#usersTable').DataTable().ajax.reload(null,
                    false);
                $('#add-survey').modal('hide');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengirim data.');
            })
            .finally(() => {
                // Enable kembali tombol submit dan kembalikan teks
                $submitButton.prop('disabled', false).html(originalButtonText);
            });
    });
</script>
