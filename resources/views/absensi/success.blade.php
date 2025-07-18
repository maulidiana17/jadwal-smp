{{--  @extends('layouts.absen')

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
<div class="row" style="min-height: 80vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div class="text-center">
        <a href="{{ url('/dashboard') }}" class="btn btn-primary mt-4 px-4 py-2 rounded-lg">
            Kembali ke Dashboard
        </a>
    </div>
</div>
<audio id="notif_berhasil">
    <source src="{{ asset('assets/sound/notif_berhasil.mp3') }}" type="audio/mpeg">
  </audio>
@endsection

<!-- SweetAlert Pop-up -->
@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var notif_masuk = document.getElementById('notif_berhasil');
        if (notif_masuk) {
            notif_masuk.play().catch(function(error) {
                console.warn("Gagal memutar suara:", error);
            });
        }
        Swal.fire({
            icon: 'success',
            title: 'Absen Berhasil!',
            text: 'Terimakasih, Selamat Belajar dan Semoga SUKSES!',
            confirmButtonText: 'OK',
            timer: 10000
        });
    });
</script>
@endpush  --}}


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
{{--  <div class="row" style="min-height: 80vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">

</div>  --}}

<audio id="notif_berhasil">
    <source src="{{ asset('assets/sound/notif_berhasil.mp3') }}" type="audio/mpeg">
</audio>
@endsection

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var notif_masuk = document.getElementById('notif_berhasil');
        if (notif_masuk) {
            notif_masuk.play().catch(function(error) {
                console.warn("Gagal memutar suara:", error);
            });
        }
        Swal.fire({
            icon: 'success',
            title: 'Absen Berhasil!',
            text: 'Terimakasih, Selamat Belajar dan Semoga SUKSES!',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
            didClose: () => {
                window.location.href = "{{ url('/dashboard') }}";
            }
        });
    });
</script>
@endpush
