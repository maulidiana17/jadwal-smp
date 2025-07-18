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
          <li class="nav-item {{ request()->is('dashboardguru') ? 'active' : '' }}">
            <a href="/dashboardguru">
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
          <li class="nav-item {{ request()->is('qr*') ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#base" aria-expanded="{{ request()->is('qr*') ? 'true' : 'false' }}">
            <svg style="margin-right: 18px; vertical-align: middle;" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-qrcode">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 17l0 .01" />
                <path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M7 7l0 .01" />
                <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M17 7l0 .01" />
                <path d="M14 14l3 0" /><path d="M20 14l0 .01" />
                <path d="M14 14l0 3" /><path d="M14 20l3 0" />
                <path d="M17 17l3 0" /><path d="M20 17l0 3" />
            </svg>
              <p class="display-inline">Kode QR</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ request()->is('qr*') ? 'show' : '' }}" id="base">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('qr*') ? 'active' : '' }}">
                  <a href="/qr">
                    <span class="sub-item">QR Guru </span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

        </ul>
      </div>
    </div>
  </div>
