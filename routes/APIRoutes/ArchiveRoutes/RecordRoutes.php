<?php

use App\Http\Controllers\Record\RecordController;
use App\Http\Middleware\Accessibility;
use Illuminate\Support\Facades\Route;

Route::prefix('/records')->group(function () {

    // 1️⃣ إنشاء محضر جديد
    Route::middleware(Accessibility::class . ':record,create')->post('/', [RecordController::class, 'store']);

    // 6️⃣ استرجاع قائمة للاختيارات (DropDowns)
    Route::middleware(Accessibility::class . ':record,read')->get('/list', [RecordController::class, 'listOfAllRecords'])
        ->name('records.dropdowns');

    // جلب عدد المحاضر
    Route::middleware(Accessibility::class . ':record,read')->get('/count', [RecordController::class, 'getRecordsCount'])
        ->name('records.count');

    // 2️⃣ عرض قائمة المحاضر مع البحث والترتيب والفلترة
    Route::middleware(Accessibility::class . ':record,read')->get('/', [RecordController::class, 'index']);

    // 3️⃣ عرض محضر معيّن
    Route::get('/{id}', [RecordController::class, 'show'])
        ->name('records.show');

    // 4️⃣ تحديث محضر
    Route::put('/{id}', [RecordController::class, 'update'])
        ->name('records.update');

    // 5️⃣ حذف محضر
    Route::delete('/{id}', [RecordController::class, 'destroy'])
        ->name('records.destroy');





    // 7️⃣ التحقق من رقم المحضر قبل الحفظ (اختياري)
    Route::post('/validate/no', [RecordController::class, 'validateRecordNo'])
        ->name('records.validate.no');

    // 8️⃣ التحقق من الرقم الإشاري قبل الحفظ (اختياري)
    Route::post('/validate/reference', [RecordController::class, 'validateReference'])
        ->name('records.validate.reference');
});
