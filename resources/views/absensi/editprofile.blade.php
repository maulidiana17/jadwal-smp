@extends('layouts.absen')

@section('header')
<!-- App Header -->
<div class="appHeader bg-darkred text-light shadow-sm">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Profile</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="container-fluid px-3 px-md-5 mt-5 mb-5">
    {{-- Alert Messages --}}
    <div class="mt-5 position-relative" style="z-index: 999;">
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ Session::get('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-center align-items-start py-4" style="min-height: 80vh;">
        <div class="card shadow-sm w-100" style="max-width: 500px;">
            <div class="card-body p-4">
                <form action="/absensi/{{ $siswa->nis }}/updateprofile" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="{{ $siswa->nama_lengkap }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" name="no_hp" value="{{ $siswa->no_hp }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (opsional)</label>
                        <input type="password" class="form-control" name="password" placeholder="Biarkan kosong jika tidak diganti">
                    </div>

                    <div class="mb-3 text-center">
                        <label for="fileuploadInput" class="form-label d-block">Foto Profil</label>
                        <div id="previewContainer" class="mb-2">
                            <img id="previewImage" src="{{ $siswa->foto ? Storage::url('uploads/siswa/'.$siswa->foto) : asset('assets/img/sample/avatar/nouser.jpg') }}"
                                alt="Preview Foto" class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                        </div>
                        <div class="border p-3 rounded" style="cursor: pointer;" id="fileUploadBox">
                            <ion-icon name="cloud-upload-outline" size="large" class="mb-2"></ion-icon>
                            <div><strong>Klik untuk upload foto</strong></div>
                            <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg" style="display: none;">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="refresh-outline" class="me-1"></ion-icon> Update Profil
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


@section('scripts')
<script>
    document.getElementById('fileUploadBox').addEventListener('click', function () {
        document.getElementById('fileuploadInput').click();
    });

    document.getElementById('fileuploadInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const previewImage = document.getElementById('previewImage');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            previewImage.src = "{{ asset('assets/img/sample/avatar/nouser.jpg') }}";
        }
    });
</script>

<style>
    @media (max-width: 576px) {
        #previewImage {
            width: 80px !important;
            height: 80px !important;
        }

        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>
@endsection
@endsection
