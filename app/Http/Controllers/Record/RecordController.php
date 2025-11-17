<?php

namespace App\Http\Controllers\Record;

use App\Http\Controllers\Controller;
use App\Http\Requests\Record\StoreRecordRequest;
use App\Http\Requests\Record\UpdateRecordRequest;
use App\Models\Record;
use App\Services\FileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class RecordController extends Controller
{

    private function uploadDocument(Request $request)
    {
        try {
            $fileService = new FileService();
            $documentId = NULL;
            if ($request->file('document')) {
                return $fileService->upload($request->file('document'), '/records')->id;
            }
            return NULL;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /**
     * 1️⃣ عرض جميع السجلات مع البحث والترتيب والتقسيم إلى صفحات
     */
    public function index(Request $request)
    {
        try {
            // 1️⃣ بناء الاستعلام الأساسي
            $recordsQuery = Record::query();

            // 2️⃣ البحث العام
            if ($request->filled('searchKey')) {
                $key = $request->searchKey;

                $recordsQuery->where(function ($q) use ($key) {
                    $q->where('no', 'like', "%$key%")
                        ->orWhere('referenceNumber', 'like', "%$key%")
                        ->orWhere('year', 'like', "%$key%");
                });
            }

            // 🔹 تصفية حسب الفرع
            if ($request->filled('branchId')) {
                $recordsQuery->where('branchId', $request->branchId);
            }

            // 🔹 تصفية حسب اللجنة
            if ($request->filled('committeeId')) {
                $recordsQuery->where('committeeId', $request->committeeId);
            }

            // 🔹 تصفية حسب السنة
            if ($request->filled('year')) {
                $recordsQuery->where('year', $request->year);
            }

            // 3️⃣ الترتيب
            if ($request->has('sortBy') && $request->has('sortDir')) {
                $allowed = ['no', 'referenceNumber', 'year', 'branchId', 'committeeId'];

                if (in_array($request->sortBy, $allowed)) {
                    $recordsQuery->orderBy(
                        $request->sortBy,
                        $request->boolean('sortDir') ? 'desc' : 'asc'
                    );
                }
            } else {
                // ترتيب افتراضي بالأحدث
                $recordsQuery->latest('id');
            }

            // 4️⃣ التقسيم إلى صفحات
            $records = $recordsQuery
                ->with(['branch:id,name', 'committee:id,no,yearOfEstablishment', 'document:id,name', 'creator:id,username'])
                ->paginate(
                    $request->get('perPage', config('request.pagination.per_page', 10)),
                    ['*'],
                    'page',
                    $request->get('page', 1)
                );

            return response()->json($records, 200);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء جلب السجلات: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء تحميل السجلات.'], 500);
        }
    }

    /**
     * 2️⃣ إضافة سجل جديد
     */
    public function store(StoreRecordRequest $request)
    {
        try {



            // 1️⃣ البيانات التي تم التحقق منها
            $validated = $request->validated();

            // 2️⃣ إضافة المستخدم المنشئ
            $validated['createdBy'] = auth()->user()->id;

            // 3️⃣ تحميل الوثيقة ان وجدت


            $validated['docId'] = $this->uploadDocument($request);

            // 4️⃣ إنشاء السجل
            $record = Record::create($validated);

            return response()->json([
                'message' => 'تم إنشاء السجل بنجاح.',
                'data'    => $record
            ], 201);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء إضافة سجل: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء إنشاء المحضر.'], 500);
        }
    }

    /**
     * 3️⃣ عرض سجل محدد
     */
    public function show(int $id)
    {
        try {
            $record = Record::with(['branch', 'committee', 'document', 'creator'])
                ->findOrFail($id);

            return response()->json($record, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'السجل غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء عرض السجل: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء عرض السجل.'], 500);
        }
    }

    /**
     * 4️⃣ تحديث سجل معين
     */
    public function update(UpdateRecordRequest $request, int $id)
    {
        try {
            $record = Record::findOrFail($id);

            $validated = $request->validated();

            $validated['docId'] = $request->file('document') ? $this->uploadDocument($request) : $record->docId;
            $record->update($validated);

            return response()->json([
                'message' => 'تم تحديث بيانات السجل بنجاح.',
                'data'    => $record
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'السجل غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء التحديث: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء تحديث السجل.'], 500);
        }
    }

    /**
     * 5️⃣ حذف سجل معين
     */
    public function destroy(int $id)
    {
        try {
            $record = Record::findOrFail($id);

            $record->delete();

            return response()->json(['message' => 'تم حذف السجل بنجاح.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'السجل غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء الحذف: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء حذف السجل.'], 500);
        }
    }

    /**
     * 6️⃣ عرض قائمة مختصرة من السجلات (للاستخدام في select)
     */
    public function listOfAllRecords()
    {
        try {
            $records = Record::select('id', 'no', 'referenceNumber', 'year')
                ->orderBy('no')
                ->get();

            return response()->json($records, 200);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء جلب قائمة السجلات: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء جلب السجلات.'], 500);
        }
    }

    /**
     * 7️⃣ جلب العدد الإجمالي للسجلات
     */
    public function getRecordsCount()
    {
        try {
            $count = Record::count();
            return response()->json($count, 200);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء حساب عدد السجلات: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء حساب العدد.'], 500);
        }
    }

    /**
     * 8️⃣ تعديل بيانات متقدمة (تغيير اللجنة / السنة / الفرع)
     */
    public function changeRecordInfo(Request $request, int $id)
    {
        try {
            $record = Record::findOrFail($id);

            if ($request->filled('committeeId')) {
                $record->committeeId = $request->committeeId;
            }

            if ($request->filled('branchId')) {
                $record->branchId = $request->branchId;
            }

            if ($request->filled('year')) {
                $record->year = $request->year;
            }

            $record->save();

            return response()->json([
                'message' => 'تم تعديل بيانات السجل.',
                'data'    => $record
            ], 200);
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء تعديل بيانات السجل: {$e->getMessage()}");
            return response()->json(['message' => 'حدث خطأ أثناء التعديل.'], 500);
        }
    }
}
