{{--
@extends('layouts.admin.dashboard')
@section('content')
<div class="container">
    <div class="page-inner">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
          <h3 class="fw-bold mb-3">Tambah Admin</h3>
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif
        </div>
      </div>

      <!-- Button untuk membuka modal -->
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdminModal">
        Tambah Admin
      </button>

      <!-- Modal Create Admin -->
      <div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createAdminModalLabel">Tambah Admin</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label>Nama</label>
                  <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection  --}}




@extends('layouts.admin.dashboard')
@section('content')
<div class="container">
    <div class="page-inner">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
          <h3 class="fw-bold mb-3">Tambah Guru</h3>
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif
        </div>
      </div>

      <!-- Button untuk membuka modal -->
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGuruModal">
        Tambah Guru
      </button>

      <!-- Modal Create Guru -->
      <div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createAdminModalLabel">Tambah Admin</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label>Nama</label>
                  <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection
