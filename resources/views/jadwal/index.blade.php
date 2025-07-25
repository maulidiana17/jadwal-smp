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
        <a href="#" class="mdi mdi-printer">🖨 Export PDF</a>
        <a href="#" class="mdi mdi-file-excel">📥 Export Excel</a>
    </div> --}}

        <!-- Export Buttons -->
        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
            <!-- Kiri: Tombol Export -->
            <div>
                @if(isset($kelas_aktif) && $kelas_aktif->id)
                <a href="{{ route('jadwal.exportPDFKelas', $kelas_aktif->id) }}" class="mdi mdi-printer text-danger me-3"> Export PDF</a>
                <a href="{{ route('jadwal.exportExcelKelas', $kelas_aktif->id) }}" class="mdi mdi-file-excel text-success me-3"> Export Excel</a>
                @endif
                @if(isset($guru_aktif) && $guru_aktif->id)
                    <a href="{{ route('jadwal.exportPDFGuru', $guru_aktif->id) }}" class="mdi mdi-printer text-danger me-3">Export PDF Guru</a>
                    <a href="{{ route('jadwal.exportExcelGuru', $guru_aktif->id) }}" class="mdi mdi-file-excel text-success me-3"> Export Excel Guru</a>
                @endif

                <a href="{{ route('jadwal.evaluasi') }}" >
                    <i class="mdi mdi-chart-pie text-warning mb-3"></i> Lihat Evaluasi
                </a>
                <!-- Kanan: Tombol Reset -->
                
            </div>
                <!-- Link yang memicu modal -->
                <a href="#" class="text-danger" data-toggle="modal" data-target="#modal-reset-jadwal">
                    <i class="bi bi-trash"></i> Reset Jadwal
                </a>

                <!-- Modal -->
                <div class="modal fade" id="modal-reset-jadwal" tabindex="-1" role="dialog" aria-labelledby="modalResetJadwalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalResetJadwalLabel">Konfirmasi Reset Jadwal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin <b>menghapus semua jadwal</b>? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    
                    <div class="modal-footer">
                        <form action="{{ route('jadwal.reset') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Reset Jadwal</button>
                        </form>
                    </div>

                    </div>
                </div>
                </div>

        </div>

      @php
    $kelasToShow = request('kelas_id') 
        ? $kelasList->where('id', request('kelas_id')) 
        : $kelasList;

    $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
@endphp

@foreach($kelasToShow as $kelas)
    @php
        $jadwalKelas = $jadwals->where('kelas_id', $kelas->id);

        $jadwalSorted = $jadwalKelas->sortBy(function ($jadwal) use ($urutanHari) {
            $indexHari = array_search($jadwal->waktu->hari, $urutanHari);
            return sprintf('%02d-%02d', $indexHari, $jadwal->waktu->jam_ke);
        });
    @endphp

    @if($jadwalKelas->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Jadwal Kelas {{ $kelas->nama }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <p class="p-3">Total Jadwal Tersimpan: {{ $jadwalKelas->count() }}</p>
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
                            @foreach($jadwalSorted as $jadwal)
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
    @endif
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