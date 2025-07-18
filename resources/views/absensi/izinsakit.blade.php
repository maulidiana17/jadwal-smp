
@extends('layouts.admin.dashboard')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Pengajuan Data Izin/Sakit</h3>
                <h6 class="op-7 mb-2">Siswa</h6>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="/absensi/izinsakit" method="GET" class="mb-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input type="text" name="nama_siswa" class="form-control" placeholder="Nama Siswa" value="{{ request('nama_siswa') }}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Dari Tanggal</th>
                                                <th>Sampai Tanggal</th>
                                                <th>Jumlah Hari</th>
                                                <th>NIS</th>
                                                <th>Nama Siswa</th>
                                                <th>Kelas</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                                <th>File</th>
                                                <th>Status Approved</th>
                                                <th>Catatan Penolakan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($izinsakit as $s)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ date('d-m-Y', strtotime($s->tanggal_izin)) }}</td>
                                                <td>{{ date('d-m-Y', strtotime($s->tanggal_izin_akhir)) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($s->tanggal_izin)->diffInDays($s->tanggal_izin_akhir) + 1 }} hari</td>
                                                <td>{{ $s->nis }}</td>
                                                <td>{{ $s->nama_lengkap }}</td>
                                                <td>{{ $s->kelas }}</td>
                                                <td>{{ $s->status == "i" ? "Izin" : "Sakit" }}</td>
                                                <td>{{ $s->keterangan }}</td>
                                                <td>
                                                    @if ($s->file_surat)
                                                        {{--  <a href="{{ asset('storage/surat/' . $s->file_surat) }}" target="_blank" class="btn btn-sm btn-info">Lihat Surat</a>  --}}
                                                        <a href="{{ asset('storage/uploads/surat/' . $s->file_surat) }}" target="_blank">Lihat Surat</a>
                                                    @else
                                                        <span class="text-muted">Belum ada</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($s->status_approved==1)
                                                        <span class="badge bg-success">Disetujui</span>
                                                    @elseif ($s->status_approved==2)
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @elseif ($s->status_approved == 3)
                                                        <span class="badge bg-info">Perlu Perbaikan</span>

                                                    @else
                                                        <span class="badge bg-warning">Menunggu Surat</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{--  @if($s->status_approved == 2)
                                                        {{ $s->catatan_penolakan }}
                                                    @else  --}}
                                                    @if($s->status_approved == 2 || $s->status_approved == 3)
                                                            {{ $s->catatan_penolakan }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        @if($s->status_approved==0)
                                                        <button class="btn btn-sm btn-primary px-1 py-0 d-inline-flex align-items-center btn-approve" data-id="{{ $s->id }}">
                                                            Edit
                                                        </button>
                                                        @else
                                                        <a href="/absensi/{{ $s->id }}/batalkanizinsakit" class="btn btn-sm btn-danger px-1 py-0 d-inline-flex align-items-center">
                                                            Batalkan
                                                        </a>
                                                        @endif
                                                        <form action="/absensi/{{ $s->id }}/hapusizinsakit" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" data-nama="{{ $s->nama_lengkap }}" class="btn btn-link btn-danger deletee" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-3">
                                        {{ $izinsakit->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Approve --}}
        <div class="modal fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="/absensi/approvedizinsakit" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Approval Izin/Sakit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_izinsakit_form" id="id_izinsakit_form">
                            <div class="form-group">
                                <label for="status_approved">Status Persetujuan</label>
                                <select name="status_approved" id="status_approved" class="form-select">
                                    <option value="1">Disetujui</option>
                                    <option value="2">Ditolak</option>
                                    <option value="3">Perlu Perbaikan</option>
                                </select>
                            </div>
                            <div class="form-group mt-3" id="catatan_penolakan_group" style="display: none;">
                                <label for="catatan_penolakan">Catatan Penolakan</label>
                                <textarea name="catatan_penolakan" id="catatan_penolakan" class="form-control" rows="3"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('myscript')
<script>
$(document).ready(function(){
    // Tombol Edit per baris
    $(".btn-approve").click(function(){
        var id = $(this).data("id");
        $("#id_izinsakit_form").val(id);
        $("#modal-izinsakit").modal("show");
    });

    $("#status_approved").change(function(){
        if($(this).val() == "2" || $(this).val() == "3"){
            $("#catatan_penolakan_group").show();
        } else {
            $("#catatan_penolakan_group").hide();
            $("#catatan_penolakan").val('');
        }
    });
        $(document).on('click', '.deletee', function(e){
    e.preventDefault();
    let form = $(this).closest("form");
    let nama = $(this).data("nama");

    Swal.fire({
        title: "Apakah kamu yakin?",
        html: "Data <b>" + nama + "</b> akan dihapus dan tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
            Swal.fire(
                'Terhapus!',
                'Data ' + nama + ' berhasil dihapus.',
                'success'
            )
        }
    });
});

});
</script>
@endpush
