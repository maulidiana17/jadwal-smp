{{--  <!DOCTYPE html>
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
  <h5>Welcome to Sistem Penjadwalan dan Presensi<br><strong>SMPN 1 GENTENG</strong></h5>

  <form action="{{ route('login_proses') }}" method="POST">
    @csrf

    <!-- Select login sebagai -->
    <label class="form-label">Login Sebagai</label>
    <div class="input-group input-group-outline my-3">
      <select name="login_sebagai" id="login_sebagai" class="form-control" required>
        <option value="">Pilih</option>
        <option value="admin">Admin</option>
        <option value="guru">Guru</option>
        <option value="kurikulum">Kurikulum</option>
        <option value="siswa">Siswa</option>
      </select>
    </div>

    <!-- Form login email -->
    <div id="emailLoginSection">
      <input type="email" name="email" id="emailInput" class="form-control" placeholder="Email">
      @error('email')
        <small class="text-danger">{{ $message }}</small>
      @enderror

      <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password">
      @error('password')
        <small class="text-danger">{{ $message }}</small>
      @enderror
    </div>

    <!-- Form login siswa -->
    <div id="studentLoginSection" style="display: none;">
      <input type="text" name="nis" id="nisInput" class="form-control" placeholder="NIS Siswa">
      @error('nis')
        <small class="text-danger">{{ $message }}</small>
      @enderror
      <input type="password" name="password_siswa"" id="passwordSiswaInput" class="form-control" placeholder="Password">
    </div>

    <button type="submit" class="btn btn-login btn-block w-100">LOGIN</button>
  </form>
</div>

@if (session('failed'))
<script>
  Swal.fire("{{ session('failed') }}");
</script>
@endif

<script>
  const loginSelect = document.getElementById('login_sebagai');
  const emailSection = document.getElementById('emailLoginSection');
  const studentSection = document.getElementById('studentLoginSection');

  const emailInput = document.getElementById('emailInput');
  const passwordInput = document.getElementById('passwordInput');
  const nisInput = document.getElementById('nisInput');
  const passwordSiswaInput = document.getElementById('passwordSiswaInput');

  loginSelect.addEventListener('change', function () {
    if (this.value === 'siswa') {
      emailSection.style.display = 'none';
      studentSection.style.display = 'block';

      emailInput.removeAttribute('required');
      passwordInput.removeAttribute('required');

      nisInput.setAttribute('required', 'required');
      passwordSiswaInput.setAttribute('required', 'required');
    } else {
      emailSection.style.display = 'block';
      studentSection.style.display = 'none';

      emailInput.setAttribute('required', 'required');
      passwordInput.setAttribute('required', 'required');

      nisInput.removeAttribute('required');
      passwordSiswaInput.removeAttribute('required');
    }
  });
</script>

</body>
</html>  --}}

{{--  mencoba  --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.3s ease;
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

    /* Backgrounds per role */
    body.siswa-bg {
      background-color: #e0f7fa;
    }
    body.guru-bg {
      background-color: #fff3e0;
    }
    body.admin-bg {
      background-color: #fce4ec;
    }
    body.kurikulum-bg {
      background-color: #ede7f6;
    }
  </style>
</head>
<body id="loginBody">

  <div class="login-box">
    <img src="{{ asset('sd/images/logo.png') }}" alt="Logo" />
    <h5 id="welcomeText">Welcome to SPENSA<br><strong>SMPN 1 GENTENG</strong></h5>

    <form action="{{ route('login_proses') }}" method="POST">
      @csrf

      <label class="form-label">Login Sebagai</label>
      <div class="input-group input-group-outline my-3">
        <select name="login_sebagai" id="login_sebagai" class="form-control" required>
          <option value="">Pilih</option>
          <option value="admin">Admin</option>
          <option value="guru">Guru</option>
          <option value="kurikulum">Kurikulum</option>
          <option value="siswa">Siswa</option>
        </select>
      </div>

      <!-- Email & password -->
      <div id="emailLoginSection">
        <input type="email" name="email" id="emailInput" class="form-control" placeholder="Email" />
        @error('email')
        <small class="text-danger">{{ $message }}</small>
        @enderror

        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password" />
        @error('password')
        <small class="text-danger">{{ $message }}</small>
        @enderror
      </div>

      <!-- NIS siswa -->
      <div id="studentLoginSection" style="display: none;">
        <input type="text" name="nis" id="nisInput" class="form-control" placeholder="NIS Siswa" />
        @error('nis')
        <small class="text-danger">{{ $message }}</small>
        @enderror

        <input type="password" name="password_siswa" id="passwordSiswaInput" class="form-control" placeholder="Password" />
      </div>

      <button type="submit" class="btn btn-login btn-block w-100">LOGIN</button>
    </form>
  </div>

  @if (session('failed'))
  <script>
    Swal.fire("{{ session('failed') }}");
  </script>
  @endif

  <script>
    const loginSelect = document.getElementById('login_sebagai');
    const emailSection = document.getElementById('emailLoginSection');
    const studentSection = document.getElementById('studentLoginSection');

    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');
    const nisInput = document.getElementById('nisInput');
    const passwordSiswaInput = document.getElementById('passwordSiswaInput');

    const welcomeText = document.getElementById('welcomeText');
    const body = document.getElementById('loginBody');

    loginSelect.addEventListener('change', function () {
      body.classList.remove('siswa-bg', 'guru-bg', 'admin-bg', 'kurikulum-bg');

      if (this.value === 'siswa') {
        emailSection.style.display = 'none';
        studentSection.style.display = 'block';

        emailInput.removeAttribute('required');
        passwordInput.removeAttribute('required');
        nisInput.setAttribute('required', 'required');
        passwordSiswaInput.setAttribute('required', 'required');

        body.classList.add('siswa-bg');
        welcomeText.innerHTML = `Selamat Datang <br><strong>Siswa SMPN 1 GENTENG</strong>`;
      } else {
        emailSection.style.display = 'block';
        studentSection.style.display = 'none';

        emailInput.setAttribute('required', 'required');
        passwordInput.setAttribute('required', 'required');
        nisInput.removeAttribute('required');
        passwordSiswaInput.removeAttribute('required');

        if (this.value === 'admin') {
          body.classList.add('admin-bg');
          welcomeText.innerHTML = `Selamat Datang <br><strong>Admin Presensi</strong>`;
        } else if (this.value === 'guru') {
          body.classList.add('guru-bg');
          welcomeText.innerHTML = `Selamat Datang <br><strong>Guru Presensi</strong>`;
        } else if (this.value === 'kurikulum') {
          body.classList.add('kurikulum-bg');
          welcomeText.innerHTML = `Selamat Datang di <br><strong>Sistem Penjadwalan</strong>`;
        } else {
          welcomeText.innerHTML = `Welcome to Sistem Penjadwalan dan Presensi<br><strong>SMPN 1 GENTENG</strong>`;
        }
      }
    });
  </script>

</body>
</html>
