<?php

use App\Http\Controllers\Applicants\ApplicantsController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('applicants')->group(function () {

    Route::middleware(Accessibility::class . ':applicants,create')->post('/', [ApplicantsController::class, 'store']);

    Route::middleware(Accessibility::class . ':applicants,read')->get('/types', [ApplicantsController::class, 'getApplicantsTypes']);

    Route::middleware(Accessibility::class . ':applicants,read')->get('/', [ApplicantsController::class, 'index']);
});
