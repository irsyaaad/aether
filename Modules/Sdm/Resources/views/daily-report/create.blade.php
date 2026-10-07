@extends('template.root')
@section('page-name', 'Laporan Harian')
@php
    $isEdit = isset($report);
@endphp
@section('title-page', $isEdit ? 'Edit Rencana Kerja Hari Ini' : 'Rencana Kerja Hari Ini')
@php
    $getItem = function($description) use ($items_map) {
        return isset($items_map[$description]) ? $items_map[$description]->first() : null;
    };
    $getItemNotes = function($description) use ($getItem) {
        $item = $getItem($description);
        return $item ? $item->notes : '';
    };
    $getArmadaItem = function($armadaId) use ($report, $isEdit) {
        if (!$isEdit) return null;
        return $report->items->where('type', 'armada')->where('reference_id', $armadaId)->first();
    };
    $getArmadaPosisi = function($armadaId) use ($getArmadaItem) {
        $item = $getArmadaItem($armadaId);
        if (!$item || !$item->notes) return '';
        if (str_contains($item->notes, 'Posisi: ') && str_contains($item->notes, ' | Sopir: ')) {
            preg_match('/Posisi: (.*) \| Sopir: /', $item->notes, $matches);
            return $matches[1] ?? $item->notes;
        }
        return $item->notes;
    };
    $getArmadaDriverName = function($armadaId) use ($getArmadaItem) {
        $item = $getArmadaItem($armadaId);
        if (!$item || !$item->notes) return '';
        if (str_contains($item->notes, 'Nama Sopir: ')) {
            preg_match('/Nama Sopir: (.*?)(?: \||$)/', $item->notes, $matches);
            return $matches[1] ?? '';
        }
        return '';
    };
@endphp


