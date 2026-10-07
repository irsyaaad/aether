<div class="container">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-muted">Nama Karyawan</label>
                    <div class="fw-bold">
                        {{ $activity->user->nm_user ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted">Tanggal Aktivitas</label>
                    <div class="fw-bold">
                        {{ $activity->activity_date->format('d M Y') }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted">Jam Kegiatan</label>
                    <div class="fw-bold">
                        {{ $activity->start_time }} - {{ $activity->end_time }}
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label class="text-muted">Deskripsi Kegiatan</label>
                <div class="border rounded p-3">
                    {{ $activity->activity }}
                </div>
            </div>

            <div class="mb-3">
                <label class="text-muted">Foto Kegiatan</label><br>

                @if ($activity->photo)
                    <img src="{{ asset('storage/' . $activity->photo) }}" class="img-fluid rounded border"
                        style="max-height:300px">
                @else
                    <span class="text-muted fst-italic">
                        Tidak ada foto kegiatan
                    </span>
                @endif
            </div>

            @if ($activity->do_today || $activity->do_tomorrow || !empty($truck_locations))
                <hr>
                <h6 class="mb-3">Informasi Operasional</h6>
                <div class="row mb-3">
                    @if ($activity->do_today)
                        <div class="col-md-4">
                            <label class="text-muted small">Total DO Hari Ini</label>
                            <div class="fw-bold">{{ $activity->do_today }}</div>
                        </div>
                    @endif

                    @if ($activity->do_tomorrow)
                        <div class="col-md-4">
                            <label class="text-muted small">Total DO Besok</label>
                            <div class="fw-bold">{{ $activity->do_tomorrow }}</div>
                        </div>
                    @endif
                </div>

                @if (!empty($truck_locations))
                    <div class="mb-3">
                        <label class="text-muted small">Lokasi Armada</label>
                        <div class="card border-info mt-2">
                            <div class="card-body p-2">
                                <div class="row g-2">
                                    @foreach ($truck_locations as $nopol => $lokasi)
                                        @if (!empty($lokasi))
                                            <div class="col-12">
                                                <div
                                                    class="px-3 py-2 border-start border-3 border-info rounded bg-light mb-1">
                                                    <span class="fw-bold me-2">{{ $nopol }}:</span>
                                                    <span>{{ $lokasi }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <hr>

            <div class="row text-muted small">
                <div class="col-md-6">
                    Dibuat pada:
                    <strong>{{ $activity->created_at->format('d M Y H:i') }}</strong>
                </div>
                <div class="col-md-6 text-end">
                    Terakhir diperbarui:
                    <strong>{{ $activity->updated_at->format('d M Y H:i') }}</strong>
                </div>
            </div>

        </div>
    </div>
</div>
