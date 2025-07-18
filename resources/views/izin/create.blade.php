@extends('layouts.absen')
@section('header')


{{--  App Header  --}}
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
<div class="alert alert-warning d-flex align-items-center" role="alert" style="margin-top: 65px">
    <ion-icon name="alert-circle-outline" style="font-size: 1.2rem; margin-right: 8px;"></ion-icon>
    <div>
        <strong>Perhatian!</strong> Jika ingin mengajukan izin atau sakit.
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <form action="/absensi/storeizin" method="POST" id="formizin">
            @csrf
                    <div class="form-group">
                        <label for="tanggal_izin_dari">Dari Tanggal</label>
                        <input type="date" name="tanggal_izin_dari" id="tanggal_izin_dari" class="form-control" placeholder="Dari" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_izin_sampai">Sampai Tanggal</label>
                        <input type="date" name="tanggal_izin_sampai" id="tanggal_izin_sampai" class="form-control" placeholder="Sampai" required>
                    </div>
                     <div class="form-group">
                        <label for="jml_hari">Jumlah Hari</label>
                        <input type="date" name="jml_hari" id="jml_hari" class="form-control" placeholder="Jumlah Hari" required>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" cols="20" rows="7" class="form-control" required></textarea>
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

               });
        });
    </script>

@endpush
