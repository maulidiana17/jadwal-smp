  @extends('layouts.absen')

  @section('header')
  <div class="appHeader bg-darkred text-light">
      <div class="left">
          <a href="javascript:history.back()" class="headerButton goBack">
              <ion-icon name="chevron-back-outline"></ion-icon>
          </a>

      </div>
      <div class="pageTitle">Scan QR Barcode</div>
      <div class="right"></div>
  </div>

  @endsection

  @section('content')

  <div class="row justify-content-center" style="margin-top: 70px;">
      <div class="col-12 text-center">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
    @endif
          <div style="padding: 20px;">
              <video id="preview"
                  width="100%"
                  style="max-width: 400px;
                      margin: 20px auto;
                      border-radius: 10px;
                      display: block;
                      box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
              </video>
          </div>

          <!-- Spinner Loading -->
          <div id="loadingMessage" style="display:none; font-weight:bold; color:green; margin-top:10px;">
              Mengirim data, mohon tunggu...
          </div>

          <!-- Pesan Berhasil Absen -->
          <div id="successMessage" style="display:none; font-weight:bold; color:green; margin-top:10px;">
              Absen berhasil! Anda akan diarahkan ke dashboard dalam 1 menit.
          </div>

          <!-- Tombol Mulai Scan -->
          <div class="mt-3">
              <button onclick="mulaiScan()" class="btn btn-primary m-1">Mulai Scan</button>
          </div>

          <!-- Form Absen -->
          <form id="absenForm" method="POST" action="{{ url('/absensi/simpanScanQR') }}" style="display:none;">
              @csrf
              <input type="hidden" name="nama" id="nama">
              <input type="hidden" name="nis" id="nis">
              <input type="hidden" name="kelas" id="kelas">
              <input type="hidden" name="nip" id="nip">
              <input type="hidden" name="mapel" id="mapel">
          </form>
      </div>
  </div>
  @endsection

  @push('myscript')
  <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

  <script>
      let scanner;

      function mulaiScan() {
          if (!scanner) {
              scanner = new Instascan.Scanner({ video: document.getElementById('preview'), mirror: false });

              scanner.addListener('scan', function (content) {
                  console.log("QR Content:", content);
                  try {
                      let data = JSON.parse(content);

                      let currentToken = Math.floor(Date.now() / 1000 / 1800);
                      console.log('Token dari QR:', data.token);
                      console.log('Token Sekarang:', currentToken);

                      if (data.token != currentToken) {
                          alert("QR Code tidak valid atau kadaluarsa!");
                          return;
                      }

                      document.getElementById('nama').value = data.nama;
                      document.getElementById('nis').value = data.nis;
                      document.getElementById('kelas').value = data.kelas;
                      document.getElementById('nip').value = data.nip;
                      document.getElementById('mapel').value = data.mapel;
                      document.getElementById('loadingMessage').style.display = 'block';
                      document.getElementById('absenForm').submit();

                      document.getElementById('successMessage').style.display = 'block';

                      setTimeout(function() {
                          window.location.href = "{{ url('/dashboard') }}";
                      }, 60000);

                  } catch (e) {
                      console.error("Format JSON salah:", e);
                      alert("Format QR salah. Pastikan QR Code berisi data JSON yang valid.");
                  }
              });
          }

          Instascan.Camera.getCameras().then(function (cameras) {
              if (cameras.length > 0) {
                  let selectedCamera = cameras.length > 1 ? cameras[1] : cameras[0];
                  scanner.start(selectedCamera);
              } else {
                  alert('Kamera tidak tersedia!');
              }
          }).catch(function (e) {
              console.error(e);
              alert('Tidak bisa mengakses kamera: ' + e);
          });
      }
  </script>
  @endpush
