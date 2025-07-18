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

    <h1>Scan QR untuk Presensi</h1>
    <p class="subtitle">QR akan berubah otomatis setiap 30 detik</p>

    <div id="qrCode"></div>
   <p id="kodeTampil" style="display:none;"></p>


    <div class="info">Halaman ini disiapkan untuk keperluan presensi di sekolah.</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
    let qrDiv = document.getElementById("qrCode");
    let kodeTampil = document.getElementById("kodeTampil");

    function ambilQRCode() {
    fetch('/absensi/qr-terbaru')
        .then(res => res.json())
        .then(data => {
            if (data.aktif && data.kode) {
                qrDiv.innerHTML = "";
                new QRCode(qrDiv, {
                    text: data.kode,
                    width: 300,
                    height: 300
                });
            } else {
                qrDiv.innerHTML = "<p style='color:red; font-weight:bold;'>" + (data.pesan ?? "Presensi belum/telah ditutup.") + "</p>";
            }
        }).catch(err => {
            console.error("Gagal mengambil QR:", err);
            qrDiv.innerHTML = "<p style='color:red'>Gagal memuat QR</p>";
        });
}


    ambilQRCode();
    setInterval(ambilQRCode, 30000);
    </script>

</body>
</html>
