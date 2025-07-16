@extends('layout.main')

@section('content')
<div class="content-wrapper">
         <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Jadwal Pelajaran</h3>
                    <a href="{{ route('jadwal.generate') }}" class="btn btn-info">
                        <i class="fa fa-cogs"></i> Genetic Algoritmh 
                    </a>
                </div>

    @if($jadwals->isEmpty())
        <div class="alert alert-warning">
            Jadwal belum ada!!. Silakan klik tombol Algoritmh Genetic untuk memulai proses penjadwalan.
        </div>
    @else

    <!-- Filter Form -->
    <form method="GET" action="{{ route('jadwal.index') }}" class="row g-3 mb-3">
        <div class="col-md-3">
            <select name="kelas_id" class="form-select">
                <option value="">-- Semua Kelas --</option>
                @foreach ($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="hari" class="form-select">
                <option value="">-- Semua Hari --</option>
                @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="guru_id" class="form-select">
                <option value="">-- Semua Guru --</option>
                @foreach ($guruList as $g)
                    <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                        {{ $g->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-grid">
            <button class="btn btn-outline-info btn-sm ">🔍</button>
        </div>
    </form>


    {{-- <div class="mb-3">
        <a href="#" class="mdi mdi-printer">🖨️ Export PDF</a>
        <a href="#" class="mdi mdi-file-excel">📥 Export Excel</a>
    </div> --}}

        <!-- Export Buttons -->
        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
            <!-- Kiri: Tombol Export -->
            <div>
                <a href="#" class="mdi mdi-printer text-danger me-3"> Export PDF</a>
                <a href="#" class="mdi mdi-file-excel text-success me-3"> Export Excel</a>
                <a href="{{ route('jadwal.evaluasi') }}" >
                    <i class="mdi mdi-chart-pie text-warning mb-3"></i> Lihat Evaluasi
                </a>

            </div>

            <!-- Kanan: Tombol Reset -->
            <form action="{{ route('jadwal.reset') }}" method="POST" onsubmit="return confirm('Yakin ingin mereset semua jadwal?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash"></i> Reset Jadwal
                </button>
            </form>
        </div>


        @php
            $kelasToShow = request('kelas_id') 
                ? $kelasList->where('id', request('kelas_id')) 
                : $kelasList;
        @endphp

        @foreach($kelasToShow as $kelas)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    Jadwal Kelas {{ $kelas->nama }}
                </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <p>Total Jadwal Tersimpan: {{ $jadwals->count() }}</p>
                    <table class="table table-bordered text-center m-0">
                        <thead class="table-light">
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Mapel</th>
                                <th>Guru</th>
                                <th>Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwals->where('kelas_id', $kelas->id)->sortBy('waktu.hari')->sortBy('waktu.jam_ke') as $jadwal)
                                <tr>
                                    <td>{{ $jadwal->waktu->hari }}</td>
                                    <td>{{ $jadwal->waktu->jam_ke }} ({{ $jadwal->waktu->jam_mulai }} - {{ $jadwal->waktu->jam_selesai }})</td>
                                    <td>{{ $jadwal->mapel->mapel }}</td>
                                    <td>{{ $jadwal->guru->nama }}</td>
                                    <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
 @endif
 <script>
document.getElementById().addEventListener('click', function () {
    if (!confirm("Yakin ingin generate ulang jadwal?")) return;

    document.getElementById('loading').style.display = 'block';

    fetch("{{ route('jadwal.generate') }}", {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(res => res.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';
        alert(data.message || 'Jadwal berhasil digenerate.');
        location.reload();
    }).catch(err => {
        document.getElementById('loading').style.display = 'none';
        alert("Terjadi kesalahan saat generate jadwal.");
        console.error(err);
    });
});
</script>
                </div>
              </div>
         </div>
</div>
@endsection