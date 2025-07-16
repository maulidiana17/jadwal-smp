@extends('layout.main')

@section('content')
    <div class="content-wrapper">
         <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Data User</h4>
                  <a href="{{route('user.create')}}" class="btn btn-info">Tambah</a>
                  
                  @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" 
                          role="alert"  style="max-width: 600px; margin-top: 20px;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                  
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Email</th>
                          <th>Role</th>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($data as $user)
                            <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $user->name }}</td>
                          <td>{{ $user->email }}</td>
                          <td>{{ $user->role  }}</td>
                          <td>
                            <a href="{{route('user.edit',['id'=>$user->id])}}" class="btn btn-info fa-sm"><i class="ti-pencil"></i></a>
                            <a href="" class="btn btn-danger fa-sm" data-toggle="modal" data-target="#modal-hapus{{ $user->id }}">
                              <i class="ti-trash"></i></a>
                          </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-hapus{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                               <div class="modal-body">
                                 <p>Apakah anda yakin menghapus data <b>{{$user->name}}</b></p> 
                               </div>
                              <div class="modal-footer">
                                <form action="{{route('user.delete',$user->id)}}" method="POST">
                                  @csrf
                                  @method('DELETE')
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                                
                              </div>
                            </div>
                          </div>
                        </div>

                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        </div>
@endsection