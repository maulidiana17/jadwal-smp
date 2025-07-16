@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Tambah Data Kelas</h4>
                  <form class="forms-sample" action="{{route('kelas.store')}}" method="POST">

                    @csrf
                    <div class="col-md-8 form-group">
                      <label for="exampleInputName1">Kelas</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="nama" placeholder="Kelas">
                      @error('nama')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="col-md-8 form-group">
                      <label for="exampleInputName1">Tingkat Kelas</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="tingkat_kelas" placeholder="Tingkat Kelas">
                      @error('tingkat_kelas')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('kelas.index') }}" class="btn btn-light">Kembali</a>
                  </form>
                </div>
              </div>
            </div>
    </div>
@endsection