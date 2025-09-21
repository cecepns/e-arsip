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
    Route::get('/bagian', function () {
        return view('pages.bagian.index');
    })->name('bagian.index');
});
// !SECTION Protected routes - require authentication

// SECTION Guest routes - only accessible when not authenticated
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
// !SECTION Guest routes - only accessible when not authenticated