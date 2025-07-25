
@extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Data Guru</h3>
                <h6 class="op-7 mb-2">Guru</h6>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                  @hasanyrole('admin|kurikulum')
                        <a href="{{route('guru.create')}}" class="btn btn-info">Tambah</a>
                        <a class="mdi mdi-file-excel text-success" data-toggle="modal" data-target="#importModal">Impor Excel</a>
                    @endhasanyrole
                    {{--  <a href="#" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#addAdminModal"><i class="fa fa-plus me-2"></i></i>Tambah Admin</a>  --}}
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">

                            </div>
                        </div>

                        <div class="row mt-2 mb-2">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                        <thead>
                                             <tr>
                                                <th>No</th>
                                                <th>Kode Guru</th>
                                                <th>Nama</th>
                                                <th>NIP</th>
                                                <th>Email</th>
                                                <th>Alamat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                          <tbody>
                        @foreach ($gurus as $g)
                            <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $g->kode_guru }}</td>
                          <td>{{ $g->nama}}</td>
                          <td>{{ $g->nip }}</td>
                          <td>{{ $g->email }}</td>
                          <td>{{ $g->alamat }}</td>
                          <td>
                            @hasanyrole('admin|kurikulum')
                                <a href="{{route('guru.edit',['id'=>$g->id])}}" class="ti-pencil text-info"></a>
                                <a href="" class="ti-trash text-danger" data-toggle="modal" data-target="#modal-hapus{{ $g->id }}"></a>
                            @endhasanyrole
                          </td>

                          {{--  <td>
                            <a href="{{route('guru.edit',['id'=>$g->id])}}" class="ti-pencil text-info"></a>
                            <a href="" class="ti-trash text-danger" data-toggle="modal" data-target="#modal-hapus{{ $g->id }}"></a>
                          </td>  --}}
                        </tr>

                        <!-- Modal -->
                        @hasanyrole('admin|kurikulum')
<!-- Modal -->
<div class="modal fade" id="modal-hapus{{ $g->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       <div class="modal-body">
         <p>Apakah anda yakin menghapus data <b>{{$g->nama}}</b></p>
       </div>
      <div class="modal-footer">
        <form action="{{route('guru.delete',$g->id)}}" method="POST">
          @csrf
          @method('DELETE')
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endhasanyrole

                        {{--  <div class="modal fade" id="modal-hapus{{ $g->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                               <div class="modal-body">
                                 <p>Apakah anda yakin menghapus data <b>{{$g->nama}}</b></p>
                               </div>
                              <div class="modal-footer">
                                <form action="{{route('guru.delete',$g->id)}}" method="POST">
                                  @csrf
                                  @method('DELETE')
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>

                              </div>
                            </div>
                          </div>
                        </div>  --}}
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  {{-- Pagination --}}
                  <div class="d-flex justify-content-center mt-3">
                      {{ $gurus->links('pagination::bootstrap-5') }}
                  </div>
                </div>
              </div>
            </div>
        </div>
@endsection
