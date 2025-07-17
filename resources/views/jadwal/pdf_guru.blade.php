<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Guru</title>
    <style>table, th, td { border: 1px solid black; border-collapse: collapse; padding: 5px; }</style>

    <a href="{{ route('exportExcelGuru', $guru->id) }}" class="btn btn-success">Excel</a>
    <a href="{{ route('exportPDFGuru', $guru->id) }}" class="btn btn-danger">PDF</a>
</head>
<body>
    <h3>Jadwal Guru {{ $jadwal->first()?->guru->nama }}</h3>
    <table width="100%">
        <tr>
            <th>Mapel</th><th>kelas</th><th>Ruangan</th><th>Hari</th><th>Jam</th>
        </tr>
        @foreach ($jadwal as $item)
        <tr>
            <td>{{ $item->mapel->mapel }}</td>
            <td>{{ $item->kelas->nama }}</td>
            <td>{{ $item->ruangan->nama }}</td>
            <td>{{ $item->waktu->hari }}</td>
            <td>Jam ke-{{ $item->waktu->jam_ke }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
