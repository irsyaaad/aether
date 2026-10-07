@extends('template.root')
@section('page-name', 'absensi')
@section('title-page', 'Ambil Absensi')
@section('style')
    <style>
        .camera-box {
            position: relative;
            width: 100%;
            height: 320px;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
        }

        .camera-box video,
        .camera-box canvas {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-box {
            font-size: 14px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 12px;
        }
    </style>
@endsection
@section('content')
    <div class="container d-flex justify-content-center mt-4">
        <div class="card shadow-lg" style="max-width:420px;width:100%;border-radius:20px;">
            <div class="card-body text-center">

                <h4 class="fw-bold mb-2">📸 Absensi Karyawan</h4>
                <p class="text-muted mb-3">Ambil foto & pastikan GPS aktif</p>

                {{-- CAMERA --}}
                <div class="camera-box mb-3">
                    <video id="video" autoplay playsinline></video>
                    <canvas id="canvas" class="d-none"></canvas>
                </div>

                {{-- INFO --}}
                <div class="info-box mb-3">
                    <div><strong>🕒 Waktu:</strong> <span id="time">-</span></div>
                    <div><strong>📍 GPS:</strong> <span id="gps-status" class="text-danger">Belum aktif</span></div>
                    <div><strong>🎯 Akurasi:</strong> <span id="accuracy">-</span> m</div>
                </div>

                {{-- BUTTONS --}}
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="capturePhoto()">
                        📷 Ambil Foto
                    </button>

                    <button type="button" class="btn btn-success" onclick="submitAbsen('masuk')">
                        ✅ Absen Masuk
                    </button>

                    <button type="button" class="btn btn-danger" onclick="submitAbsen('pulang')">
                        🚪 Absen Pulang
                    </button>
                </div>

            </div>
        </div>
    </div>
@section('script')
    <script>
        let video = document.getElementById('video');
        let canvas = document.getElementById('canvas');
        let photoBlob = null;
        let gpsData = {};

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user'
            },
            audio: false
        }).then(stream => {
            video.srcObject = stream;
        });

        // JAM REALTIME
        setInterval(() => {
            document.getElementById('time').innerText =
                new Date().toLocaleTimeString('id-ID');
        }, 1000);

        // GPS
        navigator.geolocation.getCurrentPosition(pos => {
            gpsData = pos.coords;
            document.getElementById('gps-status').innerHTML =
                '<span class="text-success">Aktif</span>';
            document.getElementById('accuracy').innerText =
                Math.round(pos.coords.accuracy);
        }, () => {
            alert('Aktifkan GPS untuk absensi');
        }, {
            enableHighAccuracy: true
        });

        // AMBIL FOTO
        function capturePhoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob(blob => {
                photoBlob = blob;
                alert('Foto berhasil diambil');
            }, 'image/jpeg', 0.8);
        }

        // SUBMIT ABSENSI
        function submitAbsen(type) {
            if (!photoBlob) {
                alert('Ambil foto dulu');
                return;
            }

            let formData = new FormData();
            formData.append('type', type);
            formData.append('photo', photoBlob, 'absen.jpg');
            formData.append('latitude', gpsData.latitude);
            formData.append('longitude', gpsData.longitude);
            formData.append('accuracy', gpsData.accuracy);

            fetch("{{ route('absensi.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json' // ⬅️ WAJIB
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        // ⛔ ERROR dari backend
                        throw data;
                    }

                    // ✅ SUCCESS
                    alert(data.message);
                    window.location.href = "{{ route('absensi.index') }}";
                })
                .catch(err => {
                    // 🔴 TAMPILKAN PESAN ERROR ASLI
                    if (err.message) {
                        alert(err.message);
                    } else if (err.errors) {
                        // Validasi Laravel
                        let msg = Object.values(err.errors).flat().join('\n');
                        alert(msg);
                    } else {
                        alert('Terjadi kesalahan saat absensi');
                    }
                });
        }
    </script>

@endsection
@endsection
