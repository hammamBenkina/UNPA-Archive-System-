<?php

use App\Http\Controllers\Record\RecordController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('records')->group(function () {

    Route::middleware(Accessibility::class . ':record,create')->post('/', [RecordController::class, 'store']);

    Route::middleware(Accessibility::class . ':record,edit')->put('/{recordId}', [RecordController::class, 'update']);

    Route::middleware(Accessibility::class . ':record,read')->get('/', [RecordController::class, 'index']);

    Route::middleware(Accessibility::class . ':record,read')->get('/list', [RecordController::class, 'listOfAllRecords']);

    Route::middleware(Accessibility::class . ':record,read')->get('/count', [RecordController::class, 'getRecordsCount']);
});
