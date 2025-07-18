<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Cetak Rekap Bulanan</title>

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
        display: table-header-group;
        background-color: #cac3c3;
        }
        tbody {
        background-color: #ffffff;
        }
        tfoot {
        display: table-footer-group;
        }
        th, td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
        vertical-align: top;
        }
        tr {
        page-break-inside: avoid;
        break-inside: avoid;
        }

    </style>
    </head>
    <body class="A4 landscape">
        <div class="container">
            @php
                $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            @endphp

            <!-- HEADER -->
            <div class="header">
                <img src="{{ asset('assets/img/smp.png') }}" alt="Logo Sekolah">
                    <h2>
                        REKAP PRESENSI SISWA BULAN {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                        SMP NEGERI 1 GENTENG
                    </h2>
            </div>
            <div class="info-sekolah">
                Jl. Bromo No.49, Dusun Krajan, Genteng Kulon, Kec. Genteng, Kabupaten Banyuwangi, Jawa Timur 68465
            </div>

            <!-- TABEL -->
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">NIS</th>
                        <th rowspan="2">Nama Siswa</th>
                        <th rowspan="2">Kelas</th>
                        <th colspan="{{ $jumlahHari }}">Tanggal</th>
                        <th rowspan="2">Jumlah Hadir</th>
                        <th rowspan="2">Jumlah Terlambat</th>
                        <th rowspan="2">Jumlah Izin/Sakit</th>
                        <th rowspan="2">Total Izin/Sakit 6 Bulan</th>
                        {{--  <th rowspan="2">Rentang Izin/Sakit</th>  --}}
                    </tr>
                    <tr>
                        @for ($i = 1; $i <= $jumlahHari; $i++)
                        <th>{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->nis }}</td>
                            <td style="text-align: left;">{{ $d->nama_lengkap }}</td>
                            <td>{{ $d->kelas }}</td>
                        {{--  @for ($i = 1; $i <= $jumlahHari; $i++)
                            @php
                                $field = "tgl_$i";
                                $tglLengkap = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i);
                                $isMerah = array_key_exists($tglLengkap, $tanggalMerah);
                                $keterangan = $isMerah ? $tanggalMerah[$tglLengkap] : '';
                                $jamMasuk = $d->$field;

                                // Cek apakah siswa ini ada pengajuan izin/sakit di tanggal ini
                                $izinHariIni = $izinPerTanggal[$d->nis][$tglLengkap] ?? null;
                                $izinKeterangan = '';
                                if ($izinHariIni) {
                                    $izinKeterangan = $izinHariIni->status === 'i' ? 'IZIN' : 'SAKIT';
                                }
                            @endphp
                            <td style="{{ $isMerah ? 'background-color: #fdd; color: red;' : '' }}">
                                @if($jamMasuk)
                                    {{ $jamMasuk }}
                                @elseif($izinKeterangan)
                                    <span style="color: blue; font-weight: bold;">{{ $izinKeterangan }}</span>
                                @endif
                                @if($isMerah)
                                    <br><small style="font-size: 8px;">{{ $keterangan }}</small>
                                @endif
                            </td>

                        @endfor  --}}
                        @for ($i = 1; $i <= $jumlahHari; $i++)
    @php
        $field = "tgl_$i";
        $tglLengkap = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i);
        $isMerah = array_key_exists($tglLengkap, $tanggalMerah);
        $keterangan = $isMerah ? $tanggalMerah[$tglLengkap] : '';
        $jamMasuk = $d->$field;

        $izinKeterangan = '';

        // Cek rentang izin/sakit per siswa
        $izinRentang = $izinRentangPerSiswa[$d->nis] ?? collect();
        foreach ($izinRentang as $izin) {
            if ($tglLengkap >= $izin->tanggal_awal && $tglLengkap <= $izin->tanggal_izin_akhir) {
                $izinKeterangan = $izin->status === 'i' ? 'IZIN' : 'SAKIT';
                break;
            }
        }
    @endphp

    <td style="{{ $isMerah ? 'background-color: #fdd; color: red;' : '' }}">
        @if($jamMasuk)
            {{ $jamMasuk }}
        @elseif($izinKeterangan)
            <span style="color: blue; font-weight: bold;">{{ $izinKeterangan }}</span>
        @endif
        @if($isMerah)
            <br><small style="font-size: 8px;">{{ $keterangan }}</small>
        @endif
    </td>
@endfor

                        <td>{{ $d->jumlah_hadir }}</td>
                        <td>{{ $d->jumlah_terlambat }}</td>
                        <td>{{ $d->izin_sakit_detail }}</td>
                        <td>{{ $d->total_izin_sakit_6bulan_detail }}</td>
                        {{--  <td>
                            @php
                                $r = $rentangIzinSakit[$d->nis] ?? null;
                            @endphp
                            @if($r)
                                {{ \Carbon\Carbon::parse($r->tanggal_awal)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($r->tanggal_akhir)->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </td>  --}}

                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- TANDA TANGAN -->
            <div style="margin-top: 30px; text-align: right; font-size: 12px;">
                Banyuwangi, {{ date('d-m-Y') }}<br><br><br><br>
                <strong>______________________</strong>
            </div>

            <!-- FOOTER -->
        </div>
    </body>
</html>
