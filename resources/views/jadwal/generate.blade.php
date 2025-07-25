@extends('layout.main')

@section('content')
<div class="content-wrapper">
         <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                    <h3>Generate Jadwal (Genetic Algorithm)</h3>

                    <form id="formGenerate" action="{{ route('jadwal.process') }}" method="POST">
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
</script>


@endsection