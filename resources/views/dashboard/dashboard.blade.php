@extends('layouts.absen')

@section('content')

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Navbar -->

<nav class="navbar navbar-dark" style="background-color: #890909;">
     <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <button class="btn btn-light p-1" id="toggleSidebar" style="background-color: #890909; color: white; ">
            <ion-icon name="menu-outline" size="small"></ion-icon>
        </button>
        <h2 class="navbar-brand mb-0 h4" style="color: #ffffff; margin-left: 12px;">
            Dashboard Presensi Siswa
        </h2>
    </div>
  </div>

</nav>

<!-- Profil -->
<div class="container my-3">
    <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm">
        <img src="{{ !empty(Auth::guard('siswa')->user()->foto)
            ? url(Storage::url('uploads/siswa/' . Auth::guard('siswa')->user()->foto))
            : asset('assets/img/sample/avatar/nouser.jpg') }}"
             class="rounded-circle me-3" style="width:64px; height:64px; object-fit:cover; margin-right: 15px;">
        <div>
            <h5 class="mb-0">Halo, {{ Auth::guard('siswa')->user()->nama_lengkap }}!</h5>
            <small class="text-muted">Selamat datang di halaman presensi</small>
        </div>
    </div>
</div>


  <!-- Intro Slider -->
    <div id="introSlider" class="carousel slide" data-bs-ride="carousel" style="margin-top: 0px;">
    <div class="carousel-inner">
        <div class="carousel-item active text-center p-2">
        <div class="rounded shadow p-1" style="background-color: #ffcf30fa;">
            <h5>Selamat Datang</h5>
            <p>Gunakan fitur presensi secara tepat waktu.</p>
        </div>
        </div>
        <div class="carousel-item text-center p-2">
        <div class="rounded shadow p-1" style="background-color: #ffcf30fa;">
            <h5>Ambil Foto</h5>
            <p>Kamera akan digunakan untuk foto presensi.</p>
        </div>
        </div>
        <div class="carousel-item text-center p-2">
        <div class="rounded shadow p-1" style="background-color: #ffcf30fa;">
            <h5>Pastikan Lokasi</h5>
            <p>Presensi hanya valid dalam radius sekolah.</p>
        </div>
        </div>
    </div>
    </div>

<!-- INFO PRESENSI HARI INI -->
<div class="container mt-3">
    <div class="row g-3">
        <!-- Masuk -->
        <div class="col-6">
            <div class="card card-custom p-3 text-center h-100">
                <ion-icon name="log-in-outline" size="large" class="text-success mb-1"></ion-icon>
                <div class="card-title">Masuk</div>
                <div class="card-subtext">
                    @if ($presensihariini != null && $presensihariini->foto_masuk)
                        @php
                            $path = Storage::url('uploads/presensi/'.$presensihariini->foto_masuk);
                        @endphp
                        <img src="{{ url($path) }}" class="image-placeholder" />
                    @else
                        <div class="image-placeholder no-image"></div>
                    @endif
                    {{ $presensihariini->jam_masuk ?? 'Belum Absen' }}
                </div>
            </div>
        </div>

        <!-- Pulang -->
        <div class="col-6">
            <div class="card card-custom p-3 text-center h-100">
                <ion-icon name="log-out-outline" size="large" class="text-danger mb-1"></ion-icon>
                <div class="card-title">Pulang</div>
                <div class="card-subtext">
                    {{-- Tidak tampilkan gambar, tapi tetap beri placeholder agar tingginya sama --}}
                    <div class="image-placeholder no-image"></div>
                    {{ $presensihariini->jam_keluar ?? 'Belum Absen' }}
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<!-- Rekap Presensi -->
<div class="container mb-5">
<div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                       Rekap Presensi Bulan {{ $namabulan[$bulanini] }} {{ $tahunini }}
                    </a>
                </li>
            </ul>
</div>
 <div id="rekappresensi">
   <div class="row">
        <div class="col-3">
            <div class="card">
                <div class="card-body text-center" style="padding: 16px 12px !important">
                    <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; z-index:999;">{{ $rekappresensi->jmlhadir }}</span>
                    <ion-icon name="body" style="font-size: 1.5rem" class="text-primary"></ion-icon>
                    <span style="font-size: 0.8rem; font-weight: bold; color: #4d4f59;">Hadir</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card">
                <div class="card-body text-center" style="padding: 16px 12px !important">
                    <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; z-index:999;">{{ $rekapizin->jumlahizin }}</span>
                    <ion-icon name="newspaper-outline" style="font-size: 1.5rem" class="text-warning"></ion-icon>
                    <span style="font-size: 0.8rem; font-weight: bold; color: #4d4f59;">Izinn</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card">
                <div class="card-body text-center" style="padding: 16px 12px !important">
                    <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; z-index:999;">{{ $rekapizin->jumlahsakit }}</span>
                    <ion-icon name="medkit-outline" style="font-size: 1.5rem" class="text-warning"></ion-icon>
                    <span style="font-size: 0.8rem; font-weight: bold; color: #4d4f59;">Sakit</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card">
                <div class="card-body text-center" style="padding: 16px 12px !important">
                    <span class="badge bg-danger" style="position: absolute; top:3px; right:10px; z-index:999;">{{ $rekappresensi->jmlterlambat }}</span>
                    <ion-icon name="alarm-outline" style="font-size: 1.5rem" class="text-danger"></ion-icon>
                    <span style="font-size: 0.8rem; font-weight: bold; color: #4d4f59;">Telat</span>
                </div>
            </div>
        </div>
    </div>
   </div>


