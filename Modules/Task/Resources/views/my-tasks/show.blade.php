@extends('layouts.app')

@section('title', 'Kerjakan Tugas: ' . $task->task_number . ' - ' . config('app.name'))
@section('page_title', 'Kerjakan Tugas: ' . $task->task_number)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        height: 38px !important;
        padding: 0.35rem 0.5rem !important;
        font-size: 0.75rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        color: #1f2937 !important;
        font-size: 0.75rem !important;
        line-height: 1.5 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 6px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #2563eb !important;
        outline: none !important;
        box-shadow: 0 0 0 1px #2563eb !important;
    }
    .select2-dropdown {
        border-color: #e5e7eb !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        font-size: 0.75rem !important;
        z-index: 99999 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('toolbar_actions')
<a href="{{ route('tasks.my-tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-2xs">
    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
    Kembali ke Tugas Saya
</a>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    @if(isset($activeSibling) && $activeSibling)
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 text-blue-700 rounded-lg shrink-0">
                <i data-lucide="info" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-xs sm:text-sm font-bold text-blue-950">Anda sedang melihat riwayat tugas yang telah selesai (100%).</h4>
                <p class="text-xs text-blue-700 mt-0.5">Tugas ini telah diploting ulang untuk Anda kerjakan hari ini.</p>
            </div>
        </div>
        <a href="{{ route('tasks.my-tasks.show', $activeSibling->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs shrink-0 transition-colors">
            <i data-lucide="play-circle" class="w-4 h-4"></i>
            Buka Pengerjaan Hari Ini
        </a>
    </div>
    @endif

    @if(isset($completedSibling) && $completedSibling)
    <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-emerald-900 flex items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <i data-lucide="history" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>Tugas ini pernah Anda selesaikan pada <strong>{{ $completedSibling->completed_at ? $completedSibling->completed_at->format('d M Y') : ($completedSibling->start_date ? $completedSibling->start_date->format('d M Y') : '-') }}</strong>.</span>
        </div>
        <a href="{{ route('tasks.my-tasks.show', $completedSibling->id) }}?view=history" class="inline-flex items-center gap-1 font-semibold text-emerald-700 hover:text-emerald-900 hover:underline shrink-0">
            Lihat Hasil Sebelumnya <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>
    @endif

    <!-- Task Header & Overview Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-gray-100">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-bold text-primary px-2.5 py-1 bg-primary/10 rounded-md">
                        {{ $task->task_number }}
                    </span>

                    <!-- Tipe Sifat Tugas -->
                    @if($task->task_type === 'harian')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                            <i data-lucide="repeat" class="w-3.5 h-3.5"></i> Tugas Harian
                        </span>
                    @elseif($task->task_type === 'bulanan')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Tugas Bulanan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <i data-lucide="zap" class="w-3.5 h-3.5"></i> Tugas Insidentil
                        </span>
                    @endif

                    <!-- Bentuk Output -->
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border {{ $task->output_type_badge_class }}">
                        {{ $task->output_type_label }}
                    </span>

                    <!-- Prioritas -->
                    @if($task->priority)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $task->priority->badge_class }}">
                            {{ $task->priority->name }}
                        </span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 leading-snug">
                    {{ $task->title }}
                </h1>
            </div>

            <!-- Status & Progress Badge -->
            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 shrink-0">
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider block">Status Tugas Saya</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $assignee?->status_badge_class ?? $submission->status_badge_class }} mt-0.5" id="task-status-badge">
                        {{ $assignee?->status_label ?? $submission->status_label }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider block">Progress Saya</span>
                    <span class="text-sm font-bold text-primary font-mono" id="task-progress-pct">{{ $assignee?->progress ?? $submission->progress }}%</span>
                </div>
            </div>
        </div>

        <!-- Task Instruction / Description -->
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5 flex items-center gap-1.5">
                <i data-lucide="align-left" class="w-3.5 h-3.5"></i>
                Instruksi & Keterangan Tugas
            </h4>
            <div class="text-xs sm:text-sm text-gray-700 leading-relaxed bg-gray-50/70 p-4 rounded-xl border border-gray-100">
                @if($task->description)
                    {!! nl2br(e($task->description)) !!}
                @else
                    <p class="text-gray-400 italic">Tidak ada instruksi khusus tertulis dari atasan.</p>
                @endif
            </div>
        </div>

        <!-- Meta Info Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 block text-[11px]">Kategori</span>
                <span class="font-medium text-gray-800 mt-0.5 block">{{ $task->category->name ?? 'Umum' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Tanggal Mulai</span>
                <span class="font-medium text-gray-800 mt-0.5 block">{{ $assignee?->effective_start_date ? $assignee->effective_start_date->format('d M Y') : ($task->start_date ? $task->start_date->format('d M Y') : '-') }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Tenggat Waktu</span>
                @php
                    $effDueDate = $assignee?->effective_due_date ?? $task->due_date;
                    $isOverdue = $assignee?->is_overdue ?? false;
                @endphp
                <span class="font-medium {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-800' }} mt-0.5 block">
                    {{ $effDueDate ? $effDueDate->format('d M Y') : '-' }}
                    @if($isOverdue) (Terlambat) @endif
                </span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Pemberi Tugas</span>
                <span class="font-medium text-gray-800 mt-0.5 block">{{ $task->creator->nm_user ?? 'Atasan / Admin' }}</span>
            </div>
        </div>
    </div>

    @php
        $isExpired = ($assignee?->status === \Modules\Task\Services\TaskStatusService::STATUS_EXPIRED || $submission->status === \Modules\Task\Services\TaskStatusService::STATUS_EXPIRED);
        $isCompleted = ($assignee?->status === \Modules\Task\Services\TaskStatusService::STATUS_COMPLETED || $submission->status === \Modules\Task\Services\TaskStatusService::STATUS_COMPLETED);
        $isLocked = $isCompleted || $isExpired;
    @endphp

    @if($isExpired)
    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-300 text-slate-800 flex items-start gap-4 shadow-xs">
        <div class="p-2.5 bg-slate-200 text-slate-700 rounded-xl shrink-0 mt-0.5">
            <i data-lucide="clock-alert" class="w-6 h-6 text-slate-600"></i>
        </div>
        <div class="space-y-1">
            <h4 class="text-sm font-bold text-slate-900">Tugas Ini Telah Kadaluarsa (Batas Waktu Berakhir)</h4>
            <p class="text-xs text-slate-600 leading-relaxed">
                Tenggat waktu pengerjaan tugas ini telah berakhir pada 
                <strong>{{ $effDueDate ? $effDueDate->format('d M Y') : '-' }}</strong> 
                sebelum sempat diselesaikan. Status penugasan Anda untuk tugas ini ditutup secara otomatis sebagai 
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-200 text-slate-800 border border-slate-300">Kadaluarsa</span>.
                Anda tetap dapat melihat rincian instruksi tugas di bawah ini sebagai riwayat arsip.
            </p>
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. CHECKLIST TASK WORKSPACE                                               -->
    <!-- ========================================================================= -->
    @if($task->output_type === 'checklist')
    <div class="bg-white rounded-xl border border-emerald-200/90 shadow-xs p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Lembar Pengerjaan: Checklist Selesai
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Konfirmasi & Bukti Penyelesaian Tugas</h3>
            </div>
        </div>

        @if($isCompleted)
            <!-- State: Already Completed -->
            <div class="p-6 bg-emerald-50/50 rounded-xl border border-emerald-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-emerald-200/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-emerald-600 text-white rounded-xl shadow-xs">
                            <i data-lucide="check-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-emerald-900">Tugas Telah Selesai Dikerjakan</h4>
                            <p class="text-[11px] text-emerald-700">
                                Diselesaikan pada: <span class="font-semibold">{{ $submission->completed_at ? $submission->completed_at->format('d M Y, H:i') : '-' }}</span>
                                @if($submission->employee)
                                    oleh <span class="font-semibold">{{ $submission->employee->display_name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 self-start sm:self-auto">
                        <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i>
                        Tugas Selesai & Terkunci
                    </span>
                </div>

                <!-- Proof Showcase (if any) -->
                <div class="space-y-3 pt-1">
                    @if($submission->completion_url)
                        <div>
                            <span class="text-[11px] font-semibold text-gray-500 block mb-1">Link URL Bukti Pengerjaan:</span>
                            <a href="{{ $submission->completion_url }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-gray-200 text-xs font-bold text-primary hover:bg-primary/5 hover:border-primary transition-all shadow-2xs group max-w-full truncate">
                                <i data-lucide="external-link" class="w-4 h-4 shrink-0 text-primary"></i>
                                <span class="truncate">{{ $submission->completion_url }}</span>
                            </a>
                        </div>
                    @endif

                    @if($submission->completion_file)
                        <div>
                            <span class="text-[11px] font-semibold text-gray-500 block mb-1">Foto Bukti Pengerjaan:</span>
                            <a href="{{ $submission->completion_file_url }}" target="_blank" class="inline-block group relative rounded-xl overflow-hidden border border-gray-200 shadow-xs hover:shadow-md transition-all">
                                <img src="{{ $submission->completion_file_url }}" alt="Bukti Pengerjaan" class="max-h-64 max-w-full rounded-xl object-contain bg-gray-100 group-hover:scale-102 transition-transform">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold gap-1.5 transition-opacity">
                                    <i data-lucide="zoom-in" class="w-4 h-4"></i> Buka Foto Ukuran Penuh
                                </div>
                            </a>
                        </div>
                    @endif

                    @if($submission->completion_notes)
                        <div>
                            <span class="text-[11px] font-semibold text-gray-500 block mb-1">Catatan Pengerjaan:</span>
                            <div class="p-3 bg-white rounded-lg border border-gray-200 text-xs text-gray-700 leading-relaxed">
                                {!! nl2br(e($submission->completion_notes)) !!}
                            </div>
                        </div>
                    @endif

                    @if(!$submission->completion_url && !$submission->completion_file && !$submission->completion_notes)
                        <p class="text-xs text-gray-400 italic">Tidak ada lampiran bukti tertulis untuk tugas ini.</p>
                    @endif
                </div>
            </div>
        @elseif($isExpired)
            <!-- State: Expired -->
            <div class="p-8 bg-slate-50 rounded-xl border border-slate-200 text-center space-y-2">
                <i data-lucide="clock-alert" class="w-8 h-8 text-slate-400 mx-auto"></i>
                <h4 class="text-sm font-bold text-slate-700">Batas Waktu Telah Berakhir</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Tugas ini telah melewati tenggat waktu sehingga pengunggahan bukti pengerjaan ditutup.
                </p>
            </div>
        @else
            <!-- State: Not Completed Yet -->
            <div class="p-8 bg-emerald-50/40 rounded-xl border border-emerald-100 text-center space-y-4">
                <p class="text-xs sm:text-sm text-gray-700 max-w-lg mx-auto leading-relaxed">
                    Setelah Anda menyelesaikan instruksi tugas, klik tombol di bawah untuk melampirkan bukti pengerjaan (bisa berupa link URL postingan/drive maupun foto/tangkapan layar).
                </p>

                <div class="pt-2">
                    <button type="button" onclick="$('#completion-proof-modal').removeClass('hidden')" id="btn-open-proof-modal" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm hover:shadow-md transition-all">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        Tandai Tugas Selesai Dikerjakan
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Upload Bukti Penyelesaian Tugas -->
    <div id="completion-proof-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-200">
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-white/20 rounded-lg">
                        <i data-lucide="upload-cloud" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold leading-tight">Bukti Penyelesaian Tugas</h3>
                        <p class="text-[10px] text-white/80">Lampirkan link URL atau foto bukti pengerjaan tugas</p>
                    </div>
                </div>
                <button type="button" onclick="$('#completion-proof-modal').addClass('hidden')" class="p-1.5 text-white/80 hover:text-white rounded-lg hover:bg-white/10 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="form-completion-proof" onsubmit="submitCompletionProof(event)" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <!-- Link URL Bukti -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center gap-1">
                        <i data-lucide="link" class="w-3.5 h-3.5 text-emerald-600"></i>
                        Link URL Bukti <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="url" name="completion_url" placeholder="https://... Contoh: link postingan Facebook, Instagram, Google Drive, Shopee" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <p class="text-[10px] text-gray-400 mt-0.5">Tempel link hasil postingan, tautan cloud drive, atau dokumen hasil kerja.</p>
                </div>

                <!-- Unggah Foto Bukti -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center gap-1">
                        <i data-lucide="image" class="w-3.5 h-3.5 text-emerald-600"></i>
                        Unggah Foto / Tangkapan Layar <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-gray-300 border-dashed rounded-xl hover:border-emerald-500 transition-colors bg-gray-50/50 cursor-pointer" onclick="$('#input_completion_file').click()">
                        <div class="space-y-1.5 text-center">
                            <i data-lucide="camera" class="mx-auto h-8 w-8 text-gray-400"></i>
                            <div class="text-xs text-gray-600">
                                <span class="font-bold text-emerald-600 hover:underline">Pilih Foto</span> atau seret file ke sini
                            </div>
                            <p class="text-[10px] text-gray-400">PNG, JPG, JPEG, WEBP hingga 10MB</p>
                        </div>
                    </div>
                    <input type="file" name="completion_file" id="input_completion_file" accept="image/*" class="hidden" onchange="previewCompletionPhoto(this)">

                    <!-- Preview Thumbnail -->
                    <div id="photo-preview-box" class="hidden mt-2 flex items-center gap-3 p-2 bg-emerald-50/60 rounded-lg border border-emerald-200">
                        <img id="photo-preview-img" src="" alt="Preview" class="w-12 h-12 object-cover rounded-md border border-emerald-300">
                        <div class="flex-1 min-w-0">
                            <span id="photo-preview-name" class="text-xs font-medium text-gray-800 truncate block"></span>
                            <span class="text-[10px] text-emerald-700">Foto siap diunggah</span>
                        </div>
                        <button type="button" onclick="removeCompletionPhoto()" class="p-1 text-gray-400 hover:text-red-600 rounded">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Catatan Pengerjaan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center gap-1">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-emerald-600"></i>
                        Catatan Tambahan <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="completion_notes" rows="2" placeholder="Tuliskan keterangan bila ada (contoh: sudah diposting di 3 grup dan story WA)..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary"></textarea>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/60 flex items-start gap-2 text-[11px] text-amber-800">
                    <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                    <span>Anda dapat mengisi Link URL, melampirkan Foto bukti, ataupun keduanya.</span>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="$('#completion-proof-modal').addClass('hidden')" class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-proof" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm flex items-center gap-1.5 transition-colors">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Konfirmasi & Selesaikan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 2. TARGET VALUE TASK WORKSPACE                                            -->
    <!-- ========================================================================= -->
    @if($task->output_type === 'target')
    <div class="bg-white rounded-xl border border-amber-200/90 shadow-xs p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="target" class="w-4 h-4"></i>
                    Lembar Pengerjaan: Target Kinerja
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Input Realisasi Target Tugas</h3>
            </div>
            <div class="flex items-center gap-4 bg-amber-50/80 px-4 py-2.5 rounded-xl border border-amber-200/60">
                <div>
                    <span class="text-[10px] text-gray-500 block font-semibold uppercase">Target Ditetapkan</span>
                    <span class="text-base font-bold text-gray-900">{{ $task->formatted_target }}</span>
                </div>
                <div class="border-l border-amber-300 pl-4">
                    <span class="text-[10px] text-amber-700 block font-semibold uppercase">Realisasi Saya</span>
                    <span class="text-base font-bold text-amber-800" id="current-achieved-display">{{ number_format($submission->achieved_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Progress Meter -->
        <div class="space-y-1.5">
            <div class="flex justify-between text-xs font-semibold text-gray-700">
                <span>Persentase Pencapaian Saya</span>
                <span class="text-amber-700 font-bold font-mono" id="target-percent-display">{{ $submission->progress }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-amber-500 to-emerald-500 h-3 rounded-full transition-all duration-500" id="target-progress-bar" style="width: {{ min(100, $submission->progress) }}%"></div>
            </div>
        </div>

        @if($isCompleted)
            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-800 text-xs text-center flex items-center justify-center gap-2">
                <i data-lucide="lock" class="w-4 h-4 text-emerald-600"></i>
                <span>Tugas target ini telah selesai dan submission telah dikunci. Capaian target tidak dapat diubah lagi.</span>
            </div>
        @elseif($isExpired)
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-slate-600 text-xs text-center flex items-center justify-center gap-2">
                <i data-lucide="clock-alert" class="w-4 h-4 text-slate-500"></i>
                <span>Batas waktu pengerjaan tugas target ini telah berakhir. Penginputan realisasi target baru telah ditutup.</span>
            </div>
        @else
            <!-- Form Update Realisasi Target -->
            <form id="form-my-target" onsubmit="submitMyTarget(event)" class="p-4 bg-amber-50/40 rounded-xl border border-amber-100 space-y-3">
                <h4 class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                    Update Capaian Baru
                </h4>
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        @if($task->target_type === 'currency')
                            <span class="absolute left-3 top-2.5 text-xs font-bold text-gray-500">Rp</span>
                        @endif
                        <input type="number" step="any" name="achieved_amount" id="my_achieved_amount" value="{{ old('achieved_amount', $submission->achieved_amount) }}" required placeholder="Contoh: {{ $task->target_amount }}" class="w-full text-xs py-2 {{ $task->target_type === 'currency' ? 'pl-9' : 'pl-3' }} pr-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary font-mono font-bold">
                    </div>
                    <button type="submit" id="btn-submit-my-target" class="w-full sm:w-auto px-5 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-xs transition-colors shrink-0 flex items-center justify-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan Capaian Target
                    </button>
                </div>
                <p class="text-[11px] text-gray-500">
                    Progress penyelesaian tugas akan dihitung secara otomatis berdasarkan target: {{ $task->formatted_target }}. Jika capaian mencapai 100%, tugas otomatis berstatus Selesai.
                </p>
            </form>
        @endif
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 3. DATA LIST TASK WORKSPACE                                               -->
    <!-- ========================================================================= -->
    @if($task->output_type === 'data_list')
    @php
        $columns = (array)($task->list_columns ?: ['Nama', 'Kontak / Identitas', 'Catatan']);
    @endphp
    <div class="bg-white rounded-xl border border-blue-200/90 shadow-xs overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-blue-50/80 to-indigo-50/50 border-b border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="table" class="w-4 h-4"></i>
                    Lembar Pengerjaan: Entri Data Jawaban
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Daftar Data yang Telah Anda Kumpulkan</h3>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                @if($isCompleted)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i>
                        Tugas Selesai & Terkunci
                    </span>
                @elseif($isExpired)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">
                        <i data-lucide="clock-alert" class="w-3.5 h-3.5 text-slate-500"></i>
                        Tugas Kadaluarsa
                    </span>
                @else
                    <button type="button" onclick="openAddEntryModal()" class="px-3.5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs flex items-center gap-1.5 transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        + Tambah Baris Data
                    </button>
                    <button type="button" onclick="completeDataListTask()" class="px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs flex items-center gap-1.5 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Tandai Tugas Selesai
                    </button>
                @endif
            </div>
        </div>

        @if($isCompleted)
        <div class="p-3.5 bg-emerald-50 border-b border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-emerald-900">
            <div class="flex items-center gap-2">
                <i data-lucide="check-check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span><strong>Tugas Telah Selesai:</strong> Pengerjaan tugas ini telah Anda tandai selesai pada <span class="font-semibold">{{ $submission->completed_at ? $submission->completed_at->format('d M Y, H:i') : '-' }}</span>.</span>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 self-start sm:self-auto">
                Status: Selesai (100%)
            </span>
        </div>
        @endif

        @if($task->target_count)
        <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200/60 flex items-center justify-between text-xs mx-4 my-3">
            <span class="text-blue-900 font-medium">
                Target entri Anda: <strong>{{ $task->target_count }} baris</strong> (Terkumpul: <span id="my-entry-count" class="font-bold">{{ count($myDataEntries ?? []) }}</span> baris)
            </span>
            <span class="font-bold text-primary font-mono" id="my-entry-progress-badge">{{ $submission->progress }}% Selesai</span>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="my-data-entries-table">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        @foreach($columns as $col)
                            <th class="py-3 px-4 font-semibold text-gray-700">{{ $col }}</th>
                        @endforeach
                        <th class="py-3 px-4 w-32">Waktu Input</th>
                        <th class="py-3 px-4 text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse(($myDataEntries ?? collect()) as $index => $entry)
                    <tr id="entry-row-{{ $entry->id }}" class="hover:bg-gray-50/50">
                        <td class="py-2.5 px-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                        @foreach($columns as $col)
                            @php
                                $val = $entry->entry_data[$col] ?? '-';
                                if (preg_match('/toko|store|cabang|marketplace|channel/i', $col) && is_numeric($val) && $entry->toko) {
                                    $val = $entry->toko->name;
                                } elseif (preg_match('/produk|barang|item|sku/i', $col) && is_numeric($val) && $entry->produk) {
                                    $val = $entry->produk->name;
                                }
                            @endphp
                            <td class="py-2.5 px-4 font-medium text-gray-800">
                                {{ $val }}
                            </td>
                        @endforeach
                        <td class="py-2.5 px-4 text-gray-500 font-mono text-[11px]">
                            {{ $entry->created_at ? $entry->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            @if(!$isLocked)
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="openEditEntryModal({{ $entry->id }}, {{ json_encode($entry->entry_data ?? []) }}, '{{ $entry->toko_id ?? '' }}', '{{ addslashes($entry->toko?->name ?? '') }}', '{{ $entry->produk_id ?? '' }}', '{{ addslashes($entry->produk?->name ?? '') }}')" class="p-1 text-gray-400 hover:text-blue-600 transition-colors rounded" title="Edit Baris Data">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button type="button" onclick="deleteMyDataEntry({{ $entry->id }})" class="p-1 text-gray-400 hover:text-red-600 transition-colors rounded" title="Hapus Baris Data">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            @else
                            <span class="text-gray-400 text-xs italic">Terkunci</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-entries-row">
                        <td colspan="{{ count($columns) + 3 }}" class="py-8 text-center text-gray-400 italic">
                            Belum ada baris data yang Anda inputkan. Klik tombol "+ Tambah Baris Data" di atas untuk menambahkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah / Edit Data Entry -->
    <div id="add-entry-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-gray-200">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-primary" id="modal-entry-icon"></i>
                    <span id="modal-entry-title">Tambah Baris Data Jawaban</span>
                </h3>
                <button type="button" onclick="closeAddEntryModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="form-my-entry" onsubmit="submitMyEntry(event)" class="p-5 space-y-3">
                <input type="hidden" name="entry_id" id="modal_entry_id" value="">
                <input type="hidden" name="toko_id" id="entry_toko_id" value="">
                <input type="hidden" name="produk_id" id="entry_produk_id" value="">
                @foreach($columns as $col)
                @php
                    $isTokoCol = (bool) preg_match('/toko|store|cabang|marketplace|channel/i', $col);
                    $isProdukCol = (bool) preg_match('/produk|barang|item|sku/i', $col);
                @endphp
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $col }} <span class="text-red-500">*</span></label>
                    @if($isTokoCol)
                        <select name="entry_data[{{ $col }}]" data-column="{{ $col }}" required class="w-full text-xs select2-toko-ajax entry-dynamic-field" data-placeholder="-- Cari dan Pilih Toko / Marketplace --" style="width: 100%;">
                            <option value="">-- Cari dan Pilih Toko / Marketplace --</option>
                        </select>
                        <span class="text-[10px] text-gray-400 mt-1 block flex items-center gap-1">
                            <i data-lucide="info" class="w-3 h-3 text-blue-500 inline"></i>
                            Pilih toko dari master database atau ketik nama toko/channel.
                        </span>
                    @elseif($isProdukCol)
                        <select name="entry_data[{{ $col }}]" data-column="{{ $col }}" required class="w-full text-xs select2-produk-ajax entry-dynamic-field" data-placeholder="-- Cari dan Pilih Produk / Barang --" style="width: 100%;">
                            <option value="">-- Cari dan Pilih Produk / Barang --</option>
                        </select>
                        <span class="text-[10px] text-gray-400 mt-1 block flex items-center gap-1">
                            <i data-lucide="info" class="w-3 h-3 text-blue-500 inline"></i>
                            Pilih produk/barang dari master database atau ketik nama produk.
                        </span>
                    @else
                        <input type="text" name="entry_data[{{ $col }}]" data-column="{{ $col }}" required placeholder="Masukkan {{ $col }}..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary entry-dynamic-field">
                    @endif
                </div>
                @endforeach
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeAddEntryModal()" class="px-3.5 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-entry" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span id="modal-entry-btn-text">Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 4. STOCK UPDATE TASK WORKSPACE                                            -->
    <!-- ========================================================================= -->
    <!-- ========================================================================= -->
    <!-- 4. STOCK UPDATE TASK WORKSPACE (MATRIX PRODUK X TOKO)                     -->
    <!-- ========================================================================= -->
    @if($task->output_type === 'stock_update')
    @php
        $stockMap = [];
        $updatesSource = $myStockUpdates ?? collect();
        foreach($updatesSource as $su) {
            if ($su->stock_status === 'kosong') {
                $stockMap[$su->toko_id . '_' . $su->produk_id] = true;
            }
        }
    @endphp
    <div class="bg-white rounded-xl border border-rose-200/90 shadow-xs overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-rose-50/80 to-pink-50/50 border-b border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-bold text-rose-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="package-x" class="w-4 h-4"></i>
                    Lembar Pengerjaan: Matriks Update Stock Kosong
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Checklist Stok Kosong per Toko & Produk</h3>
            </div>
            <div class="flex items-center gap-2">
                @if($isCompleted)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i>
                    Tugas Selesai & Terkunci
                </span>
                @elseif(!$isExpired)
                <button type="button" onclick="submitStockMatrixForm(event)" id="btn-save-top-matrix" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-xs flex items-center gap-1.5 transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Pembaruan Stok
                </button>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">
                    <i data-lucide="clock-alert" class="w-3.5 h-3.5 text-slate-500"></i>
                    Tugas Kadaluarsa
                </span>
                @endif
            </div>
        </div>

        <!-- Instruction & Legend Box -->
        <div class="p-4 bg-emerald-50/60 border-b border-emerald-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-emerald-900">
            <div class="flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span><strong>Petunjuk:</strong> Centang checkbox pada kolom toko jika produk tersebut sudah <strong>SELESAI</strong> dicek/diperbarui. Hilangkan centang jika belum selesai.</span>
            </div>
            <div class="flex items-center gap-3 text-[11px] shrink-0 font-medium">
                <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Dicentang = Selesai</span>
                <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded border border-gray-300 bg-white inline-block"></span> Tidak Dicentang = Belum Selesai</span>
            </div>
        </div>

        <!-- Quick Filter & Helpers -->
        <div class="p-3 bg-gray-50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-2.5"></i>
                <input type="text" id="filter-matrix-product" onkeyup="filterMatrixTable()" placeholder="Cari nama atau kode produk..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
            </div>
            @if(!$isLocked)
            <div class="flex items-center gap-2 text-xs">
                <button type="button" onclick="checkAllMatrix(true)" class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded hover:bg-emerald-100 transition-colors">
                    Centang Semua Selesai
                </button>
                <button type="button" onclick="checkAllMatrix(false)" class="px-2.5 py-1 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded hover:bg-gray-50 transition-colors">
                    Hapus Semua Centang
                </button>
            </div>
            @endif
        </div>

        <!-- Form & Matrix Table -->
        <form id="form-stock-matrix" onsubmit="submitStockMatrixForm(event)">
            @csrf
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="w-full text-xs text-left border-collapse" id="table-stock-matrix">
                    <thead class="bg-gray-100 text-gray-700 font-semibold border-b border-gray-200 sticky top-0 z-20 shadow-2xs">
                        <tr>
                            <th class="py-3 px-3 w-12 text-center bg-gray-100 border-r border-gray-200">#</th>
                            <th class="py-3 px-4 min-w-[240px] bg-gray-100 sticky left-0 z-30 border-r border-gray-200 shadow-2xs">
                                Nama Produk
                            </th>
                            @foreach($availableTokos as $toko)
                                <th class="py-3 px-4 text-center min-w-[130px] border-r border-gray-200 last:border-r-0 bg-gray-100">
                                    <div class="font-bold text-gray-900 leading-tight">{{ $toko->name }}</div>
                                    <span class="text-[10px] font-mono text-gray-400 font-normal">({{ $toko->code }})</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($availableProduks as $index => $prod)
                        <tr class="hover:bg-emerald-50/20 transition-colors product-matrix-row" data-product-title="{{ strtolower($prod->name . ' ' . $prod->code) }}">
                            <td class="py-3 px-3 text-center text-gray-500 font-medium border-r border-gray-200">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-gray-900 sticky left-0 bg-white z-10 border-r border-gray-200 shadow-2xs">
                                <div class="text-xs font-bold text-gray-900 leading-snug">{{ $prod->name }}</div>
                                <span class="text-[10px] font-mono text-gray-400 font-normal">{{ $prod->code }}</span>
                            </td>
                            @foreach($availableTokos as $toko)
                                @php
                                    $key = $toko->id . '_' . $prod->id;
                                    $isKosong = isset($stockMap[$key]);
                                @endphp
                                <td class="py-2.5 px-3 text-center border-r border-gray-200 last:border-r-0 cell-matrix-item {{ $isKosong ? 'bg-emerald-50/70' : '' }}">
                                    <label class="inline-flex flex-col items-center justify-center p-2 rounded-lg {{ $isLocked ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-emerald-100/50' }} transition-colors w-full h-full">
                                        <input type="checkbox"
                                               name="matrix[{{ $prod->id }}][{{ $toko->id }}]"
                                               value="kosong"
                                               data-toko-id="{{ $toko->id }}"
                                               data-produk-id="{{ $prod->id }}"
                                               data-toko-name="{{ $toko->name }}"
                                               data-produk-name="{{ $prod->name }}"
                                               {{ $isKosong ? 'checked' : '' }}
                                               {{ $isLocked ? 'disabled' : '' }}
                                               onchange="handleMatrixCheckboxChange(this)"
                                               class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 {{ $isLocked ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }} matrix-cb">
                                        <span class="text-[10px] mt-1 font-bold text-emerald-700 status-badge {{ $isKosong ? '' : 'hidden' }}">
                                            Selesai
                                        </span>
                                    </label>
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($availableTokos) + 2 }}" class="py-12 text-center text-gray-400 italic">
                                Tidak ada produk yang dipilih pada tugas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Menampilkan <strong class="text-gray-800">{{ count($availableProduks) }}</strong> produk & <strong class="text-gray-800">{{ count($availableTokos) }}</strong> toko yang di-ploting.
                </span>
                @if(!$isLocked)
                <button type="submit" id="btn-save-bottom-matrix" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-xs flex items-center gap-1.5 transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Pembaruan Stok
                </button>
                @endif
            </div>
        </form>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 5. FULL CUSTOM TASK WORKSPACE                                             -->
    <!-- ========================================================================= -->
    @if($task->output_type === 'full_custom')
    <div class="bg-white rounded-xl border border-purple-200/90 shadow-xs p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-purple-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    Lembar Pengerjaan: Form Input Full Custom
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Input Data Sesuai Format yang Ditentukan</h3>
            </div>
            @if($isCompleted)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i>
                    Tugas Selesai & Terkunci
                </span>
            @endif
        </div>

        @if($isCompleted)
            <!-- State: Completed (Show Submitted Custom Data - Multi-Row) -->
            @php
                $rawCustomData = $submission->custom_data ?? [];
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
            <div class="p-6 bg-purple-50/40 rounded-xl border border-purple-200/80 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-purple-200/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-purple-600 text-white rounded-xl shadow-xs">
                            <i data-lucide="check-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-purple-900">Data Pengerjaan Telah Disimpan</h4>
                            <p class="text-[11px] text-purple-700">
                                Diselesaikan pada: <span class="font-semibold">{{ $submission->completed_at ? $submission->completed_at->format('d M Y, H:i') : '-' }}</span>
                                • <span class="font-bold">{{ count($submittedRows) }} Baris Data Tersimpan</span>
                            </p>
                        </div>
                    </div>
                    @if(!$isExpired)
                    <button type="button" onclick="reopenFullCustomTask()" class="px-3.5 py-2 text-xs font-semibold text-purple-700 bg-white hover:bg-purple-100 border border-purple-300 rounded-lg transition-colors flex items-center gap-1.5 shadow-2xs self-start sm:self-auto">
                        <i data-lucide="edit-3" class="w-4 h-4 text-purple-600"></i>
                        Edit Kembali Data (Buka Kunci)
                    </button>
                    @endif
                </div>

                <div class="space-y-4 pt-1">
                    @forelse($submittedRows as $rIdx => $rowVal)
                        <div class="bg-white rounded-xl border border-purple-200 shadow-2xs overflow-hidden">
                            <div class="px-4 py-2 bg-purple-50/80 border-b border-purple-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-purple-900 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-purple-600 text-white text-[11px] font-bold flex items-center justify-center">
                                        {{ $rIdx + 1 }}
                                    </span>
                                    Baris Data ke-{{ $rIdx + 1 }}
                                </span>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                                @foreach($task->custom_fields ?? [] as $field)
                                    @php
                                        $fKey = $field['key'] ?? '';
                                        $fVal = $rowVal[$fKey] ?? null;
                                        $fType = $field['type'] ?? 'text';
                                    @endphp
                                    <div class="p-3 bg-gray-50/60 rounded-lg border border-gray-100 space-y-1 {{ $fType === 'textarea' ? 'sm:col-span-2 md:col-span-3' : '' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ $field['label'] }}</span>
                                            <span class="text-[9px] text-gray-400 font-mono">{{ $fType }}</span>
                                        </div>
                                        <div class="text-xs text-gray-800 pt-0.5">
                                            @if($fType === 'file')
                                                @if(is_array($fVal) && !empty($fVal['url']))
                                                    @php
                                                        $ext = strtolower(pathinfo($fVal['path'] ?? '', PATHINFO_EXTENSION));
                                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                    @endphp
                                                    @if($isImg)
                                                        <a href="{{ $fVal['url'] }}" target="_blank" class="inline-block group relative rounded-lg overflow-hidden border border-gray-200 mt-1">
                                                            <img src="{{ $fVal['url'] }}" alt="{{ $field['label'] }}" class="max-h-28 rounded-lg object-contain bg-white group-hover:scale-105 transition-transform">
                                                            <div class="text-[10px] text-primary font-semibold mt-1 flex items-center gap-1">
                                                                <i data-lucide="external-link" class="w-3 h-3"></i> Buka Foto Penuh
                                                            </div>
                                                        </a>
                                                    @else
                                                        <a href="{{ $fVal['url'] }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 text-purple-700 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors font-medium">
                                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                                            {{ $fVal['original_name'] ?? 'Unduh Dokumen' }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 italic">Tidak ada file</span>
                                                @endif
                                            @elseif($fType === 'textarea')
                                                <div class="leading-relaxed bg-white p-2.5 rounded-lg border border-gray-200">
                                                    {!! nl2br(e($fVal ?: '-')) !!}
                                                </div>
                                            @elseif($fType === 'number')
                                                <span class="font-bold font-mono text-sm text-gray-900">{{ is_numeric($fVal) ? number_format((float)$fVal, 0, ',', '.') : ($fVal ?: '-') }}</span>
                                            @elseif($fType === 'url')
                                                @if(!empty($fVal))
                                                    <a href="{{ Str::startsWith($fVal, ['http://', 'https://']) ? $fVal : 'https://' . $fVal }}" target="_blank" class="text-primary hover:underline font-semibold inline-flex items-center gap-1 break-all">
                                                        <span>{{ $fVal }}</span>
                                                        <i data-lucide="external-link" class="w-3 h-3 shrink-0"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            @elseif($fType === 'email')
                                                @if(!empty($fVal))
                                                    <a href="mailto:{{ $fVal }}" class="text-primary hover:underline font-semibold inline-flex items-center gap-1 break-all">
                                                        <i data-lucide="mail" class="w-3 h-3 shrink-0"></i>
                                                        <span>{{ $fVal }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            @elseif($fType === 'date')
                                                @if(!empty($fVal))
                                                    <span class="inline-flex items-center gap-1 font-medium text-gray-900">
                                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                                        {{ \Carbon\Carbon::hasFormat($fVal, 'Y-m-d') ? \Carbon\Carbon::parse($fVal)->translatedFormat('d M Y') : $fVal }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            @elseif($fType === 'time')
                                                @if(!empty($fVal))
                                                    <span class="inline-flex items-center gap-1 font-medium text-gray-900 font-mono">
                                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i>
                                                        {{ $fVal }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            @elseif($fType === 'datetime')
                                                @if(!empty($fVal))
                                                    <span class="inline-flex items-center gap-1 font-medium text-gray-900 font-mono">
                                                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-gray-400"></i>
                                                        {{ str_replace('T', ' ', $fVal) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            @else
                                                <span class="font-medium text-gray-900">{{ $fVal ?: '-' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Belum ada data pengerjaan.</p>
                    @endforelse
                </div>

                @if($submission->completion_notes)
                    <div class="p-3 bg-white rounded-lg border border-purple-100 text-xs text-gray-700">
                        <span class="font-bold text-gray-900 block mb-1">Catatan Tambahan:</span>
                        {{ $submission->completion_notes }}
                    </div>
                @endif
            </div>
        @elseif($isExpired)
            <div class="p-8 bg-slate-50 rounded-xl border border-slate-200 text-center space-y-2">
                <i data-lucide="clock-alert" class="w-8 h-8 text-slate-400 mx-auto"></i>
                <h4 class="text-sm font-bold text-slate-700">Batas Waktu Telah Berakhir</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Tugas ini telah melewati tenggat waktu sehingga pengisian form ditutup.
                </p>
            </div>
        @else
            <!-- State: Input Form (Multi-Row Dynamic) -->
            @php
                $existingCustomData = $submission->custom_data ?? [];
                $initialRows = [];
                if (!empty($existingCustomData) && is_array($existingCustomData)) {
                    $firstKey = array_key_first($existingCustomData);
                    if ($firstKey !== null && is_numeric($firstKey)) {
                        $initialRows = $existingCustomData;
                    } else {
                        $initialRows = [ $existingCustomData ];
                    }
                }
                if (empty($initialRows)) {
                    $initialRows = [ [] ];
                }
            @endphp
            <form id="form-submit-full-custom" action="{{ route('tasks.my-tasks.complete', $assignee->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Action Bar: Add Row & Selesaikan Tugas -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 bg-purple-50/70 rounded-xl border border-purple-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 text-xs font-bold text-purple-800 bg-purple-100 rounded-lg border border-purple-200" id="custom-rows-counter">
                            {{ count($initialRows) }} Baris Data
                        </span>
                        <p class="text-xs text-gray-600">
                            Anda dapat mengisi satu atau beberapa baris data pengerjaan sekaligus.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="addCustomRow()" class="px-3.5 py-2 text-xs font-bold text-purple-700 bg-purple-100 hover:bg-purple-200 border border-purple-300 rounded-lg shadow-2xs flex items-center justify-center gap-1.5 transition-colors shrink-0">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Tambah Baris
                        </button>
                        <button type="button" onclick="confirmFinishFullCustom()" class="px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs flex items-center justify-center gap-1.5 transition-colors shrink-0">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Tandai Tugas Selesai
                        </button>
                    </div>
                </div>

                <!-- Container of dynamic rows -->
                <div id="custom-rows-container" class="space-y-4">
                    @foreach($initialRows as $rIdx => $rowVal)
                        <div class="custom-row-card bg-white rounded-xl border border-purple-200 shadow-2xs overflow-hidden transition-all" data-row-index="{{ $rIdx }}">
                            <div class="px-4 py-2.5 bg-purple-50/60 border-b border-purple-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-purple-900 flex items-center gap-2 row-title-label">
                                    <span class="w-5 h-5 rounded-full bg-purple-600 text-white text-[11px] font-bold flex items-center justify-center row-badge-num">
                                        {{ $rIdx + 1 }}
                                    </span>
                                    Baris #{{ $rIdx + 1 }}
                                </span>
                                <button type="button" onclick="removeCustomRow(this)" class="btn-remove-row p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-1 text-xs font-medium {{ count($initialRows) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($initialRows) <= 1 ? 'disabled' : '' }} title="Hapus Baris Ini">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span class="hidden sm:inline">Hapus Baris</span>
                                </button>
                            </div>
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                @forelse($task->custom_fields ?? [] as $field)
                                    @php
                                        $fKey = $field['key'] ?? '';
                                        $fReq = !empty($field['required']);
                                        $fType = $field['type'] ?? 'text';
                                        $fExisting = $rowVal[$fKey] ?? null;
                                    @endphp
                                    <div class="{{ $fType === 'textarea' ? 'md:col-span-2' : '' }} space-y-1.5 field-col">
                                        <label class="block text-xs font-semibold text-gray-700">
                                            {{ $field['label'] }}
                                            @if($fReq)
                                                <span class="text-red-500">*</span>
                                            @else
                                                <span class="text-[10px] text-gray-400 font-normal">(Opsional)</span>
                                            @endif
                                        </label>

                                        @if($fType === 'number')
                                            <input type="number" step="any" name="custom_data[{{ $rIdx }}][{{ $fKey }}]" value="{{ is_array($fExisting) ? '' : $fExisting }}" placeholder="Masukkan angka/nominal..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>
                                        @elseif($fType === 'date')
                                            <input type="date" name="custom_data[{{ $rIdx }}][{{ $fKey }}]" value="{{ is_array($fExisting) ? '' : $fExisting }}" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>
                                        @elseif($fType === 'time')
                                            <input type="time" name="custom_data[{{ $rIdx }}][{{ $fKey }}]" value="{{ is_array($fExisting) ? '' : $fExisting }}" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>
                                        @elseif($fType === 'datetime')
                                            <input type="datetime-local" name="custom_data[{{ $rIdx }}][{{ $fKey }}]" value="{{ is_array($fExisting) ? '' : $fExisting }}" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>
                                        @elseif($fType === 'url')
                                            <input type="url" name="custom_data[{{ $rIdx }}][{{ $fKey }}]" value="{{ is_array($fExisting) ? '' : $fExisting }}" placeholder="https://example.com" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>
                                        @elseif($fType === 'email')
                                            <input type="email" name="custom_data[{{ $rIdx }}][{{ $fKey }}]" value="{{ is_array($fExisting) ? '' : $fExisting }}" placeholder="nama@email.com" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>
                                        @elseif($fType === 'textarea')
                                            <textarea name="custom_data[{{ $rIdx }}][{{ $fKey }}]" rows="3" placeholder="Tuliskan keterangan detail..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>{{ is_array($fExisting) ? '' : $fExisting }}</textarea>
                                        @elseif($fType === 'file')
                                            <div class="space-y-1">
                                                <input type="file" name="custom_files[{{ $rIdx }}][{{ $fKey }}]" class="w-full text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary bg-white file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" {{ ($fReq && empty($fExisting)) ? 'required' : '' }}>
                                                @if(is_array($fExisting) && !empty($fExisting['url']))
                                                    <p class="text-[11px] text-gray-500">File tersimpan: <a href="{{ $fExisting['url'] }}" target="_blank" class="text-primary hover:underline font-medium">{{ $fExisting['original_name'] ?? 'Lihat file' }}</a></p>
                                                @endif
                                            </div>
                                        @else
                                            <input type="text" name="custom_data[{{ $rIdx }}][{{ $fKey }}]" value="{{ is_array($fExisting) ? '' : $fExisting }}" placeholder="Masukkan data..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" {{ $fReq ? 'required' : '' }}>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400 italic col-span-2">Tidak ada konfigurasi field pada tugas ini.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Bottom Add Row Button -->
                <div class="flex justify-center pt-1">
                    <button type="button" onclick="addCustomRow()" class="px-4 py-2 text-xs font-semibold text-purple-700 bg-purple-100 hover:bg-purple-200 rounded-lg transition-colors flex items-center gap-1.5">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah Baris Lainnya
                    </button>
                </div>

                <!-- Notes / Additional Proof (Optional) -->
                <div class="pt-3 border-t border-gray-100 space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700">
                        Catatan Tambahan Keseluruhan (Opsional)
                    </label>
                    <textarea name="completion_notes" rows="2" placeholder="Catatan opsional saat menyelesaikan tugas..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">{{ $submission->completion_notes }}</textarea>
                </div>

                <input type="hidden" name="mark_complete" id="input-mark-complete" value="0">

                <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-purple-600 shrink-0"></i>
                        <span>Klik <strong>Simpan Data</strong> untuk menyimpan tanpa mengunci (bisa diedit kapan saja).</span>
                    </p>
                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end flex-wrap">
                        <button type="submit" onclick="$('#input-mark-complete').val('0')" id="btn-save-draft-custom" class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-purple-700 bg-purple-100 hover:bg-purple-200 border border-purple-300 rounded-xl shadow-2xs transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Data (Bisa Diedit Kembali)
                        </button>
                        <button type="button" onclick="confirmFinishFullCustom()" id="btn-submit-full-custom" class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Tandai Tugas Selesai
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Initialize Toko Select2 Ajax
    function initTokoSelect2() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2-toko-ajax').each(function() {
                var $el = $(this);
                if (!$el.hasClass('select2-hidden-accessible')) {
                    $el.select2({
                        dropdownParent: $('#add-entry-modal'),
                        width: '100%',
                        placeholder: $el.data('placeholder') || '-- Cari & Pilih Toko / Marketplace --',
                        allowClear: true,
                        tags: true,
                        ajax: {
                            url: "{{ route('tasks.my-tasks.tokos-select2') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term,
                                    task_id: "{{ $task->id }}"
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results || []
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 0
                    });

                    $el.on('select2:select', function(e) {
                        var data = e.params.data;
                        if (data.toko_id) {
                            $('#entry_toko_id').val(data.toko_id);
                        } else if (data.id && !isNaN(data.id)) {
                            $('#entry_toko_id').val(data.id);
                        }
                    });

                    $el.on('select2:clear', function(e) {
                        $('#entry_toko_id').val('');
                    });
                }
            });
        }
    }

    // Initialize Produk / Barang Select2 Ajax
    function initProdukSelect2() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2-produk-ajax').each(function() {
                var $el = $(this);
                if (!$el.hasClass('select2-hidden-accessible')) {
                    $el.select2({
                        dropdownParent: $('#add-entry-modal'),
                        width: '100%',
                        placeholder: $el.data('placeholder') || '-- Cari & Pilih Produk / Barang --',
                        allowClear: true,
                        tags: true,
                        ajax: {
                            url: "{{ route('tasks.my-tasks.produks-select2') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term,
                                    task_id: "{{ $task->id }}"
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results || []
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 0
                    });

                    $el.on('select2:select', function(e) {
                        var data = e.params.data;
                        if (data.produk_id) {
                            $('#entry_produk_id').val(data.produk_id);
                        } else if (data.id && !isNaN(data.id)) {
                            $('#entry_produk_id').val(data.id);
                        }
                    });

                    $el.on('select2:clear', function(e) {
                        $('#entry_produk_id').val('');
                    });
                }
            });
        }
    }

    function openAddEntryModal() {
        $('#modal_entry_id').val('');
        $('#modal-entry-title').text('Tambah Baris Data Jawaban');
        $('#modal-entry-btn-text').text('Simpan Data');
        $('#form-my-entry')[0].reset();
        $('#entry_toko_id').val('');
        $('#entry_produk_id').val('');
        $('.select2-toko-ajax').val(null).trigger('change');
        $('.select2-produk-ajax').val(null).trigger('change');
        $('#add-entry-modal').removeClass('hidden');
        setTimeout(function() {
            initTokoSelect2();
            initProdukSelect2();
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 50);
    }

    function openEditEntryModal(id, entryData, tokoId, tokoName, produkId, produkName) {
        $('#modal_entry_id').val(id);
        $('#modal-entry-title').text('Edit Baris Data Jawaban');
        $('#modal-entry-btn-text').text('Perbarui Data');
        $('#form-my-entry')[0].reset();

        // Populate dynamic fields
        if (entryData && typeof entryData === 'object') {
            $('.entry-dynamic-field').each(function() {
                const colName = $(this).data('column');
                if (colName && entryData[colName] !== undefined) {
                    if (!$(this).hasClass('select2-toko-ajax') && !$(this).hasClass('select2-produk-ajax')) {
                        $(this).val(entryData[colName]);
                    }
                }
            });
        }

        // Populate Toko Select2
        if (tokoId) {
            $('#entry_toko_id').val(tokoId);
            const optName = tokoName || ('Toko #' + tokoId);
            const opt = new Option(optName, tokoId, true, true);
            $('.select2-toko-ajax').empty().append(opt).trigger('change');
        } else {
            $('#entry_toko_id').val('');
            $('.select2-toko-ajax').val(null).trigger('change');
        }

        // Populate Produk Select2
        if (produkId) {
            $('#entry_produk_id').val(produkId);
            const prodOptName = produkName || ('Produk #' + produkId);
            const opt = new Option(prodOptName, produkId, true, true);
            $('.select2-produk-ajax').empty().append(opt).trigger('change');
        } else {
            $('#entry_produk_id').val('');
            $('.select2-produk-ajax').val(null).trigger('change');
        }

        $('#add-entry-modal').removeClass('hidden');
        setTimeout(function() {
            initTokoSelect2();
            initProdukSelect2();
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 50);
    }

    function closeAddEntryModal() {
        $('#add-entry-modal').addClass('hidden');
        $('#modal_entry_id').val('');
        $('#modal-entry-title').text('Tambah Baris Data Jawaban');
        $('#modal-entry-btn-text').text('Simpan Data');
    }

    $(document).ready(function() {
        initTokoSelect2();
        initProdukSelect2();
    });

    // Preview completion photo
    function previewCompletionPhoto(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photo-preview-img').attr('src', e.target.result);
                $('#photo-preview-name').text(file.name);
                $('#photo-preview-box').removeClass('hidden');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
            reader.readAsDataURL(file);
        }
    }

    // Remove selected photo
    function removeCompletionPhoto() {
        $('#input_completion_file').val('');
        $('#photo-preview-box').addClass('hidden');
        $('#photo-preview-img').attr('src', '');
    }

    // Submit completion proof (URL, photo, notes)
    function submitCompletionProof(e) {
        e.preventDefault();
        const form = $('#form-completion-proof')[0];
        const formData = new FormData(form);
        const btn = $('#btn-submit-proof');

        btn.prop('disabled', true).text('Menyimpan Bukti...');

        $.ajax({
            url: "{{ route('tasks.my-tasks.complete', $assignee?->id ?? $task->id) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    $('#completion-proof-modal').addClass('hidden');
                    Swal.fire({
                        icon: 'success',
                        title: 'Tugas Selesai!',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Konfirmasi & Selesaikan Tugas');
                if (typeof lucide !== 'undefined') lucide.createIcons();
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    // 2. Target achievement update
    function submitMyTarget(e) {
        e.preventDefault();
        const form = $('#form-my-target');
        const btn = $('#btn-submit-my-target');
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ route('tasks.my-tasks.update-target', $assignee?->id ?? $task->id) }}",
            type: 'POST',
            data: form.serialize() + "&_token={{ csrf_token() }}",
            success: function(res) {
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Simpan Capaian Target');
                if (res.status === 'success') {
                    $('#current-achieved-display').text(res.achieved);
                    $('#task-progress-pct').text(res.progress + '%');
                    $('#target-percent-display').text(res.progress + '%');
                    $('#target-progress-bar').css('width', Math.min(100, res.progress) + '%');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (res.progress >= 100) location.reload();
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Simpan Capaian Target');
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    // 3. Data Entry submission (Store or Update)
    function submitMyEntry(e) {
        e.preventDefault();
        const form = $('#form-my-entry');
        const btn = $('#btn-submit-entry');
        const entryId = $('#modal_entry_id').val();
        const isEdit = Boolean(entryId);

        const btnOriginalText = isEdit ? 'Perbarui Data' : 'Simpan Data';
        btn.prop('disabled', true).text(isEdit ? 'Memperbarui...' : 'Menyimpan...');

        let url = "{{ route('tasks.my-tasks.data-entries.store', $assignee?->id ?? $task->id) }}";
        let method = 'POST';
        let data = form.serialize() + "&_token={{ csrf_token() }}";

        if (isEdit) {
            url = "{{ url('my-tasks/data-entries') }}/" + entryId;
            data += "&_method=PUT";
        }

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(res) {
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> <span id="modal-entry-btn-text">' + btnOriginalText + '</span>');
                if (typeof lucide !== 'undefined') lucide.createIcons();

                if (res.status === 'success') {
                    closeAddEntryModal();
                    form[0].reset();
                    $('#modal_entry_id').val('');
                    $('#entry_toko_id').val('');
                    $('#entry_produk_id').val('');
                    $('.select2-toko-ajax').val(null).trigger('change');
                    $('.select2-produk-ajax').val(null).trigger('change');
                    Swal.fire({
                        icon: 'success',
                        title: isEdit ? 'Data Diperbarui!' : 'Data Tersimpan!',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> <span id="modal-entry-btn-text">' + btnOriginalText + '</span>');
                if (typeof lucide !== 'undefined') lucide.createIcons();
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    // Delete data entry
    function deleteMyDataEntry(id) {
        Swal.fire({
            title: 'Hapus Baris Data?',
            text: 'Data yang telah diinput akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('my-tasks/data-entries') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#entry-row-' + id).remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    // Complete Data List task
    function completeDataListTask() {
        const entryCount = $('#my-data-entries-table tbody tr').not('#empty-entries-row').length;
        let confirmText = `Anda telah menginputkan ${entryCount} baris data. Tandai tugas ini sebagai Selesai?`;
        if (entryCount === 0) {
            confirmText = 'Anda belum menginputkan baris data. Tetap tandai tugas ini sebagai Selesai?';
        }

        Swal.fire({
            title: 'Selesaikan Tugas?',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Tandai Selesai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('tasks.my-tasks.complete', $assignee?->id ?? $task->id) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tugas Selesai!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    // 4. Matrix Stock Update Handlers
    function handleMatrixCheckboxChange(cb) {
        if ($(cb).prop('disabled')) return;
        const isChecked = $(cb).is(':checked');
        const cell = $(cb).closest('.cell-matrix-item');
        const badge = cell.find('.status-badge');
        const tokoId = $(cb).data('toko-id');
        const produkId = $(cb).data('produk-id');
        const tokoName = $(cb).data('toko-name');
        const prodName = $(cb).data('produk-name');

        if (isChecked) {
            cell.addClass('bg-emerald-50/70');
            badge.text('Selesai').removeClass('hidden');
        } else {
            cell.removeClass('bg-emerald-50/70');
            badge.addClass('hidden');
        }

        // Auto-save in realtime via AJAX
        $.ajax({
            url: "{{ route('tasks.my-tasks.stock-matrix', $assignee?->id ?? $task->id) }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                single_toggle: 1,
                toko_id: tokoId,
                produk_id: produkId,
                is_kosong: isChecked ? 1 : 0
            },
            success: function(res) {
                // Non-blocking mini toast
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: isChecked ? 'success' : 'info',
                    title: isChecked ? `${prodName} di ${tokoName}: SELESAI` : `${prodName} di ${tokoName}: BELUM SELESAI`
                });
            },
            error: function(xhr) {
                // Revert on error
                $(cb).prop('checked', !isChecked);
                if (!isChecked) {
                    cell.addClass('bg-emerald-50/70');
                    badge.text('Selesai').removeClass('hidden');
                } else {
                    cell.removeClass('bg-emerald-50/70');
                    badge.addClass('hidden');
                }
                Swal.fire('Gagal Menyimpan!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    // Batch save all matrix
    function submitStockMatrixForm(e) {
        if (e) e.preventDefault();
        const form = $('#form-stock-matrix');
        const btnTop = $('#btn-save-top-matrix');
        const btnBottom = $('#btn-save-bottom-matrix');

        btnTop.prop('disabled', true).text('Menyimpan...');
        btnBottom.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ route('tasks.my-tasks.stock-matrix', $assignee?->id ?? $task->id) }}",
            type: 'POST',
            data: form.serialize() + "&_token={{ csrf_token() }}",
            success: function(res) {
                btnTop.prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4"></i> Simpan Pembaruan Stok');
                btnBottom.prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4"></i> Simpan Pembaruan Stok');
                if (typeof lucide !== 'undefined') lucide.createIcons();

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: `${res.message} Total ${res.total_kosong} produk berstatus selesai tercatat.`,
                        timer: 1800,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                btnTop.prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4"></i> Simpan Pembaruan Stok');
                btnBottom.prop('disabled', false).html('<i data-lucide="save" class="w-4 h-4"></i> Simpan Pembaruan Stok');
                if (typeof lucide !== 'undefined') lucide.createIcons();
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    // Filter matrix table rows by product name/code
    function filterMatrixTable() {
        const query = $('#filter-matrix-product').val().toLowerCase();
        $('.product-matrix-row').each(function() {
            const title = $(this).data('product-title') || '';
            if (title.indexOf(query) !== -1) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    }

    // Check / uncheck all visible matrix checkboxes
    function checkAllMatrix(check) {
        $('.product-matrix-row:visible .matrix-cb').each(function() {
            const current = $(this).is(':checked');
            if (current !== check) {
                $(this).prop('checked', check);
                const cell = $(this).closest('.cell-matrix-item');
                const badge = cell.find('.status-badge');
                if (check) {
                    cell.addClass('bg-emerald-50/70');
                    badge.text('Selesai').removeClass('hidden');
                } else {
                    cell.removeClass('bg-emerald-50/70');
                    badge.addClass('hidden');
                }
            }
        });
        // Batch save immediately
        submitStockMatrixForm();
    }

    // =========================================================================
    // FULL CUSTOM DYNAMIC ROWS LOGIC
    // =========================================================================
    const taskCustomFields = @json($task->custom_fields ?? []);

    function addCustomRow() {
        const nextIdx = $('#custom-rows-container .custom-row-card').length;
        let fieldsHtml = '';

        taskCustomFields.forEach(function(field) {
            const fKey = field.key || '';
            const fLabel = escapeHtml(field.label || '');
            const fReq = !!field.required;
            const fType = field.type || 'text';
            const colClass = fType === 'textarea' ? 'md:col-span-2' : '';
            const reqAsterisk = fReq ? '<span class="text-red-500">*</span>' : '<span class="text-[10px] text-gray-400 font-normal">(Opsional)</span>';
            const reqAttr = fReq ? 'required' : '';

            let inputHtml = '';
            if (fType === 'number') {
                inputHtml = `<input type="number" step="any" name="custom_data[${nextIdx}][${fKey}]" placeholder="Masukkan angka/nominal..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}>`;
            } else if (fType === 'date') {
                inputHtml = `<input type="date" name="custom_data[${nextIdx}][${fKey}]" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}>`;
            } else if (fType === 'time') {
                inputHtml = `<input type="time" name="custom_data[${nextIdx}][${fKey}]" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}>`;
            } else if (fType === 'datetime') {
                inputHtml = `<input type="datetime-local" name="custom_data[${nextIdx}][${fKey}]" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}>`;
            } else if (fType === 'url') {
                inputHtml = `<input type="url" name="custom_data[${nextIdx}][${fKey}]" placeholder="https://example.com" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}>`;
            } else if (fType === 'email') {
                inputHtml = `<input type="email" name="custom_data[${nextIdx}][${fKey}]" placeholder="nama@email.com" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}>`;
            } else if (fType === 'textarea') {
                inputHtml = `<textarea name="custom_data[${nextIdx}][${fKey}]" rows="3" placeholder="Tuliskan keterangan detail..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}></textarea>`;
            } else if (fType === 'file') {
                inputHtml = `<input type="file" name="custom_files[${nextIdx}][${fKey}]" class="w-full text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary bg-white file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" ${reqAttr}>`;
            } else {
                inputHtml = `<input type="text" name="custom_data[${nextIdx}][${fKey}]" placeholder="Masukkan data..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" ${reqAttr}>`;
            }

            fieldsHtml += `
                <div class="${colClass} space-y-1.5 field-col">
                    <label class="block text-xs font-semibold text-gray-700">
                        ${fLabel} ${reqAsterisk}
                    </label>
                    ${inputHtml}
                </div>
            `;
        });

        const rowNum = nextIdx + 1;
        const cardHtml = `
            <div class="custom-row-card bg-white rounded-xl border border-purple-200 shadow-2xs overflow-hidden transition-all" data-row-index="${nextIdx}">
                <div class="px-4 py-2.5 bg-purple-50/60 border-b border-purple-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-purple-900 flex items-center gap-2 row-title-label">
                        <span class="w-5 h-5 rounded-full bg-purple-600 text-white text-[11px] font-bold flex items-center justify-center row-badge-num">
                            ${rowNum}
                        </span>
                        Baris #${rowNum}
                    </span>
                    <button type="button" onclick="removeCustomRow(this)" class="btn-remove-row p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-1 text-xs font-medium" title="Hapus Baris Ini">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Hapus Baris</span>
                    </button>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${fieldsHtml}
                </div>
            </div>
        `;

        $('#custom-rows-container').append(cardHtml);
        reindexCustomRows();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeCustomRow(btn) {
        const total = $('#custom-rows-container .custom-row-card').length;
        if (total <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Perhatian', 'Minimal harus ada 1 baris data.', 'warning');
            } else {
                alert('Minimal harus ada 1 baris data.');
            }
            return;
        }
        $(btn).closest('.custom-row-card').remove();
        reindexCustomRows();
    }

    function reindexCustomRows() {
        const cards = $('#custom-rows-container .custom-row-card');
        cards.each(function(idx) {
            const rowNum = idx + 1;
            $(this).attr('data-row-index', idx);
            $(this).find('.row-badge-num').text(rowNum);
            $(this).find('.row-title-label').html(`
                <span class="w-5 h-5 rounded-full bg-purple-600 text-white text-[11px] font-bold flex items-center justify-center row-badge-num">${rowNum}</span>
                Baris #${rowNum}
            `);

            // Re-index names: custom_data[X][key] or custom_files[X][key]
            $(this).find('input, textarea, select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/^(custom_data|custom_files)\[\d+\]/, `$1[${idx}]`);
                    $(this).attr('name', newName);
                }
            });

            // If only 1 row, style remove button as disabled
            if (cards.length === 1) {
                $(this).find('.btn-remove-row').addClass('opacity-50 cursor-not-allowed').attr('disabled', true);
            } else {
                $(this).find('.btn-remove-row').removeClass('opacity-50 cursor-not-allowed').removeAttr('disabled');
            }
        });

        $('#custom-rows-counter').text(`${cards.length} Baris Data`);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function confirmFinishFullCustom() {
        const form = document.getElementById('form-submit-full-custom');
        if (!form) return;

        // Check HTML5 validity for required fields
        if (!form.reportValidity()) {
            return;
        }

        Swal.fire({
            title: 'Tandai Tugas Selesai?',
            text: 'Pastikan seluruh baris data pengerjaan sudah benar. Apakah Anda yakin ingin menyelesaikan tugas ini sekarang?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Tandai Selesai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#input-mark-complete').val('1');
                const btn = $('#btn-submit-full-custom');
                btn.prop('disabled', true).html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menyelesaikan...');
                if (typeof lucide !== 'undefined') lucide.createIcons();
                form.submit();
            }
        });
    }

    function reopenFullCustomTask() {
        Swal.fire({
            title: 'Buka Kunci untuk Edit Data?',
            text: 'Kunci pengerjaan tugas akan dibuka dan status dikembalikan ke Sedang Dikerjakan agar Anda dapat mengubah baris data.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Buka Kunci untuk Edit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('tasks.my-tasks.complete', $assignee?->id ?? $task->id) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        uncomplete: '1'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Kunci Dibuka!',
                            text: res.message || 'Tugas dikembalikan ke status Sedang Dikerjakan.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        $('#form-submit-full-custom').on('submit', function(e) {
            const isFinish = $('#input-mark-complete').val() === '1';
            const btn = isFinish ? $('#btn-submit-full-custom') : $('#btn-save-draft-custom');
            btn.prop('disabled', true).html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menyimpan...');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    });
</script>
@endpush
