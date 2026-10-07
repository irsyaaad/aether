@extends('template.root')
@section('page-name', 'Rangkuman Laporan Harian')
@section('title-page', 'Rangkuman Detail Laporan Harian Karyawan')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fa fa-chart-line text-primary"></i> Rangkuman Detail Laporan Harian
            </h5>
            <div>
                <a href="{{ route('daily-report.create') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-plus"></i> Buat Laporan
                </a>
                <a href="{{ route('daily-report.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-list"></i> Daftar Laporan
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($reports->count() > 0)
                @foreach($reports as $report)
                    <div class="border rounded p-4 mb-4 report-item {{ $loop->last ? '' : 'border-bottom' }}">
                        {{-- Report Header --}}
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Tanggal:</strong><br>
                                <span class="text-primary">{{ $report->report_date->format('d F Y') }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Karyawan:</strong><br>
                                {{ $report->user->nm_user ?? '-' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Role:</strong><br>
                                <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $report->role_type)) }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Total Item:</strong><br>
                                <span class="badge bg-primary">{{ $report->items->count() }} item</span>
                            </div>
                        </div>

                        {{-- Summary --}}
                        @if($report->summary)
                            <div class="mb-3">
                                <strong>Ringkasan:</strong>
                                <div class="alert alert-info mt-2">{{ $report->summary }}</div>
                            </div>
                        @endif

                        {{-- Role-specific content --}}
                        @if(in_array($report->role_type, ['operasional', 'staff_operasional']))
                            {{-- Operasional/Staff Operasional: Planning & Locations --}}
                            @php
                                $doTomorrowItem = $report->items->where('type', 'task')->where('description', 'Rencana DO Besok')->first();
                                $doPlanItems = $report->items->where('type', 'do_plan');
                                $armadaItems = $report->items->where('type', 'armada');
                            @endphp

                            <div class="row">
                                {{-- DO Planning --}}
                                @if($doTomorrowItem || $doPlanItems->count() > 0)
                                    <div class="col-md-6">
                                        <h6 class="text-info mb-2"><i class="fa fa-calendar-plus"></i> Rencana DO Besok</h6>
                                        
                                        @if($doTomorrowItem)
                                            <p><strong>Estimasi:</strong> {{ $doTomorrowItem->notes }} DO</p>
                                        @endif
                                        
                                        @if($doPlanItems->count() > 0)
                                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light sticky-top">
                                                        <tr>
                                                            <th>DO #</th>
                                                            <th>Customer</th>
                                                            <th>Rute</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($doPlanItems as $item)
                                                            @php
                                                                $notes = explode(' | ', $item->notes);
                                                                $customer = isset($notes[0]) ? str_replace('Customer: ', '', $notes[0]) : '-';
                                                                $rute = isset($notes[1]) ? str_replace('Rute: ', '', $notes[1]) : '-';
                                                            @endphp
                                                            <tr>
                                                                <td><strong>{{ str_replace('Rencana DO #', '', $item->description) }}</strong></td>
                                                                <td>{{ $customer }}</td>
                                                                <td>{{ $rute }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Armada Locations --}}
                                @if($armadaItems->count() > 0)
                                    <div class="col-md-6">
                                        <h6 class="text-warning mb-2"><i class="fa fa-map-marker-alt"></i> Lokasi Armada ({{ $armadaItems->count() }})</h6>
                                        
                                        <div class="table-responsive">
                                            <table id="armada-location-table-{{ $report->id }}" class="table table-sm table-bordered" style="width: 100%;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="20%">No. Polisi</th>
                                                        <th width="20%">Nama Armada</th>
                                                        <th width="25%">Lokasi</th>
                                                        <th width="15%">Status</th>
                                                        <th width="15%">Sopir</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        @elseif($report->role_type === 'pic_operasional')
                            <h6 class="text-warning mb-2"><i class="fa fa-clipboard-list"></i> Ringkasan PIC Operasional</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report->items as $item)
                                            @if($item->description)
                                                <tr>
                                                    <td>{{ $item->description }}</td>
                                                    <td>{{ $item->notes ?: '-' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        @elseif($report->role_type === 'keuangan')
                            {{-- Keuangan: Bank Reports --}}
                            @php
                                $bankItems = $report->items->whereIn('type', ['bank_transaksi', 'bank_nominal', 'bank_mutasi', 'bank_nominal_mutasi']);
                                $groupedBankItems = $bankItems->groupBy('reference_id');
                            @endphp

                            @if($groupedBankItems->count() > 0)
                                <h6 class="text-success mb-2"><i class="fa fa-money-bill"></i> Laporan Keuangan ({{ $groupedBankItems->count() }} Bank)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Bank</th>
                                                <th>Total Transaksi</th>
                                                <th>Nominal Transaksi</th>
                                                <th>Total Mutasi</th>
                                                <th>Nominal Mutasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupedBankItems as $bankId => $items)
                                                @php
                                                    $firstItem = $items->first();
                                                    $bankName = $firstItem->description ?? "Bank ID: $bankId";
                                                    $transaksiItem = $items->where('type', 'bank_transaksi')->first();
                                                    $nominalItem = $items->where('type', 'bank_nominal')->first();
                                                    $mutasiItem = $items->where('type', 'bank_mutasi')->first();
                                                    $nominalMutasiItem = $items->where('type', 'bank_nominal_mutasi')->first();
                                                    
                                                    $cleanBankName = str_replace(['Total Transaksi ', 'Nominal Transaksi ', 'Total Mutasi ', 'Nominal Mutasi '], '', $bankName);
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $cleanBankName }}</strong></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary">{{ $transaksiItem->notes ?? '0' }}</span>
                                                    </td>
                                                    <td class="text-end">{{ $nominalItem->notes ?? 'Rp. 0' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info">{{ $mutasiItem->notes ?? '0' }}</span>
                                                    </td>
                                                    <td class="text-end">{{ $nominalMutasiItem->notes ?? 'Rp. 0' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        @elseif($report->role_type === 'general')
                            {{-- General: Activities --}}
                            @php
                                $mainTaskItem = $report->items->where('type', 'task')->first();
                            @endphp

                            @if($mainTaskItem)
                                <h6 class="text-secondary mb-2"><i class="fa fa-tasks"></i> Kegiatan Umum</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr>
                                                <td width="25%"><strong>Kegiatan Utama:</strong></td>
                                                <td>{{ $mainTaskItem->description }}</td>
                                            </tr>
                                            @if($mainTaskItem->notes)
                                                <tr>
                                                    <td><strong>Keterangan:</strong></td>
                                                    <td>{{ $mainTaskItem->notes }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif

                        {{-- Timestamp --}}
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">
                                <i class="fa fa-clock"></i> Dibuat: {{ $report->created_at->format('d/m/Y H:i') }}
                                @if($report->updated_at->ne($report->created_at))
                                    | <i class="fa fa-edit"></i> Diperbarui: {{ $report->updated_at->format('d/m/Y H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fa fa-info-circle"></i> Belum ada laporan harian yang disubmit.
                </div>
            @endif
        </div>
    </div>

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize DataTables for each armada location table
            @foreach($reports as $report)
                @php
                    $armadaItems = $report->items->where('type', 'armada');
                @endphp
                @if($armadaItems->count() > 0)
                    $('#armada-location-table-{{ $report->id }}').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('daily-report.armada-location-data', $report->id) }}",
                        pageLength: 5,
                        lengthMenu: [[5, 10, 25], [5, 10, 25]],
                        language: {
                            processing: "Memuat...",
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ armada",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 armada",
                            infoFiltered: "(disaring dari _MAX_ total armada)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data armada"
                        },
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                            { data: 'nopol', name: 'nopol' },
                            { data: 'nama_armada', name: 'nama_armada' },
                            { data: 'lokasi', name: 'lokasi' },
                            { data: 'status_armada', name: 'status_armada', orderable: false, searchable: false },
                            { data: 'sopir', name: 'sopir', orderable: false, searchable: false }
                        ],
                        order: [[1, 'asc']],
                        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
                        responsive: true,
                        autoWidth: false,
                        scrollX: false
                    });
                @endif
            @endforeach
        });
    </script>
@endsection

@section('style')
    <style>
        .report-item {
            transition: box-shadow 0.2s ease;
        }
        .report-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .table-responsive {
            border-radius: 0.375rem;
        }
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        /* DataTables styling for armada tables */
        .dataTables_wrapper .dataTables_length select {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .dataTables_wrapper .dataTables_info {
            font-size: 0.875rem;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Compact table styling */
        .table-sm th, .table-sm td {
            padding: 0.3rem;
            font-size: 0.875rem;
        }
        
        /* Ensure tables don't overflow */
        .dataTables_wrapper {
            overflow-x: auto;
        }
    </style>
@endsection
@endsection
