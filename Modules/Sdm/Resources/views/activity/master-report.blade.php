@extends('template.root')
@section('page-name', 'Master Laporan Aktivitas')
@section('title-page', 'Master Laporan per Jabatan')
@section('style')
    <style>
        .report-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .table-premium thead th {
            background-color: #f8f9fc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #4e73df;
            border-bottom: 2px solid #e3e6f0;
        }

        .badge-fixed {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .badge-approved {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .btn-confirm {
            border-radius: 20px;
            padding: 5px 15px;
            font-weight: 600;
        }

        .tab-content-custom {
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="report-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">Halo, {{ auth()->user()->nm_user }}!</h2>
                <p class="mb-0 opacity-75">Laporan untuk jabatan: <strong
                        class="text-white">{{ strtoupper($jabatan) }}</strong> | Tanggal:
                    {{ date('d F Y', strtotime($date)) }}</p>
            </div>
            <div class="col-md-4">
                <form action="{{ route('employee-activities.master-report') }}" method="GET" class="d-flex gap-2">
                    <input type="date" name="date" class="form-control" value="{{ $date }}"
                        onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>

    @if (str_contains($jabatan, 'operasional'))
        <!-- SEGMENT OPERASIONAL -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase small">Total DO Hari Ini</h6>
                        <h3>{{ count($dos) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase small">DO Fixed (Staff)</h6>
                        <h3>{{ $dos->where('is_fixed', true)->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase small">DO Approved (PIC)</h6>
                        <h3>{{ $dos->where('is_approved_ops', true)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card stat-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 font-weight-bold text-primary">Daftar Delivery Order</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-premium table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Kode DO</th>
                                <th>Customer</th>
                                <th>Nopol</th>
                                <th>Status Fixed</th>
                                <th>Status Approved</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dos as $do)
                                <tr>
                                    <td class="fw-bold">{{ $do->kode_do }}</td>
                                    <td>{{ $do->customer->nm_customer ?? '-' }}</td>
                                    <td>{{ $do->armada->nopol ?? ($do->nopol_dipakai ?? '-') }}</td>
                                    <td>
                                        @if ($do->is_fixed)
                                            <span class="badge badge-fixed"><i class="fa fa-check-circle"></i> Fixed</span>
                                            <br><small class="text-muted">{{ $do->fixed_at }}</small>
                                        @else
                                            <span class="badge bg-light text-muted">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($do->is_approved_ops)
                                            <span class="badge badge-approved"><i class="fa fa-check-double"></i>
                                                Approved</span>
                                            <br><small class="text-muted">{{ $do->approved_ops_at }}</small>
                                        @else
                                            <span class="badge bg-light text-muted">Waiting</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($jabatan == 'staff operasional' && !$do->is_fixed)
                                            <button onclick="fixDo({{ $do->id }})"
                                                class="btn btn-sm btn-outline-primary btn-confirm">FIX OK</button>
                                        @elseif(str_contains($jabatan, 'pic') && $do->is_fixed && !$do->is_approved_ops)
                                            <button onclick="approveDo({{ $do->id }})"
                                                class="btn btn-sm btn-success btn-confirm">APPROVE</button>
                                        @else
                                            <i class="fa fa-minus text-muted"></i>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada DO untuk tanggal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if (str_contains($jabatan, 'pic'))
            <!-- PIC Specific Section: Daily Plan -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="m-0 font-weight-bold text-primary"><i class="fa fa-truck me-2"></i> Posisi Truk &
                                Muat/Bongkar</h5>
                        </div>
                        <div class="card-body">
                            <textarea id="truck_positions" class="form-control" rows="5"
                                placeholder="Contoh: Truck A muat di SDA, Truck B bongkar di JKT...">{{ $daily_plan->truck_positions ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="m-0 font-weight-bold text-primary"><i class="fa fa-calendar-alt me-2"></i> Rencana DO
                                Besok</h5>
                        </div>
                        <div class="card-body">
                            <textarea id="tomorrow_plan" class="form-control" rows="5"
                                placeholder="Contoh: Rencana 3 unit ke Tangerang, 2 unit ke Bandung...">{{ $daily_plan->tomorrow_plan ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button onclick="saveDailyPlan()" class="btn btn-primary px-5 shadow-sm">SIMPAN LAPORAN PIC OD</button>
            </div>
        @endif
    @elseif (str_contains($jabatan, 'keuangan') || str_contains($jabatan, 'admin'))
        <!-- SEGMENT KEUANGAN -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-success text-uppercase small">Kas Masuk</h6>
                        <h3>Rp {{ number_format($kas_masuk->sum('nominal'), 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-start border-danger border-4">
                    <div class="card-body">
                        <h6 class="text-danger text-uppercase small">Kas Keluar</h6>
                        <h3>Rp {{ number_format($kas_keluar->sum('nominal'), 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-primary text-uppercase small">Invoice Tagihan</h6>
                        <h3>{{ count($invoices) }} Inv</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-kas-tab" data-bs-toggle="pill" data-bs-target="#pills-kas"
                            type="button">Kas & Bank</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-inv-tab" data-bs-toggle="pill" data-bs-target="#pills-inv"
                            type="button">Penagihan (Invoice)</button>
                    </li>
                </ul>
                <div class="tab-content tab-content-custom" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-kas">
                        <div class="table-responsive">
                            <table class="table table-premium">
                                <thead>
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        <th>Keterangan</th>
                                        <th>Penerima/Penyetor</th>
                                        <th class="text-end">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kas_masuk as $km)
                                        <tr>
                                            <td class="text-success">BM - {{ $km->kode_kas_bank_masuk }}</td>
                                            <td>{{ $km->keterangan }}</td>
                                            <td>{{ $km->nama_penerima }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($km->nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach ($kas_keluar as $kk)
                                        <tr>
                                            <td class="text-danger">BK - {{ $kk->kode_kas_bank_keluar }}</td>
                                            <td>{{ $kk->keterangan }}</td>
                                            <td>{{ $kk->nama_penerima }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($kk->nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-inv">
                        <div class="table-responsive">
                            <table class="table table-premium">
                                <thead>
                                    <tr>
                                        <th>No Invoice</th>
                                        <th>Customer</th>
                                        <th>Grand Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $inv)
                                        <tr>
                                            <td class="fw-bold">{{ $inv->nomor_invoice }}</td>
                                            <td>{{ $inv->customer->nm_customer ?? '-' }}</td>
                                            <td>Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                                            <td><span
                                                    class="badge bg-{{ $inv->status == 'lunas' ? 'success' : 'warning' }}">{{ $inv->status }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- DEFAULT SEGMENT: Activities (Existing Activity Log) -->
        <div class="card stat-card">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 font-weight-bold text-primary">Laporan Aktivitas Harian</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Gunakan menu <strong>Aktivitas Karyawan</strong> untuk menginput laporan harian Anda
                    dengan lampiran foto.</p>
                <a href="{{ route('employee-activities.index') }}" class="btn btn-primary">Buka Laporan Aktivitas</a>
            </div>
        </div>
    @endif

@endsection

@section('script')
    <script>
        function fixDo(id) {
            if (!confirm('Konfirmasi bahwa DO ini sudah FIXED?')) return;
            $.post("{{ route('employee-activities.fix-do') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status == 'success') location.reload();
                else alert(res.message);
            });
        }

        function approveDo(id) {
            if (!confirm('Konfirmasi APPROVE untuk DO ini?')) return;
            $.post("{{ route('employee-activities.approve-do') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status == 'success') location.reload();
                else alert(res.message);
            });
        }

        function saveDailyPlan() {
            const data = {
                date: "{{ $date }}",
                tomorrow_plan: $('#tomorrow_plan').val(),
                truck_positions: $('#truck_positions').val(),
                _token: "{{ csrf_token() }}"
            };
            $.post("{{ route('employee-activities.save-daily-plan') }}", data, function(res) {
                if (res.status == 'success') alert(res.message);
                else alert(res.message);
            });
        }
    </script>
@endsection
