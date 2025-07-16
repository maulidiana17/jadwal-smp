@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Tambah Data Guru</h4>
                  <form class="forms-sample" action="{{route('guru.store')}}" method="POST">

                    @csrf
                    <div class="form-group">
                      <label for="exampleInputName1">Kode Guru</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="kode_guru" placeholder="Kode Guru">
                      @error('kode_guru')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Nama</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="nama" placeholder="Nama">
                      @error('nama')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">NIP</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="nip" placeholder="NIP">
                      @error('nip')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail3">Email</label>
                      <input type="email" class="form-control" id="exampleInputEmail3"name="email" placeholder="Email">
                    @error('email')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Alamat</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="alamat" placeholder="Alamat">
                      @error('alamat')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('guru.index') }}" class="btn btn-light">Kembali</a>
                  </form>
                </div>
              </div>
            </div>
    </div>
@endsection