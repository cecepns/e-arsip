<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/login', function () {
    return view('pages.login.view');
});

Route::post('/login', function (Request $request) {
    return back()->with('error', 'Login belum diimplementasi.');
})->name('login');
