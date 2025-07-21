{{--  @extends('layouts.absen')
  @section('header')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <div class="appHeader bg-darkred text-light">
      <div class="left">
          <a href="javascript:;" class="headerButton goBack">
              <ion-icon name="chevron-back-outline"></ion-icon>
          </a>
      </div>
      <div class="pageTitle">Absensi Siswa</div>
      <div class="right"></div>
  </div>
  <style>
      .webcam-camera,
      .webcam-camera video {
        display: block;
        width: 80% !important;
        margin: auto;
        height: auto !important;
        border-radius: 5px;

      }

      #map {
          width: 100%;
          height: 200px;
          max-height: 400px;
      }

      button#presensi {
        width: 90%;
        padding: 12px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 5px auto;
        border-radius: 5px;
      }

      button#presensi i {
        margin-right: 8px;
      }

  </style>

  <!-- Preload Leaflet CSS untuk peta -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>

  @endsection

  @section('content')

  <!-- Section Webcam -->
  <div class="row" style="margin-top: 70px;">
      <div class="col-12">
          <input type="hidden" id="lokasi">
          <div class="webcam-camera"></div>
      </div>
  </div>

  <!-- Section Tombol Presensi -->
  <div class="row mt-2">
      <div class="col-12">
        @if($cek > 0)
        <button id="presensi" class="btn btn-primary btn-danger" action="" method="POST" type="submit">
            <ion-icon name="camera"></ion-icon> Presensi Pulang
        </button>
        @else
        <button id="presensi" class="btn btn-primary btn-block" action="" method="POST" type="submit">
            <ion-icon name="camera"></ion-icon> Presensi Masuk
        </button>
        @endif
      </div>
  </div>

  <!-- Section Peta -->
  <div class="row mt-2">
      <div class="col-12">
          <div id="map"></div>
      </div>
  </div>
  <audio id="notif_masuk">
    <source src="{{ asset('assets/sound/notif_masuk.mp3') }}" type="audio/mpeg">
  </audio>
  <audio id="notif_keluar">
    <source src="{{ asset('assets/sound/notif_keluar.mp3') }}" type="audio/mpeg">
  </audio>
  <audio id="radius_sekolah">
    <source src="{{ asset('assets/sound/radius_sekolah.mp3') }}" type="audio/mpeg">
  </audio>
  @endsection

  @push('myscript')
  <script>

      var notif_masuk = document.getElementById('notif_masuk');
      var notif_keluar = document.getElementById('notif_keluar');
      var radius_sekolah = document.getElementById('radius_sekolah');
      Webcam.set({
          width: window.innerWidth * 0.9,
          height: window.innerHeight * 0.4,
          image_format: 'jpeg',
          jpeg_quality: 80,
      });

      Webcam.attach('.webcam-camera');

      Webcam.on('error', function(err) {
          console.error("Webcam.js Error: ", err);
          alert("Webcam.js Error: " + err.message);
      });

      var lokasi = document.getElementById('lokasi');
      if(navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
      }

      function successCallback(posisi) {
          lokasi.value = posisi.coords.latitude + "," + posisi.coords.longitude;
          var map = L.map('map').setView([posisi.coords.latitude, posisi.coords.longitude], 18);
          var lokasi_sekolah = "{{ $lok_sekolah->lokasi_sekolah }}";
          var lok = lokasi_sekolah.split(",");
          var lat_sekolah = lok[0];
          var long_sekolah = lok[1];
          var radius = "{{ $lok_sekolah->radius }}";

          L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
              maxZoom: 19,
              attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          }).addTo(map);
          var marker = L.marker([posisi.coords.latitude, posisi.coords.longitude]).addTo(map);
          var circle = L.circle([lat_sekolah, long_sekolah], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: radius
        }).addTo(map);
      }


      function errorCallback() {
          alert("Gagal mendapatkan lokasi.");
      }

      $("#presensi").click(function(e){
        Webcam.snap(function(uri){
            image = uri;
        });
            var lokasi = $("#lokasi").val();
            $.ajax({
                type:'POST',
                url:'/absensi/store',
                data:{
                    _token:"{{ csrf_token() }}",
                    image:image,
                    lokasi:lokasi
                },
                cache:false,
                success: function(respond){
                    console.log("RESPOND:", respond);
                    var status = respond.split("|");

                    for (var i = 0; i < status.length; i++) {
                        status[i] = status[i].trim();
                    }

                    if (status[0] === "success") {
                        if(status[2] === "in") {
                            notif_masuk.play();
                        } else {
                            notif_keluar.play();
                        }
                        Swal.fire({
                            title: 'Berhasil !',
                            text: status[1],
                            icon: 'success',
                        });
                        setTimeout(() => {
                            location.href = '/dashboard';
                        }, 3000);
                    } else {
                        if(status[2] === "radius") {
                            radius_sekolah.play();
                        }
                        Swal.fire({
                            title: 'Error !',
                            text: status[1],
                            icon: 'error',
                        });
                    }
                }
            });
      });
  </script>
  @endpush

  --}}



@extends('layouts.absen')

@section('header')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<div class="appHeader bg-darkred text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Absensi Siswa</div>
    <div class="right"></div>
</div>

