@extends('layout.main')

@section('content')
<div class="container mt-4">
    <h3>Hasil Generate Jadwal</h3>

    <div class="alert alert-info">
        {{ $message }}
    </div>

    @if(count($conflicts) > 0)
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="text-danger">Daftar Konflik ({{ count($conflicts) }} jadwal tidak bisa disimpan)</h5>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Guru</th>
                        <th>Waktu</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conflicts as $i => $c)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \App\Models\Kelas::find($c['kelas_id'])->nama ?? '-' }}</td>
                        <td>{{ \App\Models\Mapel::find($c['mapel_id'])->mapel ?? '-' }}</td>
                        <td>{{ \App\Models\Guru::find($c['guru_id'])->nama ?? '-' }}</td>
                        <td>
                            @php
                                $w = \App\Models\Waktu::find($c['waktu_id']);
                            @endphp
                            {{ $w ? $w->hari . ' ' . $w->jam_mulai . ' - ' . $w->jam_selesai : '-' }}
                        </td>
                        <td>{{ \App\Models\Ruangan::find($c['ruangan_id'])->nama ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary mt-3">Kembali ke Jadwal</a>
</div>
@endsection
