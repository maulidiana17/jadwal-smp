@extends('layout.main')

@section('content')
    <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3>Selamat Datang {{ auth()->user()->name }}</h3>

                    {{-- @role('admin')
                        <p>Anda login sebagai <strong>Admin</strong></p> --}}
                        {{-- Tampilkan data khusus admin --}}
                    {{-- @endrole --}}

                    @role('kurikulum')
                        <p>Anda login sebagai <strong>Kurikulum</strong></p>
                        {{-- Tampilkan data khusus kurikulum --}}
                    @endrole
                  <h6 class="font-weight-normal mb-0">Sistem Penjadwalan Mata Pelajaran SMPN 1 Genteng <span class="text-primary">TP 2025/2026</span></h6>
                </div>
                <div class="col-12 col-xl-4">
                 <div class="justify-content-end d-flex">
                  <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                    <button class="btn btn-sm btn-light bg-white" type="button">
                    <i class="mdi mdi-calendar"></i> Today ({{ \Carbon\Carbon::now()->format('d M Y') }})
                    </button>
                  </div>
                 </div>
                </div>
              </div>
            </div>
          </div>

            <div class="row">
              <!-- Guru -->
              <div class="col-md-3 col-sm-6 mb-4">
                <a href="{{ route('guru.index') }}" class="text-decoration-none">
                  <div class="card text-white h-100 shadow-lg" style="background-color: #7ab9f8;">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                      <i class="mdi mdi-account-tie" style="font-size: 2rem;"></i>
                      <h5 class="card-title mt-2">Guru</h5>
                      <p class="card-text fs-4">{{ $jumlahGuru }}</p>
                    </div>
                  </div>
                </a>
              </div>

              <!-- Mata Pelajaran -->
              <div class="col-md-3 col-sm-6 mb-4">
                <a href="{{ route('mapel.index') }}" class="text-decoration-none">
                  <div class="card text-white h-100 shadow-lg" style="background-color: #7af880;">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                      <i class="mdi mdi-book-open-page-variant" style="font-size: 2rem;"></i>
                      <h5 class="card-title mt-2">Mata Pelajaran</h5>
                      <p class="card-text fs-4">{{ $jumlahMapel }}</p>
                    </div>
                  </div>
                </a>
              </div>

              <!-- Ruangan -->
              <div class="col-md-3 col-sm-6 mb-4">
                <a href="{{ route('ruangan.index') }}" class="text-decoration-none">
                  <div class="card text-white h-100 shadow-lg" style="background-color: #ff922c;">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                      <i class="mdi mdi-door" style="font-size: 2rem;"></i>
                      <h5 class="card-title mt-2">Ruangan</h5>
                      <p class="card-text fs-4">{{ $jumlahRuangan }}</p>
                    </div>
                  </div>
                </a>
              </div>

              <!-- Kelas -->
              <div class="col-md-3 col-sm-6 mb-4">
                <a href="{{ route('kelas.index') }}" class="text-decoration-none">
                  <div class="card text-white h-100 shadow-lg" style="background-color: #08ad53;">
                    <div class="card-body d-flex flex-column align-items-center text-center">
                      <i class="mdi mdi-google-classroom" style="font-size: 2rem;"></i>
                      <h5 class="card-title mt-2">Kelas</h5>
                      <p class="card-text fs-4">{{ $jumlahKelas }}</p>
                    </div>
                  </div>
                </a>
              </div>
            </div>


            <!-- Bootstrap Icons -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
      </div>
@endsection