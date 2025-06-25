<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Controller Imports
|--------------------------------------------------------------------------
| Mengimpor semua controller yang dibutuhkan sesuai struktur folder Anda.
| Alias 'as' digunakan untuk membedakan controller dengan nama yang sama.
*/

// Controller Utama & Otentikasi
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SiswaController; // Controller utama untuk dashboard siswa

// Controller untuk Admin
// Tidak ada DashboardController di Admin/, jadi kita gunakan AdminController
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController; // Alias untuk Admin
use App\Http\Controllers\Admin\TagihanController as AdminTagihanController;
use App\Http\Controllers\Admin\LaporanController; // INI YANG PENTING!

// Controller untuk Bendahara
use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\Bendahara\PembayaranController as BendaharaPembayaranController;
use App\Http\Controllers\Bendahara\VerifikasiController;
use App\Http\Controllers\Bendahara\LaporanController as BendaharaLaporanController;

// Controller untuk Siswa (Fitur spesifik)
use App\Http\Controllers\Siswa\TagihanController;
use App\Http\Controllers\Siswa\UploadController;
use App\Http\Controllers\Siswa\RiwayatController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route untuk otentikasi (Login, Logout, Register)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Grup route yang hanya bisa diakses setelah login
Route::middleware(['auth'])->group(function () {

    // Route utama setelah login, akan mengarahkan berdasarkan peran
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'bendahara') return redirect()->route('bendahara.dashboard');
        if ($user->role === 'siswa') return redirect()->route('siswa.dashboard');
        return redirect('/login'); // Fallback
    })->name('dashboard.redirect');

    // ===================================
    // Grup Route untuk ADMIN
    // ===================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Menggunakan AdminController yang ada di root Controllers
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('siswa', AdminSiswaController::class); // Menggunakan alias AdminSiswaController
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Rute untuk menampilkan bukti pembayaran spesifik (melalui ID)
    // Pastikan ini juga berada di dalam grup admin jika hanya admin yang boleh melihatnya
    Route::get('/laporan/bukti/{id}', [LaporanController::class, 'showBukti'])->name('laporan.show_bukti');        
     Route::get('/tagihan', [AdminTagihanController::class, 'index'])->name('tagihan.index');
    Route::post('/tagihan', [AdminTagihanController::class, 'store'])->name('tagihan.store');
     Route::get('/laporan/export-csv', [AdminLaporanController::class, 'exportCsv'])->name('laporan.export_csv');
    // Tambahkan rute ini untuk mengambil data kwitansi melalui Ajax di panel Admin
    Route::get('/laporan/get-kwitansi-data/{pembayaran}', [AdminLaporanController::class, 'getKwitansiData'])->name('laporan.get_kwitansi_data');
    });

    // ===================================
    // Grup Route untuk BENDAHARA
    // ===================================
    Route::middleware(['role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [BendaharaDashboardController::class, 'index'])->name('dashboard');
    
    // Route untuk mencatat pembayaran diubah
    Route::get('pembayaran', [BendaharaPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('pembayaran', [BendaharaPembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('pembayaran/get-tagihan', [BendaharaPembayaranController::class, 'getTagihan'])->name('pembayaran.getTagihan');
    
    // Route lainnya tetap sama
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi/update/{pembayaran}', [VerifikasiController::class, 'update'])->name('verifikasi.update');
    Route::get('/laporan', [BendaharaLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/cetak-kwitansi/{pembayaran}', [BendaharaPembayaranController::class, 'cetakKwitansi'])->name('cetak.kwitansi');

    Route::get('/laporan/get-kwitansi-data/{pembayaran}', [LaporanController::class, 'getKwitansiData'])->name('laporan.get_kwitansi_data');
    // Rute untuk export laporan ke CSV
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export_csv');
    
});

    // ===================================
    // Grup Route untuk SISWA
    // ===================================
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'index'])->name('dashboard'); // Menggunakan SiswaController utama
        Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/upload', [UploadController::class, 'create'])->name('upload.create');
        Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
        Route::get('/kwitansi/{pembayaran}', [RiwayatController::class, 'cetakKwitansi'])->name('kwitansi.cetak');
    });
});