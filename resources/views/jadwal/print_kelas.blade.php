<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Jadwal Pelajaran {{ $kelas->nama }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        body { font-family: Cambria, serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 10px; position: relative; }
        .header img { position: absolute; left: 0; top: 0; width: 60px; height: 60px; }
        .header h2 { margin: 0; font-size: 16px; }
        .info { text-align: center; font-size: 12px; font-style: italic; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px; text-align: center; }
    </style>
</head>
<body class="A4 landscape">
    <div class="header">
        @php
            $path = public_path('sd/images/logo.png');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        @endphp
        <img src="{{ $base64 }}" alt="Logo">
        <h2>
            JADWAL PELAJARAN<br>
            {{ strtoupper($kelas->nama) }}<br>
            SMP NEGERI 1 GENTENG
        </h2>
    </div>
    <div class="info">
        Jl. Bromo No.49, Dusun Krajan, Genteng Kulon, Kec. Genteng, Kabupaten Banyuwangi, Jawa Timur 68465
    </div>

    @php
        $colors = [];
        $colorList = ['#f66', '#6f6', '#66f', '#fc3', '#f6f', '#3cf', '#ccc', '#ff6', '#6ff', '#d9b'];
        $jadwalPerKelas = [];

        foreach ($jadwals as $item) {
            $hari = $item->waktu->hari;
            $jam = $item->waktu->jam_ke;
            $kodeGuru = $item->guru->kode_guru ?? '';
            $mapelId = $item->mapel_id;

            if (!isset($colors[$mapelId])) {
                $colors[$mapelId] = $colorList[array_rand($colorList)];
            }

            $jadwalPerKelas[$jam][$hari] = [
                'kode_guru' => $kodeGuru,
                'color' => $colors[$mapelId]
            ];
        }

        $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    @endphp

    <table>
        <thead>
            <tr>
                <th>Jam ke-</th>
                @foreach($hariList as $hari)
                    <th>{{ $hari }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
    @for($jam = 1; $jam <= 8; $jam++)
        <tr>
            <td>{{ $jam }}</td>
            @foreach($hariList as $hari)
                @php
                    // Kalau Jumat atau Sabtu dan jam >= 6, kasih strip & background abu
                    if (in_array($hari, ['Jumat','Sabtu']) && $jam >= 6) {
                        $bg = '#eee';
                        $text = '-';
                    } elseif ($hari == 'Senin' && in_array($jam, [1, 2])) {
                                $bg = '#ddd';
                                $text = 'Up Lit';
                    } elseif (isset($jadwalPerKelas[$jam][$hari])) {
                        $bg = $jadwalPerKelas[$jam][$hari]['color'];
                        $text = $jadwalPerKelas[$jam][$hari]['kode_guru'];
                    } else {
                        $bg = '#fff';
                        $text = '';
                    }
                @endphp
                <td style="background-color: {{ $bg }}">{{ $text }}</td>
            @endforeach
        </tr>
    @endfor
</tbody>
    </table> 
</body>
</html>
