<div class="card mt-3">
    <div class="card-body">
        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Nama Perusahaan</label>
                <p>{{ $perusahaan->nama_perusahaan }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Pemilik</label>
                <p>{{ $perusahaan->pemilik }}</p>
            </div>

            <div class="col-md-12 mb-3">
                <label class="fw-bold">Alamat</label>
                <p>{{ $perusahaan->alamat }}</p>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-bold">Kota</label>
                <p>{{ $perusahaan->kota }}</p>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-bold">Telepon</label>
                <p>{{ $perusahaan->telp }}</p>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-bold">Email</label>
                <p>{{ $perusahaan->email }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">NPWP</label>
                <p>{{ $perusahaan->npwp }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Status</label>
                <p>{!! $perusahaan->status == 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>' !!}
                </p>
            </div>

            <div class="col-md-12 mb-3">
                <label class="fw-bold">Keterangan</label>
                <p>{{ $perusahaan->keterangan }}</p>
            </div>

            <div class="col-md-12">
                <div class="mt-4">
                    <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-file-contract me-2"></i> Dokumen Perusahaan
                    </h6>
                    <div class="row g-4">
                        @foreach (['company_profile' => 'Company Profile', 'akta_perusahaan' => 'Akta Perusahaan', 'visi_misi' => 'Visi Misi'] as $field => $label)
                            <div class="col-md-6">
                                <div class="card shadow-none border mb-0 h-100 doc-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="doc-icon-wrapper me-3">
                                                @php
                                                    $ext = pathinfo($perusahaan->$field, PATHINFO_EXTENSION);
                                                    $bgClass = 'bg-light-primary text-primary';
                                                    $icon = 'fa-file-alt';
                                                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $icon = 'fa-file-image';
                                                        $bgClass = 'bg-light-success text-success';
                                                    } elseif (strtolower($ext) == 'pdf') {
                                                        $icon = 'fa-file-pdf';
                                                        $bgClass = 'bg-light-danger text-danger';
                                                    }
                                                @endphp
                                                <div class="doc-icon {{ $bgClass }} d-flex align-items-center justify-content-center rounded"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="fas {{ $icon }} fa-lg"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="mb-1 text-dark fw-bold text-truncate">{{ $label }}
                                                </h6>
                                                @if ($perusahaan->$field)
                                                    <p class="mb-0 text-muted small text-truncate">
                                                        {{ basename($perusahaan->$field) }}
                                                    </p>
                                                @else
                                                    <p class="mb-0 text-secondary small italic">Belum ada file</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($perusahaan->$field)
                                            <div class="mt-3 d-flex gap-2">
                                                <a href="{{ Storage::url($perusahaan->$field) }}" target="_blank"
                                                    class="btn btn-primary btn-sm flex-grow-1">
                                                    <i class="fas fa-eye me-1"></i> Preview
                                                </a>
                                                <a href="{{ Storage::url($perusahaan->$field) }}" download
                                                    class="btn btn-outline-secondary btn-sm px-3">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <style>
                .doc-card {
                    transition: all 0.3s ease;
                }

                .doc-card:hover {
                    border-color: #7366ff !important;
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
                }

                .bg-light-primary {
                    background-color: rgba(115, 102, 255, 0.1);
                }

                .bg-light-success {
                    background-color: rgba(81, 187, 37, 0.1);
                }

                .bg-light-danger {
                    background-color: rgba(220, 53, 69, 0.1);
                }
            </style>

        </div>
    </div>
</div>
