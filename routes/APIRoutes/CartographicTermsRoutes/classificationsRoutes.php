<?php

use App\Http\Controllers\cartographicTerms\ClassificationController;
use App\Http\Controllers\cartographicTerms\ClassificationsCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('cartographic-terms')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('/', [ClassificationsCategoryController::class, 'index']);
        Route::get('/list', [ClassificationsCategoryController::class, 'listOfAllCategories']);
        Route::post('/', [ClassificationsCategoryController::class, 'store']);
        Route::get('/{classificationsCategory}', [ClassificationsCategoryController::class, 'show']);
        Route::put('/{classificationsCategory}', [ClassificationsCategoryController::class, 'update']);
        Route::delete('/{classificationsCategory}', [ClassificationsCategoryController::class, 'destroy']);
    });

    Route::prefix('classifications')->group(function () {
        Route::get('/', [ClassificationController::class, 'index']);
        Route::get('/list', [ClassificationController::class, 'listOfAllClassifications']);
        Route::post('/', [ClassificationController::class, 'store']);
        Route::get('/{classificationsCategory}', [ClassificationController::class, 'show']);
        Route::put('/{classificationsCategory}', [ClassificationController::class, 'update']);
        Route::delete('/{classificationsCategory}', [ClassificationController::class, 'destroy']);
    });
});
