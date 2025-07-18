{{--  @extends('layouts.absen')
@section('header')



<div class="appHeader bg-darkred text-light">
    <div class="left">
        <a href="javascript:'" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Form Izin/Sakit</div>
    <div class="right"></div>
</div>
@endsection

@section('content')


<div class="row" style="margin-top: 70px;">
    <div class="col">
        <form action="/absensi/storeizin" method="POST" id="formizin" enctype="multipart/form-data">
            @csrf
                    <div class="form-group">
                        <label for="tanggal_izin">Dari Tanggal</label>
                        <input type="date" name="tanggal_izin" id="tanggal_izin" class="form-control"required>
                    </div>

                     <div class="form-group">
                        <label for="tanggal_izin_akhir">Sampai Tanggal</label>
                        <input type="date" name="tanggal_izin_akhir" id="tanggal_izin_akhir" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Izin / Sakit</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="i">Izin</option>
                            <option value="s">Sakit</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" cols="20" rows="7" class="form-control" required></textarea>
                    </div>
                     <div class="form-group">
                        <label for="file_surat">Upload Surat Izin / Sakit</label>
                        <input type="file" name="file_surat" id="file_surat" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    </div>


            <div class="form-group">
                <button class="btn btn-primary w-100">kirim</button>

            </div>
        </form>

    </div>
</div>

@endsection

@push('myscript')


<script>
$(document).ready(function(){
    $("#formizin").submit(function(){
        var tanggal_izin = $("#tanggal_izin").val();
        var tanggal_izin_akhir = $("#tanggal_izin_akhir").val();
        var status = $("#status").val();
        var keterangan = $("#keterangan").val();

        if(tanggal_izin == "" || tanggal_izin_akhir == "") {
            Swal.fire({
                title: 'Oops!',
                text: 'Tanggal Mulai dan Tanggal Selesai harus diisi',
                icon: 'warning',
            });
            return false;
        } else if (tanggal_izin_akhir < tanggal_izin) {
            Swal.fire({
                title: 'Oops!',
                text: 'Tanggal Selesai tidak boleh sebelum Tanggal Mulai',
                icon: 'warning',
            });
            return false;
        } else if (status == "") {
            Swal.fire({
                title: 'Oops!',
                text: 'Status harus diisi',
                icon: 'warning',
            });
            return false;
        } else if (keterangan == "") {
            Swal.fire({
                title: 'Oops!',
                text: 'Keterangan harus diisi',
                icon: 'warning',
            });
            return false;
        }
    });
});
</script>
@endpush  --}}
{{--  <script>
        $(document).ready(function(){
            $("#formizin").submit(function(){
                var tanggal_izin = $("#tanggal_izin").val();
                var status = $("#status").val();
                var keterangan = $("#keterangan").val();

                if(tanggal_izin == "") {
                    Swal.fire({
                        title: 'Oops !',
                        text: 'Tanggal Harus Diisi',
                        icon: 'warning',
                      });
                    return false;
                }else if (status == "") {
                    Swal.fire({
                        title: 'Oops !',
                        text: 'Status Harus Diisi',
                        icon: 'warning',
                      });
                    return false;
                }else if (keterangan == "") {
                    Swal.fire({
                        title: 'Oops !',
                        text: 'Keterangan Harus Diisi',
                        icon: 'warning',
                      });
                    return false;
                }
            });
        });
</script>  --}}


@extends('layouts.absen')

@section('header')
<!-- App Header -->
<div class="appHeader bg-darkred text-light shadow-sm">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Form Izin / Sakit</div>
    <div class="right"></div>
</div>
@endsection

@section('content')

<div class="container" style="padding: 80px 16px 100px;">
    <div class="card shadow-sm mx-auto" style="max-width: 480px;">
        <div class="card-body p-4">
            <form action="/absensi/storeizin" method="POST" id="formizin" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="tanggal_izin" class="form-label">Dari Tanggal</label>
                    <input type="date" name="tanggal_izin" id="tanggal_izin" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="tanggal_izin_akhir" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="tanggal_izin_akhir" id="tanggal_izin_akhir" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="i">Izin</option>
                        <option value="s">Sakit</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="4" class="form-control" placeholder="Tuliskan alasan izin atau sakit" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_surat" class="form-label">Upload Surat (wajib)</label>
                    <input type="file" name="file_surat" id="file_surat" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>


                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <ion-icon name="send-outline" class="me-1"></ion-icon> Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $("#formizin").submit(function () {
            var tanggal_izin = $("#tanggal_izin").val();
            var tanggal_izin_akhir = $("#tanggal_izin_akhir").val();
            var status = $("#status").val();
            var keterangan = $("#keterangan").val();

            if (tanggal_izin === "" || tanggal_izin_akhir === "") {
                Swal.fire('Oops!', 'Tanggal mulai dan selesai harus diisi.', 'warning');
                return false;
            } else if (tanggal_izin_akhir < tanggal_izin) {
                Swal.fire('Oops!', 'Tanggal selesai tidak boleh sebelum tanggal mulai.', 'warning');
                return false;
            } else if (status === "") {
                Swal.fire('Oops!', 'Status harus dipilih.', 'warning');
                return false;
            } else if (keterangan === "") {
                Swal.fire('Oops!', 'Keterangan harus diisi.', 'warning');
                return false;
            }
        });
    });
</script>
@endpush
