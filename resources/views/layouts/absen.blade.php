<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta name="theme-color" content="#000000">
    <title>Dashboard</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit">
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="manifest" href="/__manifest.json">
    {{--  webcam  --}}
    <script src="{{ asset('assets/js/lib/webcam.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{--  instascan js  --}}
    <script src="{{ asset('assets/js/instascan/instascan.min.js') }}"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

</head>
<body style="background-color:#e9ecef;">
    <!-- Splash Screen -->
    <div id="splashScreen" style="position:fixed;top:0;left:0;width:100%;height:100%;background:#890909;z-index:9999;display:flex;justify-content:center;align-items:center;color:white;flex-direction:column;">
        <img src="{{ asset('assets/img/smp.png') }}" alt="Logo" style="width:100px;height:100px;">
        <h1 style="margin-top:20px;font-size:22px;">Selamat Datang</h1>
    </div>

   <!-- Sidebar -->
<div id="mobileSidebar" style="
    position: fixed;
    top: 0;
    left: -250px;
    width: 250px;
    height: 100%;
    background-color: #fff;
    box-shadow: 2px 0 5px rgba(0,0,0,0.2);
    z-index: 9998;
    padding: 20px;
    transition: left 0.3s ease;
">
    <h2 class="mb-4" style="color: #890909;">Menu</h2>
    <ul class="list-unstyled d-grid gap-2">
        <li class="mb-2">
            <a href="/dashboard" class="btn btn-outline-danger w-100 text-start">Dashboard</a>
        </li>
        <li class="mb-2">
            <a href="/absensi/histori" class="btn btn-outline-danger w-100 text-start">Histori</a>
        </li>
        <li class="mb-2">
            <a href="/absensi/create" class="btn btn-outline-danger w-100 text-start">Presensi</a>
        </li>
        <li class="mb-2">
            <a href="/absensi/izin" class="btn btn-outline-danger w-100 text-start">Izin / Sakit</a>
        </li>
        <li class="mb-2">
            <a href="/absensi/scan" class="btn btn-outline-danger w-100 text-start">Presensi QR</a>
        </li>
        <li class="mb-2">
            <a href="/editprofile" class="btn btn-outline-danger w-100 text-start">Profil</a>
        </li>
        <li class="mb-2">
            <form action="{{ url('/logout') }}" method="GET" class="mb-0">
                @csrf
                <button type="submit" onclick="sessionStorage.clear();" class="btn btn-danger w-100 text-start">Logout</button>
            </form>
        </li>
    </ul>
</div>


<!-- Overlay -->
<div id="sidebarOverlay" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.3);
    z-index: 9997;
    display: none;
"></div>


    <!-- loader -->
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <!-- * loader -->
    @yield('header')
    <div id="appCapsule">
        @yield('content')
    </div>
    <!-- App Bottom Menu -->
        @include('layouts.bottom')
    <!-- * App Bottom Menu -->
    <!-- ///////////// Js Files ////////////////////  -->
    <!-- Jquery -->
        @include('layouts.scripts')
</body>
</html>




















