<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Jadwal Pelajaran SMPN 1 Genteng</title>

    <!-- Normalize & Paper CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }

        body {
            font-family: Cambria, serif;
            background-color: #ffffff;
        }

        .container {
            width: 100%;
            padding: 10mm;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            position: absolute;
            left: 0;
            width: 80px;
            height: 80px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            line-height: 1.4;
        }

        .info-sekolah {
            font-size: 12px;
            text-align: center;
            font-style: italic;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        thead {
            background-color: #ccc;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: top;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>
<body class="A4 landscape">
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            @php
                $path = public_path('sd/images/logo.png');
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            @endphp
            <img src="{{ $base64 }}" alt="Logo Sekolah">
            <h2>
                DAFTAR JADWAL PELAJARAN<br>
                SMP NEGERI 1 GENTENG
            </h2>
        </div>

        <div class="info-sekolah">
            Jl. Bromo No.49, Dusun Krajan, Genteng Kulon, Kec. Genteng, Kabupaten Banyuwangi, Jawa Timur 68465
        </div>

        <!-- TABEL JADWAL -->
        {{-- <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Hari</th>
                    <th>Jam ke-</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwals as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kelas->nama }}</td>
                    <td>{{ $item->waktu->hari }}</td>
                    <td>{{ $item->waktu->jam_ke }}</td>
                    <td class="text-left">{{ $item->mapel->mapel }}</td>
                    <td class="text-left">{{ $item->guru->nama }}</td>
                    <td>{{ $item->ruangan->nama }}</td>
                </tr>
                @endforeach
            </tbody>
        </table> --}}
        <div style="overflow-x: auto;">
    <table style="width: 100%; font-size: 10px; table-layout: fixed;" border="1" cellspacing="0" cellpadding="2">
            <thead>
                <tr>
                    <th rowspan="2">Kelas</th>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                        <th colspan="8">{{ $hari }}</th>
                    @endforeach
                </tr>
                <tr>
                    @for($i = 0; $i < 6; $i++) {{-- 6 hari --}}
                        @for($j = 1; $j <= 8; $j++)
                            <th>{{ $j }}</th>
                        @endfor
                    @endfor
                </tr>
            </thead>
            <tbody>
                @php
                    $colors = [];
                    $colorList = ['#f66', '#6f6', '#66f', '#fc3', '#f6f', '#3cf', '#ccc', '#ff6', '#6ff', '#d9b'];
                    $jadwalPerKelas = [];

                    foreach ($jadwals as $item) {
                        $kls = $item->kelas->nama;
                        $hari = $item->waktu->hari;
                        $jam = $item->waktu->jam_ke;

                        $kodeGuru = $item->guru->kode_guru ?? '';
                        $mapelId = $item->mapel_id;

                        if (!isset($colors[$mapelId])) {
                            $colors[$mapelId] = $colorList[array_rand($colorList)];
                        }

                        $jadwalPerKelas[$kls][$hari][$jam] = [
                            'kode_guru' => $kodeGuru,
                            'color' => $colors[$mapelId]
                        ];
                    }
                @endphp

                @foreach($jadwalPerKelas as $kelas => $dataHari)
                    <tr>
                        <td>{{ $kelas }}</td>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                            @for($jam = 1; $jam <= 8; $jam++)
                                @php
                                    if ($hari == 'Senin' && in_array($jam, [1, 2])) {
                                        $bg = '#ddd';
                                        $text = 'Up Lit';
                                    } elseif (isset($dataHari[$hari][$jam])) {
                                        $bg = $dataHari[$hari][$jam]['color'];
                                        $text = $dataHari[$hari][$jam]['kode_guru'];
                                    } else {
                                        $bg = '#fff';
                                        $text = '';
                                    }
                                @endphp
                                <td style="background-color: {{ $bg }}">{{ $text }}</td>
                            @endfor
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
