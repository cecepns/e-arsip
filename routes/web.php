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
});

// Guest routes - only accessible when not authenticated
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
