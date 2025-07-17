@extends('layout.main')

@section('content')
<div class="content-wrapper">
  <h3 class="page-title">Pengaturan Profil</h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Profil</li>
    </ol>
  </nav>


<div class="row">
  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Edit Profil</h4>

        @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" 
                          role="alert"  style="max-width: 600px; margin-top: 20px;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="forms-sample">
          @csrf
          @method('PUT')

          <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name', auth()->user()->name) }}">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <!-- Password Baru -->
          <div class="form-group position-relative">
            <label for="password">Password Baru</label>
            <div class="input-group">
              <input type="password" class="form-control @error('password') is-invalid @enderror"
                    id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
              <div class="input-group-append">
                <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword('password', 'toggleIcon')">
                  <i id="toggleIcon" class="bi bi-eye-fill"></i>
                </span>
              </div>
              @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- Konfirmasi Password -->
          <div class="form-group position-relative">
            <label for="password_confirmation">Konfirmasi Password</label>
            <div class="input-group">
              <input type="password" class="form-control @error('password') is-invalid @enderror"
                    id="password_confirmation" name="password_confirmation" placeholder="Kosongkan jika tidak ingin mengubah">
              <div class="input-group-append">
                <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                  <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                </span>
              </div>
              @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>

           <div class="form-group mt-3">
              <label for="photo">Upload Foto Profil</label>
              <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
              @error('photo')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>

          <button type="submit" class="btn btn-info me-2">Simpan</button>
          <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body text-center">
        <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('assets/images/faces/face1.jpg') }}"
             class="img-lg rounded-circle mb-3" alt="Foto Profil">
        <h6 class="card-title">Foto Profil Saat Ini</h6>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@push('scripts')
      <script>
        function togglePassword(inputId, iconId) {
          const passwordInput = document.getElementById(inputId);
          const icon = document.getElementById(iconId);

          if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("bi-eye-fill");
            icon.classList.add("bi-eye-slash-fill");
          } else {
            passwordInput.type = "password";
            icon.classList.remove("bi-eye-slash-fill");
            icon.classList.add("bi-eye-fill");
          }
        }
      </script>
@endpush