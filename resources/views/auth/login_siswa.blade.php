<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#000000">
    <title>Absensi GPS</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    {{-- Webcam --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="{{ asset('assets/js/lib/webcam.min.js') }}"></script>

    <style>
        body {
            background-color: #eaf6ff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background-color: #d9efff;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-box img {
            width: 80px;
            margin-bottom: 20px;
        }

        .login-box h1 {
            font-size: 20px;
            color: #007BFF;
        }

        .login-box h4 {
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-control {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .btn-login {
            background-color: #51aaff;
            color: white;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert {
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <img src="{{ asset('assets/img/sample/photo/smp.png') }}" alt="image">
        <h1>Absensi Siswa</h1>
        <h4>Silakan log in</h4>

        @if (Session::get('warning'))
            <div class="alert alert-outline-warning">
                {{ Session::get('warning') }}
            </div>
        @endif

        <form action="/prosesLogin" method="POST">
            @csrf

            <input type="text" name="nis" class="form-control" id="nis" placeholder="Masukkan NIS">
            <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan Password">

            <button type="submit" class="btn btn-login">Log in</button>
        </form>
    </div>

</body>

</html>
