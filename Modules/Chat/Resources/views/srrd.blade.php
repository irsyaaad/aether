<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{ asset('images/logo_persada_duta.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('images/logo_persada_duta.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_persada_duta.png') }}">
    <title>Cetak Konsep Surat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link
        href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/vendors/font-awesome.css') }}">
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin-left: 3cm;
                /* size: landscape; */
            }
        }

        body {
            font-family: sans-serif !important;
            /* line-height: 15px; */
            font-size: 10px;
            /* font-weight: bold; */
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .tr-bold {
            font-weight: bold !important;
        }

        .container {
            margin-left: 50px;
            margin-right: 50px;
            font-size: 12pt;
            font-family: 'Times New Roman', Times, serif
        }

        .table1 {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table1 th {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table1 td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px;
        }

        .text-success {
            text-align: center;
            color: green;
            font-weight: bold;
        }

        .text-danger {
            text-align: center;
            color: red;
            font-weight: bold;
        }

        .heading {
            text-align: center;
            font-size: 14px;
            border: 0px
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td>
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/cuba/images/logo/logo-surabaya.png'))) }}"
                    style="height: 60px">
            </td>
            <td class="text-center">
                <span style="font-size: 14px"><b>PEMERINTAH KOTA SURABAYA
                    </b></span><br>
                <span style="font-size: 14px"><b>Surat Setoran Retribusi Daerah</b></span><br>
                <span style="font-size: 14px"><b>No. Bukti : <u>40636/ST/SRRD-UPTSA-T/2024</u></b></span><br>
            </td>
        </tr>
    </table>
    <hr>
    <div>
        <table width="100%">
            <tr>
                <td style="vertical-align: top">a.</td>
                <td colspan="3">Bendahara Penerimaan / Bendahara Penerimaan Pembantu telah menerima uang sebesar
                    <br><b><span style="font-size: 14px">Rp.
                            9.894.342,00</span></b>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top" rowspan="9">b.</td>
                <td>dari Nama</td>
                <td width="5px">:</td>
                <td>NUR AINI WULANDARI</td>
            </tr>
            <tr>
                <td>Nama PT</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>SIDOTOPO LOR 86</td>
            </tr>
            <tr>
                <td>Lokasi Persil</td>
                <td>:</td>
                <td>JL. SIDORAME NO.36 [ID Persil : 158224]</td>
            </tr>
            <tr>
                <td>Peruntukan</td>
                <td>:</td>
                <td>PERDAGANGAN DAN JASA</td>
            </tr>
            <tr>
                <td>Penggunaan</td>
                <td>:</td>
                <td>Rumah Usaha</td>
            </tr>
            <tr>
                <td>Luas Tanah</td>
                <td>:</td>
                <td>42.48 m</td>
            </tr>
            <tr>
                <td>NJOP</td>
                <td>:</td>
                <td>Rp. 3.410.214,17</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>I (Lebar jalan lebih dari 15 meter)</td>
            </tr>
            <tr>
                <td>c.</td>
                <td colspan="3">Sebagai Pembayaran <b>Retribusi Ijin Pemakaian Tanah</b> [Retribusi Pemutihan Ijin
                    Pemakaian Tanah]</td>
            </tr>
            <tr>
                <td style="vertical-align: top" rowspan="2">d.</td>
                <td>No. Pendaftaran</td>
                <td>:</td>
                <td>233827/ST.TAHUN/UPTSA-T/2024 (Tgl. Daftar : 24 Oktober 2024)</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td>Dengan rincian Penerimaan untuk periode <b>TAHUN 2024</b> sebagai berikut</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3">
                    <table class="table1" width="100%">
                        <thead>
                            <th colspan="6" style="height: 40px;">Kode Rekening</th>
                            <th colspan="2">Uraian Retribusi</th>
                            <th>Ket</th>
                            <th>Jumlah (Rp.)</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>4</td>
                                <td>1</td>
                                <td>02</td>
                                <td>02</td>
                                <td>01</td>
                                <td>0002</td>
                                <td>Retribusi Penyewaan Tanah</td>
                                <td>Rp. 1.100.981,00</td>
                                <td>2024</td>
                                <td>1.100.981,00</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Tunggakan Retribusi Penyewaan Tanah</td>
                                <td>Rp. 0,00</td>
                                <td></td>
                                <td>0,00</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Retribusi Peresmian / Pemutihan</td>
                                <td>Rp. 8.793.361,00</td>
                                <td></td>
                                <td>8.793.361,00</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>1</td>
                                <td>02</td>
                                <td>02</td>
                                <td>01</td>
                                <td>0001</td>
                                <td>Pendapatan Denda Retrubusi Pemakaian Kekayaan Daerah</td>
                                <td>Rp. 0,00</td>
                                <td>2024</td>
                                <td>0,00</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>e.</td>
                <td>Tanggal diterima Uang</td>
                <td>:</td>
                <td>25 Oktober 2024</td>
            </tr>
            <tr>
                <td>f.</td>
                <td>Pengambilan Surat Ijin</td>
                <td>:</td>
                <td>TIMUR</td>
            </tr>
        </table>
        <table width="100%" style="margin-top: 50px">
            <tr>
                <td class="text-center" width="75%"></td>
                <td class="text-center" width="25%">
                    <div class="text-center" style="line-height: 4px; margin-left : 1px">
                        <p></p>
                        <p>Mengetahui</p>
                        <p>Bendahara Penerima /</p>
                        <p>Bendahara Pembantu Penerima</p>
                        <p style="height: 70px"></p>
                        <p><u><b>TRI MURDIANI</b></u></p>
                        <p>NIP. 197302182006042008</p>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <u>Keterangan : </u>
        <table width="100%">
            <tr>
                <td>
                    < 1995</td>
                <td>:</td>
                <td>PERDA 3/1987</td>

                <td>2000 - 2003</td>
                <td>:</td>
                <td>PERDA 16/1999</td>

                <td>2013 - 2016</td>
                <td>:</td>
                <td>PERDA 2/2013</td>
            </tr>
            <tr>
                <td>1995 - 1997</td>
                <td>:</td>
                <td>PERDA 12/1994</td>

                <td>2003 - 2010</td>
                <td>:</td>
                <td>PERDA 21/2003</td>

                <td>2016 - 2017</td>
                <td>:</td>
                <td>PERWALI 42/2016</td>
            </tr>
            <tr>
                <td>1997 - 2000</td>
                <td>:</td>
                <td>PERDA 1/1997</td>

                <td>2010 - 2013</td>
                <td>:</td>
                <td>PERDA 13/2010</td>

            </tr>
        </table>
    </div>
</body>

</html>
