<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');
});
