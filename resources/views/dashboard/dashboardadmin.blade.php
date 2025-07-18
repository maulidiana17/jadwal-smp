@extends('layouts.admin.dashboard')
@section('content')
<div class="container">
    <div class="page-inner">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
          <h3 class="fw-bold mb-3">Dashboard Admin</h3>

          @if(Auth::user()->role === 'guru')
          <span class="fw-bold">Halo Guru {{ Auth::user()->name }}</span>
      @elseif(Auth::user()->role === 'admin')
          <span class="fw-bold">Selamat datang Admin {{ Auth::user()->name }}</span>
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
                    <h4 class="card-title">{{ $rekappresensi->jmlhadir }}</h4>
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
                  <div
                    class="icon-big text-center icon-primary bubble-shadow-small"
                  >
                    <i class="fas fa-calendar-check"></i>
                  </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                  <div class="numbers">
                    <p class="card-category">Izin</p>
                    <h4 class="card-title">{{ is_null($rekapizin->jumlahizin) ? 0 : $rekapizin->jumlahizin }}
                    </h4>
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
                  <div
                    class="icon-big text-center icon-warning bubble-shadow-small"
                  >
                  <i class="fas fa-thermometer-half"></i>
                  </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                  <div class="numbers">
                    <p class="card-category">Sakit</p>
                    <h4 class="card-title">{{ is_null($rekapizin->jumlahsakit) ? 0 : $rekapizin->jumlahsakit }}
                    </h4>
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
                  <div
                    class="icon-big text-center icon-danger bubble-shadow-small"
                  >
                    <i class="far fa-clock"></i>
                  </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                  <div class="numbers">
                    <p class="card-category">Terlambat</p>
                    <h4 class="card-title">{{ $rekappresensi->jmlterlambat }}</h4>
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

      <div class="card">
        <div class="card-body">
            <div class="mt-4">
                <h4>Detail Presensi Hari Ini ({{ date('d-m-Y') }})</h4>

                <div class="table-responsive">
                    <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas</th>
                                <th>Jam Masuk</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasiswa as $index => $siswa)
                            <tr
                            @if($siswa->keterangan == 'Terlambat') class="table-warning"
                            @elseif($siswa->keterangan == 'Izin') class="table-primary"
                            @elseif($siswa->keterangan == 'Sakit') class="table-danger"
                            @elseif($siswa->keterangan == 'Belum Hadir')
                            @endif>
                                <td>{{ ($datasiswa->currentPage() - 1) * $datasiswa->perPage() + $index + 1 }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama_lengkap }}</td>
                                <td>{{ $siswa->kelas }}</td>
                                <td>{{ $siswa->jam_masuk ?? '-' }}</td>
                                <td>
                                    @if($siswa->keterangan == 'Hadir')    <i class="fas fa-fingerprint me-1"></i> Hadir
                                    @elseif($siswa->keterangan == 'Terlambat')   <i class="far fa-clock me-1"></i> Terlambat
                                    @elseif($siswa->keterangan == 'Izin') <i class="fas fa-calendar-check me-1"></i>Izin
                                    @elseif($siswa->keterangan == 'Sakit')  <i class="fas fa-thermometer-half me-1"></i>Sakit
                                    @else <i class="fas fa-exclamation-triangle text-warning"></i>Belum Hadir
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <div class="mt-3">
                            {{ $datasiswa->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
