@extends('layouts.admin.dashboard')

@section('content')
<div class="container-fluid">
    <div class="page-inner">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center pt-3 pb-4">
            <div>
                <h3 class="fw-bold mb-1">Data Guru</h3>
                <h6 class="text-muted mb-2">Daftar seluruh guru yang terdaftar</h6>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mt-3">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Guru</th>
                                        <th>Nama</th>
                                        <th>NIP</th>
                                        <th>Email</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gurus as $g)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $g->kode_guru }}</td>
                                            <td>{{ $g->nama }}</td>
                                            <td>{{ $g->nip }}</td>
                                            <td>{{ $g->email }}</td>
                                            <td>{{ $g->alamat }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $gurus->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
