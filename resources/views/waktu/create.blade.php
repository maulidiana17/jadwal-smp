@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Tambah Data Waktu</h4>
                  <form class="forms-sample" action="{{route('waktu.store')}}" method="POST">

                    @csrf
                    <div class="form-group">
                      <label for="exampleInputName1">Hari</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="hari" placeholder="Hari">
                      @error('hari')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Jam Ke</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_ke" placeholder="Jam Ke">
                      @error('jam_ke')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Jam Mulai</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_mulai" placeholder="Jam Mulai">
                      @error('jam_mulai')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Jam Selesai</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="jam_selesai" placeholder="Jam Selesaii">
                      @error('jam_selesai')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Keterangan</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="ket" placeholder="Jam Selesaii">
                      @error('ket')
                        <small  class="text-danger">{{$message}}</small>
                    @enderror
                    </div>
                    <button type="submit" class="btn btn-info mr-2">Simpan</button>
                    <a href="{{ route('waktu.index') }}" class="btn btn-light">Kembali</a>
                  </form>
                </div>
              </div>
            </div>
    </div>
@endsection