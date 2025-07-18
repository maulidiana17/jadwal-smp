{{--  <table style="width: 350%; border-collapse: collapse; margin-bottom: 20px;">
    <tr>
        <td style="width: 100px;">NIS</td>
        <td style="width: 10px;">:</td>
        <td>{{ $siswa->nis }}</td>
    </tr>
    <tr>
        <td>Nama Siswa</td>
        <td>:</td>
        <td>{{ $siswa->nama_lengkap }}</td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $siswa->kelas }}</td>
    </tr>
    <tr>
        <td>No. HP</td>
        <td>:</td>
        <td>{{ $siswa->no_hp }}</td>
    </tr>
</table>

<table style="width: 100%; border-collapse: collapse;">
    <tr style="background-color: #f2f2f2;">
        <th style="border: 1px solid #000; padding: 8px;">No.</th>
        <th style="border: 1px solid #000; padding: 8px;">Tanggal</th>
        <th style="border: 1px solid #000; padding: 8px;">NIS</th>
        <th style="border: 1px solid #000; padding: 8px;">Jam Masuk</th>
        <th style="border: 1px solid #000; padding: 8px;">Jam Pulang</th>
        <th style="border: 1px solid #000; padding: 8px;">Keterangan</th>
    </tr>
    @if(count($absensi) > 0)
        @foreach($absensi as $d)
            @php
                $jamterlambat = selisih('07:00:00', $d->jam_masuk)
            @endphp
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ date("d-m-Y", strtotime($d->tgl_absen)) }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $d->nis }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $d->jam_masuk }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $d->jam_keluar != null ? $d->jam_keluar : 'Belum Absen' }}</td>
                <td style="border: 1px solid #000; padding: 6px;">
                    @if($d->jam_masuk > '07:00')
                        Terlambat {{ $jamterlambat }}
                    @else
                        Tepat Waktu
                    @endif
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center;">
                Tidak ada data absensi.
            </td>
        </tr>
    @endif
</table>  --}}
<table style="width: 350%; border-collapse: collapse; margin-bottom: 20px;">
    <tr>
        <td style="width: 100px;">NIS</td>
        <td style="width: 10px;">:</td>
        <td>{{ $siswa->nis }}</td>
    </tr>
    <tr>
        <td>Nama Siswa</td>
        <td>:</td>
        <td>{{ $siswa->nama_lengkap }}</td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $siswa->kelas }}</td>
    </tr>
    <tr>
        <td>No. HP</td>
        <td>:</td>
        <td>{{ $siswa->no_hp }}</td>
    </tr>
</table>

<table style="width: 100%; border-collapse: collapse;">
    <tr style="background-color: #f2f2f2;">
        <th style="border: 1px solid #000; padding: 8px;">No.</th>
        <th style="border: 1px solid #000; padding: 8px;">Tanggal</th>
        <th style="border: 1px solid #000; padding: 8px;">NIS</th>
        <th style="border: 1px solid #000; padding: 8px;">Jam Masuk</th>
        <th style="border: 1px solid #000; padding: 8px;">Jam Pulang</th>
        <th style="border: 1px solid #000; padding: 8px;">Keterangan</th>
    </tr>

    @if(count($tanggalData) > 0)
        @php $no = 1; @endphp
        @foreach($tanggalData as $data)
            @php
                $jamterlambat = isset($data['jam_masuk']) ? selisih('07:45:00', $data['jam_masuk']) : null;
            @endphp
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $no++ }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ date("d-m-Y", strtotime($data['tgl'])) }}</td>
                <td style="border: 1px solid #000; padding: 6px;">{{ $data['nis'] }}</td>
                <td style="border: 1px solid #000; padding: 6px;">
                    {{ $data['jam_masuk'] ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px;">
                    {{ $data['jam_keluar'] ?? '-' }}
                </td>
                <td style="border: 1px solid #000; padding: 6px;">
                    @if($data['status'] == 'absen')
                        @if(isset($data['jam_masuk']) && $data['jam_masuk'] > '07:45')
                            Terlambat {{ $jamterlambat }}
                        @else
                            Tepat Waktu
                        @endif
                    @elseif($data['status'] == 'izin')
                        Izin
                    @elseif($data['status'] == 'sakit')
                        Sakit
                    @endif
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center;">
                Tidak ada data.
            </td>
        </tr>
    @endif
</table>
