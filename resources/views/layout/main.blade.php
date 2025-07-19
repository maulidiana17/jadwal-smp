<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>JADWAL SMPN 1 GENTENG</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset("sd/vendors/feather/feather.css")}}">
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{asset("sd/vendors/css/vendor.bundle.base.css")}}">
  <link rel="stylesheet" href="{{ asset('sd/vendors/mdi/css/materialdesignicons.min.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Tambahkan di <head> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <!-- Tambahkan sebelum </body> -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">



  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="{{asset("sd/vendors/datatables.net-bs4/dataTables.bootstrap4.css")}}">
  <link rel="stylesheet" href="{{asset("sd/vendors/ti-icons/css/themify-icons.css")}}">
  <link rel="stylesheet" type="text/css" href="{{asset("sd/js/select.dataTables.min.css")}}">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{asset("sd/css/vertical-layout-light/style.css")}}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('sd/images/logo.png')}}" />
  <style>
  .navbar .navbar-menu-wrapper {
    background-color: #62d2f8 !important; /* light blue */
  }
  /* Atur agar konten tidak tertutup oleh navbar */
  .page-body-wrapper {
      padding-top: 20px;
      flex: 1;
      display: flex;
      flex-direction: row;
  }
  /* Tambahan CSS untuk membuat sidebar tetap -->
  /* Sidebar tetap di kiri */
    .sidebar {
      position: fixed;
      top: 50px; /* sesuaikan dengan tinggi navbar */
      bottom: 0;
      left: 0;
      width: 250px; /* harus sesuai dengan sidebar width */
      overflow-y: auto;
      z-index: 100;
      background-color: #fff; /* opsional: warna latar */
    }
    /* Atur agar wrapper tidak ikut men-scroll sidebar */
    .page-body-wrapper {
      padding-left: 0 !important;
    }

    /* atur bgaian footer agar tetap */
    html, body {
    height: 100%;
    margin: 0;
  }

  /* .container-scroller {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  } */

  .main-panel {
    padding: 20px;
    min-height: 100vh;
    overflow-y: auto;
    margin-left: 250px; /* sesuaikan dengan sidebar */
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .content-wrapper {
    flex: 1;
  }

  footer.footer {
    background-color: #f8f9fa; /* warna latar (opsional) */
    padding: 10px 20px;
  }

</style>

</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar navbar-dark col-lg-12 col-12 p-0 fixed-top d-flex flex-row" >
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center" style="background-color: #62d2f8 !important;">
        <a class="navbar-brand brand-logo mr-3" href="index.html"><img src="{{asset('sd/images/logo.png')}}" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{asset('sd/images/logo.png')}}" alt="logo"/></a>
        <span class="brand-text font-weight-bold" style="font-size: 12px; color: #ffffff;">SMPN 1 Genteng</span>

      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        {{-- <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul> --}}
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="{{route('profile.setting')}}" data-toggle="dropdown" id="profileDropdown">
              <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Foto Profil" height="30" width="30">
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a href="{{route('profile.setting')}}" class="dropdown-item">
                <i class="mdi mdi-account text-primary"></i>
                 {{ auth()->user()->getRoleNames()->implode(', ') }}
               </a>
              <a href="{{route('user.index')}}" class="dropdown-item">
                <i class="ti-settings text-primary"></i>
                Settings
              </a>
              <a href="{{ route('logout') }}" class="dropdown-item">
                <i class="ti-power-off text-primary"></i>
                Logout
              </a>
              <!-- Logout Modal -->
              {{-- <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                      Apakah Anda yakin ingin logout, <strong>{{ Auth()->user()->name }}</strong>?
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div> --}}

            </div>
          </li>
        </ul>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
          <a class="nav-link {{ request()->is('dashboardkurikulum') ? 'bg-info text-white' : '' }}" href="/dashboardkurikulum">
            <i class="mdi mdi-view-dashboard-outline menu-icon"></i>
            <span class="menu-title">Dashboard</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->is('waktu*') ? 'bg-info text-white' : '' }}" href="/waktu">
            <i class="mdi mdi-calendar-clock menu-icon"></i>
            <span class="menu-title">Data Waktu</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->is('guru*') ? 'bg-info text-white' : '' }}" href="/guru">
            <i class="mdi mdi-account-tie menu-icon"></i>
            <span class="menu-title">Guru</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->is('mapel*') ? 'bg-info text-white' : '' }}" href="/mapel">
            <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            <span class="menu-title">Mata Pelajaran</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->is('pengampu*') ? 'bg-info text-white' : '' }}" href="/pengampu">
            <i class="mdi mdi-account-group menu-icon"></i>
            <span class="menu-title">Pengampu</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->is('ruangan*') ? 'bg-info text-white' : '' }}" href="/ruangan">
            <i class="mdi mdi-door menu-icon"></i>
            <span class="menu-title">Ruang</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->is('kelas*') ? 'bg-info text-white' : '' }}" href="/kelas">
            <i class="mdi mdi-google-classroom menu-icon"></i>
            <span class="menu-title">Kelas</span>
          </a>
        </li>

        <a class="nav-link text-gray">--proses--</a>

        <li class="nav-item">
          <a class="nav-link {{ request()->is('jadwal*') ? 'bg-info text-white' : '' }}" href="/jadwal">
            <i class="icon-paper menu-icon"></i>
            <span class="menu-title">Jadwal</span>
          </a>
        </li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        @yield('content')
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025.<a href="https://smpn1genteng.sch.id//" target="_blank">SMPN 1 Genteng</a></span>
            {{-- <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span> --}}
          </div>
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Distributed by <a href="https://www.instagram.com/nana_di05?igsh=MTBhMGI3bjJjemh3eg==" target="_blank">Nana_di05</a></span> 
          </div>
        </footer> 
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="{{asset("sd/vendors/js/vendor.bundle.base.js")}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{asset("sd/vendors/chart.js/Chart.min.js")}}"></script>
  <script src="{{asset("sd/vendors/datatables.net/jquery.dataTables.js")}}"></script>
  <script src="{{asset("sd/vendors/datatables.net-bs4/dataTables.bootstrap4.js")}}"></script>
  <script src="{{asset("sd/js/dataTables.select.min.js")}}"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{asset("sd/js/off-canvas.js")}}"></script>
  <script src="{{asset("sd/js/hoverable-collapse.js")}}"></script>
  <script src="{{asset("js/template.js")}}"></script>
  <script src="{{asset("sd/js/settings.js")}}"></script>
  <script src="{{asset("sd/js/todolist.js")}}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{asset("sd/js/dashboard.js")}}"></script>
  <script src="{{asset("sd/js/Chart.roundedBarmapel.js")}}"></script>
  <!-- End custom js for this page-->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- JQuery (wajib sebelum Bootstrap JS) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <!-- jQuery dan Select2 JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Bootstrap Bundle JS (sudah termasuk Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  @yield('scripts')
  @stack('scripts')

</body>

</html>

