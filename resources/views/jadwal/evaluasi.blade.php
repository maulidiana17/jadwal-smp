@extends('layout.main')

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

@endsection
