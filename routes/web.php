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

Route::middleware(['auth', 'role:admin|kurikulum'])->group(function () {

Route::get('/dashboard', [HomeController::class,'dashboard'])->name('dashboard');
// USER
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

