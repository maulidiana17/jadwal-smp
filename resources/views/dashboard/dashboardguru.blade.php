@extends('layouts.guru.dashboard')
@section('content')
    <div class="container">
          <div class="page-inner">
            <div class="row justify-content-center mb-4">
                <div class="col-12">
                    <div class="card card-round">
                        <div class="card-body">
                            <h3 class="fw-bold mb-3">Dashboard Guru</h3>
                            @if(Auth::user()->role === 'guru')
                            <span class="fw-bold">Halo Guru {{ Auth::user()->name }}</span>
                            @elseif(Auth::user()->role === 'admin')
                            <span class="fw-bold">Selamat datang Admin {{ Auth::user()->name }}</span>
                            @endif
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="fw-bold mb-3">Jadwal Mengajar Selama Seminggu</h4>

                                @if(count($jadwalMengajarMinggu) > 0)
                                    @foreach($jadwalMengajarMinggu as $hari => $jadwals)
                                        <h5 class="mt-3">{{ ucfirst($hari) }}</h5>
                                        <ul class="list-group mt-2">
                                            @foreach($jadwals as $jadwal)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $jadwal->mapel }}</strong><br>
                                                        Kelas: {{ $jadwal->nama_kelas }}<br>
                                                        Jam: {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                                    </div>
                                                    <div class="badge bg-success">Mengajar</div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                @else
                                    <small class="text-muted">Tidak ada jadwal mengajar terdata.</small>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                    <div
                                        class="icon-big text-center icon-success bubble-shadow-small"
                                    >
                                        <i class="fas fa-fingerprint"></i>
                                    </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Siswa Hadir</p>
                                        <h4 class="card-title">{{ $jmlhadir }}</h4>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>
                              <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Izin</p>
                                                <h4 class="card-title">{{ $jumlahizin }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                <i class="fas fa-thermometer-half"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Sakit</p>
                                                <h4 class="card-title">{{ $jumlahsakit }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                <i class="far fa-clock"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Terlambat</p>
                                                <h4 class="card-title">{{ $jumlahterlambat }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="mt-4">
                            <h5 class="fw-bold">Daftar Izin Aktif Hari Ini</h5>

                            @if($siswaIzin->count() > 0)
                            <div class="table-responsive mt-2">
                                <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIS</th>
                                            <th>Nama Lengkap</th>
                                            <th>Kelas</th>
                                            <th>Tanggal Izin</th>
                                            <th>Sampai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswaIzin as $index => $izin)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $izin->nis }}</td>
                                            <td>{{ $izin->nama_lengkap }}</td>
                                            <td>{{ $izin->kelas }}</td>
                                            <td>{{ \Carbon\Carbon::parse($izin->tanggal_izin)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($izin->tanggal_izin_akhir)->format('d-m-Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted mt-2">Tidak ada siswa yang sedang izin hari ini.</p>
                            @endif

                            <p class="fw-bold mt-2">Total Izin Aktif Hari Ini: {{ $rekapizin->jumlahizin ?? 0 }} orang</p>
                        </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            {{-- Tabel Daftar Sakit Aktif --}}
                        <div class="mt-4">
                            <h5 class="fw-bold">Daftar Sakit Aktif Hari Ini</h5>

                            @if($siswaSakit->count() > 0)
                            <div class="table-responsive mt-2">
                                {{--  <table class="table table-bordered">  --}}
                                <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIS</th>
                                            <th>Nama Lengkap</th>
                                            <th>Kelas</th>
                                            <th>Tanggal Sakit</th>
                                            <th>Sampai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswaSakit as $index => $sakit)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $sakit->nis }}</td>
                                            <td>{{ $sakit->nama_lengkap }}</td>
                                            <td>{{ $sakit->kelas }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sakit->tanggal_izin)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sakit->tanggal_izin_akhir)->format('d-m-Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted mt-2">Tidak ada siswa yang sedang sakit hari ini.</p>
                            @endif

                            <p class="fw-bold mt-2">Total Sakit Aktif Hari Ini: {{ $rekapizin->jumlahsakit ?? 0 }} orang</p>
                        </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection


