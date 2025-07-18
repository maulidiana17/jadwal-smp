{{--  @extends('layouts.absen')

@section('header')
<div class="appHeader bg-darkred text-light">
    <div class="left">
        <a href="{{ url('/absensi/izin') }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Perbaiki Pengajuan</div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: 70px;">
    <div class="col">
        <form action="{{ url('/absensi/updateizin') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $izin->id }}">

            <div class="form-group">
                <label>Dari Tanggal</label>
                <input type="date" class="form-control" value="{{ $izin->tanggal_izin }}" readonly>
            </div>

            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input type="date" class="form-control" value="{{ $izin->tanggal_izin_akhir }}" readonly>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" required>{{ $izin->keterangan }}</textarea>
            </div>

            <div class="form-group">
                <label>Upload Ulang Surat (opsional)</label>
                @if($izin->file_surat)
                    <div>
                        <a href="{{ asset('storage/uploads/surat/'.$izin->file_surat) }}" target="_blank">Lihat Surat Sebelumnya</a>
                    </div>
                @endif
                <input type="file" name="file_surat" class="form-control mt-1">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti</small>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Kirim Perbaikan</button>
        </form>
    </div>
</div>
@endsection  --}}



@extends('layouts.absen')

@section('header')
<div class="appHeader bg-darkred text-light">
    <div class="left">
        <a href="{{ url('/absensi/izin') }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Perbaiki Pengajuan</div>
</div>
@endsection

@section('content')
<div class="container" style="margin-top: 70px; padding-bottom: 100px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ url('/absensi/updateizin') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $izin->id }}">

                <!-- Tanggal Mulai -->
                <div class="mb-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" value="{{ $izin->tanggal_izin }}" readonly>
                </div>

                <!-- Tanggal Akhir -->
                <div class="mb-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" value="{{ $izin->tanggal_izin_akhir }}" readonly>
                </div>

                <!-- Keterangan -->
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="4" required>{{ $izin->keterangan }}</textarea>
                </div>

                <!-- Upload Surat -->
                <div class="mb-3">
                    <label class="form-label">Upload Ulang Surat (opsional)</label>
                    @if($izin->file_surat)
                        <div class="mb-1">
                            <a href="{{ asset('storage/uploads/surat/'.$izin->file_surat) }}" target="_blank">📎 Lihat Surat Sebelumnya</a>
                        </div>
                    @endif
                    <input type="file" name="file_surat" class="form-control">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti</small>
                </div>

                <!-- Tombol Submit -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="send-outline" class="me-1"></ion-icon> Kirim Perbaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
