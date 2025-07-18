<nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">


              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

                {{--  <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="notification">0</span>
                    </a>
                    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                        <li>
                            <div class="dropdown-title">You have 0 new notifications</div>
                        </li>
                        <li>
                            <div class="notif-scroll scrollbar-outer">
                                <div class="notif-center"></div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">See all notifications <i class="fa fa-angle-right"></i></a>
                        </li>
                    </ul>
                </li>  --}}
                <li class="nav-item topbar-icon dropdown hidden-caret">
    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bell"></i>
        <span class="notification">0</span>
    </a>
    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
        <li>
            <div class="dropdown-title">You have 0 new notifications</div>
        </li>
        <li>
            <div class="notif-scroll scrollbar-outer">
                <div class="notif-center"></div>
            </div>
        </li>
        <li>
            <a class="see-all" href="/absensi/izinsakit">Lihat semua notifikasi <i class="fa fa-angle-right"></i></a>
        </li>
    </ul>
</li>


                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                      <img
                        src="{{ asset('admin/dashboard/assets/img/profile1.png') }}"
                        alt="..."
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>

                      @if (Auth::check())
                        @if (Auth::user()->role === 'admin')
                            <span class="fw-bold text-primary">{{ Auth::user()->name }} (Admin)</span>
                        @elseif (Auth::user()->role === 'guru')
                            <span class="fw-bold text-success">{{ Auth::user()->name }} (Guru)</span>
                        @endif
                      @endif
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img
                              src="{{ asset('admin/dashboard/assets/img/profile1.png') }}"
                              alt="image profile"
                              class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                            <h4>{{ Auth::user('role')->name }}</h4>
                            <p class="text-muted">{{ Auth::user()->email }}</p>

                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>

          @push('myscript')
<script>
function loadNotifikasi() {
    $.ajax({
        url: '/get-notifikasi',
        method: 'GET',
        success: function (data) {
            $('.notification').text(data.length);
            $('.dropdown-title').text(`You have ${data.length} new notification${data.length > 1 ? 's' : ''}`);

            let html = '';
            data.forEach(function (item) {
                let icon = '<i class="fa fa-user"></i>';
                let bg = 'notif-primary';
                let keterangan = '';

                if (item.status_approved == 0 && !item.file_surat) {
                    icon = '<i class="fa fa-upload"></i>';
                    bg = 'notif-warning';
                    keterangan = 'belum mengunggah surat';
                } else if (item.status_approved == 0 && item.file_surat) {
                    icon = '<i class="fa fa-clock"></i>';
                    bg = 'notif-info';
                    keterangan = 'menunggu persetujuan';
                } else if (item.status_approved == 2) {
                    icon = '<i class="fa fa-times"></i>';
                    bg = 'notif-danger';
                    keterangan = 'pengajuan ditolak';
                }

                html += `
                    <a href="/absensi/izinsakit">
                        <div class="notif-icon ${bg}">
                            ${icon}
                        </div>
                        <div class="notif-content">
                            <span class="block"><b>${item.nama_lengkap}</b> ${keterangan} (${item.jenis_pengajuan})</span>
                            <span class="time">${item.tanggal_izin}</span>
                        </div>
                    </a>
                `;
            });

            $('.notif-center').html(html);
        },
        error: function (xhr, status, error) {
            console.error("Gagal mengambil notifikasi:", error);
        }
    });
}

$(document).ready(function () {

    loadNotifikasi();


    setInterval(loadNotifikasi, 10000);
});
</script>
@endpush


          {{--  @push('myscript')
            <script>
                $(document).ready(function () {
                    $.ajax({
                        url: '/get-notifikasi',
                        method: 'GET',
                        success: function (data) {
                        $('.notification').text(data.length);
                        $('.dropdown-title').text(`You have ${data.length} new notification${data.length > 1 ? 's' : ''}`);

                        let html = '';
                        data.forEach(function (item) {
                            html += `
                            <a href="/absensi/izinsakit">
                                <div class="notif-icon notif-primary">
                                <i class="fa fa-user"></i>
                                </div>
                                 <div class="notif-content">
                                    <span class="block"><b>${item.nama_lengkap}</b> mengajukan <b>${item.jenis_pengajuan}</b></span>
                                    <span class="time">${item.tanggal_izin}</span>
                                </div>
                            </a>
                            `;
                        });

                        $('.notif-center').html(html);
                        },
                        error: function (xhr, status, error) {
                        console.error("Gagal mengambil notifikasi:", error);
                        }
                    });
                    });
            </script>
          @endpush  --}}
