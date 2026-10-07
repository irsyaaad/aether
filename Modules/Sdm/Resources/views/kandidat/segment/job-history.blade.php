<div class="row g-3 mt-3">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Lokasi Kerja</th>
                <th>Status Kerja</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Atasan</th>
                <th>Shift</th>
                <th>Catatan</th>
            </tr>
        </thead>

        <tbody>
            @forelse($karyawan->job_history as $index => $job)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>

                    <td>
                        <strong>{{ $job->jabatan }}</strong>
                    </td>

                    <td>{{ $job->divisi }}</td>

                    <td>{{ $job->lokasi_kerja }}</td>

                    <td class="text-center">
                        <span
                            class="badge
                            @if ($job->status_kerja == 'Tetap') bg-success
                            @elseif($job->status_kerja == 'Kontrak') bg-warning
                            @else bg-secondary @endif
                        ">
                            {{ $job->status_kerja }}
                        </span>
                    </td>

                    <td class="text-center">
                        {{ date('d M Y', strtotime($job->tanggal_mulai)) }}
                    </td>

                    <td class="text-center">
                        {{ $job->tanggal_selesai ? date('d M Y', strtotime($job->tanggal_selesai)) : '-' }}
                    </td>

                    <td>{{ $job->atasan_langsung }}</td>

                    <td class="text-center">{{ $job->shift_kerja }}</td>

                    <td>
                        <small>{{ $job->catatan ?? '-' }}</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i>
                        Belum ada riwayat pekerjaan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
