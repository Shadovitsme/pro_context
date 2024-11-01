<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
})->name('home');

Route::get('/cheats', function () {
    return view('cheats');
})->name('cheats');

Route::get('/test', function () {
    return view('test');
})->name('test');
