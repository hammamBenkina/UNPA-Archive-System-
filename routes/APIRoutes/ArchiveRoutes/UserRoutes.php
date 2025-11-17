<?php

use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('/users')->group(function () {

    Route::middleware(Accessibility::class . ':users,create')->post('/', [UserController::class, 'store']); // Endpoint
    Route::middleware(Accessibility::class . ':users,read')->get('/', [UserController::class, 'index']);
    Route::middleware(Accessibility::class . ':users,changeActiveStatus')->put('/change-activation-status', [UserController::class, 'changeActivationStatus']);
    Route::put('/update-personal-password', [UserController::class, 'updatePersonalPassword']);
    Route::prefix('/{userId}')->group(function () {
        Route::get('/', [UserController::class, 'show']);
        Route::middleware(['auth:sanctum'])->put('/', [UserController::class, 'update']);
        Route::middleware(['auth:sanctum'])->put('/update-personal-password', [UserController::class, 'updatePersonalPassword']);
    });
    Route::post('/login', [UserController::class, 'login']);


    Route::middleware(Accessibility::class . ':roles,read')->get('/roles', [RoleController::class, 'index']);
});