<style>
    #reader {
        width: 300px;
        margin: auto;
    }

    .webcam-camera,
    .webcam-camera video {
        display: block;
        width: 80% !important;
        margin: auto;
        height: auto !important;
        border-radius: 5px;
    }

    #map {
        width: 100%;
        height: 200px;
        max-height: 400px;
    }

    button#presensi {
        width: 90%;
        padding: 12px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 5px auto;
        border-radius: 5px;
    }

    button#presensi i {
        margin-right: 8px;
    }

    #section-camera,
    #presensi {
        display: none;
    }
</style>

<!-- Preload Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
<!-- QR Code Scanner -->
<script src="https://unpkg.com/html5-qrcode" defer></script>
@endsection

@section('content')

<div class="row mt-2" style="margin-top: 70px;">
    <div class="col-12">
        <h5 class="text-center">Scan QR dari Admin</h5>
        <div id="reader"></div>
    </div>
</div>

<!-- Section Webcam -->
<div class="row mt-3" id="section-camera">
    <div class="col-12">
        <input type="hidden" id="lokasi">
        <div class="webcam-camera"></div>
    </div>
</div>

<!-- Section Tombol Presensi -->
<div class="row mt-2">
    <div class="col-12">
        @if($cek > 0)
        <button id="presensi" class="btn btn-primary btn-danger">
            <ion-icon name="camera"></ion-icon> Presensi Pulang
        </button>
        @else
        <button id="presensi" class="btn btn-primary btn-block">
            <ion-icon name="camera"></ion-icon> Presensi Masuk
        </button>
        @endif
    </div>
</div>

<!-- Section Peta -->
<div class="row mt-2">
    <div class="col-12">
        <div id="map"></div>
    </div>
</div>

<audio id="notif_masuk"><source src="{{ asset('assets/sound/notif_masuk.mp3') }}" type="audio/mpeg"></audio>
<audio id="notif_keluar"><source src="{{ asset('assets/sound/notif_keluar.mp3') }}" type="audio/mpeg"></audio>
<audio id="radius_sekolah"><source src="{{ asset('assets/sound/radius_sekolah.mp3') }}" type="audio/mpeg"></audio>
@endsection

@push('myscript')
<script>
    let image;
    const notif_masuk = document.getElementById('notif_masuk');
    const notif_keluar = document.getElementById('notif_keluar');
    const radius_sekolah = document.getElementById('radius_sekolah');

    // Inisialisasi peta dan lokasi
    var lokasi = document.getElementById('lokasi');
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(posisi) {
        lokasi.value = posisi.coords.latitude + "," + posisi.coords.longitude;
        var map = L.map('map').setView([posisi.coords.latitude, posisi.coords.longitude], 18);
        var lokasi_sekolah = "{{ $lok_sekolah->lokasi_sekolah }}";
        var lok = lokasi_sekolah.split(",");
        var lat_sekolah = lok[0];
        var long_sekolah = lok[1];
        var radius = "{{ $lok_sekolah->radius }}";

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([posisi.coords.latitude, posisi.coords.longitude]).addTo(map);
        L.circle([lat_sekolah, long_sekolah], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: radius
        }).addTo(map);
    }

    function errorCallback() {
        alert("Gagal mendapatkan lokasi.");
    }

    // QR Code Scanner
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Html5Qrcode !== 'undefined') {
            const html5QrCode = new Html5Qrcode("reader");
            const qrConfig = { fps: 10, qrbox: 250 };

            html5QrCode.start(
                { facingMode: "environment" },
                qrConfig,
                qrCodeMessage => {
                    // Setelah QR discan
                    html5QrCode.stop();
                    document.getElementById("reader").innerHTML = `<div class='alert alert-success text-center'>QR Valid: ${qrCodeMessage}</div>`;

                    // Tampilkan kamera dan tombol
                    document.getElementById("section-camera").style.display = 'block';
                    document.getElementById("presensi").style.display = 'block';

                    // Aktifkan webcam
                    Webcam.set({
                        width: window.innerWidth * 0.9,
                        height: window.innerHeight * 0.4,
                        image_format: 'jpeg',
                        jpeg_quality: 80,
                    });

                    Webcam.attach('.webcam-camera');

                    Webcam.on('error', function(err) {
                        console.error("Webcam.js Error: ", err);
                        alert("Webcam.js Error: " + err.message);
                    });
                },
                errorMessage => {
                    // ignore errors
                }
            ).catch(err => {
                console.error("QR Scanner error:", err);
            });
        }
    });

    // Presensi dengan webcam + lokasi
    $("#presensi").click(function(e){
        Webcam.snap(function(uri){
            image = uri;
        });
        var lokasi = $("#lokasi").val();

        $.ajax({
            type: 'POST',
            url: '/absensi/store',
            data: {
                _token: "{{ csrf_token() }}",
                image: image,
                lokasi: lokasi
            },
            cache: false,
            success: function(respond){
                var status = respond.split("|").map(item => item.trim());
                if (status[0] === "success") {
                    (status[2] === "in") ? notif_masuk.play() : notif_keluar.play();
                    Swal.fire({ title: 'Berhasil !', text: status[1], icon: 'success' });
                    setTimeout(() => location.href = '/dashboard', 3000);
                } else {
                    if(status[2] === "radius") radius_sekolah.play();
                    Swal.fire({ title: 'Error !', text: status[1], icon: 'error' });
                }
            }
        });
    });
</script>
@endpush