@section('content')
    <form action="{{ $isEdit ? route('daily-report.update', $report->id) : route('daily-report.store') }}" method="POST" onsubmit="return validateForm(event)">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fa fa-clipboard-list text-primary"></i>
                    {{ $isEdit ? 'Edit Laporan Harian' : 'Buat Laporan Harian' }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Laporan</label>
                        <input type="date" name="report_date" id="report_date" class="form-control"
                               value="{{ old('report_date', $isEdit ? $report->report_date->format('Y-m-d') : $date) }}"
                               required {{ $isEdit ? 'readonly' : '' }} onchange="handleDateChange()" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipe Role</label>
@if($is_admin ?? false)
                            <select name="role_type" id="role_type_selector" class="form-select" required onchange="handleRoleTypeChange()">
                                <option value="">-- Pilih Tipe Role --</option>
                                <option value="operasional" {{ $role_type === 'operasional' ? 'selected' : '' }}>
                                    Operasional
                                </option>
                                <option value="pic_operasional" {{ $role_type === 'pic_operasional' ? 'selected' : '' }}>
                                    PIC Operasional
                                </option>
                                <option value="keuangan" {{ $role_type === 'keuangan' ? 'selected' : '' }}>
                                    Keuangan
                                </option>
                                <option value="general" {{ $role_type === 'general' ? 'selected' : '' }}>
                                    Staff Umum
                                </option>
                            </select>
                        @else
                            <input type="text" class="form-control"
                                   value="{{ ucwords(str_replace('_', ' ', $role_type)) }}" readonly>
                            <input type="hidden" name="role_type" value="{{ $role_type }}">
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-control-plaintext">
                            <span class="badge bg-info">
                                {{ $isEdit ? 'Edit Mode' : 'Buat Baru' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ringkasan Laporan</label>
                    <textarea name="summary" rows="3" class="form-control"
                              placeholder="Ringkasan kegiatan hari ini..." required>{{ old('summary', $isEdit ? $report->summary : '') }}</textarea>
                </div>

                <div id="pic-operasional-problem-section" style="{{ $role_type === 'pic_operasional' ? 'display: block;' : 'display: none;' }}">
                    <div class="card border-danger mb-3">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fa fa-exclamation-triangle"></i> Problem & Solusi</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" id="problem-solusi-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 120px; text-align: center;">Jenis</th>
                                            <th style="min-width: 300px; text-align: center;">Problem</th>
                                            <th style="min-width: 300px; text-align: center;">Solusi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $problemTypes = ['DO', 'Bengkel', 'Legalitas', 'Customer'];
                                            $problemKeys = ['problem_do', 'problem_bengkel', 'problem_legalitas', 'problem_customer'];
                                            foreach ($problemTypes as $i => $type) {
                                                $key = $problemKeys[$i];
                                                $item = $getItem($type);
                                                $notes = $item ? json_decode($item->notes, true) : [];
                                                $problemValue = $notes['problem'] ?? '';
                                                $solusiValue = $notes['solusi'] ?? '';
                                            @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <strong>{{ $type }}</strong>
                                                        <input type="hidden" name="items[{{ $key }}][type]" value="task">
                                                        <input type="hidden" name="items[{{ $key }}][description]" value="{{ $type }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $key }}][problem]" class="form-control form-control-sm"
                                                               placeholder="Problem {{ strtolower($type) }}..."
                                                               value="{{ old('items.'.$key.'.problem', $problemValue) }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $key }}][solusi]" class="form-control form-control-sm"
                                                               placeholder="Solusi {{ strtolower($type) }}..."
                                                               value="{{ old('items.'.$key.'.solusi', $solusiValue) }}">
                                                    </td>
                                                </tr>
                                            @php
                                            }
                                            @endphp
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dynamic content based on role type --}}
                <div id="dynamic-content-container">
                    <div id="operasional-section" style="{{ $role_type === 'operasional' ? 'display: block;' : 'display: none;' }}">
                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fa fa-map-marker-alt"></i> Pengecekan Armada Hari Ini</h6>
                            </div>
                            <div class="card-body" id="operasional-content">
                                @if(isset($armadas) && $armadas->count() > 0)
                                    <div class="alert alert-light border mb-3">
                                        <div class="d-flex flex-wrap gap-3 align-items-center small">
                                            <span><i class="fa fa-info-circle text-primary"></i> Isi kondisi armada hari ini per unit</span>
                                            <span class="badge bg-secondary">Stand By</span>
                                            <span class="badge bg-info">Jalan</span>
                                            <span class="badge bg-danger">Rusak</span>
                                        </div>
                                    </div>
                                    <div class="table-responsive border rounded">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light" style="position: sticky; top: 0; z-index: 2;">
                                                <tr>
                                                    <th style="min-width: 180px;">Armada</th>
                                                    <th style="min-width: 280px;">Posisi Armada</th>
                                                    <th style="min-width: 160px;">Status Armada</th>
                                                    <th style="min-width: 170px;">Ketersediaan Sopir</th>
                                                    <th style="min-width: 180px;">Nama Sopir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($armadas as $index => $armada)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold text-dark">{{ $armada->nopol }}</div>
                                                            <small class="text-muted">{{ $armada->nama_armada ?: 'Unit Armada' }}</small>
                                                            <input type="hidden" name="items[armada_{{ $index }}][type]" value="armada">
                                                            <input type="hidden" name="items[armada_{{ $index }}][reference_id]" value="{{ $armada->id }}">
                                                            <input type="hidden" name="items[armada_{{ $index }}][description]" value="Pengecekan {{ $armada->nopol }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[armada_{{ $index }}][notes]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Contoh: Pool Cakung / Tol Cipali KM 120"
                                                                value="{{ old('items.armada_'.$index.'.notes', $getArmadaPosisi($armada->id)) }}">
                                                        </td>
                                                        <td>
                                                            <select name="items[armada_{{ $index }}][status]" class="form-select form-select-sm">
                                                                @php $armItem = $getArmadaItem($armada->id); $status = $armItem ? $armItem->status : 'stand_by'; @endphp
                                                                <option value="stand_by" {{ $status === 'stand_by' ? 'selected' : '' }}>Stand By</option>
                                                                <option value="jalan" {{ $status === 'jalan' ? 'selected' : '' }}>Jalan</option>
                                                                <option value="rusak" {{ $status === 'rusak' ? 'selected' : '' }}>Rusak</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="items[armada_{{ $index }}][driver_presence]" class="form-select form-select-sm">
                                                                @php
                                                                    $armItem = $getArmadaItem($armada->id);
                                                                    $sopir = 'ada';
                                                                    if ($armItem && $armItem->notes && str_contains($armItem->notes, 'Sopir: ')) {
                                                                        $sopir = str_contains($armItem->notes, 'Sopir: Ada') ? 'ada' : 'tidak_ada';
                                                                    }
                                                                @endphp
                                                                <option value="ada" {{ $sopir === 'ada' ? 'selected' : '' }}>Ada Sopir</option>
                                                                <option value="tidak_ada" {{ $sopir === 'tidak_ada' ? 'selected' : '' }}>Tidak Ada Sopir</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[armada_{{ $index }}][driver_name]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Nama Sopir"
                                                                value="{{ old('items.armada_'.$index.'.driver_name', $getArmadaDriverName($armada->id)) }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> Tidak ada data armada.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fa fa-calendar-plus"></i> Rencana DO Besok</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah DO Besok</label>
                                    <input type="number" name="items[do_tomorrow][notes]" id="jumlah_do_besok_operasional" class="form-control"
                                           placeholder="Estimasi jumlah DO besok..." min="0" max="50" onchange="generateDoInputsOperasional()"
                                           value="{{ old('items.do_tomorrow.notes', $getItemNotes('Rencana DO Besok')) }}">
                                    <input type="hidden" name="items[do_tomorrow][type]" value="task">
                                    <input type="hidden" name="items[do_tomorrow][description]" value="Rencana DO Besok">
                                    <input type="hidden" name="items[do_tomorrow][status]" value="pending">
                                </div>

                                <!-- Dynamic DO inputs will be generated here -->
                                <div id="dynamic_do_inputs_operasional" class="mt-3">
                                    @if($isEdit)
                                        @php
                                            $doBesokItems = $report->items->filter(function($item) {
                                                return str_starts_with($item->description, 'DO Besok: ') || str_starts_with($item->description, 'do_besok_');
                                            });
                                        @endphp
                                        @foreach($doBesokItems as $i => $item)
                                            <div class="row g-3 mb-2 border-bottom pb-2">
                                                <div class="col-md-4"><label class="form-label">Customer {{ $i+1 }}</label><input type="text" name="items[do_besok_{{ $i }}][description]" class="form-control" value="{{ str_replace('DO Besok: ', '', $item->description) }}" placeholder="Nama customer"></div>
                                                <div class="col-md-4"><label class="form-label">Tujuan</label><input type="text" name="items[do_besok_{{ $i }}][notes]" class="form-control" value="{{ $item->notes }}" placeholder="Kota/Tujuan"></div>
                                                <div class="col-md-4"><label class="form-label">Status</label><select name="items[do_besok_{{ $i }}][status]" class="form-select">
                                                    <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Akan Berangkat</option>
                                                    <option value="in_progress" {{ $item->status === 'in_progress' ? 'selected' : '' }}>Proses</option>
                                                </select></div>
                                                <input type="hidden" name="items[do_besok_{{ $i }}][type]" value="task">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="pic-operasional-section" style="{{ $role_type === 'pic_operasional' ? 'display: block;' : 'display: none;' }}">
                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fa fa-clipboard-check"></i> 1. Perencanaan & Realisasi Operasional</h6>
                            </div>
<div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead class="table-warning" style="position: sticky; top: 0; z-index: 2;">
                                            <tr>
                                                <th style="min-width: 150px;">No DO</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-file text-secondary"></i> Dibuat</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-truck text-primary"></i> Berangkat</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-box text-warning"></i> Muat</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-check text-success"></i> Bongkar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($dos ?? [] as $index => $do)
                                                @php
                                                    $doStatusItem = $isEdit && isset($report) ? $report->items->where('type', 'do_status')->where('reference_id', $do->id)->first() : null;
                                                    $savedDoStatus = $doStatusItem ? $doStatusItem->notes : 'dibuat';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $do->kode_do }}</strong>
                                                        @if($do->customer)
                                                            <br><small class="text-muted">{{ $do->customer->nama }}</small>
                                                        @endif
                                                        @if($do->trayek)
                                                            <br><small class="text-info"><i class="fa fa-route"></i> {{ $do->trayek->nama_trayek }}</small>
                                                        @endif
                                                        <input type="hidden" name="items[pic_do_status_{{ $index }}][type]" value="do_status">
                                                        <input type="hidden" name="items[pic_do_status_{{ $index }}][reference_id]" value="{{ $do->id }}">
                                                        <input type="hidden" name="items[pic_do_status_{{ $index }}][description]" value="{{ $do->kode_do }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_{{ $index }}][status]" value="dibuat" class="form-check-input" {{ $savedDoStatus === 'dibuat' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_{{ $index }}][status]" value="berangkat" class="form-check-input" {{ $savedDoStatus === 'berangkat' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_{{ $index }}][status]" value="muat" class="form-check-input" {{ $savedDoStatus === 'muat' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_{{ $index }}][status]" value="bongkar" class="form-check-input" {{ $savedDoStatus === 'bongkar' ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">Tidak ada DO untuk hari ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="small text-muted mt-2">
                                    <i class="fa fa-info-circle"></i> Pilih salah satu status untuk setiap DO. Radio button akan otomatis nonaktifkan opsi lain pada baris yang sama.
                                </div>

                                @if(isset($historicalDos) && $historicalDos->count() > 0)
                                <hr>
                                <h6 class="text-secondary mt-4"><i class="fa fa-history"></i> Riwayat DO (Sebelum Hari Ini)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead class="table-warning" style="position: sticky; top: 0; z-index: 2;">
                                            <tr>
                                                <th style="min-width: 150px;">No DO</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-file text-secondary"></i> Dibuat</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-truck text-primary"></i> Berangkat</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-box text-warning"></i> Muat</th>
                                                <th class="text-center" style="min-width: 90px;"><i class="fa fa-check text-success"></i> Bongkar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($historicalDos as $index => $do)
                                                @php
                                                    $doStatusItem = $isEdit && isset($report) ? $report->items->where('type', 'do_status')->where('reference_id', $do->id)->first() : null;
                                                    $savedDoStatus = $doStatusItem ? $doStatusItem->notes : 'dibuat';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $do->kode_do }}</strong>
                                                        @if($do->customer)
                                                            <br><small class="text-muted">{{ $do->customer->nama }}</small>
                                                        @endif
                                                        @if($do->trayek)
                                                            <br><small class="text-info"><i class="fa fa-route"></i> {{ $do->trayek->nama_trayek }}</small>
                                                        @endif
                                                        <input type="hidden" name="items[pic_do_status_hist_{{ $index }}][type]" value="do_status">
                                                        <input type="hidden" name="items[pic_do_status_hist_{{ $index }}][reference_id]" value="{{ $do->id }}">
                                                        <input type="hidden" name="items[pic_do_status_hist_{{ $index }}][description]" value="{{ $do->kode_do }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_hist_{{ $index }}][status]" value="dibuat" class="form-check-input" {{ $savedDoStatus === 'dibuat' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_hist_{{ $index }}][status]" value="berangkat" class="form-check-input" {{ $savedDoStatus === 'berangkat' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_hist_{{ $index }}][status]" value="muat" class="form-check-input" {{ $savedDoStatus === 'muat' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_do_status_hist_{{ $index }}][status]" value="bongkar" class="form-check-input" {{ $savedDoStatus === 'bongkar' ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                <label class="form-label mt-3">Problem Solving (target DO 10 jika tidak sampai target solusinya apa)</label>
                                <textarea class="form-control" rows="2" name="items[pic_problem_solving][notes]">{{ old('items.pic_problem_solving.notes', $getItemNotes('Problem Solving Target DO')) }}</textarea>
                                <input type="hidden" name="items[pic_problem_solving][type]" value="task"><input type="hidden" name="items[pic_problem_solving][description]" value="Problem Solving Target DO">
                            </div>
                        </div>

                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning text-dark"><h6 class="mb-0"><i class="fa fa-truck"></i> 2. Monitoring Armada</h6></div>
                            <div class="card-body">
                                <div class="table-responsive border rounded">
                                    <table class="table table-sm table-bordered align-middle mb-0" id="monitoring-armada-table">
                                        <thead class="table-warning" style="position: sticky; top: 0; z-index: 2;">
                                            <tr>
                                                <th style="min-width: 150px;">Nopol</th>
                                                <th class="text-center" style="min-width: 120px;"><i class="fa fa-route text-success"></i> Jalan</th>
                                                <th class="text-center" style="min-width: 140px;"><i class="fa fa-pause text-secondary"></i> Tidak Jalan (Stand By)</th>
                                                <th class="text-center" style="min-width: 100px;"><i class="fa fa-wrench text-danger"></i> Rusak</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($armadas as $index => $armada)
                                                @php
                                                    $armStatusItem = $isEdit ? $report->items->where('type', 'armada_status')->where('reference_id', $armada->id)->first() : null;
                                                    $savedStatus = $armStatusItem ? $armStatusItem->notes : 'stand_by';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $armada->nopol }}</strong>
                                                        @if(!empty($armada->nama_armada))
                                                            <br><small class="text-muted">{{ $armada->nama_armada }}</small>
                                                        @endif
                                                        <input type="hidden" name="items[pic_armada_status_{{ $index }}][type]" value="armada_status">
                                                        <input type="hidden" name="items[pic_armada_status_{{ $index }}][reference_id]" value="{{ $armada->id }}">
                                                        <input type="hidden" name="items[pic_armada_status_{{ $index }}][description]" value="{{ $armada->nopol }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_armada_status_{{ $index }}][status]" value="jalan" class="form-check-input" {{ $savedStatus === 'jalan' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_armada_status_{{ $index }}][status]" value="stand_by" class="form-check-input" {{ $savedStatus === 'stand_by' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="items[pic_armada_status_{{ $index }}][status]" value="rusak" class="form-check-input" {{ $savedStatus === 'rusak' ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="small text-muted mt-2">
                                    <i class="fa fa-info-circle"></i> Pilih salah satu status untuk setiap armada. Radio button akan otomatis nonaktifkan opsi lain pada baris yang sama.
                                </div>

                                <div id="armada-rusak-section" class="mt-3" style="display: none;">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0"><i class="fa fa-tools"></i> Detail Armada Rusak</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered table-hover" id="armada-rusak-table">
                                                    <thead class="table-danger">
                                                        <tr>
                                                            <th style="min-width: 120px;">Nopol</th>
                                                            <th style="min-width: 200px;">Kerusakan</th>
                                                            <th style="min-width: 150px;">PIC Bengkel</th>
                                                            <th style="min-width: 150px;">Estimasi Pengerjaan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="armada-rusak-body">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label mt-3">Penyelesaian Armada Rusak</label>
                                <textarea class="form-control" rows="3" name="items[pic_penyelesaian_armada_rusak][notes]">{{ old('items.pic_penyelesaian_armada_rusak.notes', $getItemNotes('Penyelesaian armada rusak')) }}</textarea>
                                <input type="hidden" name="items[pic_penyelesaian_armada_rusak][type]" value="task"><input type="hidden" name="items[pic_penyelesaian_armada_rusak][description]" value="Penyelesaian armada rusak">
                            </div>
                        </div>

                        <div id="pic-keuangan-section" style="{{ $role_type === 'pic_operasional' ? 'display: block;' : 'display: none;' }}">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white"><h6 class="mb-0"><i class="fa fa-money-bill"></i> 4. Keuangan Operasional</h6></div>
                            <div class="card-body">
                                <div class="mb-3"><label class="form-label">Total perkiraan sangu hari ini</label><input type="text" name="items[pic_total_perkiraan_sangu][notes]" class="form-control" placeholder="Rp ..." onkeyup="formatRupiah(this)" value="{{ old('items.pic_total_perkiraan_sangu.notes', $getItemNotes('Total perkiraan sangu')) }}"><input type="hidden" name="items[pic_total_perkiraan_sangu][type]" value="task"><input type="hidden" name="items[pic_total_perkiraan_sangu][description]" value="Total perkiraan sangu"></div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="keuangan-operasional-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th style="min-width: 200px;">Kode DO</th>
                                                <th style="min-width: 250px;">Tujuan</th>
                                                <th style="min-width: 200px;">Nominal Sangu</th>
                                                <th style="width: 80px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="keuangan-operasional-body">
                                            @if($isEdit && isset($items_map['Keuangan Operasional']))
                                                @foreach($items_map['Keuangan Operasional'] as $i => $item)
                                                    @php
                                                        $notes = $item->notes ? (json_decode($item->notes, true) ?: []) : [];
                                                    @endphp
                                                    <tr class="keuangan-row">
                                                        <td class="text-center keuangan-no">{{ $i + 1 }}</td>
                                                        <td>
                                                            <select class="form-control form-control-sm select2-do" name="items[pic_keuangan_{{ $i }}][kode_do]" style="width:100%">
                                                                @if(($notes['kode_do'] ?? ''))
                                                                    <option value="{{ $notes['kode_do'] }}" selected>{{ $notes['kode_do'] }}</option>
                                                                @endif
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control form-control-sm" name="items[pic_keuangan_{{ $i }}][tujuan]" value="{{ $notes['tujuan'] ?? '' }}" placeholder="Tujuan"></td>
                                                        <td><input type="text" class="form-control form-control-sm" name="items[pic_keuangan_{{ $i }}][nominal_sangu]" onkeyup="formatRupiah(this)" value="{{ $notes['nominal_sangu'] ?? '' }}" placeholder="Rp"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-row" title="Hapus"><i class="fa fa-times"></i></button></td>
                                                        <input type="hidden" name="items[pic_keuangan_{{ $i }}][type]" value="task">
                                                        <input type="hidden" name="items[pic_keuangan_{{ $i }}][description]" value="Keuangan Operasional">
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="keuangan-row">
                                                    <td class="text-center keuangan-no">1</td>
                                                    <td>
                                                        <select class="form-control form-control-sm select2-do" name="items[pic_keuangan_0][kode_do]" style="width:100%">
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control form-control-sm" name="items[pic_keuangan_0][tujuan]" placeholder="Tujuan"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="items[pic_keuangan_0][nominal_sangu]" onkeyup="formatRupiah(this)" placeholder="Rp"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-row" title="Hapus"><i class="fa fa-times"></i></button></td>
                                                    <input type="hidden" name="items[pic_keuangan_0][type]" value="task">
                                                    <input type="hidden" name="items[pic_keuangan_0][description]" value="Keuangan Operasional">
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-success mt-2" id="btn-add-keuangan">
                                    <i class="fa fa-plus"></i> Tambah Baris
                                </button>
                                <input type="hidden" id="keuangan-row-count" value="1">
                            </div>
                        </div>
                        </div>

                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fa fa-users"></i> 5. SDM (Driver)</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4"><label class="form-label">Total driver aktif</label><input type="number" min="0" class="form-control" name="items[pic_total_driver_aktif][notes]" value="{{ old('items.pic_total_driver_aktif.notes', $getItemNotes('Total driver aktif')) }}"><input type="hidden" name="items[pic_total_driver_aktif][type]" value="task"><input type="hidden" name="items[pic_total_driver_aktif][description]" value="Total driver aktif"></div>
                                    <div class="col-md-4"><label class="form-label">Unit tanpa driver</label><input type="number" min="0" class="form-control" name="items[pic_unit_tanpa_driver][notes]" value="{{ old('items.pic_unit_tanpa_driver.notes', $getItemNotes('Unit tanpa driver')) }}"><input type="hidden" name="items[pic_unit_tanpa_driver][type]" value="task"><input type="hidden" name="items[pic_unit_tanpa_driver][description]" value="Unit tanpa driver"></div>
                                    <div class="col-md-4"><label class="form-label">Interview hari ini (orang)</label><input type="number" min="0" class="form-control" name="items[pic_interview_hari_ini][notes]" value="{{ old('items.pic_interview_hari_ini.notes', $getItemNotes('Interview hari ini')) }}"><input type="hidden" name="items[pic_interview_hari_ini][type]" value="task"><input type="hidden" name="items[pic_interview_hari_ini][description]" value="Interview hari ini"></div>
                                </div>
                                <label class="form-label mt-3">Solusi Rekrutmen Driver</label>
                                <textarea class="form-control" rows="2" name="items[pic_solusi_rekrutmen_driver][notes]">{{ old('items.pic_solusi_rekrutmen_driver.notes', $getItemNotes('Solusi rekrutmen driver')) }}</textarea>
                                <input type="hidden" name="items[pic_solusi_rekrutmen_driver][type]" value="task"><input type="hidden" name="items[pic_solusi_rekrutmen_driver][description]" value="Solusi rekrutmen driver">
                                <div class="mt-3">
                                    <label class="form-label d-flex justify-content-between">
                                        <span>Driver Ijin</span>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addDriverIjinRow()">
                                            <i class="fa fa-plus"></i> Tambah
                                        </button>
                                    </label>
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Sopir</th>
                                                <th>Nopol Truk</th>
                                                <th>Solusi</th>
                                                <th style="width:60px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="driver-ijin-detail-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card border-dark mb-3">
                            <div class="card-header bg-dark text-white"><h6 class="mb-0"><i class="fa fa-shield"></i> 6. Sistem & Kepatuhan</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4"><label class="form-label">DO belum kembali</label><input type="number" min="0" class="form-control" name="items[pic_do_belum_kembali][notes]" value="{{ old('items.pic_do_belum_kembali.notes', $getItemNotes('DO belum kembali')) }}"><input type="hidden" name="items[pic_do_belum_kembali][type]" value="task"><input type="hidden" name="items[pic_do_belum_kembali][description]" value="DO belum kembali"></div>
                                    <div class="col-md-4"><label class="form-label">DO tidak lengkap</label><input type="number" min="0" class="form-control" name="items[pic_do_tidak_lengkap][notes]" value="{{ old('items.pic_do_tidak_lengkap.notes', $getItemNotes('DO tidak lengkap')) }}"><input type="hidden" name="items[pic_do_tidak_lengkap][type]" value="task"><input type="hidden" name="items[pic_do_tidak_lengkap][description]" value="DO tidak lengkap"></div>
                                    <div class="col-md-4"><label class="form-label">Status serah terima keamanan</label><input type="text" class="form-control" name="items[pic_status_serah_terima_keamanan][notes]" value="{{ old('items.pic_status_serah_terima_keamanan.notes', $getItemNotes('Status serah terima keamanan')) }}"><input type="hidden" name="items[pic_status_serah_terima_keamanan][type]" value="task"><input type="hidden" name="items[pic_status_serah_terima_keamanan][description]" value="Status serah terima keamanan"></div>
                                </div>
                                <div class="mt-3"><label class="form-label">Lampiran: Foto Log Book Keamanan</label><input type="text" class="form-control" name="items[pic_lampiran_logbook][notes]" placeholder="Contoh: nama file/link referensi" value="{{ old('items.pic_lampiran_logbook.notes', $getItemNotes('Lampiran log book keamanan')) }}"><input type="hidden" name="items[pic_lampiran_logbook][type]" value="task"><input type="hidden" name="items[pic_lampiran_logbook][description]" value="Lampiran log book keamanan"></div>
                            </div>
                        </div>

                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fa fa-chart-line"></i> 7. Customer & Pengembangan</h6></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label d-flex justify-content-between">
                                        <span>Customer Baru</span>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addCustomerBaruRow()">
                                            <i class="fa fa-plus"></i> Tambah
                                        </button>
                                    </label>
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Calon Customer</th>
                                                <th>No Telp</th>
                                                <th>Bidang Usaha</th>
                                                <th style="width:60px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="customer-baru-detail-body"></tbody>
                                    </table>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label d-flex justify-content-between">
                                        <span>Komplain</span>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addKomplainRow()">
                                            <i class="fa fa-plus"></i> Tambah
                                        </button>
                                    </label>
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Uraian Komplain</th>
                                                <th style="width:60px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="komplain-detail-body"></tbody>
                                    </table>
                                </div>
                                <label class="form-label mt-3">Peluang growth</label>
                                <textarea class="form-control" rows="3" name="items[pic_peluang_growth][notes]">{{ old('items.pic_peluang_growth.notes', $getItemNotes('Peluang growth')) }}</textarea>
                                <input type="hidden" name="items[pic_peluang_growth][type]" value="task"><input type="hidden" name="items[pic_peluang_growth][description]" value="Peluang growth">
                            </div>
                        </div>

                        <div class="card border-danger mb-3">
                            <div class="card-header bg-danger text-white"><h6 class="mb-0"><i class="fa fa-exclamation-circle"></i> Panel Kesimpulan</h6></div>
                            <div class="card-body">
                                <div class="mb-3"><label class="form-label">Status Operasional Hari Ini (SEHAT/WASPADA/KRITIS)</label><input type="text" class="form-control" name="items[pic_status_operasional_hari_ini][notes]" value="{{ old('items.pic_status_operasional_hari_ini.notes', $getItemNotes('Status operasional hari ini')) }}"><input type="hidden" name="items[pic_status_operasional_hari_ini][type]" value="task"><input type="hidden" name="items[pic_status_operasional_hari_ini][description]" value="Status operasional hari ini"></div>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label">3 Prioritas Hari Ini</label><textarea rows="3" class="form-control" name="items[pic_prioritas_hari_ini][notes]">{{ old('items.pic_prioritas_hari_ini.notes', $getItemNotes('3 prioritas hari ini')) }}</textarea><input type="hidden" name="items[pic_prioritas_hari_ini][type]" value="task"><input type="hidden" name="items[pic_prioritas_hari_ini][description]" value="3 prioritas hari ini"></div>
                                    <div class="col-md-6"><label class="form-label">3 Prioritas Besok</label><textarea rows="3" class="form-control" name="items[pic_prioritas_besok][notes]">{{ old('items.pic_prioritas_besok.notes', $getItemNotes('3 prioritas besok')) }}</textarea><input type="hidden" name="items[pic_prioritas_besok][type]" value="task"><input type="hidden" name="items[pic_prioritas_besok][description]" value="3 prioritas besok"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="keuangan-section" style="{{ $role_type === 'keuangan' ? 'display: block;' : 'display: none;' }}">
                        <div class="card border-success mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fa fa-money-bill"></i> Laporan Keuangan Hari Ini</h6>
                            </div>
                            <div class="card-body" id="keuangan-content">
                                @if(isset($kas_banks) && $kas_banks->count() > 0 && $role_type === 'keuangan')
                                    @foreach($kas_banks as $index => $kasBank)
                                        <div class="card border-light mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0 text-dark">
                                                    <i class="fa fa-university"></i> {{ $kasBank->id_ac }} - {{ $kasBank->nama }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Total Transaksi</label>
                                                            <input type="number" name="items[bank_{{ $kasBank->id_ac }}_transaksi][notes]" class="form-control"
                                                                   placeholder="Jumlah transaksi {{ $kasBank->nama }}..." min="0"
                                                                   value="{{ old('items.bank_'.$kasBank->id_ac.'_transaksi.notes', $getItemNotes('Total Transaksi '.$kasBank->nama)) }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_transaksi][type]" value="bank_transaksi">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_transaksi][reference_id]" value="{{ $kasBank->id_ac }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_transaksi][description]" value="Total Transaksi {{ $kasBank->nama }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_transaksi][status]" value="completed">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nominal Transaksi</label>
                                                            <input type="text" name="items[bank_{{ $kasBank->id_ac }}_nominal][notes]" class="form-control"
                                                                   placeholder="Rp. 0" onkeyup="formatRupiah(this)"
                                                                   value="{{ old('items.bank_'.$kasBank->id_ac.'_nominal.notes', $getItemNotes('Nominal Transaksi '.$kasBank->nama)) }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal][type]" value="bank_nominal">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal][reference_id]" value="{{ $kasBank->id_ac }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal][description]" value="Nominal Transaksi {{ $kasBank->nama }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal][status]" value="completed">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Total Mutasi (Real)</label>
                                                            <input type="number" name="items[bank_{{ $kasBank->id_ac }}_mutasi][notes]" class="form-control"
                                                                   placeholder="Jumlah mutasi real {{ $kasBank->nama }}..." min="0"
                                                                   value="{{ old('items.bank_'.$kasBank->id_ac.'_mutasi.notes', $getItemNotes('Total Mutasi '.$kasBank->nama)) }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_mutasi][type]" value="bank_mutasi">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_mutasi][reference_id]" value="{{ $kasBank->id_ac }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_mutasi][description]" value="Total Mutasi {{ $kasBank->nama }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_mutasi][status]" value="completed">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nominal Mutasi</label>
                                                            <input type="text" name="items[bank_{{ $kasBank->id_ac }}_nominal_mutasi][notes]" class="form-control"
                                                                   placeholder="Rp. 0" onkeyup="formatRupiah(this)"
                                                                   value="{{ old('items.bank_'.$kasBank->id_ac.'_nominal_mutasi.notes', $getItemNotes('Nominal Mutasi '.$kasBank->nama)) }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal_mutasi][type]" value="bank_nominal_mutasi">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal_mutasi][reference_id]" value="{{ $kasBank->id_ac }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal_mutasi][description]" value="Nominal Mutasi {{ $kasBank->nama }}">
                                                            <input type="hidden" name="items[bank_{{ $kasBank->id_ac }}_nominal_mutasi][status]" value="completed">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> Tidak ada kas/bank yang dapat diakses untuk role ini.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="general-section" style="{{ $role_type === 'general' ? 'display: block;' : 'display: none;' }}">
                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fa fa-tasks"></i> Laporan Kegiatan Umum</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Kegiatan Utama</label>
                                    <textarea name="items[main_task][description]" rows="3" class="form-control"
                                              placeholder="Deskripsi kegiatan utama hari ini...">{{ $getItem('main_task') ? $getItem('main_task')->description : '' }}</textarea>
                                    <input type="hidden" name="items[main_task][type]" value="task">
                                    <input type="hidden" name="items[main_task][status]" value="completed">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Keterangan Tambahan</label>
                                    <input type="text" name="items[main_task][notes]" class="form-control"
                                           placeholder="Keterangan tambahan..." value="{{ $getItem('main_task') ? $getItem('main_task')->notes : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('daily-report.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ $isEdit ? 'Update Laporan' : 'Simpan Laporan' }}
                    </button>
                </div>
            </div>
        </div>
