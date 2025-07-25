@extends('layout.main')

@section('content')
<div class="content-wrapper">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">Edit Pengampu:</h4>
                <p class="mt-2">{{ $pengampuGroup->guru->nama }}</p>

                <form action="{{ route('pengampu.updateMultiple', [$pengampuGroup->guru_id, $pengampuGroup->mapel_id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tambahkan dropdown mapel --}}
                    <div class="mb-3">
                        <label for="mapel_id">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select select2" required>
                            @foreach ($mapelList as $mapel)
                                <option value="{{ $mapel->id }}" {{ $mapel->id == $pengampuGroup->mapel_id ? 'selected' : '' }}>
                                    {{ $mapel->mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kelas --}}
                    <div class="mb-3">
                        <label>Kelas yang Diampu</label>
                        <select name="kelas_ids[]" class="form-select select2" multiple required>
                            @foreach ($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ in_array($kelas->id, $kelasSelected) ? 'selected' : '' }}>
                                    {{ $kelas->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-success">Simpan</button>
                    <a href="{{ route('pengampu.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('.select2').select2({ width: '100%' });
</script>
@endpush
