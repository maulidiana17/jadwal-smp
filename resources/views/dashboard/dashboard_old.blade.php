@extends('layouts.absen')
@section('content')
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<!-- Navbar -->

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <button class="btn p-1" id="toggleSidebar" style="background-color: #890909; color: white; ">
            <ion-icon name="menu-outline" size="small"></ion-icon>
        </button>
        <h2 class="navbar-brand mb-0" style="color: #890909; margin-left: 12px;">
            Dashboard Presensi Siswa
        </h2>
    </div>
  </div>
</nav>
<div class="section" id="user-section">
        <div id="user-detail">
            <div class="avatar">
                @if(!empty(Auth::guard('siswa')->user()) && !empty(Auth::guard('siswa')->user()->foto))
                    @php
                        $path = Storage::url('uploads/siswa/' . Auth::guard('siswa')->user()->foto);
                    @endphp
                    @if(!empty($path))
                        <img src="{{ url($path) }}" alt="Foto Profil" class="imaged w64 rounded"  style="height: 64px; object-fit: cover;">
                    @else
                        <img src="{{ asset('assets/img/sample/avatar/nouser.jpg') }}" alt="Foto Default" class="imaged w64 rounded">
                    @endif
                @else
                    <img src="{{ asset('assets/img/sample/avatar/nouser.jpg') }}" alt="Foto Default" class="imaged w64 rounded">
                @endif
            </div>


        <div id="user-info">
            <h2 id="user-name">
                Halo, {{ Auth::guard('siswa')->user()->nama_lengkap }}!
            </h2>
        </div>
    </div>

</div>