</div>
<!-- Rekap Presensi -->
<div class="container mb-5">
    <div class="tab-pane fade show active" id="pilled" role="tabpanel">
        <ul class="nav nav-tabs style1 mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                    Jadwal Mata Pelajaran Hari Ini
                </a>
            </li>
        </ul>
    </div>

    @if(count($jadwalHariIni))
        <div class="row">
            @foreach($jadwalHariIni as $jadwal)
            <div class="col-12 mb-3">
                <div class="card shadow-sm p-3 {{ $jadwalSedangBerlangsung && $jadwalSedangBerlangsung->jam_mulai == $jadwal->jam_mulai ? 'border-warning bg-warning-subtle' : 'bg-light' }}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- Nama Mapel dan Guru -->
                            <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
                                <h6 class="mb-1">{{ $jadwal->mapel }}</h6>
                                <p class="mb-0 small text-muted">Guru: {{ $jadwal->nama_guru }}</p>
                            </div>

                            <!-- Jam -->
                            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                <p class="mb-0 small text-muted">Jam: {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</p>
                            </div>

                            <!-- Tombol dan Status -->
                            <div class="col-md-3 col-sm-6 text-md-end text-sm-start">
                                <a href="/absensi/scan" class="btn btn-sm btn-danger mb-2">
                                    <ion-icon name="qr-code-outline"></ion-icon> Scan QR
                                </a><br>
                                <small>Status:
                                    <span class="badge badge-success status-absen-badge"
                                          data-jadwal="{{ $jadwal->jam_mulai }}"
                                          data-mapel="{{ $jadwal->mapel }}">
                                          Loading...
                                    </span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="alert rounded text-center" style="background-color: #ffcf30fa; color: #000;">
            Tidak ada jadwal tersedia saat ini.
        </div>
    @endif
</div>


{{--  <div class="container mb-5">
       <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                       Jadwal Mata Pelajaran Hari Ini

                    </a>
                </li>
            </ul>
        </div>

    @if(count($jadwalHariIni))
        @foreach($jadwalHariIni as $jadwal)
        <div class="card mb-3 {{ $jadwalSedangBerlangsung && $jadwalSedangBerlangsung->jam_mulai == $jadwal->jam_mulai ? 'border-warning bg-warning-subtle' : 'bg-light' }} shadow-sm">
            <div class="card-body">
                <h6 class="mb-1">{{ $jadwal->mapel }}</h6>
                <p class="mb-0 small">Guru: {{ $jadwal->nama_guru }}</p>
                <p class="mb-0 small">Jam: {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</p>
                <a href="/absensi/scan" class="btn btn-primary btn-sm mt-2">
                    <ion-icon name="qr-code-outline"></ion-icon> Scan QR
                </a>
                <br>
                <small>Status: <span class="badge badge-secondary status-absen-badge" data-jadwal="{{ $jadwal->jam_mulai }}" data-mapel="{{ $jadwal->mapel }}">Loading...</span></small>
            </div>
        </div>
        @endforeach
    @else
          <div class="alert rounded" style="background-color: #ffcf30fa; color: #000;">
            Tidak ada jadwal tersedia saat ini.
        </div>

    @endif
</div>  --}}

<!-- Histori Presensi Bulan Ini -->
<div class="container mb-5" style="padding-bottom: 80px;">
     <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                        Histori Presensi Bulan Ini
                    </a>
                </li>
            </ul>
    </div>
    <ul class="list-group">
    @foreach($historibulanini as $d)
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ date("d M Y", strtotime($d->tgl_absen)) }}</strong><br>
            <small class="text-muted">Masuk: {{ $d->jam_masuk }}</small><br>
            @if($d->jam_masuk > '10:00')
                <span class="badge bg-danger">Terlambat</span><br>
            @endif
            <small class="text-muted">Pulang: {{ $d->jam_keluar ?? 'Belum Absen' }}</small>
        </div>
        <ion-icon name="finger-print-outline" size="large" class="text-primary"></ion-icon>
        </li>
        @endforeach
    </ul>

     <div class="mt-3">
        {{ $historibulanini->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

@endsection

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const badges = document.querySelectorAll('.status-absen-badge');

    badges.forEach(badge => {
        const jamMulai = badge.getAttribute('data-jadwal');
        const mapel = badge.getAttribute('data-mapel');

        fetch(`/absensi/status?jam_mulai=${jamMulai}&mapel=${encodeURIComponent(mapel)}`)
            .then(res => res.json())
            .then(data => {
                if (data.hadir) {
                    badge.textContent = 'Hadir';
                    badge.classList.remove('badge-secondary', 'badge-danger');
                    badge.classList.add('badge-success');
                } else {
                    badge.textContent = 'Belum Absen';
                    badge.classList.remove('badge-secondary', 'badge-success');
                    badge.classList.add('badge-danger');
                }
            })
            .catch(() => {
                badge.textContent = 'Gagal Cek';
                badge.classList.remove('badge-secondary');
                badge.classList.add('badge-warning');
            });
    });
});
</script>
@endpush
