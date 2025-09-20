<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('pages.dasbor.dasbor');
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Bagian (Data Master) - static views for now
    Route::prefix('bagian')->name('bagian.')->group(function () {
        // Index - daftar bagian
        Route::get('/', function () {
            return view('pages.bagian.index');
        })->name('index');

        // Create - tambah bagian
        Route::get('/create', function () {
            return view('pages.bagian.create');
        })->name('create');

        // Show - detail bagian
        Route::get('/{id}', function ($id) {
            return view('pages.bagian.show');
        })->whereNumber('id')->name('show');

        // Edit - ubah bagian
        Route::get('/{id}/edit', function ($id) {
            return view('pages.bagian.edit');
        })->whereNumber('id')->name('edit');
    });
});

// Guest routes - only accessible when not authenticated
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
