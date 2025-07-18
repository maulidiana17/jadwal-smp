<div class="sidebar" data-background-color="light">
    <div class="sidebar-logo">
      <!-- Logo Header -->
      <div class="logo-header" data-background-color="blue">
        <a href="" class="logo">
          <img
            src="{{ asset('assets/img/smp.png') }}"
            alt="navbar brand"
            class="navbar-brand"
            height="45"
          />

          <h6 class="text-white h-25 ms-2">SMPN 1 GENTENG</h6>
        </a>
        <div class="nav-toggle">
          <button class="btn btn-toggle toggle-sidebar">
            <i class="gg-menu-right"></i>
          </button>
          <button class="btn btn-toggle sidenav-toggler">
            <i class="gg-menu-left"></i>
          </button>
        </div>
        <button class="topbar-toggler more">
          <i class="gg-more-vertical-alt"></i>
        </button>
      </div>
      <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
        <ul class="nav nav-secondary">
          <li class="nav-item {{ request()->is('dashboardadmin') ? 'active' : '' }}">
            <a href="/dashboardadmin">
              <i class="fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">MENU</h4>
          </li>
          <li class="nav-item {{ request()->is('admin*') || request()->is('siswa*') || request()->is('guru*') ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#base" aria-expanded="{{ request()->is('admin*') || request()->is('siswa*') || request()->is('guru*') ? 'true' : 'false' }}">
                <i class="fas fa-layer-group"></i>
                <p>Data Master</p>
                <span class="caret"></span>
            </a>
            <div class="collapse {{ request()->is('admin*') || request()->is('siswa*') || request()->is('guru*') ? 'show' : '' }}" id="base">
                <ul class="nav nav-collapse">
                <li class="{{ request()->is('admin*') ? 'active' : '' }}">
                    <a href="/admin/index">
                    <span class="sub-item">Data Admin</span>
                    </a>
                </li>
                <li class="{{ request()->is('siswa*') ? 'active' : '' }}">
                    <a href="/siswa">
                    <span class="sub-item">Data Siswa</span>
                    </a>
                </li>
                <li class="{{ request()->is('guru*') ? 'active' : '' }}">
                    <a href="/guru">
                    <span class="sub-item">Data Guru</span>
                    </a>
                </li>
                </ul>
            </div>
          </li>
            <li class="nav-item {{ request()->is('kelas*') ? 'active' : '' }}">
                <a data-bs-toggle="collapse" href="#datakelas" aria-expanded="{{ request()->is('kelas*') ? 'true' : 'false' }}">
                    <i class="fas fa-school"></i>
                    <p>Data Kelas</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse {{ request()->is('kelas*') ? 'show' : '' }}" id="datakelas">
                    <ul class="nav nav-collapse">
                        <li class="{{ request()->is('kelas*') ? 'active' : '' }}">
                            <a href="/absensi/kelas">
                                <span class="sub-item">Kelas</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item {{ request()->is('qr*') ? 'active' : '' }}">
                <a data-bs-toggle="collapse" href="#dataqr" aria-expanded="{{ request()->is('kelas*') ? 'true' : 'false' }}">
                    <i class="fas fa-qrcode"></i>
                    <p>QR Kode Validasi</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse {{ request()->is('qr*') ? 'show' : '' }}" id="dataqr">
                    <ul class="nav nav-collapse">
                        <li class="{{ request()->is('qr*') ? 'active' : '' }}">
                            <a href="/absensi/qr-admin">
                                <span class="sub-item">QR Validasi</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

          <li class="nav-item {{ request()->is('absensi/monitoring*') ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#izin" aria-expanded="{{ request()->is('absensi/monitoring*') ? 'true' : 'false' }}">
                <i class="fas fa-tachometer-alt"></i>
                <p>Monitoring Presensi</p>
                <span class="caret"></span>
            </a>
            <div class="collapse {{ request()->is('absensi/monitoring*') ? 'show' : '' }}" id="izin">
                <ul class="nav nav-collapse">
                <li class="{{ request()->is('absensi/monitoring*') ? 'active' : '' }}">
                    <a href="/absensi/monitoring">
                    <span class="sub-item">Monitoring Presensi</span>
                    </a>
                </li>
                </ul>
            </div>
          </li>
          <li class="nav-item {{ request()->is('absensi/izinsakit*') ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#forms" aria-expanded="{{ request()->is('absensi/izinsakit*') ? 'true' : 'false' }}">
              <i class="fas fa-pen-square"></i>
              <p>Persetujuan Izin/Sakit</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ request()->is('absensi/izinsakit*') ? 'show' : '' }}" id="forms">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('absensi/izinsakit*') ? 'active' : '' }}">
                  <a href="/absensi/izinsakit">
                    <span class="sub-item">Data Izin/Sakit</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item {{ request()->is('absensi/laporan*') || request()->is('absensi/rekap*') ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#tables" aria-expanded="{{ request()->is('absensi/laporan*') || request()->is('absensi/rekap*') ? 'true' : 'false' }}">
                <i class="fas fa-table"></i>
                <p>Laporan</p>
                <span class="caret"></span>
            </a>
            <div class="collapse {{ request()->is('absensi/laporan*') || request()->is('absensi/rekap*') ? 'show' : '' }}" id="tables">
                <ul class="nav nav-collapse">
                <li class="{{ request()->is('absensi/laporan*') ? 'active' : '' }}">
                    <a href="/absensi/laporan">
                    <span class="sub-item">Laporan Harian</span>
                    </a>
                </li>
                <li class="{{ request()->is('absensi/rekap*') ? 'active' : '' }}">
                    <a href="/absensi/rekap">
                    <span class="sub-item">Rekap Presensi</span>
                    </a>
                </li>
                </ul>
            </div>
          </li>
          <li class="nav-item {{ request()->is('konfigurasi/lokasisekolah*') ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#maps" aria-expanded="{{ request()->is('konfigurasi/lokasisekolah*') ? 'true' : 'false' }}">
              <i class="fas fa-map-marker-alt"></i>
              <p>Konfigurasi Lokasi</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ request()->is('konfigurasi/lokasisekolah*') ? 'show' : '' }}" id="maps">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('konfigurasi/lokasisekolah*') ? 'active' : '' }}">
                  <a href="/konfigurasi/lokasisekolah">
                    <span class="sub-item">Lokasi Sekolah</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
