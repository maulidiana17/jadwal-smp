@extends('layout.main')

@section('content')
    <div class="content-wrapper">
         <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Data Guru</h4>
                  <div class="d-flex justify-content-between mb-3 align-items-center">
                    @hasanyrole(['kurikulum','admin'])
                        <a href="{{route('guru.create')}}" class="btn btn-info">Tambah</a>
                          <div class="d-flex gap-2">
                            <!-- Tombol trigger modal -->
                            <a class="mdi mdi-file-excel text-success" data-toggle="modal" data-target="#importModal">Impor Excel</a>
                            <a class="mdi mdi-delete-empty text-danger" data-toggle="modal" data-target="#modal-reset">Reset</a>
                          </div>
                    @endhasanyrole
                  </div>

                  <!-- Modal -->
                  @hasanyrole(['kurikulum','admin'])
                    <!-- Modal Import -->
                    <div class="modal fade" id="importModal" tabindex="-1" role="dialog"
                        aria-labelledby="importModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Data Guru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                            <div class="form-group">
                                <label>Pilih File Excel (.xlsx / .xls)</label>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
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
                    @endhasanyrole

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
                            @hasanyrole(['kurikulum','admin'])
                                <a href="{{route('guru.edit',['id'=>$g->id])}}" class="ti-pencil text-info"></a>
                                <a href="" class="ti-trash text-danger" data-toggle="modal" data-target="#modal-hapus{{ $g->id }}"></a>
                            @endhasanyrole
                          </td>

                        </tr>

                        <!-- Modal -->
                        @hasanyrole(['kurikulum','admin'])
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

                        @hasanyrole(['kurikulum','admin'])
                        <!-- Modal Reset Semua -->
                        <div class="modal fade" id="modal-reset" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <form action="{{ route('guru.reset') }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="resetModalLabel">Konfirmasi Reset Semua Guru</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <p>Apakah Anda yakin ingin menghapus <strong>seluruh data guru</strong>? Tindakan ini tidak bisa dibatalkan.</p>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                  <button type="submit" class="btn btn-danger">Reset Semua</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        @endhasanyrole
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
