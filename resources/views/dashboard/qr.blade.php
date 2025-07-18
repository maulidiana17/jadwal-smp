
@extends('layouts.guru.dashboard')

@section('content')
<!-- Content -->
<div class="container">
    <div class="page-inner">

        <!-- QR Code Section -->
        <div class="row">
            <div class="col-12">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <h2>QR Code untuk Mapel: {{ $guru->mapel }}</h2>
                        <p><strong>Nama:</strong> {{ $guru->user->name }}</p>
                        <p><strong>NIP:</strong> {{ $guru->nip }}</p>
                        <div id="qr-code-container" class="my-4">
                            {!! $qrCode !!}
                        </div>
                        <small class="text-muted d-block">
                            QR code ini akan berubah otomatis setiap 30 menit.
                        </small>
                        <a href="{{ route('guru.qr.download') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-download me-1"></i> Download QR Code
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Absensi Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="alert alert-warning py-2 px-3 mb-3">
                            <small class="text-muted d-block mb-2">
                                Silakan pilih semester dan tahun ajaran untuk mengekspor rekap data absensi ke dalam file Excel.
                            </small>
                        </div>

                        <form method="GET" action="{{ route('qr.export') }}" class="d-flex gap-3 mb-4">
                            <div>
                                <label for="semester">Semester</label>
                                <select name="semester" class="form-control" required>
                                    <option value="1">Semester 1 (Jan–Jun)</option>
                                    <option value="2">Semester 2 (Jul–Des)</option>
                                </select>
                            </div>
                            <div>
                                <label for="tahun">Tahun</label>
                                <input type="number" name="tahun" class="form-control" min="2020" max="2100" required>
                            </div>
                            <div class="d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-success">
                                    Export Excel
                                </button>

                            </div>

                        </form>
                        <div class="alert alert-warning py-2 px-3 mb-3">
                            <small class="d-block text-dark m-0">
                                Pilih rentang tanggal untuk mengekspor rekap data absensi mingguan dalam format Excel.
                            </small>
                        </div>
                        <form action="{{ route('qr.export.mingguan.manual') }}" method="GET" class="mb-3 d-flex align-items-end gap-2">
                            <div>
                                <label for="tanggal_awal">Dari Tanggal</label>
                                <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
                            </div>
                            <div>
                                <label for="tanggal_akhir">Sampai Tanggal</label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
                            </div>
                                <button type="submit" class="btn btn-info">Export Excel</button>
                        </form>

                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="fw-bold">Daftar Siswa Kelas yang Diajar Hari Ini</h5>
                                <div class="alert alert-warning py-2 px-3 mb-3">
                                    <small class="d-block text-dark m-0">
                                        Siswa yang tidak melakukan presensi akan otomatis dihitung sebagai <strong>tidak hadir</strong>.
                                        Guru dapat mengubah status tersebut menjadi <strong>Alfa</strong> jika ketidakhadiran tanpa keterangan.
                                    </small>
                                </div>

                                @if($daftarSiswaKelasHariIni->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mt-3">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>NIS</th>
                                                <th>Nama Lengkap</th>
                                                <th>Kelas</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($daftarSiswaKelasHariIni as $index => $siswa)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $siswa->nis }}</td>
                                                <td>{{ $siswa->nama_lengkap }}</td>
                                                <td>{{ $siswa->kelas }}</td>
                                                <td>
                                                    <span class="badge
                                                        @if($siswa->status == 'Hadir') bg-success
                                                        @elseif($siswa->status == 'Sakit') bg-warning
                                                        @elseif($siswa->status == 'Izin') bg-primary
                                                        @else bg-danger @endif">
                                                        {{ $siswa->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($siswa->status == 'Hadir')
                                                        <form method="POST" action="{{ route('ubah.absen.alfa') }}">
                                                            @csrf
                                                            <input type="hidden" name="nis" value="{{ $siswa->nis }}">
                                                            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                                                            <input type="hidden" name="mapel" value="{{ $siswa->mapel }}">
                                                            <button class="btn btn-sm btn-danger">Ubah ke Alfa</button>
                                                        </form>
                                                    @elseif($siswa->status == 'Alfa')
                                                        <form method="POST" action="{{ route('ubah.absen.hadir') }}">
                                                            @csrf
                                                            <input type="hidden" name="nis" value="{{ $siswa->nis }}">
                                                            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                                                            <input type="hidden" name="mapel" value="{{ $siswa->mapel }}">
                                                            <button class="btn btn-sm btn-success">Ubah ke Hadir</button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">Tidak dapat diubah</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                    <p class="text-muted">Tidak ada siswa terdata.</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Content -->
@endsection

{{--  @push('myscript')
<script>
    function fetchAbsensi() {
        const kelas = document.getElementById('filter-kelas').value;
        const tanggal = document.getElementById('filter-tanggal').value;
        const url = new URL("{{ route('absensi.hariini') }}");
        if (kelas) url.searchParams.append('kelas', kelas);
        if (tanggal) url.searchParams.append('tanggal', tanggal);

        document.getElementById('loading').style.display = 'block';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('absen-body');
                const table = document.getElementById('absen-table');
                const judul = document.getElementById('judul-absen');
                const alert = document.getElementById('alert-absen');
                document.getElementById('loading').style.display = 'none';
                tbody.innerHTML = '';

                const isToday = !tanggal;

                if (tanggal) {
                    const tglFormatted = new Date(tanggal).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                    judul.textContent = `Daftar Siswa yang Sudah Absen pada ${tglFormatted}`;
                } else {
                    judul.textContent = 'Daftar Siswa yang Sudah Absen Hari Ini';
                }

                if (data.length === 0) {
                    alert.style.display = '';
                    table.style.display = 'none';
                } else {
                    alert.style.display = 'none';
                    table.style.display = '';
                    data.forEach((siswa, index) => {
                        const waktu = new Date(siswa.waktu.replace(' ', 'T'));
                        const jam = waktu.toLocaleTimeString('id-ID', { hour12: false });
                        const tanggalFormatted = waktu.toLocaleDateString('id-ID', {
                            weekday: 'long',
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                        tbody.innerHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${siswa.nis}</td>
                                <td>${siswa.nama}</td>
                                <td>${siswa.kelas}</td>
                                <td>${tanggalFormatted}, ${jam}</td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(error => {
                console.error("Gagal mengambil data absensi:", error);
                document.getElementById('loading').style.display = 'none';
            });
    }

    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        fetchAbsensi();
    });

    setInterval(fetchAbsensi, 5000);

    document.getElementById('download-excel').addEventListener('click', function (e) {
        e.preventDefault();
        const kelas = document.getElementById('filter-kelas').value;
        const tanggal = document.getElementById('filter-tanggal').value;

        let url = "{{ route('absensi.exportExcel') }}";
        const params = [];
        if (kelas) params.push(`kelas=${kelas}`);
        if (tanggal) params.push(`tanggal=${tanggal}`);
        if (params.length) url += '?' + params.join('&');

        window.location.href = url;
    });

    fetchAbsensi();
</script>
@endpush  --}}
   {{--  <form id="filter-form" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="filter-kelas">Pilih Kelas</label>
                                    <select id="filter-kelas" class="form-control" name="kelas">
                                        <option value="">Semua Kelas</option>
                                        <optgroup label="Kelas 7">
                                            <option value="7">Semua Kelas 7</option>
                                            @foreach(['A','B','C','D','E','F','G','H','I'] as $sub)
                                            <option value="7{{ $sub }}">7{{ $sub }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Kelas 8">
                                            <option value="8">Semua Kelas 8</option>
                                            @foreach(['A','B','C','D','E','F','G','H','I'] as $sub)
                                            <option value="8{{ $sub }}">8{{ $sub }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Kelas 9">
                                            <option value="9">Semua Kelas 9</option>
                                            @foreach(['A','B','C','D','E','F','G','H','I'] as $sub)
                                            <option value="9{{ $sub }}">9{{ $sub }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filter-tanggal">Pilih Tanggal</label>
                                    <input type="date" id="filter-tanggal" name="tanggal" class="form-control">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <a href="#" id="download-excel" class="btn btn-success">Download Excel</a>
                                </div>
                            </div>
                        </form>  --}}

                        {{--  <div id="loading" style="display: none; font-style: italic;"></div>
                        <h4 class="mt-4 mb-3" id="judul-absen">Daftar Siswa yang Sudah Absen Hari Ini</h4>
                        <div id="alert-absen" class="alert alert-warning" style="{{ $siswaAbsenHariIni->isEmpty() ? '' : 'display:none;' }}">
                            Belum ada siswa yang absen hari ini.
                        </div>  --}}

                        {{--  <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="absen-table" style="{{ $siswaAbsenHariIni->isEmpty() ? 'display:none;' : '' }}">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Waktu Absen</th>
                                    </tr>
                                </thead>
                                <tbody id="absen-body">
                                    @foreach($siswaAbsenHariIni as $index => $siswa)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $siswa->nis }}</td>
                                        <td>{{ $siswa->nama }}</td>
                                        <td>{{ $siswa->kelas }}</td>
                                        <td>{{ \Carbon\Carbon::parse($siswa->waktu)->format('H:i:s Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>  --}}
                           {{--  <form method="GET" action="{{ route('qr.export') }}" class="mb-3 d-flex align-items-end gap-2">
                            <div>
                                <label for="bulan_awal">Bulan Awal</label>
                                <input type="month" name="bulan_awal" id="bulan_awal" class="form-control" required>
                            </div>
                            <div>
                                <label for="bulan_akhir">Bulan Akhir</label>
                                <input type="month" name="bulan_akhir" id="bulan_akhir" class="form-control" required>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success mt-2">Export ke Excel</button>
                            </div>
                        </form>  --}}
