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
                                        <form action="{{ route('pengampu.destroyGroup', [$guru->id, $mapel->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus semua kelas untuk guru ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger p-0 border-0 bg-transparent" title="Hapus Semua"><i class="ti-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
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
