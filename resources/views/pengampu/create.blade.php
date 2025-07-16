@extends('layout.main')

@section('content')
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                    <h4 class="card-title mb-0">Tambah Data Pengampu</h4>

                        <form action="{{ route('pengampu.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="guru_id" class="form-label">Guru</label>
                                <select name="guru_id" id="guru_id" class="form-select select2" required>
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach ($guruList as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                                <select name="mapel_id" id="mapel_id" class="form-select select2" required>
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach ($mapelList as $mapel)
                                        <option value="{{ $mapel->id }}">{{ $mapel->mapel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kelas_ids" class="form-label">Kelas yang Diampu</label>
                                <select name="kelas_ids[]" id="kelas_ids" class="form-select select2" multiple required>
                                    @foreach ($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Gunakan Ctrl untuk memilih lebih dari satu kelas.</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('pengampu.index') }}" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $('.select2').select2({ width: '100%' });
</script>
@endpush
