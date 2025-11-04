<?php

use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(function () {
    Route::middleware(Accessibility::class . ':users,create')->post('/', [UserController::class, 'store']);
    Route::middleware(Accessibility::class . ':users,read')->get('/', [UserController::class, 'index']);
    Route::post('/login', [UserController::class, 'login']);


    Route::middleware(Accessibility::class . ':roles,read')->get('/roles', [RoleController::class, 'index']);
});
