<?php

use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(function () {
    
    Route::post('/', [UserController::class, 'store']);
    Route::get('/', [UserController::class, 'index']);
    Route::post('/login', [UserController::class, 'login']);

    Route::get('/roles', [RoleController::class, 'index']);
});
