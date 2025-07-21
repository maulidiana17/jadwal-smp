<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>QR Presensi Hari Ini</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Auto-refresh untuk backup jika JS gagal -->
    <meta http-equiv="refresh" content="300">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            background: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: #333;
            text-align: center;
        }

        h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        p.subtitle {
            font-size: 1rem;
            margin-bottom: 2rem;
            color: #666;
        }

        #qrCode {
            margin-bottom: 1rem;
        }

        #kodeTampil {
            font-size: 0.9rem;
            color: #555;
        }

        .info {
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #999;
        }

        @media print {
            .info {
                display: none;
            }
        }
    </style>
</head>
<body>


<div class="container text-center mt-5">
    <h2>QR Presensi Live</h2>

    @if ($qr)
        <div id="qrCodeDisplay" class="mt-3"></div>
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
        <script>
            new QRCode(document.getElementById("qrCodeDisplay"), {
                text: "{{ $qr->kode_qr }}",
                width: 300,
                height: 300
            });
        </script>
    @else
        <p>QR belum tersedia atau sudah kadaluarsa.</p>
    @endif
</div>


</body>
</html>
