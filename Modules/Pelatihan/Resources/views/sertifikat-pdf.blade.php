<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $sertifikat->nomor_sertifikat }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
            color: #1a202c;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        .cert-outer-border {
            border: 6px double #102654;
            padding: 8px;
            background: #ffffff;
            height: 94%;
            box-sizing: border-box;
        }
        .cert-inner-border {
            border: 2px solid #C5A059;
            padding: 25px 35px;
            height: 92%;
            box-sizing: border-box;
            text-align: center;
            position: relative;
        }
        .cert-header-tag {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #C5A059;
            margin-bottom: 5px;
        }
        .cert-institution {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #102654;
            text-transform: uppercase;
            margin: 0 0 15px 0;
        }
        .cert-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 32px;
            font-weight: bold;
            color: #102654;
            margin: 10px 0 5px 0;
            letter-spacing: 1px;
        }
        .cert-subtitle {
            font-size: 13px;
            color: #718096;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0 0 20px 0;
        }
        .cert-number {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #4a5568;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .cert-presented-to {
            font-size: 12px;
            font-style: italic;
            color: #718096;
            margin-bottom: 8px;
        }
        .cert-recipient-name {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 28px;
            font-weight: bold;
            color: #1768B5;
            text-decoration: underline;
            text-decoration-color: #C5A059;
            margin: 0 0 5px 0;
        }
        .cert-affiliation {
            font-size: 12px;
            color: #4a5568;
            margin-bottom: 15px;
        }
        .cert-description {
            font-size: 12px;
            line-height: 1.5;
            color: #2d3748;
            max-width: 80%;
            margin: 0 auto 12px auto;
        }
        .cert-course-name {
            font-size: 17px;
            font-weight: bold;
            color: #102654;
            margin: 5px 0 25px 0;
        }
        .cert-footer-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .cert-footer-table td {
            vertical-align: bottom;
            text-align: center;
            width: 33.3%;
        }
        .cert-sign-title {
            font-size: 11px;
            font-weight: bold;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 45px;
        }
        .cert-sign-name {
            font-size: 12px;
            font-weight: bold;
            color: #102654;
            border-top: 1px solid #C5A059;
            padding-top: 4px;
            display: inline-block;
            min-width: 170px;
        }
        .cert-sign-role {
            font-size: 10px;
            color: #718096;
            margin-top: 2px;
        }
        .badge-verified {
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid #38a169;
            background-color: #f0fff4;
            color: #276749;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="cert-outer-border">
        <div class="cert-inner-border">
            <div class="cert-header-tag">AETHER ACADEMIC &amp; RESEARCH EXCELLENCE</div>
            <h1 class="cert-institution">AETHER RESEARCH LAB INDONESIA</h1>
            
            <div class="cert-title">SERTIFIKAT KELULUSAN</div>
            <div class="cert-subtitle">Certificate of Completion</div>
            <div class="cert-number">No. Verifikasi: {{ $sertifikat->nomor_sertifikat }}</div>

            <div class="cert-presented-to">Diberikan dengan penuh kehormatan kepada:</div>
            <div class="cert-recipient-name">
                {{ $sertifikat->nama_pemesan ?: ($sertifikat->user->nm_user ?? 'Peserta Pelatihan') }}
            </div>
            @if(!empty($sertifikat->universitas))
                <div class="cert-affiliation">{{ $sertifikat->universitas }}</div>
            @else
                <div class="cert-affiliation">Peserta Program Pelatihan &amp; Workshop Riset Aether</div>
            @endif

            <div class="cert-description">
                Telah berhasil menyelesaikan secara tuntas seluruh silabus, materi metodologi, evaluasi praktik olah data, dan memenuhi standar kompetensi pada kelas:
            </div>
            <div class="cert-course-name">
                &ldquo;{{ $sertifikat->judul_kelas }}&rdquo;
            </div>

            <table class="cert-footer-table">
                <tr>
                    <td>
                        <div class="cert-sign-title">Instruktur Utama</div>
                        <div class="cert-sign-name">{{ $sertifikat->instruktur ?: 'Instruktur Ahli Aether' }}</div>
                        <div class="cert-sign-role">Lead Research Specialist</div>
                    </td>
                    <td>
                        <div style="margin-bottom: 5px;">
                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(65)->color(16, 38, 84)->generate($sertifikat->verify_url) !!}
                        </div>
                        <div style="font-size: 8px; color: #1768B5; letter-spacing: 0.5px; font-weight: bold; text-transform: uppercase;">
                            Pindai untuk Verifikasi
                        </div>
                        <div style="font-size: 8px; color: #718096; font-family: monospace;">
                            {{ $sertifikat->nomor_registrasi }}
                        </div>
                    </td>
                    <td>
                        <div class="cert-sign-title">Direktur Pelatihan &amp; Riset</div>
                        <div class="cert-sign-name">Dr. M. Irsyadul Anam, M.Kom.</div>
                        <div class="cert-sign-role">Director of Aether Research Lab</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
