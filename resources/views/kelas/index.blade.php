@extends('layout.main')

@section('content')
    <div class="content-wrapper">
         <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Data Kelas</h4>
                  <div class="d-flex justify-content-between mb-3 align-items-center">
                  <a href="{{route('kelas.create')}}" class="btn btn-info">Tambah</a>
                      <div class="d-flex gap-2">
                        <!-- Tombol trigger modal -->
                        <a class="mdi mdi-file-excel text-success" data-toggle="modal" data-target="#importModal">Impor Excel</a>
                        <a class="mdi mdi-delete-empty text-danger" data-toggle="modal" data-target="#modal-reset">Reset</a>
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

                  <!-- Modal -->
                  <div class="modal fade" id="importModal" tabindex="-1" role="dialog" 
                        aria-labelledby="importModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <form action="{{ route('kelas.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Data Kelas</h5>
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

                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Kelas</th>
                          <th>Tingkat Kelas</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($kelas as $k)
                            <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $k->nama }}</td>
                          <td>{{ $k->tingkat_kelas}}</td>
                          <td>
                            <a href="{{route('kelas.edit',['id'=>$k->id])}}" class="ti-pencil text-info"></a>
                            <a href="" class="ti-trash text-danger" data-toggle="modal" data-target="#modal-hapus{{ $k->id }}"></a>
                          </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-hapus{{ $k->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                               <div class="modal-body">
                                 <p>Apakah anda yakin menghapus data kelas <b>{{$k->nama}}</b></p> 
                               </div>
                              <div class="modal-footer">
                                <form action="{{route('kelas.delete',$k->id)}}" method="POST">
                                  @csrf
                                  @method('DELETE')
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                                
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Modal Reset Semua -->
                        <div class="modal fade" id="modal-reset" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <form action="{{ route('kelas.reset') }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="resetModalLabel">Konfirmasi Reset Semua Kelas</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <p>Apakah Anda yakin ingin menghapus <strong>seluruh data kelas</strong>? Tindakan ini tidak bisa dibatalkan.</p>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                  <button type="submit" class="btn btn-danger">Reset Semua</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        @endforeach
                          @if ($kelasList->isEmpty())
                              <tr>
                                  <td colspan="5" class="text-center">Belum ada data kelas.</td>
                              </tr>
                          @endif
                      </tbody>
                    </table>
                  </div>
                  {{-- Pagination --}}
                  <div class="d-flex justify-content-center mt-3">
                      {{ $kelas->links('pagination::bootstrap-5') }}
                  </div>
                </div>
              </div>
            </div>
        </div>
@endsection