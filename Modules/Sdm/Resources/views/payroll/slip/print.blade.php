<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $slip->nomor_slip }}</title>
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

        .text-right {
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

        .data td,
        .data th {
            border: 1px solid #000;
        }

        .info-table td {
            padding: 3px 5px;
        }

        .section-title {
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td>
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo_persada_duta.png'))) }}"
                    style="height: 70px; object-fit: contain;">
            </td>
            <td style="text-align: center">
                <span style="font-size: 18px"><b>PT. PUTERA ADHI PERKASA</b></span><br>
                <span><b>PAP Hardware</b></span><br>
                <span>website: www.paphardware.com</span>
            </td>
        </tr>
    </table>
    <div class="line"></div>

    <div class="header">
        <h3>SLIP GAJI KARYAWAN</h3>
        <p>No. Slip: {{ $slip->nomor_slip }}</p>
    </div>

    <div class="info">
        <table class="info-table">
            <tr>
                <td width="15%"><strong>Nama Karyawan</strong></td>
                <td width="2%">:</td>
                <td width="33%">{{ $slip->karyawan->nama_lengkap ?? '-' }}</td>
                <td width="15%"><strong>Periode</strong></td>
                <td width="2%">:</td>
                <td width="33%">{{ $slip->payrollPeriod->nama_periode ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>NIK</strong></td>
                <td>:</td>
                <td>{{ $slip->karyawan->nik_ktp ?? '-' }}</td>
                <td><strong>Tanggal Gajian</strong></td>
                <td>:</td>
                <td>{{ $slip->payrollPeriod ? \Carbon\Carbon::parse($slip->payrollPeriod->tanggal_gajian)->format('d F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td><strong>Jabatan</strong></td>
                <td>:</td>
                <td>{{ $slip->karyawan->jabatan ?? '-' }}</td>
                <td><strong>Status</strong></td>
                <td>:</td>
                <td>
                    @if ($slip->status === 'draft')
                        DRAFT
                    @elseif($slip->status === 'approved')
                        DISETUJUI
                    @elseif($slip->status === 'paid')
                        DIBAYAR
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Divisi</strong></td>
                <td>:</td>
                <td>{{ $slip->karyawan->divisi ?? '-' }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="section-title">INFORMASI KEHADIRAN</div>
    <table class="data">
        <tr>
            <td width="25%" class="text-center"><strong>Hari Kerja</strong></td>
            <td width="25%" class="text-center"><strong>Hari Hadir</strong></td>
            <td width="25%" class="text-center"><strong>Hari Alpha</strong></td>
            <td width="25%" class="text-center"><strong>Jam Lembur</strong></td>
        </tr>
        <tr>
            <td class="text-center">{{ $slip->hari_kerja }} hari</td>
            <td class="text-center">{{ $slip->hari_hadir }} hari</td>
            <td class="text-center">{{ $slip->hari_alpha }} hari</td>
            <td class="text-center">{{ number_format($slip->jam_lembur, 1) }} jam</td>
        </tr>
    </table>

    <div class="section-title">RINCIAN GAJI</div>
    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Komponen</th>
                <th>Jenis</th>
                <th class="text-right">Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Gaji Pokok</td>
                <td class="text-center">Pokok</td>
                <td class="text-right">Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
                <td>-</td>
            </tr>
            @php $no = 2; @endphp
            @foreach ($slip->details as $detail)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $detail->component->nama_komponen ?? '-' }}</td>
                    <td class="text-center">
                        @if ($detail->component->jenis === 'allowance')
                            Tunjangan
                        @else
                            Potongan
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($detail->component->jenis === 'allowance')
                            Rp {{ number_format($detail->nilai, 0, ',', '.') }}
                        @else
                            (Rp {{ number_format($detail->nilai, 0, ',', '.') }})
                        @endif
                    </td>
                    <td>{{ $detail->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">RINGKASAN GAJI</div>
    <table class="data">
        <tr>
            <td width="70%"><strong>Gaji Pokok</strong></td>
            <td width="30%" class="text-right">Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Tunjangan</strong></td>
            <td class="text-right">Rp {{ number_format($slip->total_tunjangan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Gaji Kotor</strong></td>
            <td class="text-right">Rp {{ number_format($slip->gaji_kotor, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Potongan</strong></td>
            <td class="text-right">(Rp {{ number_format($slip->total_potongan, 0, ',', '.') }})</td>
        </tr>
        <tr class="total-row">
            <td><strong>GAJI BERSIH</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    @if ($slip->catatan)
        <div class="section-title">CATATAN</div>
        <table class="data">
            <tr>
                <td>{{ $slip->catatan }}</td>
            </tr>
        </table>
    @endif

    <br><br>
    <table class="signature">
        <tr>
            <td width="33%">
                <strong>Karyawan</strong><br><br><br><br>
                <u>{{ $slip->karyawan->nama_lengkap ?? '-' }}</u>
            </td>
            <td width="33%">
                <strong>HRD</strong><br><br><br><br>
                <u>
                    @if ($slip->status === 'approved' || $slip->status === 'paid')
                        {{ $slip->approver->nm_user ?? '-' }}
                    @else
                        (........................)
                    @endif
                </u>
            </td>
            <td width="33%">
                <strong>Finance</strong><br><br><br><br>
                <u>(........................)</u>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; font-size: 9px; text-align: center; color: #666;">
        <p>Slip gaji ini dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        @if ($slip->status === 'paid' && $slip->tanggal_bayar)
            <p>Tanggal pembayaran: {{ \Carbon\Carbon::parse($slip->tanggal_bayar)->format('d F Y') }}</p>
        @endif
        <p>Dokumen ini sah dan diproses secara elektronik</p>
    </div>
</body>

</html>
