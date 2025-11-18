<?php

use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('/users')->group(function () {

    Route::middleware(Accessibility::class . ':users,create')->post('/', [UserController::class, 'store']);
    Route::middleware(Accessibility::class . ':users,read')->get('/', [UserController::class, 'index']);
    Route::post('/login', [UserController::class, 'login']);
    Route::middleware(Accessibility::class . ':users,read')->get('/roles', [RoleController::class, 'index']);
    Route::prefix('/{userId}')->group(function () {
        Route::middleware(Accessibility::class . ':users,read')->get('/', [UserController::class, 'show']);
        Route::middleware(Accessibility::class . ':users,edit')->put('/', [UserController::class, 'update']);
        Route::middleware(Accessibility::class . ':users,edit')->put('/change-activation-status', [UserController::class, 'changeActivationStatus']);
        Route::middleware(Accessibility::class . ':users,edit')->put('/update-personal-password', [UserController::class, 'updatePersonalPassword']);
    });
});
