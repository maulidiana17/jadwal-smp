<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\WaktuController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengampuController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Exports\JadwalPerKelasExport;
use App\Exports\JadwalPerGuruExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Kelas;
use App\Models\Guru;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [LoginController::class,'index'])->name('login');
Route::post('/login_proses', [LoginController::class,'login_proses'])->name('login_proses');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::middleware(['auth', 'role:admin|kurikulum'])->group(function () {

// Route::get('/dashboard', [HomeController::class,'dashboard'])->name('dashboard');
Route::middleware(['auth', 'kurikulum'])->group(function () {

Route::get('/dashboardkurikulum', [HomeController::class,'dashboard'])->name('dashboard');
// USER
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/', [HomeController::class,'index'])->name('index');
    Route::get('/create', [HomeController::class,'create'])->name('create');
    Route::post('/store', [HomeController::class,'store'])->name('store');
    Route::get('/edit/{id}', [HomeController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [HomeController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [HomeController::class,'delete'])->name('delete');
});
// Route::prefix('user')->name('user.')->group(function () {
//     Route::get('/', [HomeController::class,'index'])->name('index');
//     Route::get('/create', [HomeController::class,'create'])->name('create');
//     Route::post('/store', [HomeController::class,'store'])->name('store');
//     Route::get('/edit/{id}', [HomeController::class,'edit'])->name('edit');
//     Route::put('/update/{id}', [HomeController::class,'update'])->name('update');
//     Route::delete('/delete/{id}', [HomeController::class,'delete'])->name('delete');
// });

// GURU
Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [GuruController::class,'index'])->name('index');
    Route::get('/create', [GuruController::class,'create'])->name('create');
    Route::post('/store', [GuruController::class,'store'])->name('store');
    Route::get('/edit/{id}', [GuruController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [GuruController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [GuruController::class,'delete'])->name('delete');
    Route::post('/import', [GuruController::class, 'import'])->name('import');

});

// Mapel
Route::prefix('mapel')->name('mapel.')->group(function () {
    Route::get('/', [MapelController::class,'index'])->name('index');
    Route::get('/create', [MapelController::class,'create'])->name('create');
    Route::post('/store', [MapelController::class,'store'])->name('store');
    Route::get('/edit/{id}', [MapelController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [MapelController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [MapelController::class,'delete'])->name('delete');
    Route::post('/import', [MapelController::class, 'import'])->name('import');

});

// Kelas
Route::prefix('kelas')->name('kelas.')->group(function () {
    Route::get('/', [KelasController::class,'index'])->name('index');
    Route::get('/create', [KelasController::class,'create'])->name('create');
    Route::post('/store', [KelasController::class,'store'])->name('store');
    Route::get('/edit/{id}', [KelasController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [KelasController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [KelasController::class,'delete'])->name('delete');
    Route::post('/import', [KelasController::class, 'import'])->name('import');

});

// Ruangan
Route::prefix('ruangan')->name('ruangan.')->group(function () {
    Route::get('/', [RuanganController::class,'index'])->name('index');
    Route::get('/create', [RuanganController::class,'create'])->name('create');
    Route::post('/store', [RuanganController::class,'store'])->name('store');
    Route::get('/edit/{id}', [RuanganController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [RuanganController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [RuanganController::class,'delete'])->name('delete');
    Route::post('/import', [RuanganController::class, 'import'])->name('import');

});

// Waktu
Route::prefix('waktu')->name('waktu.')->group(function () {
    Route::get('/', [WaktuController::class,'index'])->name('index');
    Route::get('/create', [WaktuController::class,'create'])->name('create');
    Route::post('/store', [WaktuController::class,'store'])->name('store');
    Route::get('/edit/{id}', [WaktuController::class,'edit'])->name('edit');
    Route::put('/update/{id}', [WaktuController::class,'update'])->name('update');
    Route::delete('/delete/{id}', [WaktuController::class,'delete'])->name('delete');
    Route::post('/import', [WaktuController::class, 'import'])->name('import');

});

//Pengampu
Route::prefix('pengampu')->name('pengampu.')->group(function () {
    Route::get('/', [PengampuController::class, 'index'])->name('index');
    Route::get('/create', [PengampuController::class, 'create'])->name('create');
    Route::post('/store', [PengampuController::class, 'store'])->name('store');
    Route::get('/edit-multiple/{guru}/{mapel}', [PengampuController::class, 'editMultiple'])->name('editMultiple');
    Route::put('/update-multiple/{guru}/{mapel}', [PengampuController::class, 'updateMultiple'])->name('updateMultiple');
    Route::delete('/delete-group/{guru}/{mapel}', [PengampuController::class, 'destroyGroup'])->name('destroyGroup');
    Route::post('/import', [PengampuController::class, 'import'])->name('import');
});

//jadwal
Route::prefix('jadwal')->name('jadwal.')->group(function () {
    Route::get('/', [JadwalController::class, 'index'])->name('index');
    Route::post('/generate-preview', [JadwalController::class, 'generatePreview'])->name('generatePreview');
    Route::get('/generate', [JadwalController::class, 'showGenerateForm'])->name('generate');
    Route::post('/process', [JadwalController::class, 'generateProcess'])->name('process');
    Route::delete('/reset', [JadwalController::class, 'reset'])->name('reset');
    Route::get('/filter', [JadwalController::class, 'filter'])->name('filter');
    Route::get('/kelas', [JadwalController::class, 'perKelas'])->name('perKelas');
    Route::get('/guru', [JadwalController::class, 'perGuru'])->name('perGuru');
    Route::get('/mapel', [JadwalController::class, 'perMapel'])->name('perMapel');
    Route::get('/evaluasi', [App\Http\Controllers\JadwalController::class, 'evaluasi'])->name('evaluasi');
    Route::get('/export/pdf/kelas/{id}', [JadwalController::class, 'exportPDFKelas'])->name('exportPDFKelas');
    Route::get('/export/pdf/guru/{id}', [JadwalController::class, 'exportPDFGuru'])->name('exportPDFGuru');
    Route::get('/export/excel/kelas/{id}', [JadwalController::class, 'exportExcelKelas'])->name('exportExcelKelas');
    Route::get('/export/excel/guru/{id}', [JadwalController::class, 'exportExcelGuru'])->name('exportExcelGuru');

});
//  Route::post('/jadwal/process', [JadwalController::class, 'generateProcess'])->name('jadwal.process');


//profill
    Route::get('/profile/setting', fn() => view('setting'))->name('profile.setting');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

});

// 🔐 Khusus admin saja
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [HomeController::class,'index'])->name('index');
        Route::get('/create', [HomeController::class,'create'])->name('create');
        Route::post('/store', [HomeController::class,'store'])->name('store');
        Route::get('/edit/{id}', [HomeController::class,'edit'])->name('edit');
        Route::put('/update/{id}', [HomeController::class,'update'])->name('update');
        Route::delete('/delete/{id}', [HomeController::class,'delete'])->name('delete');
    });

    Route::prefix('user-role')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserRoleController::class, 'index'])->name('user-role.index');
        Route::post('/assign', [\App\Http\Controllers\UserRoleController::class, 'assign'])->name('user-role.assign');
    });
});

