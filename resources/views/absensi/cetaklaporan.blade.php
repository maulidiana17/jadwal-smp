<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Cetak Laporan </title>
   <link
      rel="icon"
      href="{{ asset('admin/dashboard/assets/img/kaiadmin/favicon.ico') }}"
      type="image/x-icon"
    />

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
    <style>
    @page { size: A4 }
    h2 {
        font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;

    }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">

<section class="sheet padding-10mm">

    <!-- Write HTML just like a web page -->
    <table style="width: 100%">
        <tr>
            <td style="width: 30px">
                <img src= "{{ asset('assets/img/smp.png') }}" width="90" height="90" alt="">
            </td>
            <td>
                <h2 style= "margin: 1%"> LAPORAN PRESENSI SISWA <br> BULAN {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br> SMP NEGERI 1 GENTENG</h2>
                <span><i>Jl. Bromo No.49, Dusun Krajan, Genteng Kulon, Kec. Genteng, Kabupaten Banyuwangi, Jawa Timur 68465</i></span>
            </td>
        </tr>
    </table>
<div style="margin-top: 40px; padding: 0 50px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 100px;">NIS</td>
            <td style="width: 10px;">:</td>
            <td>{{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td style="width: 100px;">Nama Siswa</td>
            <td style="width: 10px;">:</td>
            <td>{{ $siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td style="width: 100px;">Kelas</td>
            <td style="width: 10px;">:</td>
            <td>{{ $siswa->kelas }}</td>
        </tr>
        <tr>
            <td style="width: 100px;">No. HP</td>
            <td style="width: 10px;">:</td>
            <td>{{ $siswa->no_hp }}</td>
        </tr>
    </table>
</div>
<div style="margin-top: 40px; padding: 0 50px;">
   <table style="width: 100%; border-collapse: collapse;">
    <tr style="background-color: #f2f2f2;">
        <th style="border: 1px solid #000; padding: 8px;">No.</th>
        <th style="border: 1px solid #000; padding: 8px;">Tanggal</th>
        <th style="border: 1px solid #000; padding: 8px;">NIS</th>
        <th style="border: 1px solid #000; padding: 8px;">Jam Masuk</th>
        <th style="border: 1px solid #000; padding: 8px;">Jam Pulang</th>
        <th style="border: 1px solid #000; padding: 8px;">Keterangan</th>
    </tr>

    @forelse($tanggalData as $index => $data)
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $loop->iteration }}</td>
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
                    @if($data['jam_masuk'] > '07:45:00')
                        Terlambat {{ selisih('07:45:00', $data['jam_masuk']) }}
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
    @empty
        <tr>
            <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center;">
                Tidak ada data absensi.
            </td>
        </tr>
    @endforelse
</table>

</div>
</section>

</body>

</html>
