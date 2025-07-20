@extends('layout.main')

@section('content')
<div class="content-wrapper">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Pengampu</h4>

                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <a href="{{ route('pengampu.create') }}" class="btn btn-info">Tambah</a>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas Diampu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($groups as $key => $group)
                                @php
                                    $guru = $group->first()->guru;
                                    $mapel = $group->first()->mapel;
                                    $kelasList = $group->pluck('kelas.nama')->sort()->implode(', ');
                                @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $guru->nama }}</td>
                                    <td>{{ $mapel->mapel }}</td>
                                    <td>{{ $kelasList }}</td>
                                    <td>
                                        <a href="{{ route('pengampu.editMultiple', [$guru->id, $mapel->id]) }}" class="ti-pencil text-warning me-2" title="Edit Kelas"></a>
                                        {{-- <form action="{{ route('pengampu.destroyGroup', [$guru->id, $mapel->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus semua kelas untuk guru ini?')">
                                            @csrf @method('DELETE') --}}
                                        <a href="" class="ti-trash text-danger" data-toggle="modal" data-target="#modal-hapus{{ $guru->id }}"></a>
                          </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-hapus{{ $guru->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                               <div class="modal-body">
                                 <p>Apakah anda yakin menghapus data pengampu <b>{{$guru->nama}}</b></p> 
                               </div>
                              <div class="modal-footer">
                                <form action="{{ route('pengampu.destroyGroup', [$guru->id, $mapel->id]) }}" method="POST">
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

                            @if ($groups->isEmpty())
                                <tr><td colspan="5" class="text-center">Belum ada data pengampu.</td></tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $groups->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
