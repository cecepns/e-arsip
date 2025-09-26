<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\UserController;

// SECTIONProtected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // ANCHOR: Dashboard (Page)
    Route::get('/', [DasborController::class, 'index'])->name('dasbor.index');
    
    // ANCHOR: Logout [POST]
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ANCHOR: Manajemen Bagian (Divisi)
    Route::get('/bagian', [\App\Http\Controllers\BagianController::class, 'index'])->name('bagian.index');
    Route::post('/bagian', [\App\Http\Controllers\BagianController::class, 'store'])->name('bagian.store');
    Route::put('/bagian/{id}', [\App\Http\Controllers\BagianController::class, 'update'])->name('bagian.update');
    Route::delete('/bagian/{id}', [\App\Http\Controllers\BagianController::class, 'destroy'])->name('bagian.destroy');
    Route::get('/bagian/{id}', [\App\Http\Controllers\BagianController::class, 'show'])->name('bagian.show');

    // ANCHOR: Manajemen User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');

    // ANCHOR: Manajemen Surat Keluar
    Route::get('/surat-keluar', [\App\Http\Controllers\SuratKeluarController::class, 'index'])->name('surat_keluar.index');
    Route::get('/surat-keluar/create', [\App\Http\Controllers\SuratKeluarController::class, 'create'])->name('surat_keluar.create');
    Route::post('/surat-keluar', [\App\Http\Controllers\SuratKeluarController::class, 'store'])->name('surat_keluar.store');
    Route::get('/surat-keluar/{id}', [\App\Http\Controllers\SuratKeluarController::class, 'show'])->name('surat_keluar.show');
    Route::get('/surat-keluar/{id}/edit', [\App\Http\Controllers\SuratKeluarController::class, 'edit'])->name('surat_keluar.edit');
    Route::put('/surat-keluar/{id}', [\App\Http\Controllers\SuratKeluarController::class, 'update'])->name('surat_keluar.update');
    Route::delete('/surat-keluar/{id}', [\App\Http\Controllers\SuratKeluarController::class, 'destroy'])->name('surat_keluar.destroy');
});
// !SECTION Protected routes - require authentication

// SECTION Guest routes - only accessible when not authenticated
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
// !SECTION Guest routes - only accessible when not authenticated