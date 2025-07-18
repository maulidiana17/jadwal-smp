{{--  @extends('layouts.absen')
@section('header')
<div class="appHeader bg-darkred text-light">
    <div class="left">
        <a href="javascript:'" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Data Izin/Sakit</div>
    <div class="right"></div>
</div>
@endsection

@section('content')

<div class="row" style="margin-top: 65px">
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
<div class="row">
    <div class="col">
        <small class="text-muted">Histori Pengajuan Izin / Sakit</small>

        @foreach($dataizin as $d)
        <ul class="listview image-listview">
            <li>
                <div class="item">
                    <div class="in d-flex justify-content-between align-items-center">
                        <div>
                            <b>{{ date("Y-m-d", strtotime($d->tanggal_izin)) }} ({{ $d->status == "s" ? "Sakit" : "Izin" }})</b>
                            <small class="text-muted ms-2">Keterangan: {{ $d->keterangan }}</small>
                        </div>
                        @if($d->status_approved == 0)
                            <span class="badge bg-warning border rounded w-auto px-2 py-1 shadow-sm">Menunggu Surat</span>
                        @elseif($d->status_approved == 1)
                            <span class="badge bg-success border rounded w-auto px-2 py-1 shadow-sm">Disetujui</span>
                        @elseif($d->status_approved == 2)
                            <span class="badge bg-danger border rounded w-auto px-2 py-1 shadow-sm">Ditolak</span>
                            <br>
                            <small class="text-danger">Catatan: {{ $d->catatan_penolakan }}</small>
                        @elseif($d->status_approved == 3)
                            <span class="badge bg-info border rounded w-auto px-2 py-1 shadow-sm">Perlu Perbaikan</span>
                            <br>
                            <small class="text-primary">Catatan: {{ $d->catatan_penolakan }}</small>
                            <br>
                            <a href="{{ url('/absensi/editizin/'.$d->id) }}" class="btn btn-sm btn-outline-primary mt-2">Perbaiki</a>
                        @endif
                    </div>
                </div>
            </li>
        </ul>
        @endforeach
    </div>
</div>


<div class="fab-button bottom-right" style="margin-bottom: 70px;">
    <a href="/absensi/buatizin" class="fab">
        <ion-icon name="add-circle"></ion-icon>
    </a>
</div>
@endsection  --}}



@extends('layouts.absen')

@section('header')
<!-- App Header -->
<div class="appHeader bg-darkred text-light shadow-sm">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Data Izin / Sakit</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="container px-3" style="padding-top: 70px; padding-bottom: 100px;">

    {{-- Notifikasi --}}
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            {{--  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>  --}}
        </div>
    @endif

    @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::get('error') }}
            {{--  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>  --}}
        </div>
    @endif

    {{-- Judul --}}
    <h6 class="text-muted mb-3">Histori Pengajuan Izin / Sakit</h6>

    {{-- Daftar Izin --}}
    @forelse($dataizin as $d)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ date("Y-m-d", strtotime($d->tanggal_izin)) }}</strong>
                        <span class="badge bg-secondary ms-2">{{ $d->status == 's' ? 'Sakit' : 'Izin' }}</span>
                        <p class="mb-1 mt-1 small text-muted">Keterangan: {{ $d->keterangan }}</p>

                        @if($d->status_approved == 2 || $d->status_approved == 3)
                            <small class="{{ $d->status_approved == 2 ? 'text-danger' : 'text-primary' }}">
                                Catatan: {{ $d->catatan_penolakan }}
                            </small><br>
                        @endif

                        @if($d->status_approved == 3)
                            <a href="{{ url('/absensi/editizin/'.$d->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                                Perbaiki
                            </a>
                        @endif
                    </div>

                    <div class="text-end">
                        @switch($d->status_approved)
                            @case(0)
                                <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                @break
                            @case(1)
                                <span class="badge bg-success">Disetujui</span>
                                @break
                            @case(2)
                                <span class="badge bg-danger">Ditolak</span>
                                @break
                            @case(3)
                                <span class="badge bg-info text-light">Perlu Perbaikan</span>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Belum ada data pengajuan izin/sakit.
        </div>
    @endforelse
</div>

{{-- Floating Button --}}
<div class="fab-button bottom-right" style="margin-bottom: 85px;">
    <a href="/absensi/buatizin" class="fab">
        <ion-icon name="add-circle" size="large"></ion-icon>
    </a>
</div>
@endsection
@push('myscript')
<script>
    setTimeout(function () {
        let alert = document.querySelector('.alert');
        if (alert) {
            let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }
    }, 4000);
</script>
@endpush