</form>

@section('script')
    <script>
        // Role type from server
        var currentRoleType = '{{ $role_type }}';
        var isEditMode = {{ $isEdit ? 'true' : 'false' }};

        $(document).ready(function() {
            @if($isEdit)
                @if(isset($items_map['Keuangan Operasional']))
                    document.getElementById('keuangan-row-count').value = {{ count($items_map['Keuangan Operasional']) }};
                @endif
            @endif

            $('#btn-add-keuangan').on('click', function() {
                addKeuanganRow();
            });

            $(document).on('click', '.btn-remove-row', function() {
                var $row = $(this).closest('tr');
                $row.find('.select2-do').select2('destroy');
                $row.remove();
                $('#keuangan-operasional-body .keuangan-row').each(function(idx) {
                    $(this).find('.keuangan-no').text(idx + 1);
                });
            });

            $('.select2-do').each(function() {
                initKeuanganSelect2($(this));
            });
        });

        function generateDoInputsOperasional() {
            var count = document.getElementById('jumlah_do_besok_operasional').value;
            var container = document.getElementById('dynamic_do_inputs_operasional');
            container.innerHTML = '';

            for (var i = 0; i < count; i++) {
                var html = '<div class="row g-3 mb-2 border-bottom pb-2">' +
                    '<div class="col-md-4"><label class="form-label">Customer ' + (i+1) + '</label><input type="text" name="items[do_besok_' + i + '][description]" class="form-control" placeholder="Nama customer"></div>' +
                    '<div class="col-md-4"><label class="form-label">Tujuan</label><input type="text" name="items[do_besok_' + i + '][notes]" class="form-control" placeholder="Kota/Tujuan"></div>' +
                    '<div class="col-md-4"><label class="form-label">Status</label><select name="items[do_besok_' + i + '][status]" class="form-select"><option value="pending">Akan Berangkat</option><option value="in_progress">Proses</option></select></div>' +
                    '<input type="hidden" name="items[do_besok_' + i + '][type]" value="task">' +
                    '</div>';
                container.insertAdjacentHTML('beforeend', html);
            }
        }

        function handleRoleTypeChange() {
            var roleType = document.getElementById('role_type_selector') ? document.getElementById('role_type_selector').value : currentRoleType;
            var date = document.getElementById('report_date').value;

            if (!roleType) return;

            document.getElementById('operasional-section').style.display = 'none';
            document.getElementById('pic-operasional-section').style.display = 'none';
            document.getElementById('pic-operasional-problem-section').style.display = 'none';
            document.getElementById('keuangan-section').style.display = 'none';
            document.getElementById('general-section').style.display = 'none';

            var targetSection = roleType.replace('_', '-') + '-section';
            var section = document.getElementById(targetSection);
            if (section) {
                section.style.display = 'block';
            }

            if (roleType === 'pic_operasional') {
                var problemSection = document.getElementById('pic-operasional-problem-section');
                if (problemSection) {
                    problemSection.style.display = 'block';
                }
                var keuanganSection = document.getElementById('pic-keuangan-section');
                if (keuanganSection) {
                    keuanganSection.style.display = 'block';
                }
                if (typeof initCustomerKomplain === 'function') {
                    initCustomerKomplain();
                }
            }

            loadRoleData(roleType, date);
        }

        function handleDateChange() {
            var roleType = document.getElementById('role_type_selector') ? document.getElementById('role_type_selector').value : currentRoleType;
            var date = document.getElementById('report_date').value;

            if (roleType && date) {
                loadRoleData(roleType, date);
            }
        }

        function loadRoleData(roleType, date) {
            showLoading(true);

            fetch('{{ route("daily-report.get-role-data") }}?role_type=' + roleType + '&date=' + date)
                .then(response => response.json())
                .then(data => {
                    updateContent(roleType, data);
                    showLoading(false);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showLoading(false);
                });
        }

        function updateContent(roleType, data) {
            if (roleType === 'operasional' && data.armadas) {
                updateOperasionalContent(data.dos, data.armadas);
            } else if (roleType === 'pic_operasional' && data.dos) {
                updatePicOperasionalContent(data.dos, data.historicalDos);
            } else if (roleType === 'keuangan' && data.kas_banks) {
                updateKeuanganContent(data.kas_banks);
            }
        }

        function updatePicOperasionalContent(dos, historicalDos) {
            var container = document.querySelector('#pic-operasional-section .card-body');
            if (!container) return;

            var html = '<div class="table-responsive">';
            html += '<table class="table table-sm table-bordered align-middle mb-0">';
            html += '<thead class="table-warning" style="position: sticky; top: 0; z-index: 2;"><tr>';
            html += '<th style="min-width: 150px;">No DO</th>';
            html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-file text-secondary"></i> Dibuat</th>';
            html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-truck text-primary"></i> Berangkat</th>';
            html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-box text-warning"></i> Muat</th>';
            html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-check text-success"></i> Bongkar</th>';
            html += '</tr></thead><tbody>';

            if (dos.length === 0) {
                html += '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada DO untuk hari ini.</td></tr>';
            } else {
                dos.forEach(function(doItem, index) {
                    html += '<tr>';
                    html += '<td><strong>' + escapeHtml(doItem.kode_do) + '</strong>';
                    if (doItem.customer) {
                        html += '<br><small class="text-muted">' + escapeHtml(doItem.customer) + '</small>';
                    }
                    if (doItem.trayek) {
                        html += '<br><small class="text-info"><i class="fa fa-route"></i> ' + escapeHtml(doItem.trayek) + '</small>';
                    }
                    html += '<input type="hidden" name="items[pic_do_status_' + index + '][type]" value="do_status">';
                    html += '<input type="hidden" name="items[pic_do_status_' + index + '][reference_id]" value="' + doItem.id + '">';
                    html += '<input type="hidden" name="items[pic_do_status_' + index + '][description]" value="' + escapeHtml(doItem.kode_do) + '">';
                    html += '</td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_' + index + '][status]" value="dibuat" class="form-check-input" checked></td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_' + index + '][status]" value="berangkat" class="form-check-input"></td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_' + index + '][status]" value="muat" class="form-check-input"></td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_' + index + '][status]" value="bongkar" class="form-check-input"></td>';
                    html += '</tr>';
                });
            }

            html += '</tbody></table></div>';
            html += '<div class="small text-muted mt-2"><i class="fa fa-info-circle"></i> Pilih salah satu status untuk setiap DO. Radio button akan otomatis nonaktifkan opsi lain pada baris yang sama.</div>';

            if (historicalDos && historicalDos.length > 0) {
                html += '<hr><h6 class="text-secondary mt-4"><i class="fa fa-history"></i> Riwayat DO (Sebelum Hari Ini)</h6>';
                html += '<div class="table-responsive"><table class="table table-sm table-bordered align-middle mb-0">';
                html += '<thead class="table-warning" style="position: sticky; top: 0; z-index: 2;"><tr>';
                html += '<th style="min-width: 150px;">No DO</th>';
                html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-file text-secondary"></i> Dibuat</th>';
                html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-truck text-primary"></i> Berangkat</th>';
                html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-box text-warning"></i> Muat</th>';
                html += '<th class="text-center" style="min-width: 90px;"><i class="fa fa-check text-success"></i> Bongkar</th>';
                html += '</tr></thead><tbody>';
                historicalDos.forEach(function(doItem, index) {
                    html += '<tr>';
                    html += '<td><strong>' + escapeHtml(doItem.kode_do) + '</strong>';
                    if (doItem.customer) {
                        html += '<br><small class="text-muted">' + escapeHtml(doItem.customer) + '</small>';
                    }
                    if (doItem.trayek) {
                        html += '<br><small class="text-info"><i class="fa fa-route"></i> ' + escapeHtml(doItem.trayek) + '</small>';
                    }
                    html += '<input type="hidden" name="items[pic_do_status_hist_' + index + '][type]" value="do_status">';
                    html += '<input type="hidden" name="items[pic_do_status_hist_' + index + '][reference_id]" value="' + doItem.id + '">';
                    html += '<input type="hidden" name="items[pic_do_status_hist_' + index + '][description]" value="' + escapeHtml(doItem.kode_do) + '">';
                    html += '</td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_hist_' + index + '][status]" value="dibuat" class="form-check-input" checked></td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_hist_' + index + '][status]" value="berangkat" class="form-check-input"></td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_hist_' + index + '][status]" value="muat" class="form-check-input"></td>';
                    html += '<td class="text-center"><input type="radio" name="items[pic_do_status_hist_' + index + '][status]" value="bongkar" class="form-check-input"></td>';
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
            }

            html += '<label class="form-label mt-3">Problem Solving (target DO 10 jika tidak sampai target solusinya apa)</label>';
            html += '<textarea class="form-control" rows="2" name="items[pic_problem_solving][notes]"></textarea>';
            html += '<input type="hidden" name="items[pic_problem_solving][type]" value="task"><input type="hidden" name="items[pic_problem_solving][description]" value="Problem Solving Target DO">';

            container.innerHTML = html;
        }

        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        function updateOperasionalContent(dos, armadas) {
            var container = document.getElementById('operasional-content');
            if (!armadas || armadas.length === 0) {
                container.innerHTML = '<div class="alert alert-info"><i class="fa fa-info-circle"></i> Tidak ada data armada.</div>';
                return;
            }
            // Use existing form structure
        }

        function updateKeuanganContent(kasBanks) {
            var container = document.getElementById('keuangan-content');
            if (!kasBanks || kasBanks.length === 0) {
                container.innerHTML = '<div class="alert alert-info"><i class="fa fa-info-circle"></i> Tidak ada kas/bank.</div>';
            }
        }

        function showLoading(show) {
            console.log(show ? 'Loading...' : 'Done');
        }

        function validateForm(event) {
            try {
                var roleType = document.getElementById('role_type_selector') ? document.getElementById('role_type_selector').value : currentRoleType;

                if (!roleType) {
                    if (event) event.stopPropagation();
                    Swal.fire({ icon: 'warning', title: 'Pilih Role', text: 'Silakan pilih tipe role terlebih dahulu.', confirmButtonText: 'OK' });
                    return false;
                }

                var mainSections = [];
                if (roleType === 'operasional') {
                    mainSections.push(document.getElementById('operasional-section'));
                } else if (roleType === 'pic_operasional') {
                    mainSections.push(document.getElementById('pic-operasional-section'));
                    mainSections.push(document.getElementById('pic-operasional-problem-section'));
                } else if (roleType === 'keuangan') {
                    mainSections.push(document.getElementById('keuangan-section'));
                } else if (roleType === 'general') {
                    mainSections.push(document.getElementById('general-section'));
                }

                var empties = [];

                mainSections.forEach(function(section) {
                    if (!section) return;
                    var container = section.style.display === 'none' ? null : section;
                    if (!container) return;

                    container.querySelectorAll('textarea, input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="radio"]):not([type="checkbox"]):not([readonly]), select').forEach(function(el) {
                        if (el.type === 'select-one' && !el.value) {
                            empties.push(getFieldLabel(el));
                        } else if (el.type !== 'select-one' && !el.value.trim()) {
                            empties.push(getFieldLabel(el));
                        }
                    });
                });

                var minLengthErrors = [];

                if (roleType === 'pic_operasional') {
                    var prioritasHariIni = document.querySelector('textarea[name="items[pic_prioritas_hari_ini][notes]"]');
                    if (prioritasHariIni && prioritasHariIni.offsetParent !== null && prioritasHariIni.value.trim().length < 100) {
                        minLengthErrors.push('3 Prioritas Hari Ini minimal 100 karakter (saat ini ' + prioritasHariIni.value.trim().length + ')');
                    }
                    var prioritasBesok = document.querySelector('textarea[name="items[pic_prioritas_besok][notes]"]');
                    if (prioritasBesok && prioritasBesok.offsetParent !== null && prioritasBesok.value.trim().length < 100) {
                        minLengthErrors.push('3 Prioritas Besok minimal 100 karakter (saat ini ' + prioritasBesok.value.trim().length + ')');
                    }
                }

                if (empties.length > 0 || minLengthErrors.length > 0) {
                    if (event) event.stopPropagation();
                    var html = '';
                    if (empties.length > 0) {
                        html += '<strong>Field berikut harus diisi:</strong><br><ul>';
                        empties.forEach(function(f) { html += '<li>' + escapeHtml(f) + '</li>'; });
                        html += '</ul>';
                    }
                    if (minLengthErrors.length > 0) {
                        html += '<strong>Minimal karakter belum terpenuhi:</strong><br><ul>';
                        minLengthErrors.forEach(function(e) { html += '<li>' + escapeHtml(e) + '</li>'; });
                        html += '</ul>';
                    }
                    Swal.fire({
                        icon: 'warning',
                        title: 'Form belum lengkap',
                        html: html,
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                return true;
            } catch(e) {
                console.error('Validasi error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    text: 'Terjadi kesalahan validasi. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
                if (event) event.stopPropagation();
                return false;
            }
        }

        function getFieldLabel(el) {
            if (el.id) {
                var lbl = document.querySelector('label[for="' + el.id.replace(/["\\]/g, '') + '"]');
                if (lbl) return lbl.textContent.trim();
            }
            var prev = el.previousElementSibling;
            if (prev && prev.tagName === 'LABEL' && prev.classList.contains('form-label')) {
                return prev.textContent.trim();
            }
            var parent = el.parentElement;
            if (parent) {
                var lbl = parent.querySelector('label.form-label');
                if (lbl) return lbl.textContent.trim();
            }
            var row = el.closest('tr');
            if (row) {
                var firstTdStrong = row.querySelector('td strong');
                if (firstTdStrong && firstTdStrong.textContent.trim()) {
                    var base = firstTdStrong.textContent.trim();
                    var labelInRow = row.querySelector('label');
                    if (labelInRow) return base + ' - ' + labelInRow.textContent.trim();
                    if (el.placeholder) return base + ' - ' + el.placeholder;
                    return base;
                }
            }
            return el.placeholder || el.name || 'Field';
        }

        function formatRupiah(input) {
            var value = input.value.replace(/[^\d]/g, '');
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                input.value = 'Rp. ' + value;
            } else {
                input.value = '';
            }
        }



        function initKeuanganSelect2($el) {
            var $selectedOpt = $el.find('option:selected');
            var selectedText = $selectedOpt.text() || '';

            $el.select2({
                ajax: {
                    url: '{{ route("daily-report.get-do-select2") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data.results };
                    },
                    cache: true
                },
                placeholder: 'Cari Kode DO',
                minimumInputLength: 1,
                templateResult: function(data) {
                    if (!data.id) return data.text;
                    var txt = data.text;
                    if (data.customer) txt += ' - ' + data.customer;
                    if (data.tujuan) txt += ' (' + data.tujuan + ')';
                    return txt;
                },
                templateSelection: function(data) {
                    return data.text || data.id || 'Cari Kode DO';
                }
            }).on('select2:select', function(e) {
                var data = e.params.data;
                var row = $(this).closest('tr.keuangan-row');
                row.find('input[name$="[tujuan]"]').val(data.tujuan || '');
            });

            // Ensure pre-selected value displays properly in edit mode
            if (selectedText) {
                var val = $selectedOpt.val();
                if (val) {
                    $el.val(val).trigger('change');
                }
            }
        }

        function addKeuanganRow() {
            var rowCount = parseInt(document.getElementById('keuangan-row-count').value);
            var newRow = rowCount;

            var html = '<tr class="keuangan-row">' +
                '<td class="text-center keuangan-no">' + (newRow + 1) + '</td>' +
                '<td><select class="form-control form-control-sm select2-do" name="items[pic_keuangan_' + newRow + '][kode_do]" style="width:100%"></select></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_keuangan_' + newRow + '][tujuan]" placeholder="Tujuan"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_keuangan_' + newRow + '][nominal_sangu]" onkeyup="formatRupiah(this)" placeholder="Rp"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-row" title="Hapus"><i class="fa fa-times"></i></button></td>' +
                '<input type="hidden" name="items[pic_keuangan_' + newRow + '][type]" value="task">' +
                '<input type="hidden" name="items[pic_keuangan_' + newRow + '][description]" value="Keuangan Operasional">' +
                '</tr>';

            document.getElementById('keuangan-operasional-body').insertAdjacentHTML('beforeend', html);
            document.getElementById('keuangan-row-count').value = newRow + 1;

            var $newSelect = $('#keuangan-operasional-body tr.keuangan-row:last .select2-do');
            initKeuanganSelect2($newSelect);
        }

        function initArmadaRusakHandler() {
            const armadaTable = document.getElementById('monitoring-armada-table');
            if (!armadaTable) return;

            const radioButtons = armadaTable.querySelectorAll('input[type="radio"][value="rusak"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', handleArmadaRusakChange);
            });
            handleArmadaRusakChange();
        }

        function handleArmadaRusakChange() {
            const section = document.getElementById('armada-rusak-section');
            const tbody = document.getElementById('armada-rusak-body');
            const armadaTable = document.getElementById('monitoring-armada-table');
            if (!section || !tbody || !armadaTable) return;

            const rows = armadaTable.querySelectorAll('tbody tr');
            const rusakRadios = Array.from(rows).map((row, index) => {
                const radio = row.querySelector('input[type="radio"][value="rusak"]');
                return radio ? { radio, index, nopol: row.querySelector('td strong')?.textContent?.trim() } : null;
            }).filter(item => item && item.radio.checked);

            if (rusakRadios.length > 0) {
                section.style.display = 'block';
                tbody.innerHTML = '';
                rusakRadios.forEach((item, i) => {
                    const armadaId = item.radio.closest('tr').querySelector('input[name*="reference_id"]')?.value || '';
                    const nopol = item.nopol || '';

                    const savedData = savedArmadaRusak[nopol] || {};
                    const savedKerusakan = savedData.kerusakan || '';
                    const savedPic = savedData.pic_bengkel || '';
                    const savedEstimasi = savedData.estimasi || '';

                    const html = `<tr class="armada-rusak-row">
                        <td>
                            <input type="text" class="form-control form-control-sm" value="${nopol}" readonly>
                            <input type="hidden" name="items[armada_rusak_${item.index}][type]" value="armada_rusak">
                            <input type="hidden" name="items[armada_rusak_${item.index}][reference_id]" value="${armadaId}">
                            <input type="hidden" name="items[armada_rusak_${item.index}][description]" value="${nopol}">
                        </td>
                        <td><textarea class="form-control form-control-sm" name="items[armada_rusak_${item.index}][kerusakan]" rows="2" placeholder="Deskripsi kerusakan">${savedKerusakan}</textarea></td>
                        <td><input type="text" class="form-control form-control-sm" name="items[armada_rusak_${item.index}][pic_bengkel]" value="${savedPic}" placeholder="Nama montir"></td>
                        <td><input type="text" class="form-control form-control-sm" name="items[armada_rusak_${item.index}][estimasi]" value="${savedEstimasi}" placeholder="Contoh: 3 hari"></td>
                    </tr>`;
                    tbody.insertAdjacentHTML('beforeend', html);
                });
            } else {
                section.style.display = 'none';
            }
        }

        var savedArmadaRusak = {};

        var customerBaruCounter = 0;
        var komplainCounter = 0;
        var driverIjinCounter = 0;

        function addCustomerBaruRow(data) {
            data = data || {};
            var tbody = document.getElementById('customer-baru-detail-body');
            if (!tbody) return;
            var i = customerBaruCounter++;
            var html = '<tr>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_customer_baru_detail_' + i + '][nama]" value="' + (data.nama || '') + '" placeholder="Nama calon customer"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_customer_baru_detail_' + i + '][no_telp]" value="' + (data.no_telp || '') + '" placeholder="Nomor telepon"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_customer_baru_detail_' + i + '][bidang_usaha]" value="' + (data.bidang_usaha || '') + '" placeholder="Bidang usaha"></td>' +
                '<td class="text-center">' + (i > 0 ? '<button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fa fa-trash"></i></button>' : '') + '</td>' +
                '<input type="hidden" name="items[pic_customer_baru_detail_' + i + '][type]" value="customer_baru">' +
                '<input type="hidden" name="items[pic_customer_baru_detail_' + i + '][description]" value="Customer Baru #' + (i + 1) + '">' +
                '</tr>';
            tbody.insertAdjacentHTML('beforeend', html);
        }

        function addKomplainRow(data) {
            data = data || {};
            var tbody = document.getElementById('komplain-detail-body');
            if (!tbody) return;
            var i = komplainCounter++;
            var html = '<tr>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_komplain_detail_' + i + '][uraian]" value="' + (data.uraian || '') + '" placeholder="Uraian komplain"></td>' +
                '<td class="text-center">' + (i > 0 ? '<button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fa fa-trash"></i></button>' : '') + '</td>' +
                '<input type="hidden" name="items[pic_komplain_detail_' + i + '][type]" value="komplain">' +
                '<input type="hidden" name="items[pic_komplain_detail_' + i + '][description]" value="Komplain #' + (i + 1) + '">' +
                '</tr>';
            tbody.insertAdjacentHTML('beforeend', html);
        }

        function removeRow(btn) {
            var tr = btn.closest('tr');
            if (tr) tr.remove();
        }

        function addDriverIjinRow(data) {
            data = data || {};
            var tbody = document.getElementById('driver-ijin-detail-body');
            if (!tbody) return;
            var i = driverIjinCounter++;
            var html = '<tr>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_driver_ijin_detail_' + i + '][nama_sopir]" value="' + (data.nama_sopir || '') + '" placeholder="Nama sopir"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_driver_ijin_detail_' + i + '][nopol]" value="' + (data.nopol || '') + '" placeholder="Nopol truk"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="items[pic_driver_ijin_detail_' + i + '][solusi]" value="' + (data.solusi || '') + '" placeholder="Solusi"></td>' +
                '<td class="text-center">' + (i > 0 ? '<button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fa fa-trash"></i></button>' : '') + '</td>' +
                '<input type="hidden" name="items[pic_driver_ijin_detail_' + i + '][type]" value="driver_ijin">' +
                '<input type="hidden" name="items[pic_driver_ijin_detail_' + i + '][description]" value="Driver Ijin #' + (i + 1) + '">' +
                '</tr>';
            tbody.insertAdjacentHTML('beforeend', html);
        }

        function initArmadaRusakHandler() {
            const armadaTable = document.getElementById('monitoring-armada-table');
            if (!armadaTable) return;

            const radioButtons = armadaTable.querySelectorAll('input[type="radio"][name*="pic_armada_status_"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', handleArmadaRusakChange);
            });
            handleArmadaRusakChange();
        }

        function handleArmadaRusakChange() {
            const section = document.getElementById('armada-rusak-section');
            const tbody = document.getElementById('armada-rusak-body');
            const armadaTable = document.getElementById('monitoring-armada-table');
            if (!section || !tbody || !armadaTable) return;

            const rows = armadaTable.querySelectorAll('tbody tr');
            const rusakRadios = Array.from(rows).map((row, index) => {
                const radio = row.querySelector('input[type="radio"][value="rusak"]');
                return radio ? { radio, index, nopol: row.querySelector('td strong')?.textContent?.trim() } : null;
            }).filter(item => item && item.radio.checked);

            if (rusakRadios.length > 0) {
                section.style.display = 'block';
                tbody.innerHTML = '';
                rusakRadios.forEach((item, i) => {
                    const armadaId = item.radio.closest('tr').querySelector('input[name*="reference_id"]')?.value || '';
                    const nopol = item.nopol || '';

                    const savedData = savedArmadaRusak[nopol] || {};
                    const savedKerusakan = savedData.kerusakan || '';
                    const savedPic = savedData.pic_bengkel || '';
                    const savedEstimasi = savedData.estimasi || '';

                    const html = `<tr class="armada-rusak-row">
                        <td>
                            <input type="text" class="form-control form-control-sm" value="${nopol}" readonly>
                            <input type="hidden" name="items[armada_rusak_${item.index}][type]" value="armada_rusak">
                            <input type="hidden" name="items[armada_rusak_${item.index}][reference_id]" value="${armadaId}">
                            <input type="hidden" name="items[armada_rusak_${item.index}][description]" value="${nopol}">
                        </td>
                        <td><textarea class="form-control form-control-sm" name="items[armada_rusak_${item.index}][kerusakan]" rows="2" placeholder="Deskripsi kerusakan">${savedKerusakan}</textarea></td>
                        <td><input type="text" class="form-control form-control-sm" name="items[armada_rusak_${item.index}][pic_bengkel]" value="${savedPic}" placeholder="Nama montir"></td>
                        <td><input type="text" class="form-control form-control-sm" name="items[armada_rusak_${item.index}][estimasi]" value="${savedEstimasi}" placeholder="Contoh: 3 hari"></td>
                    </tr>`;
                    tbody.insertAdjacentHTML('beforeend', html);
                });
            } else {
                section.style.display = 'none';
            }
        }

        @php
            $savedArmadaRusak = [];
            $savedCustomerBaru = [];
            $savedKomplain = [];
            $savedDriverIjin = [];
            if ($isEdit && isset($report)) {
                $rusakItems = $report->items->where('type', 'armada_rusak');
                foreach ($rusakItems as $rusak) {
                    $details = is_array($rusak->notes) ? $rusak->notes : json_decode($rusak->notes, true);
                    $savedArmadaRusak[$rusak->description] = $details;
                }
                $customerItems = $report->items->where('type', 'customer_baru')->values();
                foreach ($customerItems as $item) {
                    $details = is_array($item->notes) ? $item->notes : json_decode($item->notes, true);
                    $savedCustomerBaru[] = $details;
                }
                $komplainItems = $report->items->where('type', 'komplain')->values();
                foreach ($komplainItems as $item) {
                    $details = is_array($item->notes) ? $item->notes : json_decode($item->notes, true);
                    $savedKomplain[] = $details;
                }
                $driverIjinItems = $report->items->where('type', 'driver_ijin')->values();
                foreach ($driverIjinItems as $item) {
                    $details = is_array($item->notes) ? $item->notes : json_decode($item->notes, true);
                    $savedDriverIjin[] = $details;
                }
            }
        @endphp
        savedArmadaRusak = @json($savedArmadaRusak);
        var savedCustomerBaru = @json($savedCustomerBaru);
        var savedKomplain = @json($savedKomplain);
        var savedDriverIjin = @json($savedDriverIjin);

        function initCustomerKomplain() {
            customerBaruCounter = 0;
            komplainCounter = 0;
            driverIjinCounter = 0;
            document.getElementById('customer-baru-detail-body').innerHTML = '';
            document.getElementById('komplain-detail-body').innerHTML = '';
            document.getElementById('driver-ijin-detail-body').innerHTML = '';

            if (isEditMode && savedCustomerBaru.length > 0) {
                savedCustomerBaru.forEach(function(data) {
                    addCustomerBaruRow(data);
                });
            } else {
                addCustomerBaruRow();
            }

            if (isEditMode && savedKomplain.length > 0) {
                savedKomplain.forEach(function(data) {
                    addKomplainRow(data);
                });
            } else {
                addKomplainRow();
            }

            if (isEditMode && savedDriverIjin.length > 0) {
                savedDriverIjin.forEach(function(data) {
                    addDriverIjinRow(data);
                });
            } else {
                addDriverIjinRow();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initArmadaRusakHandler();
            initCustomerKomplain();
        });
    </script>
@endsection
@endsection
