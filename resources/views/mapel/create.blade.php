@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Tambah Data Mata Pelajaran</h4>
                  <form class="forms-sample" action="{{route('mapel.store')}}" method="POST">

                    @csrf
                    <div class="form-group">
                      <label for="exampleInputName1">Kode Mapel</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="kode_mapel" placeholder="Kode mapel">
                      @error('kode_mapel')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Mata Pelajaran</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="mapel" placeholder="Mata Pelajaran">
                      @error('mapel')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Jam/Minggu</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_per_minggu" placeholder="Mata Pelajaran">
                      @error('jam_per_minggu')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Ruang Khusus</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="ruang_khusus" placeholder="Ruang Khusus">
                      @error('ruang_khusus')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('mapel.index') }}" class="btn btn-light">Kembali</a>
                  </form>
                </div>
              </div>
            </div>
    </div>
@endsection