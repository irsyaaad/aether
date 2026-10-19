<!DOCTYPE html>
<html lang="id">

<head>
    <script src="https://cdn.jsdelivr.net/npm/easyqrcodejs@4.4.10/dist/easy.qrcode.min.js"></script>
</head>
<style>
    table {
        border-collapse: collapse;
        border: 1px solid black;
        outline: 4px double black;
        /* Memberikan efek garis ganda dengan jarak */
        outline-offset: -4px;
        /* Mengatur jarak antar garis */
    }

    th,
    td {
        /* border: 2px solid black; */
        padding: 3px;
        text-align: left;
    }
</style>

<body>
    <table>
        <tr>
            <td>
                <div id="qrcode"></div>
            </td>
            <td>Surat ini Ditandatangani Elektronik Oleh :
                <br>
                KEPALA BADAN,
                <br><br><br>
                <b>Dra. Wiwiek Widayati</b>
                <br>
                Pembina Utama Muda / IV/c
                <br>
                NIP. 196705161993022001
            </td>
            <td>&emsp;&emsp;</td>
        </tr>
    </table>

    <script>
        var options = {
            text: "https://suket-bpkad.surabaya.go.id/",
            width: 125,
            height: 125,
            logo: "{{ asset('images/logo_aether.png') }}", // Ganti dengan URL logo
            logoWidth: 20,
            logoHeight: 20,
            quietZone: 10
        };

        new QRCode(document.getElementById("qrcode"), options);
    </script>

    <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=https%3A%2F%2Fcontoh.com&choe=UTF-8" alt="">
</body>

</html>
