<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\MatkulController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PerkuliahanController;
use App\Http\Controllers\Admin\PertanyaanController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\FeedbackController;
use App\Http\Controllers\Dosen\LaporanController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\KelasMahasiswaController;
use App\Http\Controllers\Mahasiswa\LogAktivitasController;
use App\Http\Controllers\Mahasiswa\PenilaianController;
use App\Models\LogAktivitas;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login.index');
    } else if (Auth::user()->role == 'admin') {
        return redirect()->route('admin.dashboard');
    } else if (Auth::user()->role == 'dosen') {
        return redirect()->route('dosen.dashboard');
    } else if (Auth::user()->role == 'mahasiswa') {
        return redirect()->route('mahasiswa.dashboard');
    } else {
        return redirect()->route('login.index');
    }

    // User is authenticated — go to dashboard or home
});

Route::get('/login', [AuthController::class, 'indexLogin'])->name('login.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::group(['middleware' => ['auth']], function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AuthController::class, 'profile'])->name('index');
        Route::put('/update/{user}', [AuthController::class, 'updateProfile'])->name('update');
        Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('password.update');
    });
});

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::post('import', [MahasiswaController::class, 'import'])->name('import');
            Route::get('data', [MahasiswaController::class, 'data'])->name('data');
            Route::resource('/', MahasiswaController::class)->parameters(['' => 'mahasiswa']);
        });
        Route::prefix('kriteria')->name('kriteria.')->group(function () {
            Route::get('data', [KriteriaController::class, 'data'])->name('data');
            Route::resource('/', KriteriaController::class)->parameters(['' => 'kriteria']);
        });
        Route::prefix('pertanyaan')->name('pertanyaan.')->group(function () {
            Route::get('data', [PertanyaanController::class, 'data'])->name('data');
            Route::resource('/', PertanyaanController::class)->parameters(['' => 'pertanyaan']);
        });
        Route::prefix('matkul')->name('matkul.')->group(function () {
            Route::get('data', [MatkulController::class, 'data'])->name('data');
            Route::resource('/', MatkulController::class)->parameters(['' => 'matkul']);
        });
        Route::prefix('semester')->name('semester.')->group(function () {
            Route::get('data', [SemesterController::class, 'data'])->name('data');
            Route::resource('/', SemesterController::class)->parameters(['' => 'semester']);
        });
        Route::prefix('kelas')->name('kelas.')->group(function () {
            Route::get('data', [KelasController::class, 'data'])->name('data');
            Route::resource('/', KelasController::class)->parameters(['' => 'kelas']);
        });
        Route::prefix('perkuliahan')->name('perkuliahan.')->group(function () {
            Route::get('data', [PerkuliahanController::class, 'data'])->name('data');
            Route::resource('/', PerkuliahanController::class)->parameters(['' => 'perkuliahan']);
        });
        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::post('import', [DosenController::class, 'import'])->name('import');
            Route::get('data', [DosenController::class, 'data'])->name('data');
            Route::resource('/', DosenController::class)->parameters(['' => 'dosen']);
        });
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::get('/', [AdminFeedbackController::class, 'index'])->name('index');
        });
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/cetak', [AdminLaporanController::class, 'cetak'])->name('cetak');
            Route::get('/data', [AdminLaporanController::class, 'data'])->name('data');
            Route::get('/', [AdminLaporanController::class, 'index'])->name('index');
        });
        Route::prefix('pengguna')->name('pengguna.')->group(function () {
            Route::get('/data', [PenggunaController::class, 'data'])->name('data');
            Route::resource('/', PenggunaController::class)->parameters(['' => 'pengguna']);
        });
    });
});


Route::group(['middleware' => ['auth', 'role:mahasiswa']], function () {
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::prefix('matkul')->name('matkul.')->group(function () {
            Route::post('import', [KelasMahasiswaController::class, 'import'])->name('import');
            Route::get('data', [KelasMahasiswaController::class, 'data'])->name('data');
            Route::resource('/', KelasMahasiswaController::class)->parameters(['' => 'matkul']);
        });
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::post('import', [PenilaianController::class, 'import'])->name('import');
            Route::resource('/', PenilaianController::class)->parameters(['' => 'feedback']);
        });
        Route::prefix('log')->name('log.')->group(function () {
            Route::get('data', [LogAktivitasController::class, 'data'])->name('data');
            Route::resource('/', LogAktivitasController::class)->parameters(['' => 'log']);
        });
    });
});

Route::group(['middleware' => ['auth', 'role:dosen']], function () {
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/', [DosenDashboardController::class, 'index'])->name('dashboard');
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
            Route::get('/data', [LaporanController::class, 'data'])->name('data');
            Route::resource('/', LaporanController::class)->parameters(['' => 'laporan']);
        });
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::get('/', [FeedbackController::class, 'index'])->name('index');
        });
    });
});


Route::prefix('global')->name('global.')->group(function () {
    Route::get('matkul', [GlobalController::class, 'getMatkul'])->name('matkul');
    Route::get('jumlah/{semesterId}', [GlobalController::class, 'getJumlahResponden'])->name('jumlah-reponden');
    Route::get('/admin/jumlah/{semesterId}', [GlobalController::class, 'getJumlahRespondenAdmin'])->name('admin-jumlah-reponden');
});
