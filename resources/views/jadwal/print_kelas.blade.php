<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Kelas</title>
    <style>table, th, td { border: 1px solid black; border-collapse: collapse; padding: 5px; }</style>
    

</head>
<body>
    <h3>Jadwal Kelas {{ $jadwals->first()?->kelas->nama }}</h3>
    <table width="100%">
        <tr>
            <th>Hari</th><th>Jam</th><th>Mapel</th><th>Guru</th><th>Ruangan</th>
        </tr>
        @foreach ($jadwals as $item)
        <tr>
            <td>{{ $item->waktu->hari }}</td>
            <td>Jam ke-{{ $item->waktu->jam_ke }}</td>
            <td>{{ $item->mapel->mapel }}</td>
            <td>{{ $item->guru->nama }}</td>
            <td>{{ $item->ruangan->nama }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
