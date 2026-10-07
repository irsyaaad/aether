@extends('layouts.app')

@section('title', 'Plotting Tugas: ' . $employee->display_name . ' - ' . config('app.name'))
@section('page_title', 'Plotting Tugas: ' . $employee->display_name)

@section('toolbar_actions')
<a href="{{ route('tasks.assignment.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
    Kembali ke Daftar Karyawan
</a>
@endsection

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Employee Profile Banner Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($employee->avatar_url)
                <img src="{{ $employee->avatar_url }}" alt="{{ $employee->display_name }}" class="w-14 h-14 rounded-full object-cover border-2 border-primary/20 shrink-0">
            @else
                <div class="w-14 h-14 rounded-full bg-primary/10 text-primary font-bold text-lg flex items-center justify-center border-2 border-primary/20 shrink-0">
                    {{ strtoupper(substr($employee->display_name, 0, 2)) }}
                </div>
            @endif
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base font-bold text-gray-900">{{ $employee->display_name }}</h2>
                    <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                        {{ $employee->employee_number ?: ($employee->nik_ktp ?: '-') }}
                    </span>
                </div>
                <p class="text-xs text-gray-600 mt-1 flex items-center gap-1.5">
                    <i data-lucide="briefcase" class="w-3.5 h-3.5 text-gray-400"></i>
                    {{ $employee->position->name ?? $employee->jabatan ?? '-' }}
                    @if($employee->department)
                        <span class="text-gray-300">•</span>
                        <i data-lucide="network" class="w-3.5 h-3.5 text-gray-400"></i>
                        {{ $employee->department->name }}
                    @endif
                    <span class="text-gray-300">•</span>
                    <i data-lucide="building-2" class="w-3.5 h-3.5 text-gray-400"></i>
                    {{ $employee->company->name ?? '-' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end sm:self-auto bg-gray-50 p-3 rounded-xl border border-gray-200/80">
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wider">Total Tugas Terploting</span>
                <span class="text-lg font-bold text-primary" id="selected-counter">
                    {{ count($assignedTaskIds) }} Tugas
                </span>
            </div>
        </div>
    </div>

    <!-- Plotting Form -->
    <form action="{{ route('tasks.assignment.save-plot', $employee->id) }}" method="POST" id="form-plot-tasks">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden space-y-4">
            <!-- Filter & Quick-action Toolbar -->
            <div class="p-5 border-b border-gray-100 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <i data-lucide="list-checks" class="w-4 h-4 text-primary"></i>
                            Plotting Master Tugas ke Karyawan
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Pilih master task yang akan diberikan dan tentukan <strong>tenggat waktu</strong> spesifik untuk karyawan ini.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button type="button" onclick="checkAllVisible(true)" class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Centang Semua
                        </button>
                        <button type="button" onclick="checkAllVisible(false)" class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Hapus Centang
                        </button>
                    </div>
                </div>

                <!-- Batch Set Due Date Toolbar -->
                <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-1.5 text-blue-900 font-semibold">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600"></i>
                        Set Cepat Tenggat Waktu untuk Tugas Tercentang:
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <button type="button" onclick="setDueDateForChecked('today')" class="px-2 py-1 text-[11px] font-semibold bg-white text-blue-700 border border-blue-200 rounded hover:bg-blue-50 transition-colors">
                            Hari Ini
                        </button>
                        <button type="button" onclick="setDueDateForChecked('plus3')" class="px-2 py-1 text-[11px] font-semibold bg-white text-blue-700 border border-blue-200 rounded hover:bg-blue-50 transition-colors">
                            +3 Hari
                        </button>
                        <button type="button" onclick="setDueDateForChecked('plus7')" class="px-2 py-1 text-[11px] font-semibold bg-white text-blue-700 border border-blue-200 rounded hover:bg-blue-50 transition-colors">
                            +7 Hari (1 Minggu)
                        </button>
                        <button type="button" onclick="setDueDateForChecked('endOfMonth')" class="px-2 py-1 text-[11px] font-semibold bg-white text-blue-700 border border-blue-200 rounded hover:bg-blue-50 transition-colors">
                            Akhir Bulan Ini
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-1">
                    <!-- Search Input -->
                    <div class="sm:col-span-2 relative">
                        <input type="text" id="task-search-input" onkeyup="filterTasks()" placeholder="Cari nomor tugas atau judul master task..." class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                    </div>

                    <!-- Type Filter Dropdown -->
                    <div>
                        <select id="task-type-select" onchange="filterTasks()" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="">Semua Tipe Sifat</option>
                            <option value="harian">Harian (Auto-daily)</option>
                            <option value="bulanan">Bulanan (Auto-monthly)</option>
                            <option value="insidentil">Insidentil (Sekali jalan)</option>
                        </select>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div>
                        <select id="task-category-select" onchange="filterTasks()" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="plotting-tasks-table">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">Plot</th>
                            <th class="py-3 px-4">No. Master</th>
                            <th class="py-3 px-4 min-w-[220px]">Judul Master Task</th>
                            <th class="py-3 px-4">Tipe & Output</th>
                            <th class="py-3 px-4">Kategori & Prioritas</th>
                            <th class="py-3 px-4 min-w-[180px]">Tanggal & Tenggat Waktu</th>
                            <th class="py-3 px-4">Status & Progres Saya</th>
                            <th class="py-3 px-4">Penerima Lain</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tasks as $t)
                        @php
                            $currentAssignee = $assignedTaskMap->get($t->id);
                            $isChecked = !is_null($currentAssignee);
                            $otherAssignees = $t->assignedEmployees->where('id', '!=', $employee->id);
                            $defaultDueDate = $currentAssignee?->due_date ? $currentAssignee->due_date->format('Y-m-d') : ($t->due_date ? $t->due_date->format('Y-m-d') : '');
                            $defaultStartDate = $currentAssignee?->start_date ? $currentAssignee->start_date->format('Y-m-d') : ($t->start_date ? $t->start_date->format('Y-m-d') : date('Y-m-d'));
                        @endphp
                        <tr class="task-row hover:bg-gray-50/80 transition-colors {{ $isChecked ? 'bg-primary/[0.02]' : '' }}" data-title="{{ strtolower($t->title) }}" data-number="{{ strtolower($t->task_number) }}" data-category="{{ $t->category_id ?? '' }}" data-type="{{ $t->task_type }}">
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox" name="task_ids[]" value="{{ $t->id }}" {{ $isChecked ? 'checked' : '' }} onchange="onTaskCheckboxChange(this)" class="task-item-checkbox rounded border-gray-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
                            </td>
                            <td class="py-3 px-4 font-mono font-bold">
                                <a href="{{ route('tasks.show', $t->id) }}" target="_blank" class="text-primary hover:underline flex items-center gap-1">
                                    {{ $t->task_number }}
                                    <i data-lucide="external-link" class="w-3 h-3 text-gray-400"></i>
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-gray-900 block" title="{{ $t->title }}">
                                    {{ $t->title }}
                                </span>
                                @if($t->description)
                                    <span class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">{{ $t->description }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $t->task_type_badge_class }}">
                                        {{ $t->task_type_label }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $t->output_type_badge_class }}">
                                        {{ $t->output_type_label }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col gap-1 items-start">
                                    @if($t->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                            {{ $t->category->name }}
                                        </span>
                                    @endif
                                    @if($t->priority)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $t->priority->badge_class }}">
                                            {{ $t->priority->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <!-- Tanggal Assignment & Tenggat Waktu Input -->
                            <td class="py-3 px-4">
                                <div class="space-y-1.5">
                                    <div>
                                        <span class="text-[10px] text-gray-400 block font-medium">Tgl Assignment:</span>
                                        <input type="date"
                                               name="start_dates[{{ $t->id }}]"
                                               value="{{ old('start_dates.' . $t->id, $defaultStartDate) }}"
                                               class="w-full text-xs py-1 px-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary task-start-date-input font-mono">
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 block font-medium">Deadline:</span>
                                        <input type="date"
                                               name="due_dates[{{ $t->id }}]"
                                               value="{{ old('due_dates.' . $t->id, $defaultDueDate) }}"
                                               class="w-full text-xs py-1 px-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary task-due-date-input font-mono">
                                    </div>
                                    @if($currentAssignee && $currentAssignee->is_overdue)
                                        <span class="inline-flex items-center gap-1 text-[10px] text-red-600 font-bold">
                                            <i data-lucide="alert-triangle" class="w-3 h-3"></i> Terlewat Tenggat
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <!-- Status & Progres Ploting Saat Ini -->
                            <td class="py-3 px-4">
                                @if($currentAssignee)
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $currentAssignee->status_badge_class }}">
                                            {{ $currentAssignee->status_label }}
                                        </span>
                                        <div class="flex items-center gap-1.5 text-[10px] font-mono text-gray-500">
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-primary h-1.5 rounded-full" style="width: {{ min(100, $currentAssignee->progress) }}%"></div>
                                            </div>
                                            <span>{{ $currentAssignee->progress }}%</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Belum Diplot</span>
                                @endif
                            </td>
                            <!-- Penerima Lain -->
                            <td class="py-3 px-4">
                                @if($otherAssignees->isNotEmpty())
                                    <div class="flex items-center -space-x-1.5 overflow-hidden">
                                        @foreach($otherAssignees->take(2) as $oa)
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold ring-2 ring-white" title="{{ $oa->display_name }}">
                                                {{ strtoupper(substr($oa->display_name, 0, 2)) }}
                                            </span>
                                        @endforeach
                                        @if($otherAssignees->count() > 2)
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-600 text-[9px] font-bold ring-2 ring-white">
                                                +{{ $otherAssignees->count() - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 italic">
                                Belum ada daftar master tugas pada perusahaan ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Sticky Save Bar -->
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-600">
                    Klik <strong>"Simpan Plotting Tugas"</strong> untuk menyimpan hasil penugasan dan tenggat waktu ke karyawan ini.
                </span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('tasks.assignment.index') }}" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Simpan Plotting Tugas
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function filterTasks() {
        const query = ($('#task-search-input').val() || '').toLowerCase();
        const categoryId = $('#task-category-select').val();
        const taskType = $('#task-type-select').val();

        $('.task-row').each(function() {
            const title = $(this).data('title') || '';
            const number = $(this).data('number') || '';
            const cat = String($(this).data('category') || '');
            const type = String($(this).data('type') || '');

            const matchesSearch = title.includes(query) || number.includes(query);
            const matchesCategory = !categoryId || cat === categoryId;
            const matchesType = !taskType || type === taskType;

            if (matchesSearch && matchesCategory && matchesType) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    }

    function onTaskCheckboxChange(el) {
        updateSelectedCounter();
        const row = $(el).closest('tr');
        if (el.checked) {
            row.addClass('bg-primary/[0.02]');
        } else {
            row.removeClass('bg-primary/[0.02]');
        }
    }

    function checkAllVisible(check) {
        $('.task-row:not(.hidden) .task-item-checkbox').each(function() {
            this.checked = check;
            onTaskCheckboxChange(this);
        });
    }

    function updateSelectedCounter() {
        const count = $('.task-item-checkbox:checked').length;
        $('#selected-counter').text(`${count} Tugas`);
    }

    function formatDateISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function setDueDateForChecked(type) {
        let targetDate = new Date();
        if (type === 'today') {
            // today
        } else if (type === 'plus3') {
            targetDate.setDate(targetDate.getDate() + 3);
        } else if (type === 'plus7') {
            targetDate.setDate(targetDate.getDate() + 7);
        } else if (type === 'endOfMonth') {
            targetDate = new Date(targetDate.getFullYear(), targetDate.getMonth() + 1, 0);
        }

        const dateStr = formatDateISO(targetDate);

        let modifiedCount = 0;
        $('.task-row .task-item-checkbox:checked').each(function() {
            const row = $(this).closest('tr');
            row.find('.task-due-date-input').val(dateStr);
            modifiedCount++;
        });

        if (modifiedCount === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Pilih Tugas Terlebih Dahulu',
                text: 'Centang tugas yang ingin diatur tenggat waktunya.',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: `Tenggat diatur ke ${dateStr} untuk ${modifiedCount} tugas.`
            });
        }
    }

    $(document).ready(function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
