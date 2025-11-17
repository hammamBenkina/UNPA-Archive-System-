<?php

use App\Http\Controllers\BranchController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('/branches')->group(function () {
    Route::middleware(Accessibility::class . ':branch,read')->get('/list', [BranchController::class, 'listOfAllBranches']);
});
