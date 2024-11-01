<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {
    
    Route::get('/users/{id?}', [UserController::class, 'get'])->where('id', '[0-9]*');

    Route::post('/users', [UserController::class, 'register']);

    Route::patch('/users/{id?}', [UserController::class, 'update'])->where('id', '[0-9]*');

    Route::delete('/users/{id}', [UserController::class, 'delete'])->where('id', '[0-9]*');

    Route::post('users/authenticate', [UserController::class, 'authenticate']);
});

