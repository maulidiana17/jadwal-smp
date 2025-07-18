{{--  @php
    function selisih($jam_masuk, $jam_keluar)
{
    list($h, $m, $s) = explode(":", $jam_masuk);
    $dtAwal = mktime($h, $m, $s, "1", "1", "1");
    list($h, $m, $s) = explode(":", $jam_keluar);
    $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
    $dtSelisih = $dtAkhir - $dtAwal;
    $totalmenit = $dtSelisih / 60;
    $jam = explode(".", $totalmenit / 60);
    $sisamenit = ($totalmenit / 60) - $jam[0];
    $sisamenit2 = $sisamenit * 60;
    $jml_jam = $jam[0];
    return $jml_jam . ":" . round($sisamenit2);
}
@endphp  --}}


@foreach($absensi as $d)
@php
    $foto_masuk = Storage::url('uploads/presensi/'.$d->foto_masuk);
@endphp
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->nis }}</td>
        <td>{{ $d->nama_lengkap }}</td>
        <td>{{ $d->kelas }}</td>
        <td>{{ $d->jam_masuk }}</td>
        <td>
             {{--  <img src="{{ url($foto_masuk) }}" alt="" class="img-fluid ms-4" style="max-width: 100px;">  --}}
              @if($d->foto_masuk)
                @php
                    $foto_masuk = Storage::url('uploads/presensi/'.$d->foto_masuk);
                @endphp
                <img src="{{ url($foto_masuk) }}" alt="" class="img-fluid ms-4" style="max-width: 100px;">
            @else
                <span class="badge bg-secondary">Tidak Ada Foto</span>
            @endif
        </td>
        <td>{!! $d->jam_keluar != null ? $d->jam_keluar : '<span class="badge bg-danger"> Belum Absen </span>' !!}</td>
        <td>
            @if($d->jam_masuk >= '07:45')
            @php
                $jamterlambat = selisih('07:45:00', $d->jam_masuk);
            @endphp
                <span class="badge bg-danger">Terlambat {{ $jamterlambat }}</span>
            @else
                <span class="badge bg-success">Tepat Waktu</span>
            @endif
        </td>
        <td>
            <a href="#" class="btn btn-primary showmap" id="{{ $d->id }}">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7.5" /><path d="M9 4v13" /><path d="M15 7v5.5" /><path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                    <path d="M19 18v.01" />
                </svg>
            </a>
        </td>
    </tr>
@endforeach

<tr>
    <td colspan="9">
        <div class="d-flex justify-content-center">
            {{ $absensi->links('vendor.pagination.bootstrap-5') }}
        </div>
    </td>
</tr>

    <script>
        $(function() {
            $(".showmap").click(function(e) {
            var id = $(this).attr("id");
            $.ajax({
                type: 'POST',
                url: '/showmap',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                cache: false,
                success: function(respond){
                    $("#loadmap").html(respond);
                }
            });
            $("#modal-showmap").modal("show");
        });
        });
    </script>
