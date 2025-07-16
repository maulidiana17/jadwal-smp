@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-0">Edit Data Waktu</h4>

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
                  <form class="forms-sample" action="{{route('waktu.update',['id'=>$waktu->id])}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                      <label for="exampleInputName1">Hari</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="hari" value="{{ old('hari', $waktu->hari) }}" required>
                      @error('hari')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Jam Ke</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_ke" value="{{ old('jam_ke', $waktu->jam_ke) }}" required>
                      @error('jam_ke')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div><div class="form-group">
                      <label for="exampleInputName1">Jam Mulai</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_mulai" value="{{ old('jam_mulai', $waktu->jam_mulai) }}" required>
                      @error('jam_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Jam Selesai</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_selesai" value="{{ old('jam_selesai', $waktu->jam_selesai) }}" required>
                      @error('jam_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Keterangan</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="ket" value="{{ old('ket', $waktu->ket) }}" required>
                      @error('ket')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('waktu.index') }}" class="btn btn-light">Kembali</a>
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