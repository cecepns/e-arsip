<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('pages.dasbor.dasbor');
});

Route::get('/login', function () {
    return view('pages.autentikasi.login');
});

Route::post('/login', function (Request $request) {
    return back()->with('error', 'Login belum diimplementasi.');
})->name('login');
