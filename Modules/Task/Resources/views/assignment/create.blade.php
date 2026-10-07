@extends('layouts.app')

@section('title', 'Plotting Tugas ke Karyawan - ' . config('app.name'))
@section('page_title', 'Plotting Tugas ke Karyawan')

@section('toolbar_actions')
<a href="{{ route('tasks.assignment.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
    Kembali ke Daftar Penugasan
</a>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.4rem 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #111827 !important;
        font-size: 0.8125rem !important;
        line-height: 1.5 !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--color-primary, #e11d48) !important;
        outline: none !important;
        box-shadow: 0 0 0 1px var(--color-primary, #e11d48) !important;
    }
    .select2-container--default .select2-selection--multiple {
        min-height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.25rem 0.5rem !important;
        background-color: #ffffff !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--open .select2-selection--multiple {
        border-color: var(--color-primary, #e11d48) !important;
        outline: none !important;
        box-shadow: 0 0 0 1px var(--color-primary, #e11d48) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(225, 29, 72, 0.08) !important;
        border: 1px solid rgba(225, 29, 72, 0.25) !important;
        border-radius: 0.375rem !important;
        color: var(--color-primary, #e11d48) !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        padding: 2px 8px !important;
        margin: 2px 4px 2px 0 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--color-primary, #e11d48) !important;
        margin-right: 4px !important;
        font-weight: bold !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #be123c !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        font-size: 0.8125rem !important;
        margin-top: 4px !important;
        margin-bottom: 4px !important;
    }
    .select2-dropdown {
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        font-size: 0.8125rem !important;
        z-index: 50 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: var(--color-primary, #e11d48) !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Alert / Validation Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800 space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-sm text-red-900">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
                Terdapat kesalahan dalam pengisian form:
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tasks.assignment.store') }}" method="POST" id="form-create-assignment">
        @csrf

        <div class="space-y-6">

            <!-- Card 1: Input Penugasan (Select2 Karyawan Multiple, Tanggal Assignment, Deadline) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
                <div class="border-b border-gray-100 pb-4">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-primary"></i>
                        Informasi Penugasan Karyawan
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pilih satu atau beberapa karyawan yang akan diberikan tugas, serta tentukan tanggal assignment dan deadline-nya.
                    </p>
                </div>

                <div class="space-y-5">
                    <!-- 1. Select2 Pilih Karyawan (Multiple) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="employee_ids" class="block text-xs font-bold text-gray-800">
                                Pilih Karyawan (Bisa Pilih Beberapa Karyawan) <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-gray-500 font-medium" id="employee-selected-count">0 Karyawan Dipilih</span>
                                <span class="text-gray-300">•</span>
                                <button type="button" onclick="selectAllEmployees(true)" class="text-[11px] font-semibold text-primary hover:underline">
                                    Pilih Semua
                                </button>
                                <span class="text-gray-300">•</span>
                                <button type="button" onclick="selectAllEmployees(false)" class="text-[11px] font-semibold text-gray-500 hover:text-red-600">
                                    Hapus Pilihan
                                </button>
                            </div>
                        </div>
                        <select name="employee_ids[]" id="employee_ids" multiple="multiple" required class="w-full text-xs select2-employee-multiple" data-placeholder="-- Cari dan Pilih Satu atau Beberapa Karyawan --">
                            @foreach($employees as $emp)
                                @php
                                    $empDept = $emp->department ? $emp->department->name : ($emp->jabatan ?: 'Staff');
                                    $empCompany = $emp->company ? $emp->company->name : '';
                                    $label = $emp->display_name . ' (' . ($emp->employee_number ?: $emp->nik_ktp ?: '-') . ' - ' . $empDept . ($empCompany ? ' • ' . $empCompany : '') . ')';
                                    $isSelected = in_array($emp->id, (array)old('employee_ids', $selectedEmployeeIds ?? []));
                                @endphp
                                <option value="{{ $emp->id }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400">Ketik nama karyawan atau NIK untuk mencari dan memilih beberapa karyawan sekaligus.</p>
                    </div>

                    <!-- 2. Grid Tanggal Assignment & Deadline -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2 border-t border-gray-100">
                        <!-- Tanggal Assignment -->
                        <div class="space-y-1.5">
                            <label for="assignment_date" class="block text-xs font-bold text-gray-800">
                                Tanggal Assignment <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="assignment_date"
                                   id="assignment_date"
                                   value="{{ old('assignment_date', date('Y-m-d')) }}"
                                   required
                                   class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary font-mono h-[42px]">
                            <p class="text-[11px] text-gray-400">Tanggal tugas ini resmi diberikan ke karyawan.</p>
                        </div>

                        <!-- Deadline (Tenggat Waktu) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="due_date" class="block text-xs font-bold text-gray-800">
                                    Deadline (Tenggat Waktu) <span class="text-red-500">*</span>
                                </label>
                            </div>
                            <input type="date"
                                   name="due_date"
                                   id="due_date"
                                   value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                   required
                                   onchange="onGlobalDueDateChange(this.value)"
                                   class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary font-mono h-[42px]">

                            <!-- Quick set deadline buttons -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                <button type="button" onclick="setQuickDeadline('today')" class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                                    Hari Ini
                                </button>
                                <button type="button" onclick="setQuickDeadline('plus3')" class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                                    +3 Hari
                                </button>
                                <button type="button" onclick="setQuickDeadline('plus7')" class="px-2 py-0.5 text-[10px] font-medium bg-primary/10 hover:bg-primary/20 text-primary font-semibold rounded transition-colors">
                                    +7 Hari (1 Minggu)
                                </button>
                                <button type="button" onclick="setQuickDeadline('endOfMonth')" class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                                    Akhir Bulan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: List Tugas Tersedia (Tinggal Centang Aja) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden space-y-4">
                <div class="p-5 border-b border-gray-100 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <i data-lucide="list-checks" class="w-4 h-4 text-primary"></i>
                                List Tugas Tersedia (Centang Tugas yang Diberikan)
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Berikan tanda centang pada tugas-tugas di bawah ini untuk memplotkannya ke karyawan terpilih.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="px-3 py-1.5 bg-primary/5 border border-primary/20 rounded-lg text-right">
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">Dipilih</span>
                                <span class="text-sm font-bold text-primary" id="selected-counter">0 Tugas</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="checkAllVisible(true)" class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    Centang Semua
                                </button>
                                <button type="button" onclick="checkAllVisible(false)" class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    Hapus Centang
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
                        <!-- Search Box -->
                        <div class="sm:col-span-2 relative">
                            <input type="text" id="task-search-input" onkeyup="filterTasks()" placeholder="Cari kode atau judul tugas..." class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                        </div>

                        <!-- Filter Sifat Tugas -->
                        <div>
                            <select id="task-type-select" onchange="filterTasks()" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                <option value="">Semua Sifat Tugas</option>
                                <option value="harian">Harian (Auto-daily)</option>
                                <option value="bulanan">Bulanan (Auto-monthly)</option>
                                <option value="insidentil">Insidentil (Sekali jalan)</option>
                            </select>
                        </div>

                        <!-- Filter Kategori -->
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

                <!-- Table Daftar Tugas -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="tasks-table">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">Centang</th>
                                <th class="py-3 px-4">Kode Tugas</th>
                                <th class="py-3 px-4 min-w-[240px]">Judul Tugas & Deskripsi</th>
                                <th class="py-3 px-4">Tipe & Output</th>
                                <th class="py-3 px-4">Kategori & Prioritas</th>
                                <th class="py-3 px-4 min-w-[150px]">Deadline Khusus (Opsional)</th>
                                <th class="py-3 px-4">Penerima Lain</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tasks as $t)
                            @php
                                $isOldChecked = is_array(old('task_ids')) && in_array($t->id, old('task_ids'));
                                $defaultTaskDueDate = old('due_dates.' . $t->id, $t->due_date ? $t->due_date->format('Y-m-d') : '');
                            @endphp
                            <tr class="task-row hover:bg-gray-50/80 transition-colors {{ $isOldChecked ? 'bg-primary/[0.02]' : '' }}"
                                data-title="{{ strtolower($t->title) }}"
                                data-number="{{ strtolower($t->task_number) }}"
                                data-category="{{ $t->category_id ?? '' }}"
                                data-type="{{ $t->task_type }}">
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox"
                                           name="task_ids[]"
                                           value="{{ $t->id }}"
                                           {{ $isOldChecked ? 'checked' : '' }}
                                           onchange="onTaskCheckboxChange(this)"
                                           class="task-item-checkbox rounded border-gray-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer">
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
                                <td class="py-3 px-4">
                                    <input type="date"
                                           name="due_dates[{{ $t->id }}]"
                                           value="{{ $defaultTaskDueDate }}"
                                           placeholder="Ikuti deadline global"
                                           class="w-full text-xs py-1 px-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary task-due-date-input font-mono">
                                </td>
                                <td class="py-3 px-4">
                                    @if($t->assignedEmployees && $t->assignedEmployees->isNotEmpty())
                                        <div class="flex items-center -space-x-1.5 overflow-hidden">
                                            @foreach($t->assignedEmployees->take(2) as $oa)
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold ring-2 ring-white" title="{{ $oa->display_name }}">
                                                    {{ strtoupper(substr($oa->display_name, 0, 2)) }}
                                                </span>
                                            @endforeach
                                            @if($t->assignedEmployees->count() > 2)
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-600 text-[9px] font-bold ring-2 ring-white">
                                                    +{{ $t->assignedEmployees->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-gray-400 italic">Belum ada</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400 italic">
                                    Belum ada daftar master tugas yang tersedia.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Sticky Bottom Action Bar -->
                <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-600">
                        Pastikan karyawan, tanggal assignment, dan tugas yang dicentang sudah sesuai sebelum disimpan.
                    </span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('tasks.assignment.index') }}" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Simpan Penugasan
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 Karyawan Multiple
        $('.select2-employee-multiple').select2({
            width: '100%',
            placeholder: '-- Cari dan Pilih Satu atau Beberapa Karyawan --',
            allowClear: true,
            closeOnSelect: false
        }).on('change', function() {
            updateEmployeeSelectedCount();
        });

        updateEmployeeSelectedCount();
        updateSelectedCounter();

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function updateEmployeeSelectedCount() {
        const selected = $('#employee_ids').val() || [];
        const count = selected.length;
        $('#employee-selected-count').text(`${count} Karyawan Dipilih`);
    }

    function selectAllEmployees(select) {
        if (select) {
            $('#employee_ids > option').prop('selected', true);
            $('#employee_ids').trigger('change');
        } else {
            $('#employee_ids').val(null).trigger('change');
        }
    }

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
            // Auto fill task due date if empty
            const taskDueDateInput = row.find('.task-due-date-input');
            if (!taskDueDateInput.val()) {
                taskDueDateInput.val($('#due_date').val());
            }
        } else {
            row.removeClass('bg-primary/[0.02]');
        }
    }

    function checkAllVisible(check) {
        const globalDue = $('#due_date').val();
        $('.task-row:not(.hidden) .task-item-checkbox').each(function() {
            this.checked = check;
            const row = $(this).closest('tr');
            if (check) {
                row.addClass('bg-primary/[0.02]');
                const taskDueDateInput = row.find('.task-due-date-input');
                if (!taskDueDateInput.val()) {
                    taskDueDateInput.val(globalDue);
                }
            } else {
                row.removeClass('bg-primary/[0.02]');
            }
        });
        updateSelectedCounter();
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

    function setQuickDeadline(type) {
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
        $('#due_date').val(dateStr);
        onGlobalDueDateChange(dateStr);
    }

    function onGlobalDueDateChange(newDate) {
        // Sync to all checked tasks that don't have custom dates or update all checked
        $('.task-row .task-item-checkbox:checked').each(function() {
            const row = $(this).closest('tr');
            row.find('.task-due-date-input').val(newDate);
        });
    }

    // Client-side validation on submit
    $('#form-create-assignment').on('submit', function(e) {
        const employeeIds = $('#employee_ids').val() || [];
        const count = $('.task-item-checkbox:checked').length;

        if (employeeIds.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Karyawan',
                text: 'Silakan pilih minimal satu karyawan yang akan ditugaskan terlebih dahulu.'
            });
            return false;
        }

        if (count === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Minimal 1 Tugas',
                text: 'Silakan centang minimal satu tugas dari daftar tugas yang tersedia.'
            });
            return false;
        }
    });
</script>
@endpush
