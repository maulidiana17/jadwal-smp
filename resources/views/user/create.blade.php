@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Tambah Data User</h4>
                  <form class="forms-sample" action="{{route('user.store')}}" method="POST">
                    @csrf
                    <div class="form-group">
                      <label for="exampleInputName1">Nama</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="name" placeholder="Name">
                      @error('name')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail3">Email address</label>
                      <input type="email" class="form-control" id="exampleInputEmail3"name="email" placeholder="Email">
                    @error('email')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group position-relative">
                      <label for="password">Password</label>
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
                    <div class="form-group">
                       <label for="role">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">-- Pilih Role --</option>
                            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Guru" {{ old('role') == 'Guru' ? 'selected' : '' }}>Guru</option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('user.index') }}" class="btn btn-light">Kembali</a>
                  </form>
        </div>
              </div>
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