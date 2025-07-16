@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-0">Edit Data Guru</h4>

                  {{-- ALERT MESSAGE --}}
                  @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      {{ session('success') }}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  @endif
                  @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  @endif
                  <form class="forms-sample" action="{{route('guru.update',['id'=>$guru->id])}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                      <label for="exampleInputName1">Kode Guru</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="kode_guru" value="{{ old('kode_guru', $guru->kode_guru) }}" required>
                      @error('kode_guru')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Nama</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="nama" value="{{ old('nama', $guru->nama) }}" required>
                      @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">NIP</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="nip" value="{{ old('nip', $guru->nip) }}" required>
                      @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail3">Email</label>
                      <input type="email" class="form-control" id="exampleInputEmail3"name="email" value="{{ old('email', $guru->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Alamat</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="alamat" value="{{ old('alamat', $guru->alamat) }}" required>
                      @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('guru.index') }}" class="btn btn-light">Kembali</a>
                  </form>
</div>
              </div>
            </div>
                </div>
              </div>
            </div>
        
        </form>
    </div>
@endsection