@extends('layout.main')

@section('content')
<div class="content-wrapper">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Evaluasi Hasil Generate Jadwal</h3>
                <a href="{{ route('jadwal.index') }}" class="btn btn-light mb-4">Kembali</a>

                {{-- CHART 1: Total vs Skipped --}}
                <div style="max-width: 500px; margin: auto;">
                    <canvas id="hasilChart"></canvas>
                </div>

                {{-- CHART 2: Conflict --}}
                <div style="max-width: 500px; margin: 40px auto 0;">
                    <canvas id="conflictChart"></canvas>
                </div>

                {{-- Fitness Score --}}
                @if (isset($fitness))
                    <div class="alert alert-success mt-4 text-center">
                        Skor Fitness Terbaik: <strong>{{ $fitness }}</strong>
                    </div>
                @endif

                {{-- Rangkuman --}}
                <div class="mt-4">
                    <h5>Rangkuman:</h5>
                    <ul>
                        <li>Total Jadwal Tersimpan: <strong>{{ $total }}</strong></li>
                        <li>Total Seharusnya (Jam/Minggu): <strong>{{ $expected }}</strong></li>
                        <li>Jadwal Gagal Disimpan (Skipped): <strong>{{ $skipped }}</strong></li>
                        <li>Jadwal Tidak Conflict: <strong>{{ $nonConflictCount }}</strong></li>
                        <li>Jadwal Conflict: <strong>{{ $conflictCount }}</strong></li>
                        <li>➤ Konflik Guru: <strong>{{ $conflictGuru }}</strong></li>
                        <li>➤ Konflik Kelas: <strong>{{ $conflictKelas }}</strong></li>
                        <li>➤ Konflik Ruangan: <strong>{{ $conflictRuangan }}</strong></li>
                    </ul>
                </div>
                @if(count($conflicts) > 0)
                    <div class="mt-5">
                        <h5> Tabel Konflik Detail</h5>
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
                                                <td>
                                                    Guru: {{ $c['guru'] }}<br>
                                                    Kelas: {{ $c['kelas_a'] }}
                                                </td>
                                                <td>
                                                    Guru: {{ $c['guru'] }}<br>
                                                    Kelas: {{ $c['kelas_b'] }}
                                                </td>

                                            @elseif ($c['type'] === 'kelas')
                                                <td>
                                                    Kelas: {{ $c['kelas'] }}<br>
                                                    Mapel: {{ $c['mapel_a'] }}
                                                </td>
                                                <td>
                                                    Kelas: {{ $c['kelas'] }}<br>
                                                    Mapel: {{ $c['mapel_b'] }}
                                                </td>

                                            @elseif ($c['type'] === 'ruangan')
                                                <td>
                                                    Ruangan: {{ $c['ruangan'] }}<br>
                                                    Kelas: {{ $c['kelas_a'] }}
                                                </td>
                                                <td>
                                                    Ruangan: {{ $c['ruangan'] }}<br>
                                                    Kelas: {{ $c['kelas_b'] }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart 1: Berhasil vs Skipped
    const hasilCtx = document.getElementById('hasilChart').getContext('2d');
    new Chart(hasilCtx, {
        type: 'pie',
        data: {
            labels: ['Berhasil Disimpan', 'Gagal (Skipped)'],
            datasets: [{
                data: [{{ $total ?? 0 }}, {{ $skipped ?? 0 }}],
                backgroundColor: ['#4CAF50', '#F44336']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Hasil Generate Jadwal (Inserted vs Skipped)'
                }
            }
        }
    });

    // Chart 2: Conflict vs Tidak Conflict
    const conflictCtx = document.getElementById('conflictChart').getContext('2d');
    new Chart(conflictCtx, {
        type: 'pie',
        data: {
            labels: ['Tidak Conflict', 'Conflict'],
            datasets: [{
                data: [{{ $nonConflictCount ?? 0 }}, {{ $conflictCount ?? 0 }}],
                backgroundColor: ['#2196F3', '#FF9800']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Jadwal Conflict vs Tidak Conflict'
                }
            }
        }
    });
</script>
@endsection

{{-- @extends('layout.main')

@section('content')
    <div class="content-wrapper">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-4">Evaluasi Hasil Generate Jadwal</h3>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-light">Kembali</a>
                    <div style="max-width: 400px; margin: auto;">
                        <canvas id="jadwalChart"></canvas>
                    </div>
                    @if(isset($fitness))
                        <div class="alert alert-success mt-3">
                            Skor Fitness Terbaik: <strong>{{ $fitness }}</strong>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>Rangkuman:</h5>
                        <ul>
                            <li>Total Jadwal: {{ $total }}</li>
                            <li>Jadwal Tidak Conflict: {{ $nonConflictCount }}</li>
                            <li>Jadwal Conflict: {{ $conflictCount }}</li>
                            'conflictGuru' => $conflictGuru,
            'conflictKelas' => $conflictKelas,
            'conflictRuangan' => $conflictRuangan,
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const berhasil = {{ $total ?? 0 }};
            const gagal = {{ $skipped ?? 0 }};

            const ctx = document.getElementById('jadwalChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Berhasil Disimpan', 'Gagal (Konflik)'],
                    datasets: [{
                        label: 'Evaluasi Jadwal',
                        data: [berhasil, gagal],
                        backgroundColor: ['#4CAF50', '#F44336']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Hasil Generate Jadwal (Inserted vs Skipped)'
                        }
                    }
                }
            });
        </script>

@endsection --}}
