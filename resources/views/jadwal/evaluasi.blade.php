@extends('layout.main')

@section('content')
<div class="content-wrapper">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Evaluasi Hasil Generate Jadwal</h3>
                <a href="{{ route('jadwal.index') }}" class="btn btn-light mb-4">Kembali</a>

                {{-- Charts --}}
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="hasilChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="conflictChart"></canvas>
                    </div>
                </div>

                {{-- Fitness Score --}}
                @if (isset($fitness))
                    <div class="alert alert-success mt-4 text-center">
                        Skor Fitness Terbaik: <strong>{{ $fitness }}</strong>
                    </div>
                @endif

                {{-- Rangkuman --}}
                <div class="mt-4">
                    <h5>Rangkuman</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li>Total Jadwal Tersimpan: <strong>{{ $total }}</strong></li>
                                <li>Jadwal Gagal Disimpan: <strong>{{ $skipped }}</strong></li>
                                {{-- <li>Skipped Teknis: {{ $skippedTeknis }}</li>
                                <li>Skipped Akademik: {{ $skippedAkademik }}</li> --}}
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>Jadwal Tidak Conflict: <strong>{{ $nonConflictCount }}</strong></li>
                                <li>Jadwal Conflict: <strong>{{ $totalConflicts }}</strong></li>
                                <li>Konflik Guru: <strong>{{ $conflictGuru }}</strong></li>
                                <li>Konflik Waktu: <strong>{{ $conflictKelas }}</strong></li>
                                <li>Konflik Ruangan: <strong>{{ $conflictRuangan }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Tabel Konflik --}}
                {{-- @if(count($conflicts) > 0)
                    <div class="mt-5">
                        <h5>Tabel Konflik Detail</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Jenis Konflik</th>
                                        <th>Waktu</th>
                                        <th>Detail 1</th>
                                        <th>Detail 2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($conflicts as $index => $c)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ ucfirst($c['type']) }}</td>
                                            <td>{{ $c['waktu'] }}</td>
                                            @if ($c['type'] === 'guru')
                                                <td>Guru: {{ $c['guru'] }}<br>Kelas: {{ $c['kelas_a'] }}</td>
                                                <td>Guru: {{ $c['guru'] }}<br>Kelas: {{ $c['kelas_b'] }}</td>
                                            @elseif ($c['type'] === 'kelas')
                                                <td>Kelas: {{ $c['kelas'] }}<br>Mapel: {{ $c['mapel_a'] }}</td>
                                                <td>Kelas: {{ $c['kelas'] }}<br>Mapel: {{ $c['mapel_b'] }}</td>
                                            @elseif ($c['type'] === 'ruangan')
                                                <td>Ruangan: {{ $c['ruangan'] }}<br>Kelas: {{ $c['kelas_a'] }}</td>
                                                <td>Ruangan: {{ $c['ruangan'] }}<br>Kelas: {{ $c['kelas_b'] }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif --}}

                {{-- Pengampu Kurang Jam --}}
                {{-- @if($pengampusTidakTerjadwal->count() > 0)
                    <div class="mt-5">
                        <h5>Pengampu yang Kurang Jam</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Guru</th>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Jam Seharusnya</th>
                                        <th>Jam Terjadwal</th>
                                        <th>Jam Kurang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengampusTidakTerjadwal as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $p->guru->nama ?? '-' }}</td>
                                            <td>{{ $p->kelas->nama ?? '-' }}</td>
                                            <td>{{ $p->mapel->mapel ?? '-' }}</td>
                                            <td>{{ $p->jam_seharusnya }}</td>
                                            <td>{{ $p->jam_terjadwal }}</td>
                                            <td>{{ $p->jam_kurang }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif --}}


            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart 1
    new Chart(document.getElementById('hasilChart'), {
        type: 'pie',
        data: {
            labels: ['Berhasil Disimpan', 'Gagal (Skipped)'],
            datasets: [{
                data: [{{ $total ?? 0 }}, {{ $skipped ?? 0 }}],
                backgroundColor: ['#4CAF50', '#F44336']
            }]
        }
    });

    // Chart 2
    new Chart(document.getElementById('conflictChart'), {
        type: 'pie',
        data: {
            labels: ['Tidak Conflict', 'Conflict'],
            datasets: [{
                data: [{{ $nonConflictCount ?? 0 }}, {{ $totalConflicts ?? 0 }}],
                backgroundColor: ['#2196F3', '#FF9800']
            }]
        }
    });
</script>
@endsection
