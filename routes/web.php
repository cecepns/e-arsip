<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('pages.login.view');
});
