@extends('template.root')
@section('page-name', Request::segment(1))
@section('title-page', 'Buat Karyawan')
@section('content')
    <div>
        <ul class="nav nav-tabs" id="icon-tab" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link txt-secondary active" id="icon-home-tab"
                    data-bs-toggle="tab" href="#icon-home" role="tab" aria-controls="icon-home" aria-selected="true"> <i
                        class="icofont icofont-man-in-glasses"></i>Data Karyawan</a></li>
            <li class="nav-item" role="presentation"><a class="nav-link txt-secondary" id="data-arsip-tab"
                    data-bs-toggle="tab" href="#data-arsip" role="tab" aria-controls="contact-icon"
                    aria-selected="false" tabindex="-1"><i class="fa-solid fa-folder-open"></i>Dokumen Karyawan</a></li>
            <li class="nav-item" role="presentation"><a class="nav-link txt-secondary" id="data-bap-tab"
                    data-bs-toggle="tab" href="#data-bap" role="tab" aria-controls="contact-icon" aria-selected="false"
                    tabindex="-1"><i class="fa-solid fa-camera"></i>Job History</a></li>
        </ul>
        <div class="tab-content" id="icon-tabContent">
            <div class="tab-pane fade active show" id="icon-home" role="tabpanel" aria-labelledby="icon-home-tab">
                @include('sdm::karyawan.segment.informasi')
            </div>
            <div class="tab-pane fade" id="data-arsip" role="tabpanel" aria-labelledby="data-arsip-tab">
                @include('sdm::karyawan.segment.dokumen')
            </div>
            <div class="tab-pane fade" id="data-bap" role="tabpanel" aria-labelledby="data-bap-tab">
                @include('sdm::karyawan.segment.job-history')
            </div>
        </div>
    </div>
@section('script')
    <script>
        let rotation = 0;

        function rotateDocument(direction) {
            // Perubahan arah rotasi
            rotation += direction === 'right' ? 90 : -90;

            const iframe = document.getElementById('pdfFrame');
            const img = document.getElementById('imageView');

            // Terapkan rotasi
            if (iframe.style.display === 'block') {
                iframe.style.transform = `rotate(${rotation}deg)`;
            }

            if (img.style.display === 'block') {
                img.style.transform = `rotate(${rotation}deg)`;
            }
        }

        // Reset rotasi saat modal dibuka
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('pdfModal');
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const url = button.getAttribute('data-pdf-url');
                const type = button.getAttribute('data-type');

                const iframe = document.getElementById('pdfFrame');
                const img = document.getElementById('imageView');

                // Reset
                rotation = 0;
                iframe.style.transform = img.style.transform = 'rotate(0deg)';
                iframe.style.display = img.style.display = 'none';
                iframe.src = img.src = '';

                if (type === 'pdf') {
                    iframe.src = url;
                    iframe.style.display = 'block';
                } else {
                    img.src = url;
                    img.style.display = 'block';
                }
            });
        });
    </script>
@endsection
@endsection
