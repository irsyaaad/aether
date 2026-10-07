@extends('template.root')
@section('page-name', 'payroll-slip')
@section('title-page', 'Detail Slip Gaji')
@section('content')
    <div class="col-md-12">
        <div class="row">
            <!-- Informasi Slip -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Slip Gaji - {{ $slip->nomor_slip }}</h6>
                            <div>
                                @if($slip->canBeEdited())
                                    <a href="{{ route('payroll-slip.edit', $slip->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('payroll-slip.print', $slip->id) }}" target="_blank" class="btn btn-info btn-sm">
                                    <i class="fa fa-print"></i> Print
                                </a>
                                <a href="{{ route('payroll-slip.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Informasi Karyawan -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary">Informasi Karyawan</h6>
                                <div class="row mb-2">
                                    <div class="col-5"><strong>Nama:</strong></div>
                                    <div class="col-7">{{ $slip->karyawan->nama_lengkap ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5"><strong>Jabatan:</strong></div>
                                    <div class="col-7">{{ $slip->karyawan->jabatan ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5"><strong>Periode:</strong></div>
                                    <div class="col-7">{{ $slip->payrollPeriod->nama_periode ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Informasi Kehadiran</h6>
                                <div class="row mb-2">
                                    <div class="col-5"><strong>Hari Kerja:</strong></div>
                                    <div class="col-7">{{ $slip->hari_kerja }} hari</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5"><strong>Hari Hadir:</strong></div>
                                    <div class="col-7">{{ $slip->hari_hadir }} hari</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5"><strong>Hari Alpha:</strong></div>
                                    <div class="col-7">{{ $slip->hari_alpha }} hari</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5"><strong>Jam Lembur:</strong></div>
                                    <div class="col-7">{{ $slip->jam_lembur }} jam</div>
                                </div>
                            </div>
                        </div>

                        <!-- Rincian Gaji -->
                        <h6 class="text-primary">Rincian Gaji</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Komponen</th>
                                        <th class="text-end">Nominal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Gaji Pokok -->
                                    <tr>
                                        <td><strong>Gaji Pokok</strong></td>
                                        <td class="text-end"><strong>Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</strong></td>
                                        <td>-</td>
                                    </tr>

                                    <!-- Tunjangan -->
                                    @php $allowances = $slip->details->where('component.jenis', 'allowance'); @endphp
                                    @if($allowances->count() > 0)
                                        <tr class="table-success">
                                            <td colspan="3"><strong>TUNJANGAN</strong></td>
                                        </tr>
                                        @foreach($allowances as $detail)
                                            <tr>
                                                <td>{{ $detail->component->nama_komponen }}</td>
                                                <td class="text-end text-success">+ Rp {{ number_format($detail->nilai, 0, ',', '.') }}</td>
                                                <td>{{ $detail->keterangan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    <!-- Potongan -->
                                    @php $deductions = $slip->details->where('component.jenis', 'deduction'); @endphp
                                    @if($deductions->count() > 0)
                                        <tr class="table-danger">
                                            <td colspan="3"><strong>POTONGAN</strong></td>
                                        </tr>
                                        @foreach($deductions as $detail)
                                            <tr>
                                                <td>{{ $detail->component->nama_komponen }}</td>
                                                <td class="text-end text-danger">- Rp {{ number_format($detail->nilai, 0, ',', '.') }}</td>
                                                <td>{{ $detail->keterangan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    <!-- Total -->
                                    <tr class="table-light">
                                        <td><strong>Gaji Kotor</strong></td>
                                        <td class="text-end"><strong>Rp {{ number_format($slip->gaji_kotor, 0, ',', '.') }}</strong></td>
                                        <td>-</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>GAJI BERSIH</strong></td>
                                        <td class="text-end"><strong>Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}</strong></td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if($slip->catatan)
                            <div class="mt-3">
                                <h6 class="text-primary">Catatan</h6>
                                <p class="text-muted">{{ $slip->catatan }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status dan Aksi -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Status & Aksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            {!! $slip->status_badge !!}
                        </div>

                        @if($slip->status === 'draft')
                            <button class="btn btn-success btn-sm w-100 mb-2 btn-approve" data-id="{{ $slip->id }}">
                                <i class="fa fa-check"></i> Approve Slip
                            </button>
                        @endif

                        @if($slip->status === 'approved' && auth()->user()->can('payroll-slip.pay'))
                            <button class="btn btn-primary btn-sm w-100 mb-2 btn-pay" data-id="{{ $slip->id }}">
                                <i class="fa fa-money-bill"></i> Mark as Paid
                            </button>
                        @endif

                        @if($slip->approver)
                            <div class="mb-2">
                                <small class="text-muted">Disetujui oleh:</small><br>
                                <strong>{{ $slip->approver->nm_user }}</strong>
                            </div>
                        @endif
                        
                        @if($slip->approved_at)
                            <div class="mb-2">
                                <small class="text-muted">Tanggal approve:</small><br>
                                {{ $slip->approved_at->format('d F Y H:i') }}
                            </div>
                        @endif

                        @if($slip->tanggal_bayar)
                            <div class="mb-2">
                                <small class="text-muted">Tanggal bayar:</small><br>
                                {{ $slip->tanggal_bayar->format('d F Y') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ringkasan Gaji -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Ringkasan</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Gaji Pokok:</span>
                            <strong>Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Total Tunjangan:</span>
                            <strong>+ Rp {{ number_format($slip->total_tunjangan, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Total Potongan:</span>
                            <strong>- Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span><strong>Gaji Bersih:</strong></span>
                            <strong class="text-primary">Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Tambahan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Dibuat oleh:</small><br>
                            {{ $slip->creator->nm_user ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Tanggal dibuat:</small><br>
                            {{ $slip->created_at->format('d F Y H:i') }}
                        </div>
                        @if($slip->updated_at != $slip->created_at)
                            <div class="mb-2">
                                <small class="text-muted">Terakhir diupdate:</small><br>
                                {{ $slip->updated_at->format('d F Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Approve functionality
            $('.btn-approve').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                
                Swal.fire({
                    title: 'Approve Slip Gaji?',
                    text: "Slip gaji akan disetujui dan tidak dapat diedit lagi",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Approve!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('payroll-slip.approve', $slip->id) }}";
                    }
                });
            });

            // Mark as paid functionality
            $('.btn-pay').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                
                Swal.fire({
                    title: 'Tandai Sebagai Dibayar?',
                    text: "Slip gaji akan ditandai sebagai sudah dibayar",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tandai!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('payroll-slip.mark-as-paid', $slip->id) }}";
                    }
                });
            });
        });
    </script>
    @endpush
@endsection