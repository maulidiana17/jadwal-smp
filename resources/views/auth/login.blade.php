<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      text-align: center;
      width: 100%;
      max-width: 400px;
    }
    .login-box img {
      width: 80px;
      margin-bottom: 20px;
    }
    .login-box h5 {
      font-size: 14px;
      color: #007BFF;
      margin-bottom: 30px;
    }
    .form-control {
      margin-bottom: 15px;
      font-size: 14px;
    }
    .btn-login {
      background-color: #51aaff;
      color: white;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

  <div class="login-box">
    <img src="{{ asset('sd/images/logo.png') }}" alt="Logo">
    <h5>Welcome to Sistem Penjadwalan<br><strong>SMPN 1 GENTENG</strong></h5>
    <form action="{{ route('login_proses') }}" method="POST">
      @csrf
      <input type="email" name="email" class="form-control" placeholder="Email" required>
      @error('email')
          <small>{($message)}</small>
      @enderror
      <input type="password" name="password" class="form-control" placeholder="Password" required>
      @error('password')
          <small>{($message)}</small>
      @enderror
      <button type="submit" class="btn btn-login btn-block w-100">LOGIN</button>
    </form>
  </div>
@if ($message = Session::get('failed'))
    <script>
        Swal.fire("email dan password salah");
    </script>
@endif
</body>
</html>
