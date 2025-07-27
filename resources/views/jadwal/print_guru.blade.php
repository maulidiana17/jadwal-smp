<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Guru</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        h3 {
            text-align: center;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #eee;
        }
        .print-btn {
            display: block;
            margin: 10px auto;
            padding: 8px 15px;
            font-size: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

    <h3>Jadwal Mengajar Guru: {{ $guru->nama }}</h3>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Kelas</th>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                    <th colspan="8">{{ $hari }}</th>
                @endforeach
            </tr>
            <tr>
                @for($i = 0; $i < 6; $i++)
                    @for($j = 1; $j <= 8; $j++)
                        <th>{{ $j }}</th>
                    @endfor
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $kelas => $dataHari)
                <tr>
                    <td>{{ $kelas }}</td>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                        @for($jam = 1; $jam <= 8; $jam++)
                            @php
                                if ($hari == 'Senin' && in_array($jam, [1, 2])) {
                                    $bg = '#eee';
                                    $text = 'Up Lit';
                                } elseif (isset($dataHari[$hari][$jam])) {
                                    $bg = $dataHari[$hari][$jam]['color'];
                                    $text = $dataHari[$hari][$jam]['teks'];
                                } else {
                                    $bg = '#fff';
                                    $text = '';
                                }
                            @endphp
                            <td style="background-color: {{ $bg }}">{!! $text !!}</td>
                        @endfor
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>