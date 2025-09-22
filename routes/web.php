<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DasborController;

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
});
// !SECTION Protected routes - require authentication

// SECTION Guest routes - only accessible when not authenticated
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
// !SECTION Guest routes - only accessible when not authenticated