@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-0">Edit Data Ruangan</h4>

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
                  <form class="forms-sample" action="{{route('ruangan.update',['id'=>$ruangan->id])}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                      <label for="exampleInputName1">Kode Ruangan</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="kode_ruangan" value="{{ old('kode_ruangan', $ruangan->kode_ruangan) }}" required>
                      @error('kode_ruangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Nama</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="nama" value="{{ old('nama', $ruangan->nama) }}" required>
                      @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Tipe</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="tipe" value="{{ old('tipe', $ruangan->tipe) }}" required>
                      @error('tipe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Fasilitas</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="fasilitas" value="{{ old('fasilitas', $ruangan->fasilitas) }}" required>
                      @error('fasilitas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('ruangan.index') }}" class="btn btn-light">Kembali</a>
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