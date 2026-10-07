<div class="row g-3 mt-3">

    <!-- KTP -->
    <table class="table table-bordered table-striped align-middle shadow-sm">
        <thead class="table-primary text-center">
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 25%">Nama Dokumen</th>
                <th style="width: 40%">Preview</th>
                <th style="width: 30%">Aksi</th>
            </tr>
        </thead>
        <tbody>

            @php
                $documents = [
                    ['label' => 'Dokumen KTP', 'file' => $karyawan->dokumen_ktp],
                    ['label' => 'Dokumen KK', 'file' => $karyawan->dokumen_kk],
                    ['label' => 'Dokumen SIM', 'file' => $karyawan->dokumen_sim],
                    ['label' => 'Dokumen NPWP', 'file' => $karyawan->dokumen_npwp],
                    ['label' => 'Dokumen Ijazah', 'file' => $karyawan->dokumen_ijazah],
                    ['label' => 'Foto Diri', 'file' => $karyawan->foto_diri],
                ];
            @endphp

            @foreach ($documents as $i => $doc)
                @if ($doc['file'])
                    @php
                        $url = route('karyawan.get-doc', ['pdf' => $doc['file']]);
                        $isPdf = Str::endsWith($doc['file'], '.pdf');
                    @endphp

                    <tr>
                        <td class="text-center fw-bold">{{ $i + 1 }}</td>
                        <td>{{ $doc['label'] }}</td>

                        <!-- Preview -->
                        <td class="text-center">
                            @if ($isPdf)
                                <i class="fa-solid fa-file-pdf fa-3x text-danger"></i>
                                <div class="small text-muted mt-1">PDF Document</div>
                            @else
                                <img src="{{ $url }}" class="img-thumbnail"
                                    style="height: 100px; object-fit: cover;">
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal"
                                data-pdf-url="{{ $url }}" data-type="{{ $isPdf ? 'pdf' : 'image' }}">
                                <i class="fa-solid fa-eye"></i> Lihat
                            </button>

                            <a href="{{ $url }}" download class="btn btn-success btn-sm">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                @endif
            @endforeach

        </tbody>
    </table>

</div>
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Preview Dokumen</h5>
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center position-relative">
                <!-- Tombol rotate -->
                <div class="position-absolute top-0 start-0 p-3" style="z-index: 10;">
                    <button onclick="rotateDocument('left')" class="btn btn-outline-secondary me-2">🔄 Putar
                        Kiri</button>
                    <button onclick="rotateDocument('right')" class="btn btn-outline-secondary">🔁 Putar
                        Kanan</button>
                </div>

                <iframe id="pdfFrame"
                    style="width: 100%; height: 100vh; display: none; transition: transform 0.3s ease;"
                    frameborder="0"></iframe>

                <img id="imageView"
                    style="max-width: 100%; max-height: 100vh; display: none; transition: transform 0.3s ease;" />
            </div>
        </div>
    </div>
</div>