//Middleware login admin dan guru
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboardadmin', [DashboardController::class, 'dashboardadmin']);
    });

    // Route::middleware(['auth', 'guru'])->group(function () {
    //     Route::get('/dashboardguru', [DashboardController::class, 'dashboardguru']);
    //     Route::get('/qr', [GuruController::class, 'qr']);
    //     Route::get('/dashboardguru', [GuruController::class, 'qrIndex']);
    //     Route::get('/download-qr', [GuruController::class, 'downloadQr'])->name('guru.qr.download');
    //     //Route::get('/absensi/export-excel', [GuruController::class, 'exportExcel'])->name('absensi.exportExcel');
    //     Route::get('/qr/export', [GuruController::class, 'exportExcel'])->name('qr.export');
    //     Route::get('/qr/export-mingguan', [GuruController::class, 'exportMingguan'])->name('qr.export.mingguan');
    //     Route::get('/qr/export-mingguan-manual', [GuruController::class, 'exportMingguanManual'])->name('qr.export.mingguan.manual');
    //     //Route::get('/absensi/hari-ini', [GuruController::class, 'getSiswaAbsenHariIni'])->name('absensi.hariini');
    // });
  //Middleware absen siswa
    Route::middleware(['auth:siswa'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        // Route::post('/logout', [LoginController::class, 'logout']);

        //Absen
        Route::get('/absensi/create', [AbsensiController::class, 'create']);
        Route::post('/absensi/store', [AbsensiController::class, 'store']);

        //Edit Profile
        Route::get('/editprofile', [AbsensiController::class, 'editprofile']);
        Route::post('/absensi/{nis}/updateprofile', [AbsensiController::class, 'updateprofile']);

        //Histori
        Route::get('/absensi/histori', [AbsensiController::class, 'histori']);
        Route::post('/gethistori', [AbsensiController::class, 'gethistori']);

        //Izin
        Route::get('/absensi/izin', [AbsensiController::class, 'izin']);
        Route::get('/absensi/buatizin', [AbsensiController::class, 'buatizin']);
        Route::post('/absensi/storeizin', [AbsensiController::class, 'storeizin']);

        //QR
        Route::get('/absensi/scan', [AbsensiController::class, 'scan']);
        Route::post('/absensi/simpanScanQR', [AbsensiController::class, 'simpanScanQR']);
        Route::get('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
        Route::get('/absensi/success', [AbsensiController::class, 'success'])->name('absensi.success');
        Route::get('/absensi/status', [AbsensiController::class, 'getStatusHadir'])->middleware('auth:siswa');
        Route::get('/absensi/editizin/{id}', [AbsensiController::class, 'editizin'])->middleware('auth:siswa');
        Route::post('/absensi/updateizin', [AbsensiController::class, 'updateizin'])->middleware('auth:siswa');

    });
  //Admin
    Route::get('/absensi/kelas', [AdminController::class, 'kelas'])->middleware('auth');
    Route::get('/admin/index', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/{admin}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{admin}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');

    // Guru
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    Route::get('/guru/{guru}', [GuruController::class, 'edit'])->name('guru.edit');
    Route::put('/guru/{guru}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{guru}', [GuruController::class, 'destroy'])->name('guru.destroy');
      Route::post('/ubah-absen-alfa', [GuruController::class, 'ubahKeAlfa'])->name('ubah.absen.alfa');
    Route::post('/ubah-absen-hadir', [GuruController::class, 'ubahKeHadir'])->name('ubah.absen.hadir');

    //Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa/store', [SiswaController::class, 'store'])->name('siswa.store');
    Route::post('/siswa/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::post('/siswa/{nis}/update', [SiswaController::class, 'update'])->name('siswa.update');
    Route::post('/siswa/{nis}/delete', [SiswaController::class, 'delete'])->name('siswa.delete');
    Route::get('/siswa/import', [SiswaController::class, 'importForm'])->name('siswa.importForm');
    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::post('/siswa/aksi-massal', [SiswaController::class, 'aksiMassal'])->name('siswa.aksiMassal');
    Route::get('/alumni', [SiswaController::class, 'alumni'])->name('siswa.alumni');

   // Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.index'); // ← ini lebih tepat


    //Presensi Monitoring Admin
    Route::get('/absensi/monitoring', [AbsensiController::class, 'monitoring'])->name('absensi.monitoring');
    Route::post('/getabsensi', [AbsensiController::class, 'getabsensi'])->name('absensi.getabsensi');
    Route::get('/get-notifikasi', [AbsensiController::class, 'getNotifikasi']);
    Route::post('/showmap', [AbsensiController::class, 'showmap'])->name('absensi.showmap');
    Route::get('/absensi/laporan', [AbsensiController::class, 'laporan'])->name('absensi.laporan');
    Route::get('/siswa/bykelas/{kelas}', [AbsensiController::class, 'getSiswaByKelas'])->name('siswa.bykelas');
    Route::post('/absensi/cetaklaporan', [AbsensiController::class, 'cetaklaporan'])->name('absensi.cetaklaporan');
    Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::post('/absensi/cetakrekap', [AbsensiController::class, 'cetakrekap'])->name('absensi.cetakrekap');
    Route::get('/absensi/izinsakit', [AbsensiController::class, 'izinsakit'])->name('absensi.izinsakit');
    Route::post('/absensi/approvedizinsakit', [AbsensiController::class, 'approvedizinsakit'])->name('absensi.approvedizinsakit');
    Route::get('/absensi/{id}/batalkanizinsakit', [AbsensiController::class, 'batalkanizinsakit'])->name('absensi.batalkanizinsakit');
    Route::delete('/absensi/{id}/hapusizinsakit', [AbsensiController::class, 'hapusIzinSakit']);
    Route::get('/absensi/qr-admin', [AbsensiController::class, 'showQrPresensi'])->middleware('auth');
   // Route::get('/absensi/kode-qr', [AbsensiController::class, 'getKodeQr']);
    Route::get('/absensi/qr-terbaru', [AbsensiController::class, 'getQrTerbaru']);
    Route::get('/absensi/maps', [AbsensiController::class, 'maps'])->name('absensi.maps');


    // Route::get('/absensi/qr-display', [AbsensiController::class, 'displayQr']);
    Route::get('/absensi/qr-display/{token}', [AbsensiController::class, 'displayQr']);


    //Konfigurasi
    Route::get('/konfigurasi/lokasisekolah', [KonfigurasiController::class, 'lokasisekolah']);
    Route::post('/konfigurasi/updatelokasisekolah', [KonfigurasiController::class, 'updatelokasisekolah']);
