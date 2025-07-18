@extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <div class="page-inner">

        <div class="row">
            <div class="col-12">
                <div class="card card-stats card-round">
                    <div class="card-body text-center area-cetak">
                        <h3>QR Presensi Hari Ini</h3>
                        <p>Tempel QR ini di sekolah. Berlaku hanya hari ini.</p>

                        <div class="text-center">
                            <div id="qrCode" style="display: inline-block;"></div>
                            <p class="mt-2">QR ini akan berubah setiap 30 detik</p>
                        </div>

                        {{--  <button class="btn btn-primary d-print-none" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak QR
                        </button>  --}}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('myscript')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .area-cetak, .area-cetak * {
        visibility: visible;
    }
    .area-cetak {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        text-align: center;
    }
    .d-print-none {
        display: none !important;
    }
}
</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let kodeQRValid = '';

function ambilQRCode() {
    fetch('/absensi/qr-terbaru')
        .then(res => res.json())
        .then(data => {
            if (data.aktif && data.kode) {
                kodeQRValid = data.kode;

                document.getElementById("qrCode").innerHTML = "";
                new QRCode(document.getElementById("qrCode"), {
                    text: kodeQRValid,
                    width: 250,
                    height: 250,
                });
            } else {
                document.getElementById("qrCode").innerHTML = "<p style='color:red; font-weight:bold;'>QR tidak tersedia. " + (data.pesan ?? "") + "</p>";
            }
        }).catch(err => {
            console.error("Gagal mengambil QR:", err);
            document.getElementById("qrCode").innerHTML = "<p style='color:red;'>Gagal memuat QR</p>";
        });
}


ambilQRCode();
setInterval(ambilQRCode, 30000);
</script>


@endpush
