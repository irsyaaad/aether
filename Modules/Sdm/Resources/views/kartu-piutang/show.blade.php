@extends('template.root')
@section('page-name', 'kartu-piutang')
@section('title-page', 'Detail Kartu Piutang')
@section('add-page')
    <a href="{{ route('kartu-piutang.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali
    </a>
    <a href="{{ route('kartu-piutang.cetak', $karyawan->id) }}" target="_blank" class="btn btn-info text-white shadow-sm">
        <i class="fa fa-print me-1"></i> Cetak
    </a>
    <a href="{{ route('kartu-piutang.export-excel', $karyawan->id) }}" class="btn btn-success shadow-sm">
        <i class="fa fa-file-excel-o me-1"></i> Export Excel
    </a>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card widget-1 shadow-sm border-0">
                    <div class="card-body">
                        <div class="widget-content">
                            <div class="widget-round secondary">
                                <div class="bg-round">
                                    <i class="fa fa-user fa-2x"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $karyawan->nama_lengkap }}</h5>
                                <span class="f-light">{{ $karyawan->jabatan }} | {{ $karyawan->divisi }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card widget-1 shadow-sm border-0 bg-danger-light">
                    <div class="card-body">
                        <div class="widget-content text-danger">
                            <div class="widget-round danger">
                                <div class="bg-round">
                                    <i class="fa fa-arrow-up fa-2x"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0">Rp {{ number_format($history->sum('debet'), 0, ',', '.') }}</h4>
                                <span class="f-light">Total Piutang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card widget-1 shadow-sm border-0 bg-success-light">
                    <div class="card-body">
                        <div class="widget-content text-success">
                            <div class="widget-round success">
                                <div class="bg-round">
                                    <i class="fa fa-arrow-down fa-2x"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0">Rp {{ number_format($history->sum('kredit'), 0, ',', '.') }}</h4>
                                <span class="f-light">Total Bayar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card widget-1 shadow-sm border-0 bg-primary-light">
                    <div class="card-body">
                        <div class="widget-content text-primary">
                            <div class="widget-round primary">
                                <div class="bg-round">
                                    <i class="fa fa-money fa-2x"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0">Rp
                                    {{ number_format($history->sum('debet') - $history->sum('kredit'), 0, ',', '.') }}</h4>
                                <span class="f-light">Sisa Piutang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom-0 pb-0 bg-white">
                        <h5 class="mb-3">Daftar Transaksi Piutang</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Debet (Pinjam)</th>
                                        <th class="text-end">Kredit (Bayar)</th>
                                        <th class="border-0 text-end">Saldo Running</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $runningSaldo = 0; @endphp
                                    @forelse($history as $index => $item)
                                        @php $runningSaldo += $item->debet - $item->kredit; @endphp
                                        <tr>
                                            <td class="f-light">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-calendar me-2 f-light"></i>
                                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td>{{ $item->nomor }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td class="text-end text-danger fw-bold">
                                                {{ $item->debet > 0 ? 'Rp ' . number_format($item->debet, 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="text-end text-success fw-bold">
                                                {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0, ',', '.') : '-' }}
                                            </td>
                                            <td
                                                class="text-end fw-bold {{ $runningSaldo > 0 ? 'text-primary' : 'text-success' }}">
                                                Rp {{ number_format($runningSaldo, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <img src="{{ asset('cuba/images/other-images/no-data.png') }}"
                                                    class="mb-3" style="max-width: 150px;">
                                                <p class="f-light">Belum ada transaksi piutang untuk karyawan ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.05);
        }

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.05);
        }

        .bg-primary-light {
            background-color: rgba(115, 102, 255, 0.05);
        }

        .widget-1 .widget-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .widget-round {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .widget-round.danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .widget-round.success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .widget-round.primary {
            background-color: rgba(115, 102, 255, 0.1);
        }

        .widget-round.secondary {
            background-color: rgba(243, 145, 43, 0.1);
        }

        .badge-light-primary {
            background-color: rgba(115, 102, 255, 0.1);
            color: #7366ff;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
        }
    </style>
@endsection
