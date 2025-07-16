@extends('layout.main')

@section('content')
    <div class="content-wrapper">
         <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Data Waktu Pelajaran</h4>
                  <div class="d-flex justify-content-between mb-3 align-items-center">
                  <a href="{{route('waktu.create')}}" class="btn btn-info">Tambah</a>
                    <!-- Tombol trigger modal -->
                  <a class="mdi mdi-file-excel text-success" data-toggle="modal" 
                  data-target="#importModal">Impor Excel</a>
                  </div>

                  <!-- Modal -->
                  <div class="modal fade" id="importModal" tabindex="-1" role="dialog" 
                        aria-labelledby="importModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <form action="{{ route('waktu.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Data Waktu</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span>&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <div class="form-group">
                              <label>Pilih File Excel (.xlsx / .xls)</label>
                              <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv"  required>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-info">Import</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

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
                          <th>Hari</th>
                          <th>Jam Ke</th>
                          <th>Jam Mulai</th>
                          <th>Jam Selesai</th>
                          <th>Keterangan</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($waktu as $w)
                            <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $w->hari }}</td>
                          <td>{{ $w->jam_ke}}</td>
                          <td>{{ $w->jam_mulai}}</td>
                          <td>{{ $w->jam_selesai}}</td>
                          <td>{{ $w->ket}}</td>
                          <td>
                            <a href="{{route('waktu.edit',['id'=>$w->id])}}" class="ti-pencil text-info"></a>
                            <a href="" class="ti-trash text-danger" data-toggle="modal" data-target="#modal-hapus{{ $w->id }}"></a>
                          </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-hapus{{ $w->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                               <div class="modal-body">
                                 <p>Apakah anda yakin menghapus data <b>{{$w->hari}}</b></p> 
                               </div>
                              <div class="modal-footer">
                                <form action="{{route('waktu.delete',$w->id)}}" method="POST">
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
                  {{-- Pagination --}}
                  <div class="d-flex justify-content-center mt-3">
                      {{ $waktu->links('pagination::bootstrap-5') }}
                  </div>
                </div>
              </div>
            </div>
        </div>
@endsection