@section('style')
    <style>
        /* --- Container dan Kartu ID --- */

        .idcard-container-wrapper {
            width: 100%;
            max-width: 350px;
            /* Batasan lebar di layar */
            margin-bottom: 20px;
        }

        .idcard-main {
            width: 320px;
            height: 500px;
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* --- Desain Latar Belakang Gelombang --- */

        .idcard-header-design {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* 1. Lapisan Putih Melengkung (Gelombang Tengah) */
        .idcard-header-design::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f7f7f7;
            border-radius: 0 0 100% 0 / 0 0 45% 0;
            transform: translateY(-20px) scaleX(1.1);
            z-index: 2;
        }

        /* 2. Lapisan Biru-Cyan Besar (Gelombang Bawah) */
        .idcard-header-design::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #48cae4, #0096c7);
            border-radius: 0 0 100% 0 / 0 0 50% 0;
            transform: translateY(-50px) scaleX(1.15);
            z-index: 1;
        }

        /* --- Logo Area (Mengandung Gelombang Biru Tua) --- */
        .idcard-logo-area {
            position: relative;
            z-index: 5;
            padding: 15px;
        }

        .idcard-logo-area::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 120%;
            height: 250px;
            background: linear-gradient(135deg, #023e8a, #0077b6);
            border-radius: 0 0 100% 0 / 0 0 40% 0;
            opacity: 0.9;
            transform: rotate(5deg);
            z-index: 3;
        }

        .idcard-logo-content {
            text-align: right;
            color: #ffffff;
            position: relative;
            z-index: 11;
        }

        .idcard-logo-name {
            font-weight: 700;
            font-size: 0.8rem;
            line-height: 1;
            margin-bottom: 2px;
        }

        .idcard-slogan {
            font-size: 0.6rem;
            line-height: 1;
        }

        .idcard-logo-shape {
            width: 25px;
            height: 25px;
            background: linear-gradient(45deg, #00b4d8, #0077b6);
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
            float: right;
            margin-left: auto;
            margin-bottom: 5px;
            margin-right: 15px;
            position: relative;
        }

        /* --- Foto Profil --- */
        .idcard-photo-container {
            position: absolute;
            top: 15%;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #ffffff;
            padding: 3px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .idcard-profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        /* --- Detail Karyawan --- */
        .idcard-employee-details {
            position: relative;
            z-index: 6;
            text-align: center;
            margin-top: 150px;
            padding-top: 50px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .idcard-name {
            font-size: 1.5rem;
            color: #333;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .idcard-title {
            font-size: 1.1rem;
            color: #0077b6;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .idcard-divider {
            border: none;
            border-top: 2px solid #ccc;
            width: 60%;
            opacity: 1;
            margin: 15px auto;
        }

        .idcard-id-number-group {
            margin-bottom: 20px;
        }

        .idcard-id-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-right: 5px;
        }

        .idcard-id-value {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .idcard-date-info {
            display: flex;
            justify-content: center;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }

        .idcard-join-date {
            margin-right: 20px;
        }

        .idcard-date-value {
            font-weight: 600;
        }

        /* --- QR Code --- */
        .idcard-qr-code-container {
            position: absolute;
            bottom: 20px;
            left: 20px;
        }

        .idcard-qr-code {
            width: 70px;
            height: auto;
        }

        /* --- Tombol Cetak --- */
        .idcard-print-button-container {
            margin-top: 20px;
        }

        .idcard-print-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        /* ------------------------------------------------------------------- */
        /* OPTIMASI UNTUK MEDIA CETAK (@media print) */
        /* ------------------------------------------------------------------- */

        @media print {

            /* Sembunyikan elemen non-kartu */
            body * {
                visibility: hidden;
            }

            /* Tampilkan hanya kartu ID dan kontennya */
            .idcard-main,
            .idcard-main * {
                visibility: visible;
            }

            .idcard-container-wrapper {
                width: auto;
                max-width: none;
            }

            /* Atur ID Card ke ukuran standar cetak (85mm x 54mm) */
            .idcard-main {
                width: 85mm;
                height: 54mm;
                position: absolute;
                top: 0;
                left: 0;
                box-shadow: none !important;
                border: 1px solid #000;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

            /* Sembunyikan tombol cetak */
            .idcard-print-button,
            .idcard-print-button-container {
                display: none;
            }

            /* Penyesuaian ukuran dan posisi elemen internal menggunakan unit mm/rem */

            .idcard-photo-container {
                width: 30mm;
                height: 30mm;
                top: 10mm;
                padding: 1mm;
            }

            .idcard-employee-details {
                margin-top: 35mm;
                padding-top: 1mm;
            }

            .idcard-name {
                font-size: 0.8rem;
                margin-bottom: 0;
            }

            .idcard-title {
                font-size: 0.6rem;
                margin-bottom: 2mm;
            }

            .idcard-divider {
                width: 30mm;
                margin: 1mm auto;
            }

            .idcard-id-label {
                font-size: 0.6rem;
            }

            .idcard-id-value {
                font-size: 0.7rem;
            }

            .idcard-id-number-group {
                margin-bottom: 2mm;
            }

            .idcard-date-info {
                font-size: 0.5rem;
                margin-bottom: 2mm;
            }

            .idcard-join-date {
                margin-right: 5mm;
            }

            .idcard-qr-code-container {
                left: 5mm;
                bottom: 5mm;
            }

            .idcard-qr-code {
                width: 15mm;
            }

            /* Penyesuaian Lapisan Desain untuk Cetak */
            .idcard-header-design::before {
                transform: translateY(-5px) scaleX(1.1);
                border-radius: 0 0 100% 0 / 0 0 50% 0;
            }

            .idcard-header-design::after {
                transform: translateY(-10px) scaleX(1.2);
                border-radius: 0 0 100% 0 / 0 0 55% 0;
            }

            .idcard-logo-area {
                padding: 5px 10px 0 0;
            }

            .idcard-logo-area::before {
                top: -10px;
                left: -10px;
                height: 150px;
            }

            .idcard-logo-name {
                font-size: 0.5rem;
            }

            .idcard-slogan {
                font-size: 0.4rem;
            }
        }
    </style>
@endsection
<div class="col-md-12 text-center mt-3">
    <div class="idcard-container-wrapper">
        <div class="idcard-main">

            <div class="idcard-header-design">
            </div>

            <div class="idcard-logo-area">
                <div class="idcard-logo-content">
                    <div class="idcard-logo-shape"></div>
                    <p class="idcard-logo-name">LOGO NAME</p>
                    <p class="idcard-slogan">Slogan Goes Here</p>
                </div>
            </div>

            <div class="idcard-photo-container">
                <img src="https://i.ibb.co/L5k6yC3/anna-roe.png" class="idcard-profile-photo"
                    alt="Foto Profil Anna Roe">
            </div>

            <div class="idcard-employee-details">
                <h1 class="idcard-name">ANNA ROE</h1>
                <p class="idcard-title">Web Developer</p>

                <hr class="idcard-divider">

                <p class="idcard-id-number-group">
                    <span class="idcard-id-label">ID :</span>
                    <span class="idcard-id-value">01930381085</span>
                </p>

                <div class="idcard-date-info">
                    <p class="idcard-join-date">Join Date : <span class="idcard-date-value">07/21</span></p>
                    <p class="idcard-valid-thru">Valid Thru : <span class="idcard-date-value">09/22</span></p>
                </div>

                <div class="idcard-qr-code-container">
                    <img src="https://i.ibb.co/sK6f453/qr-code.png" alt="QR Code" class="idcard-qr-code">
                </div>
            </div>
        </div>
    </div>
</div>
