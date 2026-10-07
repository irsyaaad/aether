@extends('layouts.app')

@section('title', 'Detail Submission Plotingan - ' . config('app.name'))
@section('page_title', 'Detail Submission Plotingan Tugas')

@section('toolbar_actions')
<div class="flex items-center gap-2">
    <a href="{{ route('tasks.assignment.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
        Kembali ke Plotting Tugas
    </a>
    @if($employee)
    <a href="{{ route('tasks.assignment.edit', $employee->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
        <i data-lucide="clipboard-edit" class="w-4 h-4"></i>
        Edit Plotting Karyawan
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- 1. Employee Profile & Status Banner -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            @if($employee && $employee->avatar_url)
                <img src="{{ $employee->avatar_url }}" alt="{{ $employee->display_name }}" class="w-14 h-14 rounded-full object-cover border-2 border-primary/20 shrink-0">
            @else
                <div class="w-14 h-14 rounded-full bg-primary/10 text-primary font-bold text-lg flex items-center justify-center border-2 border-primary/20 shrink-0">
                    {{ strtoupper(substr($employee->display_name ?? 'EM', 0, 2)) }}
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">{{ $employee->display_name ?? '-' }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $assignee->status_badge_class }}">
                        {{ $assignee->status_label }}
                    </span>
                    @if($assignee->is_overdue)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            Terlambat
                        </span>
                    @endif
                </div>
                <div class="text-xs text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-700">
                        {{ $employee->employee_number ?: ($employee->nik_ktp ?: '-') }}
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="flex items-center gap-1 font-medium text-gray-700">
                        <i data-lucide="briefcase" class="w-3.5 h-3.5 text-gray-400"></i>
                        {{ $employee->position->name ?? $employee->jabatan ?? '-' }}
                    </span>
                    @if($employee && $employee->department)
                        <span class="text-gray-300">•</span>
                        <span class="flex items-center gap-1 text-gray-600">
                            <i data-lucide="network" class="w-3.5 h-3.5 text-gray-400"></i>
                            {{ $employee->department->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progress Box -->
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 min-w-[220px] space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                <span>Progress Pengerjaan</span>
                <span class="text-primary font-mono font-bold text-sm">{{ (int)$assignee->progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                <div class="{{ (int)$assignee->progress == 100 ? 'bg-emerald-500' : 'bg-primary' }} h-2.5 rounded-full transition-all" style="width: {{ (int)$assignee->progress }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-gray-500 pt-0.5">
                <span>Status Ploting</span>
                <span class="font-semibold text-gray-800">{{ $assignee->status_label }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Main Grid: Task Details & Submission Realization -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column (2 Cols): Task & Submission Output -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card: Detail Tugas yang Diploting -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/70">
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-primary"></i>
                        Informasi Tugas yang Diploting
                    </h3>
                    <div class="flex items-center gap-1.5">
                        @if($task && $task->priority)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $task->priority->badge_class }}">
                                {{ $task->priority->name }}
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $task->output_type_label ?? 'Checklist' }}
                        </span>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-gray-900">{{ $task->title ?? '-' }}</h1>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mt-1 flex-wrap">
                            <span class="flex items-center gap-1">
                                <i data-lucide="tag" class="w-3.5 h-3.5 text-gray-400"></i>
                                Kategori: <strong class="text-gray-700">{{ $task->category->name ?? 'Tanpa Kategori' }}</strong>
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="layers" class="w-3.5 h-3.5 text-gray-400"></i>
                                Tipe Tugas: <strong class="text-gray-700">{{ $task->task_type_label ?? 'Insidentil' }}</strong>
                            </span>
                        </div>
                    </div>

                    @if($task && $task->description)
                        <div class="pt-3 border-t border-gray-100">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi Tugas</label>
                            <div class="text-xs text-gray-700 leading-relaxed whitespace-pre-wrap bg-slate-50 p-3 rounded-lg border border-slate-200">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($assignee->notes)
                        <div class="pt-3 border-t border-gray-100">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Penugasan (Dari Atasan/Admin)</label>
                            <div class="text-xs text-gray-700 bg-amber-50 p-3 rounded-lg border border-amber-200 italic">
                                "{{ $assignee->notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card: Realisasi Hasil / Output Pengerjaan -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/70">
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                        Realisasi Hasil Pengerjaan (Output)
                    </h3>
                    <span class="text-xs font-medium text-gray-500">Tipe: {{ $task->output_type_label ?? 'Checklist' }}</span>
                </div>
                <div class="p-5 space-y-4">
                    @if($task && $task->output_type === 'target')
                        <!-- Realisasi Target -->
                        <div class="p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block">Capaian Realisasi Target Karyawan</span>
                                <p class="text-2xl font-bold font-mono text-amber-900 mt-1">
                                    {{ $submission ? number_format($submission->achieved_amount, 0, ',', '.') : '0' }}
                                </p>
                            </div>
                            @if($task->target_amount)
                                <div class="text-left sm:text-right border-t sm:border-t-0 sm:border-l border-amber-200 pt-3 sm:pt-0 sm:pl-4">
                                    <span class="text-xs text-amber-700 block">Target Master Tugas</span>
                                    <span class="text-lg font-bold font-mono text-gray-700">{{ number_format($task->target_amount, 0, ',', '.') }}</span>
                                    @if($task->target_unit)
                                        <span class="text-xs text-gray-500">{{ $task->target_unit }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                    @elseif($task && $task->output_type === 'stock_update')
                        <!-- Laporan Stok Produk Kosong -->
                        @php
                            $stockUpdates = $submission ? $submission->stockUpdates : collect();
                        @endphp
                        @if($stockUpdates->isNotEmpty())
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="package-x" class="w-4 h-4 text-rose-600"></i>
                                        Rincian Produk Kosong yang Dilaporkan ({{ $stockUpdates->count() }})
                                    </span>
                                </div>
                                <div class="overflow-x-auto border border-gray-200 rounded-xl">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                                            <tr>
                                                <th class="p-3">Toko</th>
                                                <th class="p-3">Produk</th>
                                                <th class="p-3 text-center">Status</th>
                                                <th class="p-3 text-center">Sisa Stok</th>
                                                <th class="p-3">Catatan</th>
                                                <th class="p-3 text-right">Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($stockUpdates as $stk)
                                                <tr class="hover:bg-gray-50/80 transition-colors">
                                                    <td class="p-3 font-medium text-gray-900">{{ $stk->toko->name ?? '-' }}</td>
                                                    <td class="p-3 text-gray-800">{{ $stk->produk->name ?? '-' }}</td>
                                                    <td class="p-3 text-center">
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $stk->stock_status_badge_class }}">
                                                            {{ $stk->stock_status_label }}
                                                        </span>
                                                    </td>
                                                    <td class="p-3 text-center font-mono font-bold {{ $stk->current_stock <= 0 ? 'text-red-600' : 'text-gray-700' }}">
                                                        {{ $stk->current_stock }}
                                                    </td>
                                                    <td class="p-3 text-gray-500 italic">{{ $stk->notes ?: '-' }}</td>
                                                    <td class="p-3 text-right font-mono text-[11px] text-gray-400">
                                                        {{ $stk->created_at ? $stk->created_at->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="py-8 text-center text-xs text-gray-400 italic bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                Belum ada laporan produk kosong yang disubmit pada plotting tugas ini.
                            </div>
                        @endif

                    @elseif($task && $task->output_type === 'data_list')
                        <!-- Entri Baris Data (Tabel) -->
                        @php
                            $dataEntries = $submission ? $submission->dataEntries : collect();
                            $columns = !empty($task->list_columns) ? (array)$task->list_columns : [];
                            if (empty($columns) && $dataEntries->isNotEmpty()) {
                                $firstEntry = $dataEntries->first();
                                if ($firstEntry && is_array($firstEntry->entry_data)) {
                                    $columns = array_keys($firstEntry->entry_data);
                                }
                            }
                            if (empty($columns)) {
                                $columns = ['Data / Jawaban'];
                            }
                        @endphp
                        @if($dataEntries->isNotEmpty())
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="table" class="w-4 h-4 text-blue-600"></i>
                                        Rincian Data Jawaban yang Diinput Karyawan ({{ $dataEntries->count() }} Baris)
                                    </span>
                                    @if($task->target_count)
                                        <span class="text-xs text-gray-500">
                                            Target: <strong class="text-gray-800 font-mono">{{ $task->target_count }} baris</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="overflow-x-auto border border-gray-200 rounded-xl">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase text-[11px]">
                                            <tr>
                                                <th class="p-3 w-12 text-center">#</th>
                                                @foreach($columns as $col)
                                                    <th class="p-3">{{ $col }}</th>
                                                @endforeach
                                                <th class="p-3 text-right w-36">Waktu Input</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($dataEntries as $idx => $entry)
                                                <tr class="hover:bg-gray-50/80 transition-colors">
                                                    <td class="p-3 text-center font-mono text-gray-400">{{ $idx + 1 }}</td>
                                                    @foreach($columns as $col)
                                                        @php
                                                            $val = $entry->entry_data[$col] ?? null;
                                                            if ($val === null && is_array($entry->entry_data)) {
                                                                foreach ($entry->entry_data as $k => $v) {
                                                                    if (strcasecmp($k, $col) === 0) {
                                                                        $val = $v;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            if (preg_match('/toko|store|cabang|marketplace|channel/i', $col) && is_numeric($val) && $entry->toko) {
                                                                $val = $entry->toko->name;
                                                            } elseif (preg_match('/produk|barang|item|sku/i', $col) && is_numeric($val) && $entry->produk) {
                                                                $val = $entry->produk->name;
                                                            }
                                                            $displayVal = $val ?? '-';
                                                            if (is_array($displayVal)) {
                                                                $displayVal = json_encode($displayVal);
                                                            }
                                                        @endphp
                                                        <td class="p-3 font-medium text-gray-800">
                                                            {{ $displayVal }}
                                                        </td>
                                                    @endforeach
                                                    <td class="p-3 text-right font-mono text-[11px] text-gray-400 whitespace-nowrap">
                                                        {{ $entry->created_at ? $entry->created_at->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="py-8 text-center text-xs text-gray-400 italic bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                Belum ada entri baris data yang diinput pada plotting tugas ini.
                            </div>
                        @endif

                    @elseif($task && $task->output_type === 'full_custom')
                        <!-- Full Custom Output (Multi-Row Support) -->
                        @php
                            $rawCustomData = $submission ? ($submission->custom_data ?? []) : [];
                            $customFields = $task->custom_fields ?? [];
                            $submittedRows = [];
                            if (!empty($rawCustomData) && is_array($rawCustomData)) {
                                $firstKey = array_key_first($rawCustomData);
                                if ($firstKey !== null && is_numeric($firstKey)) {
                                    $submittedRows = $rawCustomData;
                                } else {
                                    $submittedRows = [ $rawCustomData ];
                                }
                            }
                        @endphp
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-purple-600"></i>
                                    Hasil Pengisian Form Full Custom ({{ count($submittedRows) }} Baris Data)
                                </span>
                            </div>
                            <div class="space-y-3">
                                @forelse($submittedRows as $rIdx => $rowVal)
                                    <div class="bg-purple-50/50 p-4 rounded-xl border border-purple-200 space-y-3">
                                        <div class="flex items-center justify-between pb-2 border-b border-purple-100">
                                            <span class="text-xs font-bold text-purple-900 flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-full bg-purple-600 text-white text-[11px] font-bold flex items-center justify-center">
                                                    {{ $rIdx + 1 }}
                                                </span>
                                                Baris Data ke-{{ $rIdx + 1 }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                            @foreach($customFields as $cf)
                                                @php
                                                    $cfKey = $cf['key'] ?? '';
                                                    $cfVal = $rowVal[$cfKey] ?? null;
                                                    $cfType = $cf['type'] ?? 'text';
                                                @endphp
                                                <div class="bg-white p-3 rounded-lg border border-purple-100 shadow-2xs space-y-1 {{ $cfType === 'textarea' ? 'sm:col-span-2 md:col-span-3' : '' }}">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-[10px] font-bold text-purple-900 uppercase tracking-wider">{{ $cf['label'] ?? '-' }}</span>
                                                        <span class="text-[9px] text-gray-400 font-mono">{{ $cfType }}</span>
                                                    </div>
                                                    <div class="text-xs text-gray-800">
                                                        @if($cfType === 'file')
                                                            @if(is_array($cfVal) && !empty($cfVal['url']))
                                                                <a href="{{ $cfVal['url'] }}" target="_blank" class="text-primary hover:underline font-semibold flex items-center gap-1">
                                                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> {{ $cfVal['original_name'] ?? 'Buka File' }}
                                                                </a>
                                                            @else
                                                                <span class="text-gray-400 italic">Tidak ada file</span>
                                                            @endif
                                                        @elseif($cfType === 'textarea')
                                                            <div class="whitespace-pre-wrap leading-relaxed bg-gray-50/70 p-2 rounded">{{ $cfVal ?: '-' }}</div>
                                                        @elseif($cfType === 'number')
                                                            <span class="font-bold font-mono">{{ is_numeric($cfVal) ? number_format((float)$cfVal, 0, ',', '.') : ($cfVal ?: '-') }}</span>
                                                        @elseif($cfType === 'url')
                                                            @if(!empty($cfVal))
                                                                <a href="{{ Str::startsWith($cfVal, ['http://', 'https://']) ? $cfVal : 'https://' . $cfVal }}" target="_blank" class="text-primary hover:underline font-semibold inline-flex items-center gap-1 break-all">
                                                                    <span>{{ $cfVal }}</span>
                                                                    <i data-lucide="external-link" class="w-3 h-3 shrink-0"></i>
                                                                </a>
                                                            @else
                                                                <span class="text-gray-400 italic">-</span>
                                                            @endif
                                                        @elseif($cfType === 'email')
                                                            @if(!empty($cfVal))
                                                                <a href="mailto:{{ $cfVal }}" class="text-primary hover:underline font-semibold inline-flex items-center gap-1 break-all">
                                                                    <i data-lucide="mail" class="w-3 h-3 shrink-0"></i>
                                                                    <span>{{ $cfVal }}</span>
                                                                </a>
                                                            @else
                                                                <span class="text-gray-400 italic">-</span>
                                                            @endif
                                                        @elseif($cfType === 'date')
                                                            @if(!empty($cfVal))
                                                                <span class="inline-flex items-center gap-1 font-medium text-gray-900">
                                                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                                                    {{ \Carbon\Carbon::hasFormat($cfVal, 'Y-m-d') ? \Carbon\Carbon::parse($cfVal)->translatedFormat('d M Y') : $cfVal }}
                                                                </span>
                                                            @else
                                                                <span class="text-gray-400 italic">-</span>
                                                            @endif
                                                        @elseif($cfType === 'time')
                                                            @if(!empty($cfVal))
                                                                <span class="inline-flex items-center gap-1 font-medium text-gray-900 font-mono">
                                                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i>
                                                                    {{ $cfVal }}
                                                                </span>
                                                            @else
                                                                <span class="text-gray-400 italic">-</span>
                                                            @endif
                                                        @elseif($cfType === 'datetime')
                                                            @if(!empty($cfVal))
                                                                <span class="inline-flex items-center gap-1 font-medium text-gray-900 font-mono">
                                                                    <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-gray-400"></i>
                                                                    {{ str_replace('T', ' ', $cfVal) }}
                                                                </span>
                                                            @else
                                                                <span class="text-gray-400 italic">-</span>
                                                            @endif
                                                        @else
                                                            <span class="font-medium">{{ $cfVal ?: '-' }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400 italic">Tidak ada data pengerjaan.</p>
                                @endforelse
                            </div>
                        </div>

                    @else
                        <!-- Checklist / Insidentil Output -->
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg {{ (int)$assignee->progress == 100 ? 'bg-emerald-100 text-emerald-600' : 'bg-primary/10 text-primary' }} flex items-center justify-center">
                                    <i data-lucide="{{ (int)$assignee->progress == 100 ? 'check' : 'clock' }}" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-900">Status Pengerjaan Checklist</p>
                                    <p class="text-[11px] text-gray-500">Tugas ini diselesaikan dengan konfirmasi penyelesaian dan bukti pengerjaan.</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $assignee->status_badge_class }}">
                                {{ $assignee->status_label }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card: Bukti Pengerjaan (Foto, Tautan, Catatan) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/70">
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="paperclip" class="w-4 h-4 text-primary"></i>
                        Bukti & Keterangan Penyelesaian Tugas
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    @if($submission && ($submission->completion_file_url || $submission->completion_url || $submission->completion_notes))
                        <!-- Tautan Link -->
                        @if($submission->completion_url)
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-600">Tautan Link Bukti</label>
                                <a href="{{ $submission->completion_url }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 transition-colors w-full break-all">
                                    <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i>
                                    <span>{{ $submission->completion_url }}</span>
                                </a>
                            </div>
                        @endif

                        <!-- Foto Bukti -->
                        @if($submission->completion_file_url)
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-600">Foto Bukti Pengerjaan</label>
                                <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-slate-100 max-h-96 flex items-center justify-center">
                                    <img src="{{ $submission->completion_file_url }}" alt="Foto Bukti Pengerjaan" class="max-h-96 object-contain w-full">
                                    <a href="{{ $submission->completion_file_url }}" target="_blank" class="absolute bottom-3 right-3 px-3.5 py-2 text-xs font-semibold text-white bg-black/75 hover:bg-black rounded-lg transition-colors flex items-center gap-1.5 backdrop-blur-xs shadow-lg">
                                        <i data-lucide="maximize-2" class="w-4 h-4"></i>
                                        Buka Ukuran Penuh
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Catatan Karyawan -->
                        @if($submission->completion_notes)
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-600">Catatan Penyelesaian dari Karyawan</label>
                                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-800 leading-relaxed whitespace-pre-wrap">
                                    "{!! nl2br(e($submission->completion_notes)) !!}"
                                </div>
                            </div>
                        @endif

                        @if($submission->completed_at)
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                <span>Waktu Selesai Disubmit:</span>
                                <span class="font-mono font-bold text-gray-800">{{ $submission->completed_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    @else
                        <div class="py-10 text-center text-xs text-gray-400 italic bg-gray-50 rounded-xl border border-dashed border-gray-200 space-y-1">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto text-gray-300"></i>
                            <p class="font-medium text-gray-500">Belum ada bukti submitan</p>
                            <p class="text-[11px] text-gray-400">Karyawan belum mengunggah foto, tautan link, atau catatan penyelesaian untuk plotting tugas ini.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column (1 Col): Meta Information & Timeline -->
        <div class="space-y-6">

            <!-- Card: Parameter Penugasan & Waktu -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-gray-100">
                    <i data-lucide="calendar" class="w-4 h-4 text-primary"></i>
                    Jadwal & Penugasan
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[11px]">Tanggal Mulai Pengerjaan</span>
                        <span class="font-mono font-semibold text-gray-800">
                            {{ $assignee->effective_start_date ? $assignee->effective_start_date->format('d M Y') : '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-[11px]">Batas Waktu (Deadline)</span>
                        <span class="font-mono font-bold {{ $assignee->is_overdue ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $assignee->effective_due_date ? $assignee->effective_due_date->format('d M Y') : '-' }}
                        </span>
                        @if($assignee->is_overdue)
                            <span class="text-[10px] text-red-600 font-bold bg-red-100 px-1.5 py-0.5 rounded ml-1">Terlambat</span>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-gray-400 block text-[11px]">Ditugaskan Oleh</span>
                        <span class="font-medium text-gray-800">
                            {{ $assignee->assigner->nm_user ?? 'Admin / Sistem' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-[11px]">Waktu Penugasan</span>
                        <span class="font-mono text-gray-700">
                            {{ $assignee->assigned_at ? $assignee->assigned_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>

                    @if($submission && $submission->completed_at)
                        <div class="pt-2 border-t border-gray-100">
                            <span class="text-gray-400 block text-[11px]">Waktu Selesai Pengerjaan</span>
                            <span class="font-mono font-bold text-emerald-600">
                                {{ $submission->completed_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card: Navigasi Aksi -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-3">
                <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider pb-2 border-b border-gray-100">
                    Aksi Lainnya
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('tasks.assignment.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <i data-lucide="list" class="w-4 h-4 text-gray-500"></i>
                        Daftar Penugasan Tugas
                    </a>
                    @if($employee)
                    <a href="{{ route('tasks.assignment.edit', $employee->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <i data-lucide="clipboard-edit" class="w-4 h-4"></i>
                        Edit Plotting Tugas Karyawan Ini
                    </a>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
