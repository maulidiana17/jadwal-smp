{{--  @if($histori->isEmpty())
    <div class="alert alert-warning text-center">
        <p>Belum ada data presensi.</p>
    </div>
@else
    <div class="list-group">
        @foreach($histori as $d)
            @php
                $path = Storage::url('uploads/presensi/' . $d->foto_masuk);
                $isTerlambat = $d->jam_masuk >= "07:45";
            @endphp

            <div class="list-group-item d-flex align-items-center">
                <div>
                    <img src="{{ url($path) }}" alt="image"
                         class="rounded-circle"
                         style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">
                </div>
                <div class="flex-grow-1">
                    <strong>{{ date("l, d M Y", strtotime($d->tgl_absen)) }}</strong>
                    <div class="small text-muted mt-1">
                        Masuk:
                        <span class="badge {{ $isTerlambat ? 'bg-danger' : 'bg-success' }}">
                            {{ $d->jam_masuk }}
                        </span>
                        &nbsp;&nbsp;
                        Pulang:
                        <span class="badge bg-primary">{{ $d->jam_keluar ?? '-' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $histori->links('vendor.pagination.bootstrap-5') }}
    </div>
@endif  --}}
@if($histori->isEmpty())
    <div class="alert alert-warning text-center">
        <p>Belum ada data presensi.</p>
    </div>
@else
    <div class="list-group">
        @foreach($histori as $d)
            @php
                $path = Storage::url('uploads/presensi/' . $d->foto_masuk);
                $isTerlambat = $d->jam_masuk >= "07:45";

                // Konversi hari dan bulan ke Bahasa Indonesia
                $hariList = [
                    'Sunday' => 'Minggu',
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu'
                ];

                $bulanList = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ];

                $tanggal = date("d", strtotime($d->tgl_absen));
                $bulan = $bulanList[date("m", strtotime($d->tgl_absen))];
                $tahun = date("Y", strtotime($d->tgl_absen));
                $hariInggris = date("l", strtotime($d->tgl_absen));
                $hariIndo = $hariList[$hariInggris];
            @endphp

            <div class="list-group-item d-flex align-items-center">
                <div>
                    <img src="{{ url($path) }}" alt="image"
                         class="rounded-circle"
                         style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">
                </div>
                <div class="flex-grow-1">
                    <strong>{{ $hariIndo . ', ' . $tanggal . ' ' . $bulan . ' ' . $tahun }}</strong>
                    <div class="small text-muted mt-1">
                        Masuk:
                        <span class="badge {{ $isTerlambat ? 'bg-danger' : 'bg-success' }}">
                            {{ $d->jam_masuk }}
                        </span>
                        &nbsp;&nbsp;
                        Pulang:
                        <span class="badge bg-primary">{{ $d->jam_keluar ?? '-' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $histori->links('vendor.pagination.bootstrap-5') }}
    </div>
@endif
