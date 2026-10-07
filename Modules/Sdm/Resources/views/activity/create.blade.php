@extends('template.root')
@section('page-name', 'aktifitas karyawan')
@php
    $isEdit = isset($activity);
@endphp
@section('title-page', $isEdit ? 'Edit Aktifitas Karyawan' : 'Buat Aktifitas Karyawan')
@section('content')

    <form action="{{ $isEdit ? url('employee-activities/' . $activity->id) : url('employee-activities') }}" method="POST"
        enctype="multipart/form-data">

        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        @if ($is_admin ?? false)
            <div class="mb-3">
                <label class="form-label">Tipe Laporan (Jabatan)</label>
                <select name="jabatan" id="jabatan_selector" class="form-select" required onchange="handleJabatanChange()">
                    <option value="staff operasional"
                        {{ str_contains($jabatan, 'staff') && str_contains($jabatan, 'operasional') ? 'selected' : '' }}>
                        Staff Operasional</option>
                    <option value="pic operasional"
                        {{ str_contains($jabatan, 'pic') && str_contains($jabatan, 'operasional') ? 'selected' : '' }}>PIC
                        Operasional</option>
                    <option value="staff" {{ !str_contains($jabatan, 'operasional') ? 'selected' : '' }}>Lainnya / Umum
                    </option>
                </select>
            </div>
        @else
            <input type="hidden" name="jabatan" id="jabatan_selector" value="{{ $jabatan }}">
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Tanggal Aktivitas</label>
                <input type="date" name="activity_date" id="activity_date" class="form-control"
                    value="{{ old('activity_date', $activity->activity_date ?? date('Y-m-d')) }}" required
                    onchange="handleDateChange()">
            </div>
            {{-- ... other time inputs ... --}}
            <div class="col-md-4">
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="start_time" class="form-control"
                    value="{{ old('start_time', $activity->start_time ?? '') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="end_time" class="form-control"
                    value="{{ old('end_time', $activity->end_time ?? '') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi Kegiatan</label>
            <textarea name="activity" rows="4" class="form-control" required>{{ old('activity', $activity->activity ?? '') }}</textarea>
        </div>

        {{-- Dynamic Fields Container --}}
        <div id="dynamic-fields-container">
            <div id="ops-fields" class="{{ str_contains($jabatan ?? '', 'operasional') ? '' : 'd-none' }}">
                <div class="row mb-3">
                    <div id="do-today-wrapper" class="col-md-{{ str_contains($jabatan, 'pic') ? '6' : '12' }}">
                        <label class="form-label">Total DO Hari Ini</label>
                        <input type="number" name="do_today" id="do_today_input" class="form-control"
                            value="{{ old('do_today', $activity->do_today ?? ($do_today_count > 0 ? $do_today_count : '')) }}"
                            placeholder="Masukkan jumlah DO hari ini">
                        <small id="do_today_helper"
                            class="text-info {{ $do_today_count > 0 && !$isEdit ? '' : 'd-none' }}">
                            * Otomatis terisi dari
                            {{ str_contains($jabatan, 'pic') ? 'total DO masuk' : 'jumlah DO yang Anda buat' }} hari ini
                        </small>
                    </div>

                    <div id="pic-only-fields" class="col-md-6 {{ str_contains($jabatan, 'pic') ? '' : 'd-none' }}">
                        <label class="form-label">Total DO Besok</label>
                        <input type="number" name="do_tomorrow" id="do_tomorrow_input" class="form-control"
                            value="{{ old('do_tomorrow', $activity->do_tomorrow ?? '') }}"
                            placeholder="Masukkan jumlah estimasi DO besok">
                    </div>
                </div>

                <div id="truck-pos-wrapper" class="mb-3 {{ str_contains($jabatan ?? '', 'pic') ? '' : 'd-none' }}">
                    <label class="form-label fw-bold">Lokasi Armada</label>
                    <div class="card border-info">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                @foreach ($armadas ?? [] as $armada)
                                    <div class="col-12">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"
                                                style="width: 120px;">{{ $armada->nopol }}</span>
                                            <input type="text" name="truck_positions[{{ $armada->nopol }}]"
                                                class="form-control" value="{{ $truck_locations[$armada->nopol] ?? '' }}"
                                                placeholder="Lokasi {{ $armada->nama_armada ?? '' }}...">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto Kegiatan</label>
            <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewImage(this)">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto</small>
        </div>

        {{-- Preview Foto --}}
        <div class="mb-3">
            <img id="preview" src="{{ $isEdit && $activity->photo ? asset('storage/' . $activity->photo) : '#' }}"
                class="img-thumbnail {{ $isEdit && $activity->photo ? '' : 'd-none' }}" width="200">
        </div>

        <div class="text-end">
            <a href="{{ url('employee-activities') }}" class="btn btn-secondary">
                Kembali
            </a>
            <button class="btn btn-primary">
                {{ $isEdit ? 'Update Aktivitas' : 'Simpan Aktivitas' }}
            </button>
        </div>
    </form>

@section('script')
    <script>
        function handleJabatanChange() {
            const jabatan = document.getElementById('jabatan_selector').value.toLowerCase();
            const date = document.getElementById('activity_date').value;

            // Toggle visibility
            const opsFields = document.getElementById('ops-fields');
            const picOnlyFields = document.getElementById('pic-only-fields');
            const truckPosWrapper = document.getElementById('truck-pos-wrapper');
            const doTodayWrapper = document.getElementById('do-today-wrapper');

            if (jabatan.includes('operasional')) {
                opsFields.classList.remove('d-none');
                if (jabatan.includes('pic')) {
                    picOnlyFields.classList.remove('d-none');
                    truckPosWrapper.classList.remove('d-none');
                    doTodayWrapper.classList.remove('col-md-12');
                    doTodayWrapper.classList.add('col-md-6');
                } else {
                    picOnlyFields.classList.add('d-none');
                    truckPosWrapper.classList.add('d-none');
                    doTodayWrapper.classList.remove('col-md-6');
                    doTodayWrapper.classList.add('col-md-12');
                }

                // Fetch DO Count
                fetchDoCount(jabatan, date);
            } else {
                opsFields.classList.add('d-none');
            }
        }

        function handleDateChange() {
            const jabatan = document.getElementById('jabatan_selector').value.toLowerCase();
            const date = document.getElementById('activity_date').value;

            if (jabatan.includes('operasional')) {
                fetchDoCount(jabatan, date);
            }
        }

        function fetchDoCount(jabatan, date) {
            const url = `{{ route('employee-activities.get-do-count') }}?jabatan=${jabatan}&date=${date}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const input = document.getElementById('do_today_input');
                    const helper = document.getElementById('do_today_helper');

                    if (data.count > 0 && !@json($isEdit)) {
                        input.value = data.count;
                        helper.classList.remove('d-none');
                        helper.innerText =
                            `* Otomatis terisi dari ${jabatan.includes('pic') ? 'total DO masuk' : 'jumlah DO yang Anda buat'} hari ini`;
                    } else if (!@json($isEdit)) {
                        input.value = '';
                        helper.classList.add('d-none');
                    }
                });
        }

        function previewImage(input) {
            const preview = document.getElementById('preview');
            const file = input.files[0];

            if (!file) return;

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    </script>
@endsection
@endsection
