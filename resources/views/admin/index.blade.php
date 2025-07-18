
{{--
@extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Data Admin</h3>
                <h6 class="op-7 mb-2">Admin</h6>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <a href="#" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="fa fa-plus me-2"></i>Tambah Admin
                </a>
            </div>
        </div>

        <!-- Form Pencarian -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Tabel Admin -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $admin)
                                <tr>
                                    <td>{{ $loop->iteration + $admins->firstItem() - 1 }}</td>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-link btn-primary edit" data-bs-toggle="modal" data-bs-target="#editAdminModal"
                                            data-id="{{ $admin->id }}"
                                            data-name="{{ $admin->name }}"
                                            data-email="{{ $admin->email }}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('admin.destroy', $admin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger delete" data-original-title="Remove">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $admins->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control mb-3" placeholder="Nama Admin" required>
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Admin -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editAdminForm" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="text" name="name" id="edit_name" class="form-control mb-3" placeholder="Nama Admin" required>
                    <input type="email" name="email" id="edit_email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="password" id="edit_password" class="form-control" placeholder="Password Baru (kosongkan jika tidak diubah)">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript: Isi Modal Edit -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_password').value = '';

            const form = document.getElementById('editAdminForm');
            form.setAttribute('action', `/admin/${id}`);
        });
    });
     $(document).on('click', '.delete', function(e){
            e.preventDefault();
            let form = $(this).closest("form");

            Swal.fire({
                title: "Apakah kamu yakin?",
                text: "Data ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                    Swal.fire(
                        'Deleted!',
                        'Data berhasil dihapus.',
                        'success'
                    )
                }
            });
        });

});
</script>
@endsection  --}}





  {{--  bisaa  --}}
@extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Data Admin</h3>
                <h6 class="op-7 mb-2">Admin</h6>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                    <a href="#" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#addAdminModal"><i class="fa fa-plus me-2"></i></i>Tambah Admin</a>
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
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($admins as $admin)
                                            <tr>
                                                <td>{{ $loop->iteration + $admins->firstItem() - 1 }}</td>
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>
                                                    <!-- Tombol Edit -->
                                                    <button type="button" class="btn btn-link btn-primary edit" data-bs-toggle="modal" data-bs-target="#editAdminModal"
                                                        data-id="{{ $admin->id }}"
                                                        data-name="{{ $admin->name }}"
                                                        data-email="{{ $admin->email }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <!-- Tombol Hapus -->
                                                    <form action="{{ route('admin.destroy', $admin->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                            <button type="button" data-nama="{{ $admin->name }}" class="btn btn-link btn-danger delete" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="mt-3">
                                        {{ $admins->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Admin-->
    <div  class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addAdminModalLabel">Tambah Data Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                            </svg>
                            </span>
                            <input type="text" name="name" placeholder="Nama Admin" class="form-control ms-2" required>
                        </div>
                    </div>
                </div>
                 <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                    </span>
                                    <input type="email" name="email" placeholder="Email" class="form-control ms-2" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-password"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 10v4" /><path d="M10 13l4 -2" /><path d="M10 11l4 2" /><path d="M5 10v4" /><path d="M3 13l4 -2" /><path d="M3 11l4 2" />
                                        <path d="M19 10v4" />
                                        <path d="M17 13l4 -2" />
                                        <path d="M17 11l4 2" />
                                    </svg>
                                    </span>
                                    <input type="password" name="password" placeholder="Password" class="form-control ms-2">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                                </button>
                                <button class="btn btn-primary">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" />
                                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                    </svg>
                                    Simpan
                                </button>
                        </div>

            </form>
        </div>

        </div>
    </div>
    </div>

    <!-- Modal Edit Admin-->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAdminModalLabel">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editAdminForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="row">
                            <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                </svg>
                                </span>
                                <input type="text" name="name" id="edit_name" placeholder="Nama Admin" class="form-control ms-2" required>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                    </span>
                                    <input type="email" name="email" id="edit_email" placeholder="Email" class="form-control ms-2" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-password"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 10v4" /><path d="M10 13l4 -2" /><path d="M10 11l4 2" /><path d="M5 10v4" /><path d="M3 13l4 -2" /><path d="M3 11l4 2" />
                                        <path d="M19 10v4" />
                                        <path d="M17 13l4 -2" />
                                        <path d="M17 11l4 2" />
                                    </svg>
                                    </span>
                                    <input type="password" name="password" id="edit_password" placeholder="Kosongkan jika tidak diubah" class="form-control ms-2">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                            </button>
                            <button class="btn btn-primary">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" />
                                    <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                </svg>
                                    Simpan
                                </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('myscript')
    <script>
    const editAdminModal = document.getElementById('editAdminModal');
    editAdminModal.addEventListener('show\.bs.modal', function (event) {
    const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const email = button.getAttribute('data-email');

        //document.getElementById('edit_id').value = id;
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_password').value = '';

        const form = document.getElementById('editAdminForm');
        form.setAttribute('action', `/admin/${id}`);

    });

    $(document).on('click', '.delete', function(e){
            e.preventDefault();
            let form = $(this).closest("form");
           let nama = $(this).data("nama");

            Swal.fire({
            title: "Apakah kamu yakin?",
            html: "Data <b>" + nama + "</b> akan dihapus dan tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                    Swal.fire(
                        'Terhapus!',
                        'Data ' + nama + ' berhasil dihapus.',
                        'success'
                    )
                }
            });
        });


    </script>

@endpush

