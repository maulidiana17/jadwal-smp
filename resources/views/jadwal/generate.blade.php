@extends('layout.main')

@section('content')
<div class="content-wrapper">
         <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                    <h3>Generate Jadwal (Algoritma Genetika)</h3>

                    
                    <form id="formPreview" method="POST" action="{{ route('jadwal.generatePreview') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Jumlah Populasi</label>
                            <input type="number" name="popSize" class="form-control" value="50" required>
                        </div>

                        <div class="mb-3">
                            <label>Probabilitas Crossover (0.6 - 1)</label>
                            <input type="number" name="crossRate" class="form-control" step="0.01" value="0.7" required>
                        </div>

                        <div class="mb-3">
                            <label>Probabilitas Mutasi (0.1 - 1)</label>
                            <input type="number" name="mutRate" class="form-control" step="0.01" value="0.1" required>
                        </div>

                        <div class="mb-3">
                            <label>Jumlah Generasi</label>
                            <input type="number" name="generations" class="form-control" value="100" required>
                        </div>
                        <button class="btn btn-secondary">🔍 Coba Simulasi</button>
                    </form>

                    {{-- Tambahkan jarak di sini --}}
                    <hr class="my-4">
                    <h4>Atau langsung Generate & Simpan Jadwal</h4>

                    <form d="formGenerate" action="{{ route('jadwal.process') }}" method="POST">
                    @csrf
                        <div class="mb-3">
                            <label>Jumlah Populasi</label>
                            <input type="number" name="popSize" class="form-control" value="100" required>
                        </div>

                        <div class="mb-3">
                            <label>Probabilitas Crossover (0.6 - 1)</label>
                            <input type="number" name="crossRate" class="form-control" step="0.01" value="0.8" required>
                        </div>

                        <div class="mb-3">
                            <label>Probabilitas Mutasi (0.1 - 1)</label>
                            <input type="number" name="mutRate" class="form-control" step="0.01" value="0.2" required>
                        </div>

                        <div class="mb-3">
                            <label>Jumlah Generasi</label>
                            <input type="number" name="generations" class="form-control" value="200" required>
                        </div>

                        <button type="submit" class="btn btn-info">Mulai Generate</button>
                        <a href="{{ route('jadwal.index') }}" class="btn btn-light">Kembali</a>
                    </form>

                    <div id="loading" class="mt-4 text-info" style="display: none;">
                        ⏳ Sedang memproses jadwal, mohon tunggu...
                    </div>
                </div>
              </div>
         </div>
</div>

<script>
    document.getElementById('formGenerate').addEventListener('submit', function () {
        document.getElementById('loading').style.display = 'block';
    });

    document.getElementById('formPreview').addEventListener('submit', function () {
        document.getElementById('loading').style.display = 'block';
    });
</script>


@endsection

{{-- @section('scripts')
<script>
document.getElementById('generateForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    document.getElementById('loading').style.display = 'block';

    fetch("{{ route('jadwal.process') }}", {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
        popSize: parseInt(formData.get('popSize')),
        crossRate: parseFloat(formData.get('crossRate')),
        mutRate: parseFloat(formData.get('mutRate')),
        generations: parseInt(formData.get('generations'))
    })

    })
    .then(res => res.json())
    .then(data => {
        alert(data.message); // tampilkan info berhasil/gagal
        window.location.href = "{{ route('jadwal.index') }}"; // 👉 redirect setelah generate
    })
    .catch(err => console.error('Error:', err));

    });
});
</script>
@endsection --}}


{{-- @extends('layout.main')

@section('content')
<div class="container mt-4">

    <h3>Algoritma Genetika Jadwal</h3>

    <!-- Penjelasan Algoritma -->
    <div class="bg-primary text-white p-3 rounded mb-4">
        <h5>🧬 Garis Besar Dasar Algoritma Genetika:</h5>
        <ol class="mb-0 ps-3">
            <li>[Mulai] Menghasilkan populasi kromosom secara acak (solusi yang sesuai untuk masalah)</li>
            <li>[Fitness] Evaluasi fitness f(x) setiap kromosom x pada populasi</li>
            <li>[Populasi baru] Buat populasi baru dengan:
                <ul>
                    <li>[Seleksi] Pilih dua kromosom induk berdasarkan fitness-nya</li>
                    <li>[Crossover] Bentuk anak baru dari dua orang tua</li>
                    <li>[Mutasi] Mutasikan posisi gen dalam kromosom</li>
                    <li>[Terima] Tempatkan anak ke populasi baru</li>
                </ul>
            </li>
            <li>[Ganti] Populasi lama digantikan oleh populasi baru</li>
            <li>[Test] Jika kondisi akhir terpenuhi, berhenti dan kembalikan solusi terbaik</li>
            <li>[Loop] Kembali ke langkah 2</li>
        </ol>
    </div>

    <!-- Form Parameter -->
    <form action="{{ route('jadwal.generate.process') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-3">
                <label for="popSize" class="form-label fw-bold">Jumlah individu *</label>
                <input type="number" name="popSize" id="popSize" class="form-control" value="10" min="2" required>
                <small class="text-muted">Jumlah individu yang ingin dibangkitkan</small>
            </div>

            <div class="col-md-3">
                <label for="crossRate" class="form-label fw-bold">Probabilitas CrossOver *</label>
                <input type="number" step="0.1" min="0.6" max="1" name="crossRate" id="crossRate" class="form-control" value="0.9" required>
                <small class="text-muted">Nilai proses pindah silang (0.6 - 1)</small>
            </div>

            <div class="col-md-3">
                <label for="mutRate" class="form-label fw-bold">Probabilitas Mutasi *</label>
                <input type="number" step="0.1" min="0.1" max="1" name="mutRate" id="mutRate" class="form-control" value="0.4" required>
                <small class="text-muted">Nilai proses mutasi (0.1 - 1)</small>
            </div>

            <div class="col-md-3">
                <label for="generations" class="form-label fw-bold">Jumlah Generasi *</label>
                <input type="number" name="generations" id="generations" class="form-control" value="10" min="1" required>
                <small class="text-muted">Jumlah generasi untuk iterasi</small>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-cogs"></i> Generate Jadwal
            </button>
            <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection --}}