<div class="section" id="presence-section">
    {{--  intro splash  --}}
    <!-- Intro Slider -->
    <div id="introSlider" class="carousel slide" data-bs-ride="carousel" style="margin-top: 0px;">
    <div class="carousel-inner">
        <div class="carousel-item active text-center p-2">
        <div class="bg-white rounded shadow p-1">
            <h5>Selamat Datang</h5>
            <p>Gunakan fitur presensi secara tepat waktu.</p>
        </div>
        </div>
        <div class="carousel-item text-center p-2">
        <div class="bg-white rounded shadow p-1">
            <h5>Ambil Foto</h5>
            <p>Kamera akan digunakan untuk foto presensi.</p>
        </div>
        </div>
        <div class="carousel-item text-center p-2">
        <div class="bg-white rounded shadow p-1">
            <h5>Pastikan Lokasi</h5>
            <p>Presensi hanya valid dalam radius sekolah.</p>
        </div>
        </div>
    </div>
    </div>
    <div class="todaypresence">
        <div class="row">
            <div class="col-6">
                <div class="card gradasired">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                @if ($presensihariini != null)
                                @php
                                    $path = Storage::url('uploads/presensi/'.$presensihariini->foto_masuk);
                                @endphp
                                <img src="{{ url($path) }}" class="image"></img>
                                @else
                                <ion-icon name="camera"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail me-3">
                                <h4 class="presencetitle">Masuk</h4>
                                <span>{{ $presensihariini != null ? $presensihariini->jam_masuk : 'Belum Absen' }}</span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card gradasired">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                @if ($presensihariini != null && $presensihariini->jam_keluar != null)
                                @php
                                    $path = Storage::url('uploads/presensi/'.$presensihariini->foto_keluar);
                                @endphp
                                {{--  <img src="{{ url($path) }}" class="image"></img>  --}}
                                @else
                                <ion-icon name="camera"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle">Pulang</h4>
                                <span>{{ $presensihariini != null && $presensihariini->jam_keluar != null ? $presensihariini->jam_keluar : 'Belum Absen' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <div id="rekappresensi">
    <h3>Rekap Presensi Bulan {{ $namabulan[$bulanini] }} {{ $tahunini }}</h3>
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
   <div class="presencetab mt-2">
    <div class="tab-pane fade show active" id="pilled" role="tabpanel">
        <ul class="nav nav-tabs style1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#qr-code" role="tab">
                    Presensi QR Code
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content mt-2">
        <div class="tab-pane fade show active" id="qr-code" role="tabpanel">
            <ul class="listview image-listview">
                <!-- Jadwal Mapel -->
                <li>
                    <div class="item">
                        <div class="icon-box bg-success">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <div class="in">
                            <div class="fw-bold">Jadwal Mata Pelajaran</div>

                            @if(count($jadwalHariIni) > 0)
                                <div class="mt-2">
                                    @foreach($jadwalHariIni as $jadwal)
                                        <div class="p-2 mb-2 border rounded {{ $jadwalSedangBerlangsung && $jadwalSedangBerlangsung->jam_mulai == $jadwal->jam_mulai ? 'bg-warning' : 'bg-light' }}">
                                            <div class="fw-bold">{{ $jadwal->mapel }}</div>
                                                <div class="small {{ $jadwalSedangBerlangsung && $jadwalSedangBerlangsung->jam_mulai == $jadwal->jam_mulai ? '' : 'text-muted' }} mb-2">
                                                    Guru: {{ $jadwal->nama_guru }} <br>
                                                    Jam: {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                                </div>

                                            <!-- Scan QR di bawah jadwal -->
                                            <div class="d-flex align-items-center">
                                                <a href="/absensi/scan">
                                                    <div class="icon-box bg-primary me-2">
                                                        <ion-icon name="qr-code-outline"></ion-icon>
                                                    </div>
                                                </a>

                                                <div class="flex-grow-1">
                                                    <a href="/absensi/scan"><div>Scan QR</div></a>
                                                    <div class="span">
                                                       <small class="{{ $jadwalSedangBerlangsung && $jadwalSedangBerlangsung->jam_mulai == $jadwal->jam_mulai ? '' : 'text-muted' }}">Status</small>
                                                        <span class="status-absen-badge badge badge-secondary" data-jadwal="{{ $jadwal->jam_mulai }}" data-mapel="{{ $jadwal->mapel }}">Loading...</span>
                                                    </div>
                                                    {{--  <div class="span">
                                                        <small class="{{ $jadwalSedangBerlangsung && $jadwalSedangBerlangsung->jam_mulai == $jadwal->jam_mulai ? '' : 'text-muted' }}">Status</small>

                                                        @if ($statusIzinHariIni)
                                                            @if ($statusIzinHariIni->status == 'i')
                                                                <span class="status-absen-badge badge badge-warning">Izin</span>
                                                            @elseif ($statusIzinHariIni->status == 's')
                                                                <span class="status-absen-badge badge badge-info">Sakit</span>
                                                            @endif
                                                        @elseif ($presensihariini)
                                                            <span class="status-absen-badge badge badge-success">Hadir</span>
                                                        @else
                                                            <span class="status-absen-badge badge badge-secondary">Belum Absen</span>
                                                        @endif
                                                    </div>  --}}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <small class="text-muted">Jadwal tidak tersedia saat ini</small>
                            @endif
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
   </div>
    <div class="presencetab mt-2">
        <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                        Histori Bulan Ini
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content mt-2" style="margin-bottom:100px;">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <ul class="listview image-listview">
                    @foreach($historibulanini as $d)
                    @php
                        $path = Storage::url('uploads/presensi/'.$d->foto_masuk);
                    @endphp
                    <li>
                        <div class="item">
                            <div class="icon-box bg-primary">
                                <ion-icon name="finger-print-outline"></ion-icon>
                            </div>
                            <div class="in">
                                <div>{{ date("Y-m-d", strtotime($d->tgl_absen)) }}</div>
                            </div>

                            <div class="span">
                                <small class="text-muted">Masuk</small>
                                <span class="badge badge-success">{{ $d->jam_masuk }}</span>

                                <small class="text-muted">Pulang</small>
                                <span class="badge badge-danger">
                                    {{ $presensihariini != null && $d->jam_keluar != null ? $d->jam_keluar : 'Belum Absen' }}
                                </span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const badges = document.querySelectorAll('.status-absen-badge');

    badges.forEach(badge => {
        //const jamMulai = badge.getAttribute('data-jadwal');
        //fetch(`/absensi/status?jam_mulai=${jamMulai}`)
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
