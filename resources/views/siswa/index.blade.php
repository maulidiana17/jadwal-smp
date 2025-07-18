@extends('layouts.admin.dashboard')
@section('content')
<div class="container">
    <div class="page-inner">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
          <h3 class="fw-bold mb-3">Data Siswa</h3>
          <h6 class="op-7 mb-2">Siswa</h6>
          <h1>Impor Data Siswa</h1>
          @if(session('success'))
              <div>{{ session('success') }}</div>
          @endif

            @if (session('error'))
                <div>{{ session('error') }}</div>
            @endif
            {{--  <form action="{{ route('siswa.index') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-start gap-3">  --}}
          <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-start gap-3">
            @csrf
            <small class="form-text text-muted mb-2">
                Hanya file <strong>.xlsx</strong> yang dapat diimpor. Maksimal ukuran file 2MB.
            </small>
            <div class="mb-3">
                <a href="{{ asset('template/template_siswa.xlsx') }}" class="btn btn-success btn-sm d-block" download>
                    <i class="fa fa-download me-1"></i> Download Template
                </a>
                <small class="form-text text-muted">
                    Download template untuk impor data siswa.
                </small>
            </div>


            <div class="d-flex gap-3">
                <input type="file" class="form-control w-auto" name="file" accept=".csv" required>

                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <ion-icon name="cloud-download-outline"></ion-icon> Impor
                </button>
            </div>
        </form>

        </div>
        <div class="ms-md-auto py-2 py-md-0">
          <a href="#" class="btn btn-primary btn-round" id="btnTambahSiswa"><i class="fa fa-plus me-2"></i></i>Tambah Siswa</a>
        </div>

      </div>
      <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @if(Session::get('success'))
                                <div class="alert alert-success">
                                    {{  Session::get('success') }}
                                </div>
                            @endif

                            @if(Session::get('warning'))
                                <div class="alert alert-warning">
                                    {{  Session::get('warning') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">

                            <form action="{{ route('siswa.index') }}" method="GET">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <input type="text" name="nama_siswa" id="nama_siswa" class="form-control" placeholder="Nama Siswa" value="{{ request('nama_siswa') }}">
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Cari</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">-- Semua Status --</option>
                                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Alumni</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fa fa-filter me-1"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('siswa.index') }}" class="btn btn-secondary w-100">
                                            <i class="fa fa-refresh me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-2 mb-2">
                        <div class="col-12">
                        <form action="{{ route('siswa.aksiMassal') }}" method="POST" id="formAksiMassal">
                        @csrf
                         <!-- Tombol Aksi Massal -->
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-success" id="btnNaikkan">Naikkan Kelas</button>
                            <button type="button" class="btn btn-warning" id="btnTinggal">Tinggal Kelas</button>
                            <button type="button" class="btn btn-info" id="btnLulus">Luluskan</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAll"></th>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>No. Hp Ortu</th>
                                        <th>Status</th>
                                        <th>Foto</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($siswa as $d)
                                    @php
                                        $path = Storage::url('uploads/siswa/'.$d->foto)
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" name="nis[]" value="{{ $d->nis }}"></td>
                                        <td>{{ $loop->iteration + $siswa->firstItem()-1 }}</td>
                                        <td>{{ $d->nis }}</td>
                                        <td>{{ $d->nama_lengkap }}</td>
                                        <td>{{ $d->kelas }}</td>
                                        <td>{{ $d->no_hp }}</td>
                                        <td>
                                            @if($d->status == 'alumni')
                                                <span class="badge bg-secondary">Alumni</span>
                                            @else
                                                <span class="badge bg-success">Aktif</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if(empty($d->foto))
                                                <img src="{{ asset('assets/img/sample/avatar/nouser.jpg') }}" class="avatar" alt="">
                                            @else
                                                <img src="{{ url($path) }}" class="avatar" alt="">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <button type="button" data-bs-toggle="tooltip" title="Edit" class="btn btn-link btn-primary btn-lg edit" nis="{{ $d->nis }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <form action="/siswa/{{ $d->nis }}/delete" method="POST">
                                                    @csrf
                                                    <button type="button" data-nama="{{ $d->nama_lengkap }}" class="btn btn-link btn-danger deletee" data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <input type="hidden" name="aksi" id="aksi">
                        </div>

                        </form>

                        <!-- Pagination -->
                        <div>
                            {{ $siswa->links('vendor.pagination.bootstrap-5') }}
                        </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

<!-- Modal Tambah Siswa-->
<div class="modal fade" id="modal-inputsiswa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="/siswa/store" method="POST" id="forrmSiswa" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-barcode">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7v-1a2 2 0 0 1 2 -2h2" /><path d="M4 17v1a2 2 0 0 0 2 2h2" /><path d="M16 4h2a2 2 0 0 1 2 2v1" /><path d="M16 20h2a2 2 0 0 0 2 -2v-1" /><path d="M5 11h1v2h-1z" /><path d="M10 11l0 2" />
                            <path d="M14 11h1v2h-1z" /><path d="M19 11l0 2" />
                        </svg>
                        </span>
                        <input type="text" value="" id="nis" class="form-control ms-2" placeholder="NIS" name="nis">
                      </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                          </svg>
                        </span>
                        <input type="text" value="" id="nama_lengkap" class="form-control ms-2" placeholder="Nama Lengkap" name="nama_lengkap">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-school">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                                <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                            </svg>
                        </span>
                        <select id="kelas" name="kelas" class="form-control ms-2">
                            <option value="">Pilih Kelas</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                          <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                        </span>
                        <input type="text" value="" id="no_hp" class="form-control ms-2" placeholder="No. HP" name="no_hp">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-label">Upload Foto <strong>.jpg .jpeg .png</strong>. Maksimal ukuran file 2KB.</div>
                    <input type="file" name="foto" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Close
                </button>
                <button class="btn btn-primary">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" />
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                    </svg>
                    Simpan
                </button>
              </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Modal Edit Siswa-->
<div class="modal fade" id="modal-editsiswa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="exampleModalLabel">Edit Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="loadEditForm">
        <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Close
                </button>
                <button class="btn btn-primary">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" />
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                    </svg>
                    Simpan
                </button>
              </div>

      </div>

    </div>
  </div>
</div>
@endsection

@push('myscript')
<script>
// Daftar kelas
const tingkat = ['7', '8', '9'];
const subkelas = ['A','B','C','D','E','F','G','H','I'];
const selectKelas = document.getElementById('kelas');
tingkat.forEach(level => {
    const group = document.createElement('optgroup');
    group.label = 'Kelas ' + level;

    subkelas.forEach(sub => {
        const option = document.createElement('option');
        option.value = `${level}${sub}`;
        option.textContent = `${level}${sub}`;
        group.appendChild(option);
    });
    selectKelas.appendChild(group);
});
</script>

<script>
    $(function(){
        $("#btnTambahSiswa").click(function(){
            $("#modal-inputsiswa").modal("show");
        });

        $(".edit").click(function(){
            var nis = $(this).attr('nis');
            $.ajax({
                type: 'POST',
                url: 'siswa/edit',
                cache: false,
                data: {
                    _token:"{{ csrf_token(); }}",
                    nis: nis
                },
                success: function(respond){
                    $("#loadEditForm").html(respond);
                }
            });
            $("#modal-editsiswa").modal("show");
        });

        // Validasi form siswa
        $("#forrmSiswa").submit(function(){
            var nis = $("#nis").val();
            var nama_lengkap = $("#nama_lengkap").val();
            var kelas = $("#kelas").val();
            var no_hp = $("#no_hp").val();

            if (nis == ""){
                Swal.fire({
                    icon: "warning",
                    title: "Warning!",
                    text: "NIS Harus Diisi!",
                    confirmButtonText: 'Ok',
                }).then(() => {
                    $("#nis").focus();
                });
                return false;
            } else if (nama_lengkap == ""){
                Swal.fire({
                    icon: "warning",
                    title: "Warning!",
                    text: "Nama Lengkap Harus Diisi!",
                    confirmButtonText: 'Ok',
                }).then(() => {
                    $("#nama_lengkap").focus();
                });
                return false;
            } else if (kelas == ""){
                Swal.fire({
                    icon: "warning",
                    title: "Warning!",
                    text: "Kelas Harus Diisi!",
                    confirmButtonText: 'Ok',
                }).then(() => {
                    $("#kelas").focus();
                });
                return false;
            } else if (no_hp == ""){
                Swal.fire({
                    icon: "warning",
                    title: "Warning!",
                    text: "No HP Harus Diisi!",
                    confirmButtonText: 'Ok',
                }).then(() => {
                    $("#no_hp").focus();
                });
                return false;
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
    $(function () {
    $("#checkAll").click(function () {
        $("input[name='nis[]']").prop('checked', this.checked);
    });

    function konfirmasiAksi(aksi, judul, pesan) {
        if ($("input[name='nis[]']:checked").length === 0) {
            Swal.fire("Peringatan", "Pilih siswa terlebih dahulu.", "warning");
            return;
        }

        Swal.fire({
            title: judul,
            text: pesan,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Lanjutkan",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $("#aksi").val(aksi);
                $("#formAksiMassal").submit();
            }
        });
    }

    $("#btnNaikkan").click(() => konfirmasiAksi("naik", "Naikkan Kelas?", "Siswa terpilih akan dinaikkan ke kelas berikutnya."));
    $("#btnTinggal").click(() => konfirmasiAksi("tinggal", "Tinggal Kelas?", "Siswa terpilih akan tetap di kelas sekarang."));
    $("#btnLulus").click(() => konfirmasiAksi("lulus", "Luluskan Siswa?", "Siswa kelas 9 akan dianggap lulus dan statusnya diubah menjadi alumni."));
});
</script>

@endpush












 {{--  <tbody>
                                        @foreach($siswa as $d)
                                        @php
                                            $path = Storage::url('uploads/siswa/'.$d->foto)
                                        @endphp
                                            <tr>
                                                <td>{{ $loop->iteration + $siswa->firstItem()-1 }}</td>
                                                <td>{{ $d->nis }}</td>
                                                <td>{{ $d->nama_lengkap }}</td>
                                                <td>{{ $d->kelas }}</td>
                                                <td>{{ $d->no_hp }}</td>
                                                <td>
                                                    @if(empty($d->foto))
                                                    <img src="{{ asset('assets/img/sample/avatar/nouser.jpg') }}" class="avatar" alt="">
                                                    @else
                                                    <img src="{{ url($path) }}" class="avatar" alt=""></td>
                                                    @endif
                                                <td>
                                                    <div class="form-button-action">
                                                        <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg edit" nis="{{ $d->nis }}" data-original-title="Edit Task">
                                                        <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form action="/siswa/{{ $d->nis }}/delete" method="POST">
                                                            @csrf
                                                             <button type="button" data-nama="{{ $d->nama_lengkap }}" class="btn btn-link btn-danger deletee" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>  --}}



  {{--  <div class="table-responsive">
                                <table class="table table-striped table-striped-bg-black mt-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                            <th>No</th>
                                            <th>NIS</th>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>No. Hp Ortu</th>
                                            <th>Foto</th>
                                            <th>Aksi</th>
                                        </tr>

                                    </thead>

                                <tbody>
                                    <form action="{{ route('siswa.aksiMassal') }}" method="POST" id="formAksiMassal">
                                    @csrf
                                    @foreach($siswa as $d)
                                    @php
                                        $path = Storage::url('uploads/siswa/'.$d->foto)
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" name="nis[]" value="{{ $d->nis }}"></td>
                                        <td>{{ $loop->iteration + $siswa->firstItem()-1 }}</td>
                                        <td>{{ $d->nis }}</td>
                                        <td>{{ $d->nama_lengkap }}</td>
                                        <td>{{ $d->kelas }}</td>
                                        <td>{{ $d->no_hp }}</td>
                                        <td>
                                            @if(empty($d->foto))
                                                <img src="{{ asset('assets/img/sample/avatar/nouser.jpg') }}" class="avatar" alt="">
                                            @else
                                                <img src="{{ url($path) }}" class="avatar" alt="">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <button type="button" data-bs-toggle="tooltip" title="Edit" class="btn btn-link btn-primary btn-lg edit" nis="{{ $d->nis }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <form action="/siswa/{{ $d->nis }}/delete" method="POST">
                                                    @csrf
                                                    <button type="button" data-nama="{{ $d->nama_lengkap }}" class="btn btn-link btn-danger deletee" data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <input type="hidden" name="aksi" id="aksi">
                                    </form>
                                </tbody>

                                </table>
                                <!-- Tombol Aksi Massal -->
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-success" id="btnNaikkan">Naikkan Kelas</button>
                                    <button type="button" class="btn btn-warning" id="btnTinggal">Tinggal Kelas</button>
                                    <button type="button" class="btn btn-info" id="btnLulus">Luluskan</button>
                                </div>

                                <div class="">
                                    {{ $siswa->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>  --}}



 {{--  <form action="/siswa" method="GET">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <input type="text" name="nama_siswa" id="nama_siswa" class="form-control" placeholder="Nama Siswa" value="{{ Request('nama_siswa') }}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Cari</button>
                                        </div>
                                    </div>

                                </div>
                            </form>  --}}

