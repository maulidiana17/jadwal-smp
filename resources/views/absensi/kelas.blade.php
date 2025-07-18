@extends('layouts.admin.dashboard')

@section('content')
{{--  <div class="container mt-4">
    <h3>Daftar Kelas</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kelas</th>
                <th>Tingkat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelas as $index => $k)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $k->nama }}</td>
                    <td>{{ $k->tingkat_kelas }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>  --}}


<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Data Kelas</h3>
                <h6 class="op-7 mb-2">Kelas</h6>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            {{--  <div class="ms-md-auto py-2 py-md-0">
                    <a href="#" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#addAdminModal"><i class="fa fa-plus me-2"></i></i>Tambah Kelas</a>
            </div>  --}}
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
                                                <th>Kelas</th>
                                                <th>Tingkat</th>
                                                {{--  <th>Aksi</th>  --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($kelas as $index => $k)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $k->nama }}</td>
                                                <td>{{ $k->tingkat_kelas }}</td>
                                                {{--  <td>
                                                    <!-- Tombol Edit -->
                                                    <button type="button" class="btn btn-link btn-primary edit" data-bs-toggle="modal"
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <!-- Tombol Hapus -->
                                                    <form action="#" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger delete" data-original-title="Remove">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                </td>  --}}
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                {{--  <div class="mt-3">
                                    {{ $admins->links('vendor.pagination.bootstrap-5') }}
                                </div>  --}}
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
