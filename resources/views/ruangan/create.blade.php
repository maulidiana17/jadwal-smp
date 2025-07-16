@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Tambah Data Ruangan</h4>
                  <form class="forms-sample" action="{{route('ruangan.store')}}" method="POST">

                    @csrf
                    <div class="form-group">
                      <label for="exampleInputName1">Kode Ruangan</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="kode_ruangan" placeholder="Kode Ruangan">
                      @error('kode_ruangan')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Nama Ruangan</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="nama" placeholder="Nama Ruangan">
                      @error('nama')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Tipe</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="tipe" placeholder="tipe">
                      @error('tipe')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Fasilitas</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="fasilitas" placeholder="Fasilitas">
                      @error('fasilitas')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('ruangan.index') }}" class="btn btn-light">Kembali</a>
                  </form>
                </div>
              </div>
            </div>
    </div>
@endsection