{{--  @extends('layouts.absen')
@section('header')

<div class="appHeader bg-darkred text-light">
    <div class="left">
        <a href="javascript:'" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Profile</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: 4rem;">
    <div class="col">
        @php
            $messagesuccess = Session::get('success');
            $messageerror = Session::get('error');
        @endphp
            @if(Session::get('success'))
                <div class="alert alert-success">
                    {{ $messagesuccess }}
                </div>
            @endif
            @if(Session::get('error'))
            <div class="alert alert-danger">
                {{ $messageerror }}
            </div>
        @endif

    </div>
</div>
<form action="/absensi/{{ $siswa->nis }}/updateprofile" method="POST" enctype="multipart/form-data" style="margin-top: 4rem; max-width: 300px; margin-left: auto; margin-right: auto; padding: 20px; background: #f8f9fa; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding-bottom: 20px;">
    @csrf
    <div class="col">
        <div class="form-group boxed">
            <label for="nama_lengkap">Nama Lengkap</label>
            <div class="input-wrapper">
                <input type="text" class="form-control" value="{{ $siswa->nama_lengkap }}" name="nama_lengkap" placeholder="Nama Lengkap" autocomplete="off">
            </div>
        </div>

        <div class="form-group boxed">
            <label for="no_hp">No. HP</label>
            <div class="input-wrapper">
                <input type="text" class="form-control"  value="{{ $siswa->no_hp }}" name="no_hp" placeholder="No. HP" autocomplete="off">
            </div>
        </div>

        <div class="form-group boxed">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <input type="password" class="form-control" value=""  name="password" placeholder="Password" autocomplete="off">
            </div>
        </div>

        <div class="custom-file-upload" id="fileUpload1" style="width: 220px; height: 100px; margin: auto; text-align: center; border: 1px solid #ccc; border-radius: 5px; padding: 8px; display: flex; align-items: center; justify-content: center;">
            <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg" style="display: none;">
            <label for="fileuploadInput" style="cursor: pointer; font-size: 16px;">
                <span>
                    <strong>
                        <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline" style="font-size: 20px;"></ion-icon>
                        <i>Upload File</i>
                    </strong>
                </span>
            </label>
        </div>


    <div class="form-group boxed">
        <div class="input-wrapper">
            <button type="submit" class="btn btn-primary btn-block">
                <ion-icon name="refresh-outline"></ion-icon>
                    Update
            </button>
        </div>
    </div>
    </div>
</form>
@endsection


  --}}
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
<div class="container">
    {{-- Alert Messages --}}
    <div class="mt-4">
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

    {{-- Card Form --}}
    {{--  <div class="d-flex justify-content-center align-items-start" style="min-height: 80vh; padding-top: 40px;">  --}}
    <div class="d-flex justify-content-center align-items-start" style="min-height: 80vh; padding: 40px 16px 100px;">

        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <div class="card-body p-4">
                <form action="/absensi/{{ $siswa->nis }}/updateprofile" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="{{ $siswa->nama_lengkap }}" required>
                    </div>

                    <!-- No HP -->
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" name="no_hp" value="{{ $siswa->no_hp }}" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (opsional)</label>
                        <input type="password" class="form-control" name="password" placeholder="Biarkan kosong jika tidak diganti">
                    </div>

                    <!-- Upload Foto dengan Preview -->
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

                    <!-- Submit -->
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

{{-- Preview Script --}}
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
@endsection

