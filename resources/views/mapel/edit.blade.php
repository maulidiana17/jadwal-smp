@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-0">Edit Data Mata Pelajaran</h4>

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
                  <form class="forms-sample" action="{{route('mapel.update',['id'=>$mapel->id])}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                      <label for="exampleInputName1">Kode Mapel</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" required>
                      @error('kode_mapel')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Mata Pelajaran</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="mapel" value="{{ old('mapel', $mapel->mapel) }}" required>
                      @error('mapel')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Jam/Minggu</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_per_minggu" value="{{ old('jam_per_minggu', $mapel->jam_per_minggu) }}" required>
                      @error('jam_per_minggu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Ruang Khusus</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="ruang_khusus" value="{{ old('ruang_khusus', $mapel->ruang_khusus) }}" required>
                      @error('ruang_khusus')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('mapel.index') }}" class="btn btn-light">Kembali</a>
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