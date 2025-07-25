@extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Data Guru</h3>
                <h6 class="op-7 mb-2">Data Guru</h6>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
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

                                       {{-- Pagination --}}
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $gurus->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




</div>
@endsection



