@extends('layouts.admin.dashboard')
@section('content')
<div class="container">
    <div class="page-inner">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
          <h3 class="fw-bold mb-3">Monitoring Presensi</h3>
          <h6 class="op-7 mb-2">Siswa</h6>
        </div>
      </div>
       <a href="#" id="btn-lihat-map" class="btn btn-success mb-3">
            Lihat Semua Lokasi di Map
        </a>
       <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                   <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                 <div class="form-group">
                                    <div class="input-icon mb-3">
                                        <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-calendar-month"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8 12a1 1 0 0 1 1 1v4a1 1 0 0 1 -2 0v-4a1 1 0 0 1 1 -1" />
                                            <path d="M12 12a1 1 0 0 1 1 1v4a1 1 0 0 1 -2 0v-4a1 1 0 0 1 1 -1" />
                                            <path d="M16 12a1 1 0 0 1 1 1v4a1 1 0 0 1 -2 0v-4a1 1 0 0 1 1 -1" />
                                            <path d="M16 2a1 1 0 0 1 .993 .883l.007 .117v1h1a3 3 0 0 1 2.995 2.824l.005 .176v12a3 3 0 0 1 -2.824 2.995l-.176 .005h-12a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-12a3 3 0 0 1 2.824 -2.995l.176 -.005h1v-1a1 1 0 0 1 1.993 -.117l.007 .117v1h6v-1a1 1 0 0 1 1 -1m3 7h-14v9.625c0 .705 .386 1.286 .883 1.366l.117 .009h12c.513 0 .936 -.53 .993 -1.215l.007 -.16z" />
                                        </svg>
                                        </span>
                                        {{--  <input type="text" value=""{{ date('Y-m-d') }} class="form-control ms-2" id="tanggal" name="tanggal" placeholder="Tanggal Absensi" autocomplete="off">  --}}
                                        <input type="text" value="{{ request('tanggal', date('Y-m-d')) }}" class="form-control ms-2" id="tanggal" name="tanggal" placeholder="Tanggal Absensi" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                        <thead>
                                            <tr>
                                            <th scope="col" class="text-nowrap">No</th>
                                            <th scope="col" class="text-nowrap">NIS</th>
                                            <th scope="col" class="text-nowrap">Nama Siswa</th>
                                            <th scope="col" class="text-nowrap">Kelas</th>
                                            <th scope="col" class="text-nowrap">Jam Masuk</th>
                                            <th scope="col" class="text-nowrap">Foto</th>
                                            <th scope="col" class="text-nowrap">Jam Pulang</th>
                                            <th scope="col" class="text-nowrap">Keterangan</th>
                                            <th scope="col" class="text-nowrap">Lokasi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loadabsensi"> </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- Modal Edit Lokasi-->
<div class="modal fade" id="modal-showmap" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="exampleModalLabel">Lokasi Presensi Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="loadmap">

      </div>
    </div>
  </div>
</div>
@endsection

@push('myscript')
<script>
$(function() {
    function loadabsensi() {
        var tanggal = $('#tanggal').val();
        $.ajax({
            type: 'POST',
            url: '/getabsensi',
            data: {
                _token: "{{ csrf_token() }}",
                tanggal: tanggal
            },
            cache: false,
            success: function(respond) {
                $("#loadabsensi").html(respond);
            }
        });
    }
    $("#tanggal").flatpickr({
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            $('#tanggal').val(dateStr);
            loadabsensi();
        }
    });

    $("#tanggal").change(function(e) {
        loadabsensi();
    });

     $('#btn-lihat-map').click(function(e) {
        e.preventDefault();
        const tanggal = $('#tanggal').val();
        const url = "{{ route('absensi.maps') }}" + "?tanggal=" + tanggal;
        window.location.href = url;
        //window.open(url, '_blank');
    });

    loadabsensi();

});
</script>

@endpush




