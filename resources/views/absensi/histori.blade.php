@extends('layouts.absen')
@section('header')
{{--  App Header  --}}
<div class="appHeader bg-darkred text-light">
    <div class="left">
        <a href="javascript:'" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Histori Presensi</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
        <div class="col-12">
            <div class="form-group">
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>{{ $namabulan [$i] }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="col-12">
            <div class="form-group">
                <select name="tahun" id="tahun" class="form-control">
                    <option value="">Tahun</option>
                    @php
                        $tahunmulai = 2024;
                        $tahunsekarang = date("Y");
                    @endphp
                    @for($tahun = $tahunmulai; $tahun <= $tahunsekarang; $tahun++)
                        <option value="{{ $tahun }}" {{ date("Y") == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" id="caridata">
                    <ion-icon name="search-outline"></ion-icon>
                        Search
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col" id="showhistori">

    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function(){
        $('#caridata').click(function(e){
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST',
                url: '/gethistori',
                data: {
                    _token:"{{ csrf_token() }}",
                    bulan: bulan,
                    tahun: tahun,
                },
                cache: false,
                success: function(respond){
                    $("#showhistori").html(respond);
                }
            });
        });
    });
</script>
@endpush
