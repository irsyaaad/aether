@extends('template.root')
@section('page-name', 'Laporan Harian')
@section('title-page', 'Detail Laporan Harian')

@section('style')
<style>
    .info-card {
        transition: transform .15s ease, box-shadow .15s ease;
        border-radius: .625rem;
        overflow: hidden;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important;
    }
    .stat-icon {
        width: 42px; height: 42px;
        display: flex; align-items: center; justify-content: center;
        border-radius: .5rem;
        font-size: 1.1rem;
    }
    .section-header {
        display: flex; align-items: center; gap: .5rem;
        padding: .625rem 1rem;
        border-radius: .5rem .5rem 0 0;
        font-size: .9rem; font-weight: 600;
        letter-spacing: .02em;
    }
    .badge-status {
        font-size: .75rem; padding: .3rem .65rem;
        border-radius: 50rem;
        font-weight: 500;
    }
    .table th { font-weight: 600; font-size: .8rem; text-transform: uppercase; letter-spacing: .03em; }
    .table td { vertical-align: middle; }
    .summary-text { font-size: .95rem; line-height: 1.6; }
    .meta-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: rgba(255,255,255,.75); }
    .meta-value { font-size: 1rem; font-weight: 600; color: #fff; }
    .info-box {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: .5rem;
        padding: 1rem;
        height: 100%;
        transition: border-color .15s ease;
    }
    .info-box:hover { border-color: #ced4da; }
    .info-box .value {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
    }
    .gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-info { background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); }
    .gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .gradient-warning { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
    .gradient-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
    .gradient-dark { background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); }
    .border-left-primary { border-left: 4px solid #667eea; }
    .border-left-success { border-left: 4px solid #11998e; }
    .border-left-warning { border-left: 4px solid #f2994a; }
    .border-left-info { border-left: 4px solid #2193b0; }
    .border-left-danger { border-left: 4px solid #eb3349; }
</style>
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        {{-- HEADER --}}
        <div class="card-header gradient-dark d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 text-white"><i class="fa fa-clipboard-list me-2"></i>Detail Laporan Harian</h5>
            </div>
            <div class="d-flex gap-2">
                @if ($report->user_id === Auth::id() || check_access('daily-report.edit'))
                    <a href="{{ route('daily-report.edit', $report->id) }}" class="btn btn-light btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ route('daily-report.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- INFO BAR --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="info-box d-flex align-items-center gap-3 border-left-primary">
                        <div class="stat-icon" style="background: #eef1ff;">
                            <i class="fa fa-calendar text-primary"></i>
                        </div>
                        <div>
                            <div class="meta-label" style="color: #6c757d;">Tanggal</div>
                            <div class="value">{{ $report->report_date->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box d-flex align-items-center gap-3 border-left-info">
                        <div class="stat-icon" style="background: #e3f2fd;">
                            <i class="fa fa-user text-info"></i>
                        </div>
                        <div>
                            <div class="meta-label" style="color: #6c757d;">Karyawan</div>
                            <div class="value">{{ $report->user->nm_user ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box d-flex align-items-center gap-3 border-left-warning">
                        <div class="stat-icon" style="background: #fff8e1;">
                            <i class="fa fa-user-tag text-warning"></i>
                        </div>
                        <div>
                            <div class="meta-label" style="color: #6c757d;">Role</div>
                            <div>
                                <span class="badge bg-info fs-6">
                                    {{ ucwords(str_replace('_', ' ', $report->role_type)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box d-flex align-items-center gap-3 border-left-success">
                        <div class="stat-icon" style="background: #e6f7e6;">
                            <i class="fa fa-list-check text-success"></i>
                        </div>
                        <div>
                            <div class="meta-label" style="color: #6c757d;">Total Item</div>
                            <div class="value">{{ $report->items->count() }} item</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RINGKASAN --}}
            @if ($report->summary)
                <div class="card border-0 mb-4 info-card" style="background: linear-gradient(135deg, #e8f0fe 0%, #f3e8ff 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="stat-icon" style="background: rgba(102,126,234,.15); flex-shrink:0;">
                                <i class="fa fa-file-lines text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-2 fw-semibold text-primary"><i class="fa fa-file-text me-1"></i>Ringkasan Laporan</h6>
                                <p class="mb-0 summary-text" style="color: #212529;">{{ $report->summary }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- CONTENT PER ROLE --}}
            @if ($report->items->count() > 0)

                {{-- ===================== PIC OPERASIONAL ===================== --}}
                @if ($report->role_type === 'pic_operasional')
                    @php
                        $problemItems = $report->items->filter(fn($i) => in_array($i->description, ['DO', 'Bengkel', 'Legalitas', 'Customer']));
                        $detailDoItems = $report->items->filter(fn($i) => str_starts_with($i->description, 'Detail DO Siap Muat') || str_starts_with($i->description, 'pic_detail_do_'));
                        $doTidakTermuatItems = $report->items->filter(fn($i) => str_starts_with($i->description, 'DO Tidak Termuat') || str_starts_with($i->description, 'pic_do_tidak_termuat_'));
                        $keuanganOperasionalItems = $report->items->filter(fn($i) => str_starts_with($i->description, 'Keuangan Operasional') || str_starts_with($i->description, 'pic_keuangan_'));
                        $armadaStatusItems = $report->items->where('type', 'armada_status');
                        $armadaRusakItems = $report->items->where('type', 'armada_rusak');
                        $rusakMap = [];
                        foreach ($armadaRusakItems as $rusakItem) { $rusakMap[$rusakItem->description] = $rusakItem; }
                        $sdmItems = $report->items->filter(fn($i) => in_array($i->description, ['Total driver aktif','Unit tanpa driver','Interview hari ini','Solusi rekrutmen driver']));
                        $driverIjinItems = $report->items->where('type', 'driver_ijin');
                        $complianceItems = $report->items->filter(fn($i) => in_array($i->description, ['DO belum kembali','DO tidak lengkap','Status serah terima keamanan','Lampiran log book keamanan']));
                        $growthItems = $report->items->filter(fn($i) => in_array($i->description, ['Customer baru','Jumlah komplain','Peluang growth']));
                        $customerBaruItems = $report->items->where('type', 'customer_baru');
                        $komplainItems = $report->items->where('type', 'komplain');
                        $perkiraanSangu = $report->items->where('description', 'Total perkiraan sangu')->first();
                        $penyelesaianRusak = $report->items->where('description', 'Penyelesaian armada rusak')->first();
                        $problemSolving = $report->items->where('description', 'Problem Solving Target DO')->first();
                        $doStatusItems = $report->items->where('type', 'do_status');
                        $prioritasHariIni = $report->items->where('description', '3 prioritas hari ini')->first();
                        $prioritasBesok = $report->items->where('description', '3 prioritas besok')->first();
                        $statusOps = $report->items->where('description', 'Status operasional hari ini')->first();
                    @endphp

                    {{-- 1. PROBLEM & SOLUSI --}}
                    @if ($problemItems->count() > 0)
                        @php
                            $problemMeta = [
                                'DO' => ['icon' => 'fa-file-invoice', 'color' => '#fd7e14', 'bg' => '#fff3e0', 'label' => 'Problem DO'],
                                'Bengkel' => ['icon' => 'fa-wrench', 'color' => '#17a2b8', 'bg' => '#e3f2fd', 'label' => 'Problem Bengkel'],
                                'Legalitas' => ['icon' => 'fa-gavel', 'color' => '#6610f2', 'bg' => '#f3e8ff', 'label' => 'Problem Legalitas'],
                                'Customer' => ['icon' => 'fa-headset', 'color' => '#dc3545', 'bg' => '#fde8e8', 'label' => 'Problem Customer'],
                            ];
                        @endphp
                        <div class="card mb-4 shadow-sm info-card" style="border-color: #f5c6cb;">
                            <div class="section-header gradient-danger text-white">
                                <i class="fa fa-exclamation-triangle"></i> Problem & Solusi
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach ($problemItems as $item)
                                        @php
                                            $notes = json_decode($item->notes, true) ?: [];
                                            $meta = $problemMeta[$item->description] ?? ['icon' => 'fa-circle', 'color' => '#6c757d', 'bg' => '#f8f9fa', 'label' => $item->description];
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3 h-100" style="background: {{ $meta['bg'] }}; border-left: 4px solid {{ $meta['color'] }};">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <i class="fa {{ $meta['icon'] }}" style="color: {{ $meta['color'] }}; font-size: 1rem;"></i>
                                                    <strong style="color: {{ $meta['color'] }};">{{ $meta['label'] }}</strong>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <small class="d-block" style="color: #6c757d;">Problem</small>
                                                        <span style="color:#212529;">{{ $notes['problem'] ?? '-' }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="d-block" style="color: #6c757d;">Solusi</small>
                                                        <span style="color:#212529;">{{ $notes['solusi'] ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 2. DO STATISTIK --}}
                    @php
                        $doStats = [
                            ['label' => 'Total DO Hari Ini', 'key' => 'Total DO hari ini', 'icon' => 'fa-list', 'color' => 'primary', 'gradient' => 'gradient-primary'],
                            ['label' => 'DO Siap Muat', 'key' => 'Total DO siap muat', 'icon' => 'fa-check', 'color' => 'success', 'gradient' => 'gradient-success'],
                            ['label' => 'DO Terkirim', 'key' => 'Total DO terkirim', 'icon' => 'fa-truck', 'color' => 'info', 'gradient' => 'gradient-info'],
                            ['label' => 'DO Pending', 'key' => 'Total DO pending', 'icon' => 'fa-clock', 'color' => 'warning', 'gradient' => 'gradient-warning'],
                            ['label' => 'Total Ritase', 'key' => 'Total ritase', 'icon' => 'fa-rotate', 'color' => 'secondary', 'gradient' => 'bg-secondary'],
                        ];
                    @endphp
                    <div class="card mb-4 shadow-sm info-card">
                        <div class="section-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                            <i class="fa fa-chart-simple"></i> Statistik Operasional
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($doStats as $idx => $stat)
                                    @php $item = $report->items->where('description', $stat['key'])->first(); @endphp
                                    <div class="col-md-3 col-6">
                                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 h-100" style="background: #f8f9fa; border-left: 4px solid var(--bs-{{ $stat['color'] }});">
                                            <div class="stat-icon rounded-3 flex-shrink-0 {{ $stat['gradient'] }}" style="color: #fff;">
                                                <i class="{{ $stat['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <div class="fs-3 fw-bold" style="color: #212529;">{{ $item->notes ?? '0' }}</div>
                                                <div class="small" style="color: #6c757d;">{{ $stat['label'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 3. PROBLEM SOLVING --}}
                    @if ($problemSolving && $problemSolving->notes)
                        <div class="card mb-4 shadow-sm info-card">
                            <div class="section-header gradient-info text-white">
                                <i class="fa fa-lightbulb"></i> Problem Solving Target DO
                            </div>
                            <div class="card-body">
                                <p class="mb-0" style="color:#212529;">{{ $problemSolving->notes }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- 3b. DO STATUS --}}
                    @if ($doStatusItems->count() > 0)
                        <div class="card mb-4 shadow-sm info-card" style="border-color: #b8daff;">
                            <div class="section-header gradient-info text-white">
                                <i class="fa fa-check-double"></i> Status DO
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr><th>No DO</th><th>Status</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($doStatusItems as $item)
                                                @php
                                                    $statusLabel = ucfirst($item->notes ?: 'dibuat');
                                                    $badgeClass = match($item->notes) {
                                                        'dibuat' => 'bg-secondary',
                                                        'berangkat' => 'bg-primary',
                                                        'muat' => 'bg-warning text-dark',
                                                        'bongkar' => 'bg-success',
                                                        default => 'bg-secondary',
                                                    };
                                                    $customer = $item->doReference->customer->nama ?? '-';
                                                    $trayek = $item->doReference->trayek->nama_trayek ?? '';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="fw-semibold" style="color:#212529;">{{ $item->description }}</span>
                                                        <br><small style="color:#6c757d;">{{ $customer }}{{ $trayek ? ' - '.$trayek : '' }}</small>
                                                    </td>
                                                    <td><span class="badge-status {{ $badgeClass }}">{{ $statusLabel }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 4. DETAIL DO + DO TIDAK TERMUAT (side-by-side) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            @if ($detailDoItems->count() > 0)
                                <div class="card shadow-sm info-card h-100" style="border-color: #c3e6cb;">
                                    <div class="section-header gradient-success text-white">
                                        <i class="fa fa-truck-loading"></i> Detail DO Siap Muat
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr><th>Customer</th><th>Tujuan</th><th>Qty</th><th>Status</th><th>Kendaraan</th></tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($detailDoItems as $item)
                                                        @php $notes = json_decode($item->notes, true) ?: []; @endphp
                                                        <tr>
                                                            <td class="fw-semibold" style="color:#212529;">{{ $notes['customer'] ?? '-' }}</td>
                                                            <td style="color:#212529;">{{ $notes['tujuan'] ?? '-' }}</td>
                                                            <td class="text-center fw-bold" style="color:#212529;">{{ $notes['qty'] ?? '0' }}</td>
                                                            <td>
                                                                @php
                                                                    $muat = $notes['status_muat'] ?? '-';
                                                                    $badgeClass = match($muat) {
                                                                        'Siap Muat' => 'bg-success',
                                                                        'Sedang Muat' => 'bg-warning text-dark',
                                                                        'Selesai Muat' => 'bg-info text-white',
                                                                        'Batal' => 'bg-danger',
                                                                        default => 'bg-secondary',
                                                                    };
                                                                @endphp
                                                                <span class="badge-status {{ $badgeClass }}">{{ $muat }}</span>
                                                            </td>
                                                            <td style="color:#212529;">{{ $notes['kendaraan'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            @if ($doTidakTermuatItems->count() > 0)
                                <div class="card shadow-sm info-card h-100" style="border-color: #f5c6cb;">
                                    <div class="section-header gradient-danger text-white">
                                        <i class="fa fa-ban"></i> DO Tidak Termuat
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr><th>DO #</th><th>Alasan</th><th>Solusi</th></tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($doTidakTermuatItems as $item)
                                                        @php $notes = json_decode($item->notes, true) ?: []; @endphp
                                                        <tr>
                                                            <td class="fw-semibold" style="color:#212529;">{{ $item->doReference->kode_do ?? 'N/A' }}</td>
                                                            <td style="color:#dc3545;">{{ $notes['alasan'] ?? '-' }}</td>
                                                            <td style="color:#28a745;">{{ $notes['solusi'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 5. MONITORING ARMADA --}}
                    @if ($armadaStatusItems->count() > 0)
                        <div class="card mb-4 shadow-sm info-card" style="border-color: #ffc107;">
                            <div class="section-header gradient-warning text-dark">
                                <i class="fa fa-truck"></i> Monitoring Armada
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nopol</th>
                                                <th class="text-center">Status</th>
                                                <th>Kerusakan</th>
                                                <th>PIC Bengkel</th>
                                                <th>Estimasi</th>
                                                <th>Driver</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($armadaStatusItems as $item)
                                                @php
                                                    $status = $item->notes ?? '-';
                                                    $badgeClass = match($status) {
                                                        'jalan' => 'bg-success', 'stand_by' => 'bg-secondary', 'rusak' => 'bg-danger',
                                                        default => 'bg-secondary',
                                                    };
                                                    $badgeLabel = match($status) {
                                                        'jalan' => 'Jalan', 'stand_by' => 'Stand By', 'rusak' => 'Rusak',
                                                        default => '-',
                                                    };
                                                    $rusakItem = $rusakMap[$item->description] ?? null;
                                                    $rusakDetails = $rusakItem ? (is_array($rusakItem->notes) ? $rusakItem->notes : json_decode($rusakItem->notes, true)) : null;
                                                    $armadaNote = $report->items->where('type', 'armada')->where('description', 'Pengecekan ' . $item->description)->first();
                                                    $driverStatus = '-';
                                                    if ($armadaNote && $armadaNote->notes) {
                                                        preg_match('/Sopir: (.*?)(?: \||$)/', $armadaNote->notes, $m);
                                                        $driverStatus = ($m[1] ?? '') === 'ada' ? 'Ada' : (($m[1] ?? '') === 'tidak_ada' ? 'Tidak Ada' : '-');
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold" style="color:#212529;">{{ $item->description }}</td>
                                                    <td class="text-center"><span class="badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                                                    <td style="color:#6c757d;">{{ $status === 'rusak' ? ($rusakDetails['kerusakan'] ?? '-') : '-' }}</td>
                                                    <td style="color:#212529;">{{ $status === 'rusak' ? ($rusakDetails['pic_bengkel'] ?? '-') : '-' }}</td>
                                                    <td style="color:#212529;">{{ $status === 'rusak' ? ($rusakDetails['estimasi'] ?? '-') : '-' }}</td>
                                                    <td><span class="badge-status bg-{{ $driverStatus === 'Ada' ? 'success' : ($driverStatus === 'Tidak Ada' ? 'danger' : 'secondary') }}">{{ $driverStatus }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if ($penyelesaianRusak && $penyelesaianRusak->notes)
                                <div class="card-footer bg-light">
                                    <small style="color:#6c757d;"><i class="fa fa-wrench text-warning me-1"></i> <strong>Penyelesaian Armada Rusak:</strong> <span style="color:#212529;">{{ $penyelesaianRusak->notes }}</span></small>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- 6. KEUANGAN + SDM (side-by-side) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            @if ($keuanganOperasionalItems->count() > 0)
                                <div class="card shadow-sm info-card h-100" style="border-color: #a3d9a5;">
                                    <div class="section-header gradient-success text-white">
                                        <i class="fa fa-wallet"></i> Keuangan Operasional
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr><th style="width:40px">No</th><th>Kode DO</th><th>Tujuan</th><th class="text-end">Nominal Sangu</th></tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($keuanganOperasionalItems as $i => $item)
                                                        @php $notes = $item->notes ? (json_decode($item->notes, true) ?: []) : []; @endphp
                                                        <tr>
                                                            <td style="color:#212529;">{{ $i + 1 }}</td>
                                                            <td style="color:#212529;">{{ $notes['kode_do'] ?? '-' }}</td>
                                                            <td style="color:#212529;">{{ $notes['tujuan'] ?? '-' }}</td>
                                                            <td class="text-end fw-medium" style="color:#212529;">{{ $notes['nominal_sangu'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @if ($perkiraanSangu && $perkiraanSangu->notes)
                                        <div class="card-footer bg-light">
                                            <small style="color:#6c757d;"><i class="fa fa-calculator text-success me-1"></i> <strong>Total Perkiraan Sangu:</strong> <span style="color:#212529;">{{ $perkiraanSangu->notes }}</span></small>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            @if ($sdmItems->count() > 0)
                                <div class="card shadow-sm info-card h-100" style="border-color: #b8daff;">
                                    <div class="section-header gradient-info text-white">
                                        <i class="fa fa-users"></i> SDM & Driver
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            @foreach ($sdmItems as $item)
                                                @php
                                                    [$sdmIcon, $sdmColor, $sdmBg] = match($item->description) {
                                                        'Total driver aktif' => ['fa-user-check', '#28a745', '#e6f7e6'],
                                                        'Unit tanpa driver' => ['fa-user-slash', '#dc3545', '#fde8e8'],
                                                        'Interview hari ini' => ['fa-handshake', '#fd7e14', '#fff3e0'],
                                                        'Solusi rekrutmen driver' => ['fa-bullhorn', '#6c757d', '#f8f9fa'],
                                                        default => ['fa-user', '#6c757d', '#f8f9fa'],
                                                    };
                                                @endphp
                                                @if ($item->description === 'Solusi rekrutmen driver')
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-start gap-2 p-3 rounded-2" style="background: #f8f9fa; border-left: 3px solid {{ $sdmColor }};">
                                                            <i class="fa {{ $sdmIcon }}" style="color: {{ $sdmColor }}; margin-top: 2px;"></i>
                                                            <div>
                                                                <small class="d-block" style="color: #6c757d;">{{ $item->description }}</small>
                                                                <span style="color:#212529;">{{ $item->notes ?: '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-4">
                                                        <div class="text-center p-3 rounded-3" style="background: {{ $sdmBg }};">
                                                            <i class="fa {{ $sdmIcon }} mb-2 d-block fs-4" style="color: {{ $sdmColor }};"></i>
                                                            <div class="fs-3 fw-bold" style="color: {{ $sdmColor }};">{{ $item->notes ?: '0' }}</div>
                                                            <small style="color: #6c757d;">{{ $item->description }}</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @if ($driverIjinItems->count() > 0)
                                        <div class="mt-3 px-3 pb-3">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr><th>Nama Sopir</th><th>Nopol Truk</th><th>Solusi</th></tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($driverIjinItems as $item)
                                                        @php $d = $item->notes ? (json_decode($item->notes, true) ?: []) : []; @endphp
                                                        <tr>
                                                            <td>{{ $d['nama_sopir'] ?? '-' }}</td>
                                                            <td>{{ $d['nopol'] ?? '-' }}</td>
                                                            <td>{{ $d['solusi'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 7. SISTEM & KEPATUHAN + CUSTOMER (side-by-side) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            @if ($complianceItems->count() > 0)
                                <div class="card shadow-sm info-card h-100" style="border-color: #d6d8db;">
                                    <div class="section-header" style="background: linear-gradient(135deg, #2c3e50 0%, #495057 100%); color: #fff;">
                                        <i class="fa fa-shield"></i> Sistem & Kepatuhan
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <tbody>
                                                @foreach ($complianceItems as $item)
                                                    @php
                                                        $compIcon = match($item->description) {
                                                            'DO belum kembali' => 'fa-file-circle-exclamation',
                                                            'DO tidak lengkap' => 'fa-file-circle-minus',
                                                            'Status serah terima keamanan' => 'fa-shield-check',
                                                            'Lampiran log book keamanan' => 'fa-book',
                                                            default => 'fa-circle',
                                                        };
                                                    @endphp
                                                    <tr>
                                                        <th width="40%" class="bg-light ps-3 fw-normal" style="color:#495057;"><i class="fa {{ $compIcon }}" style="color:#6c757d; margin-right: 8px;"></i>{{ $item->description }}</th>
                                                        <td class="fw-medium" style="color:#212529;">{{ $item->notes ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            @if ($growthItems->count() > 0 || $customerBaruItems->count() > 0 || $komplainItems->count() > 0)
                                <div class="card shadow-sm info-card h-100" style="border-color: #b8daff;">
                                    <div class="section-header gradient-primary text-white">
                                        <i class="fa fa-chart-line"></i> Customer & Pengembangan
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover mb-0">
                                            <tbody>
                                                @foreach ($growthItems as $item)
                                                    @php
                                                        $gIcon = $item->description === 'Customer baru' ? 'fa-user-plus text-success' : ($item->description === 'Jumlah komplain' ? 'fa-comment-dots text-danger' : 'fa-arrow-trend-up text-primary');
                                                    @endphp
                                                    <tr>
                                                        <th width="40%" class="bg-light ps-3 fw-normal" style="color:#495057;"><i class="fa {{ $gIcon }} me-2"></i>{{ $item->description }}</th>
                                                        <td class="fw-medium" style="color:#212529;">{{ $item->notes ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if ($customerBaruItems->count() > 0)
                                            <div class="border-top">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead style="background: #e6f7e6;">
                                                            <tr><th width="5%" style="color:#28a745;">No</th><th style="color:#28a745;">Nama Calon Customer</th><th style="color:#28a745;">No Telp</th><th style="color:#28a745;">Bidang Usaha</th></tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($customerBaruItems as $index => $item)
                                                                @php $notes = is_array($item->notes) ? $item->notes : (json_decode($item->notes, true) ?: []); @endphp
                                                                <tr>
                                                                    <td class="text-center" style="color:#212529;">{{ $index + 1 }}</td>
                                                                    <td class="fw-semibold" style="color:#212529;">{{ $notes['nama'] ?? '-' }}</td>
                                                                    <td style="color:#212529;">{{ $notes['no_telp'] ?? '-' }}</td>
                                                                    <td style="color:#212529;">{{ $notes['bidang_usaha'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($komplainItems->count() > 0)
                                            <div class="border-top">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead style="background: #fde8e8;">
                                                            <tr><th width="5%" style="color:#dc3545;">No</th><th style="color:#dc3545;">Uraian Komplain</th></tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($komplainItems as $index => $item)
                                                                @php $notes = is_array($item->notes) ? $item->notes : (json_decode($item->notes, true) ?: []); @endphp
                                                                <tr>
                                                                    <td class="text-center" style="color:#212529;">{{ $index + 1 }}</td>
                                                                    <td style="color:#212529;">{{ $notes['uraian'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 8. KESIMPULAN & PRIORITAS --}}
                    <div class="card mb-4 shadow-sm info-card" style="border-color: #f5c6cb;">
                        <div class="section-header gradient-danger text-white">
                            <i class="fa fa-flag-checkered"></i> Kesimpulan & Prioritas
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-4 text-center">
                                    <div class="p-3 rounded-3 h-100" style="background: #f8f9fa;">
                                        <small class="text-muted text-uppercase d-block mb-2 fw-semibold">Status Operasional</small>
                                        @if ($statusOps)
                                            <span class="badge fs-5 px-3 py-2 {{ $statusOps->notes === 'SEHAT' ? 'bg-success' : ($statusOps->notes === 'WASPADA' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                <i class="fa fa-{{ $statusOps->notes === 'SEHAT' ? 'check-circle' : ($statusOps->notes === 'WASPADA' ? 'exclamation-triangle' : 'times-circle') }} me-1"></i>
                                                {{ $statusOps->notes }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 rounded-3 h-100" style="background: #f8f9fa; border-left: 4px solid #667eea;">
                                        <small class="text-muted text-uppercase d-block mb-2 fw-semibold">Prioritas Hari Ini</small>
                                        <p class="mb-0" style="color:#212529;">{{ $prioritasHariIni->notes ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 rounded-3 h-100" style="background: #f8f9fa; border-left: 4px solid #f2994a;">
                                        <small class="text-muted text-uppercase d-block mb-2 fw-semibold">Prioritas Besok</small>
                                        <p class="mb-0" style="color:#212529;">{{ $prioritasBesok->notes ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- ===================== OPERASIONAL ===================== --}}
                @elseif (in_array($report->role_type, ['operasional', 'staff_operasional']))
                    @php
                        $doTomorrowItem = $report->items->where('type', 'task')->where('description', 'Rencana DO Besok')->first();
                        $doPlanItems = $report->items->where('type', 'do_plan');
                        $armadaItems = $report->items->where('type', 'armada');
                    @endphp

                    @if ($doTomorrowItem || $doPlanItems->count() > 0)
                        <div class="card mb-4 shadow-sm info-card" style="border-color: #b8daff;">
                            <div class="section-header gradient-info text-white">
                                <i class="fa fa-calendar-plus"></i> Rencana DO Besok
                            </div>
                            <div class="card-body">
                                @if ($doTomorrowItem)
                                    <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3" style="background: #e3f2fd;">
                                        <div class="stat-icon rounded-3 gradient-info" style="color:#fff;"><i class="fa fa-calculator"></i></div>
                                        <div>
                                            <small class="d-block" style="color: #6c757d;">Estimasi Jumlah DO Besok</small>
                                            <span class="fs-4 fw-bold" style="color: #212529;">{{ $doTomorrowItem->notes ?: '0' }} DO</span>
                                        </div>
                                    </div>
                                @endif
                                @if ($doPlanItems->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr><th width="10%">DO #</th><th width="45%">Customer</th><th width="45%">Rute</th></tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($doPlanItems as $item)
                                                    @php
                                                        $notes = explode(' | ', $item->notes);
                                                        $customer = isset($notes[0]) ? str_replace('Customer: ', '', $notes[0]) : '-';
                                                        $rute = isset($notes[1]) ? str_replace('Rute: ', '', $notes[1]) : '-';
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center fw-bold" style="color:#212529;">{{ str_replace('Rencana DO #', '', $item->description) }}</td>
                                                        <td style="color:#212529;"><i class="fa fa-building text-muted me-2"></i>{{ $customer }}</td>
                                                        <td style="color:#212529;"><i class="fa fa-location-dot text-muted me-2"></i>{{ $rute }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($armadaItems->count() > 0)
                        <div class="card mb-4 shadow-sm info-card" style="border-color: #ffc107;">
                            <div class="section-header gradient-warning text-dark">
                                <i class="fa fa-map-location-dot"></i> Lokasi Armada Hari Ini
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr><th width="5%">No</th><th width="15%">No. Polisi</th><th width="25%">Lokasi</th><th width="15%">Status</th><th width="15%">Sopir</th><th width="20%">Nama Sopir</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($armadaItems as $index => $item)
                                                @php
                                                    $lokasi = '-'; $statusArmada = '-'; $sopir = '-'; $driverName = '-';
                                                    if (!empty($item->notes)) {
                                                        $parts = explode(' | ', $item->notes);
                                                        foreach ($parts as $part) {
                                                            if (str_starts_with($part, 'Lokasi: ')) $lokasi = trim(str_replace('Lokasi: ', '', $part)) ?: '-';
                                                            if (str_starts_with($part, 'Status: ')) { $statusRaw = trim(str_replace('Status: ', '', $part)); $statusArmada = $statusRaw !== '' ? ucfirst(str_replace('_', ' ', $statusRaw)) : '-'; }
                                                            if (str_starts_with($part, 'Sopir: ')) { $sopirRaw = trim(str_replace('Sopir: ', '', $part)); $sopir = $sopirRaw === 'ada' ? 'Ada Sopir' : ($sopirRaw === 'tidak_ada' ? 'Tidak Ada Sopir' : ($sopirRaw ?: '-')); }
                                                            if (str_starts_with($part, 'Nama Sopir: ')) { $driverNameRaw = trim(str_replace('Nama Sopir: ', '', $part)); $driverName = $driverNameRaw !== '' && $driverNameRaw !== '-' ? $driverNameRaw : '-'; }
                                                        }
                                                    }
                                                    $statusBadge = match(trim(strtolower($statusArmada))) {
                                                        'jalan' => 'bg-success', 'stand_by' => 'bg-secondary', 'rusak' => 'bg-danger', default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <tr>
                                                    <td class="text-center" style="color:#212529;">{{ $index + 1 }}</td>
                                                    <td class="fw-semibold" style="color:#212529;">{{ $item->armadaReference->nopol ?? 'N/A' }}</td>
                                                    <td style="color:#212529;"><i class="fa fa-location-dot text-muted me-1"></i>{{ $lokasi }}</td>
                                                    <td><span class="badge-status {{ $statusBadge }}">{{ ucfirst($statusArmada) }}</span></td>
                                                    <td style="color:#212529;">{{ $sopir }}</td>
                                                    <td style="color:#212529;">{{ $driverName }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                {{-- ===================== KEUANGAN ===================== --}}
                @elseif($report->role_type === 'keuangan')
                    @php
                        $bankItems = $report->items->whereIn('type', ['bank_transaksi','bank_nominal','bank_mutasi','bank_nominal_mutasi']);
                        $groupedBankItems = $bankItems->groupBy('reference_id');
                    @endphp
                    @if ($groupedBankItems->count() > 0)
                        <div class="card mb-4 shadow-sm info-card" style="border-color: #a3d9a5;">
                            <div class="section-header gradient-success text-white">
                                <i class="fa fa-money-bill-trend-up"></i> Laporan Keuangan Per Bank
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr><th width="5%">No</th><th width="25%">Bank</th><th width="15%" class="text-center">Total Transaksi</th><th width="20%" class="text-end">Nominal Transaksi</th><th width="15%" class="text-center">Total Mutasi</th><th width="20%" class="text-end">Nominal Mutasi</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedBankItems as $bankId => $items)
                                                @php
                                                    $firstItem = $items->first();
                                                    $bankName = $firstItem->description ?? "Bank ID: $bankId";
                                                    $transaksiItem = $items->where('type', 'bank_transaksi')->first();
                                                    $nominalItem = $items->where('type', 'bank_nominal')->first();
                                                    $mutasiItem = $items->where('type', 'bank_mutasi')->first();
                                                    $nominalMutasiItem = $items->where('type', 'bank_nominal_mutasi')->first();
                                                    $cleanBankName = str_replace(['Total Transaksi ','Nominal Transaksi ','Total Mutasi ','Nominal Mutasi '], '', $bankName);
                                                @endphp
                                                <tr>
                                                    <td class="text-center fw-bold" style="color:#212529;">{{ $loop->iteration }}</td>
                                                    <td class="fw-semibold" style="color:#212529;"><i class="fa fa-university text-success me-2"></i>{{ $cleanBankName }}</td>
                                                    <td class="text-center"><span class="badge bg-primary bg-opacity-75 fs-6">{{ $transaksiItem->notes ?? '0' }}</span></td>
                                                    <td class="text-end fw-bold fs-6" style="color:#28a745;">{{ $nominalItem->notes ?? 'Rp. 0' }}</td>
                                                    <td class="text-center"><span class="badge bg-info bg-opacity-75 fs-6">{{ $mutasiItem->notes ?? '0' }}</span></td>
                                                    <td class="text-end fw-bold fs-6" style="color:#17a2b8;">{{ $nominalMutasiItem->notes ?? 'Rp. 0' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                {{-- ===================== GENERAL ===================== --}}
                @elseif($report->role_type === 'general')
                    @php $mainTaskItem = $report->items->where('type', 'task')->first(); @endphp
                    @if ($mainTaskItem)
                        <div class="card mb-4 shadow-sm info-card" style="border-color: #d6d8db;">
                            <div class="section-header" style="background: linear-gradient(135deg, #2c3e50 0%, #495057 100%); color: #fff;">
                                <i class="fa fa-tasks"></i> Laporan Kegiatan Umum
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-8">
                                        <div class="p-3 rounded-3 h-100" style="background: #f8f9fa; border-left: 4px solid #667eea;">
                                            <small class="text-muted text-uppercase d-block mb-1 fw-semibold">Kegiatan Utama</small>
                                            <p class="mb-0 fs-5 fw-medium" style="color:#212529;">{{ $mainTaskItem->description }}</p>
                                        </div>
                                    </div>
                                    @if ($mainTaskItem->notes)
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3 h-100" style="background: #f8f9fa; border-left: 4px solid #f2994a;">
                                                <small class="text-muted text-uppercase d-block mb-1 fw-semibold">Keterangan Tambahan</small>
                                                <p class="mb-0" style="color:#212529;">{{ $mainTaskItem->notes }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                {{-- ===================== FALLBACK ===================== --}}
                @else
                    <div class="card shadow-sm info-card" style="border-color: #d6d8db;">
                        <div class="section-header" style="background: linear-gradient(135deg, #2c3e50 0%, #495057 100%); color: #fff;">
                            <i class="fa fa-list"></i> Detail Laporan
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr><th width="5%">No</th><th width="15%">Tipe</th><th width="35%">Deskripsi</th><th width="15%">Status</th><th width="30%">Keterangan</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($report->items as $index => $item)
                                            <tr>
                                                <td class="text-center" style="color:#212529;">{{ $index + 1 }}</td>
                                                <td><span class="badge bg-secondary">{{ ucfirst($item->type) }}</span></td>
                                                <td class="fw-semibold" style="color:#212529;">{{ $item->description }}</td>
                                                <td class="text-center">
                                                    @if ($item->status)
                                                        <span class="badge {{ $item->getStatusBadgeClass() }}">{{ $item->getStatusLabel() }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td style="color:#212529;">{{ $item->notes ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            @else
                <div class="alert alert-warning d-flex align-items-center gap-2 py-3">
                    <i class="fa fa-exclamation-triangle fs-5"></i>
                    <span>Tidak ada item detail dalam laporan ini.</span>
                </div>
            @endif

            {{-- FOOTER --}}
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <small style="color: #6c757d;">
                    <i class="fa fa-clock me-1"></i> Dibuat: {{ $report->created_at->format('d F Y H:i') }}
                    @if ($report->updated_at->ne($report->created_at))
                        | <i class="fa fa-edit me-1"></i> Diperbarui: {{ $report->updated_at->format('d F Y H:i') }}
                    @endif
                </small>
                <span class="badge bg-light text-dark px-3 py-2">ID #{{ $report->id }}</span>
            </div>

        </div>
    </div>
@endsection
