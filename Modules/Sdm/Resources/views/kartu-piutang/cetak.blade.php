<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kartu Piutang - {{ $karyawan->nama_lengkap }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 0;
            font-size: 15px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 5px;
        }

        th {
            border-bottom: 1px solid #000;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .signature td {
            height: 60px;
            vertical-align: bottom;
            text-align: center;
        }

        .data {
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .data td {
            border: 1px solid #000;
        }

        .info-table td {
            padding: 2px 5px;
        }

        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td width="120">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo_persada_duta.png'))) }}"
                    style="height: 65px; object-fit: contain;">
            </td>
            <td style="text-align: center">
                <span style="font-size: 18px; text-transform: uppercase;"><b>PT. PUTERA ADHI PERKASA</b></span><br>
                <span><b>PAP Hardware</b></span><br>
                <span>website: www.paphardware.com</span>
            </td>
        </tr>
    </table>
    <div class="line"></div>
    <div class="header">
        <h3>KARTU PIUTANG KARYAWAN</h3>
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="info" style="margin-bottom: 15px;">
        <table class="info-table" style="width: 50%;">
            <tr>
                <td width="100"><strong>Nama Karyawan</strong></td>
                <td>: {{ $karyawan->nama_lengkap }}</td>
            </tr>
            <tr>
                <td><strong>Jabatan</strong></td>
                <td>: {{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td><strong>Divisi</strong></td>
                <td>: {{ $karyawan->divisi }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th class="text-center">No</th>
                <th class="text-center">Tanggal</th>
                <th>Nomor Dokumen</th>
                <th>Keterangan</th>
                <th class="text-end">Debet (Pinjam)</th>
                <th class="text-end">Kredit (Bayar)</th>
                <th class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $saldo = 0; @endphp
            @foreach ($history as $index => $item)
                @php $saldo += $item->debet - $item->kredit; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->nomor }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-end">
                        {{ $item->debet > 0 ? 'Rp ' . number_format($item->debet, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end">
                        {{ $item->kredit > 0 ? 'Rp ' . number_format($item->kredit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end fw-bold">
                        Rp {{ number_format($saldo, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #f2f2f2;">
                <td colspan="4" class="text-end">TOTAL</td>
                <td class="text-end">Rp {{ number_format($history->sum('debet'), 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($history->sum('kredit'), 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px;">
        <table class="signature">
            <tr>
                <td width="33%">
                    Dibuat Oleh,<br><br><br><br>
                    (................................)
                </td>
                <td width="33%">
                    Diketahui Oleh,<br><br><br><br>
                    (................................)
                </td>
                <td width="33%">
                    Penerima,<br><br><br><br>
                    <strong>{{ $karyawan->nama_lengkap }}</strong>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
