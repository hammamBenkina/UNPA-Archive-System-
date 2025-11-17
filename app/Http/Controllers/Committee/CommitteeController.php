<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Committee\StoreCommitteeRequest;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommitteeController extends Controller
{
    /**
     * 1️⃣ عرض جميع اللجان مع البحث والترتيب والتقسيم إلى صفحات
     */
    public function index(Request $request)
    {
        try {

            // 1️⃣ إنشاء الاستعلام الأساسي
            $committeesQuery = Committee::query();

            // 2️⃣ البحث
            if ($request->filled('searchKey')) {
                $searchKey = $request->get('searchKey');
                $committeesQuery->where(function ($query) use ($searchKey) {
                    $query->where('no', 'like', "%$searchKey%")
                        ->orWhere('yearOfEstablishment', 'like', "%$searchKey%");
                });
            }

            // 3️⃣ الترتيب
            if ($request->has('sortBy') && $request->has('sortDir')) {
                $allowedSorts = ['no', 'yearOfEstablishment'];
                if (in_array($request->sortBy, $allowedSorts)) {
                    $committeesQuery->orderBy(
                        $request->sortBy,
                        $request->boolean('sortDir') ? 'desc' : 'asc'
                    );
                }
            } else {
                // 🔹 ترتيب افتراضي بالأحدث
                $committeesQuery->latest('id');
            }

            // 4️⃣ التقسيم إلى صفحات
            $committees = $committeesQuery->with(['user:id,username'])->withCOunt('members')->paginate(
                $request->get('perPage', config('request.pagination.per_page', 10)),
                ['*'],
                'page',
                $request->get('page', 1)
            );

            // 5️⃣ الإرجاع
            return response()->json($committees, 200);
        } catch (\Throwable $e) {
            Log::error('خطأ في جلب اللجان: ' . $e->getMessage());

            return response()->json([
                'message' => 'حدث خطأ أثناء تحميل اللجان.'
            ], 500);
        }
    }

    /**
     * 2️⃣ إضافة لجنة جديدة بعد التحقق من البيانات
     */
    public function store(StoreCommitteeRequest $request)
    {


        try {

            // 1️⃣ التحقق من صحة البيانات
            $validated = $request->validated();

            $validated['createdBy'] = auth()->user()->id;

            // 2️⃣ إنشاء اللجنة
            $committee = Committee::create($validated);

            // 3️⃣ الإرجاع
            return response()->json([
                'message' => 'تمت إضافة اللجنة بنجاح.',
                'data' => $committee
            ], 201);
        } catch (\Throwable $e) {

            Log::error('خطأ أثناء إضافة لجنة: ' . $e->getMessage());

            return response()->json([
                'message' => 'حدث خطأ أثناء إضافة اللجنة.'
            ], 500);
        }
    }


    /**
     * 3️⃣ عرض لجنة معينة
     */
    public function show($id)
    {
        try {
            // 1️⃣ البحث عن اللجنة
            $committee = Committee::findOrFail($id);



            // 2️⃣ الإرجاع
            return response()->json($committee, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'اللجنة غير موجودة.'
            ], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء عرض لجنة: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء عرض بيانات اللجنة.'
            ], 500);
        }
    }

    /**
     * 4️⃣ تحديث بيانات لجنة معينة
     */
    public function update(StoreCommitteeRequest $request, $id)
    {
        try {
            // 1️⃣ البحث عن اللجنة
            $committee = Committee::findOrFail($id);


            // 2️⃣ التحقق من البيانات
            $validated = $request->validated();

            // 3️⃣ تحديث البيانات
            $committee->update($validated);

            // 4️⃣ الإرجاع
            return response()->json([
                'message' => 'تم تحديث بيانات اللجنة بنجاح.',
                'data' => $committee
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'اللجنة غير موجودة.'
            ], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء تحديث لجنة: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث بيانات اللجنة.'
            ], 500);
        }
    }

    /**
     * 5️⃣ حذف لجنة معينة
     */
    public function destroy($id)
    {
        try {
            // 1️⃣ البحث عن اللجنة
            $committee = Committee::findOrFail($id);


            // 2️⃣ الحذف
            $committee->delete();

            // 3️⃣ الإرجاع
            return response()->json([
                'message' => 'تم حذف اللجنة بنجاح.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'اللجنة غير موجودة.'
            ], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء حذف لجنة: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء حذف اللجنة.'
            ], 500);
        }
    }

    /**
     * تحديث حالة اللجنة
     */
    public function setIsCurrent(Request $request, int $committeeId)
    {
        try {
            // ✅ التحقق من صلاحية المستخدم (يمكن تخصيصها حسب middleware أو policies)

            // 1️⃣ البحث عن اللجنة المحددة
            $committee = Committee::findOrFail($committeeId);

            DB::transaction(function () use ($committee) {

                // 2️⃣ تحديث جميع اللجان وجعل isCurrent = 0
                Committee::query()->update(['isCurrent' => 0]);

                // 3️⃣ تحديث اللجنة المحددة وجعلها الحالية
                $committee->update([
                    'isCurrent' => 1,
                ]);
            });

            return response()->json([
                'message' => 'تم تحديث حالة اللجنة الحالية بنجاح.',
                'data' => $committee
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'اللجنة غير موجودة.'
            ], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ في تحديث اللجنة الحالية: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث اللجنة الحالية.'
            ], 500);
        }
    }



    public function listOfAllCommittees()
    {
        try {
            // 1️⃣ جلب كل اللجان فقط بالحقول الضرورية
            $committees = Committee::select('id', 'no', 'yearOfEstablishment')
                ->orderBy('no', 'asc') // ترتيب حسب رقم اللجنة
                ->get();

            // 2️⃣ إرسال النتيجة
            return response()->json([
                $committees
            ], 200);
        } catch (\Throwable $e) {
            Log::error('خطأ في جلب اللجان للقائمة: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب اللجان.'
            ], 500);
        }
    }

    public function getCommitteesCount(Request $request)
    {
        try {
            // 1️⃣ جلب عدد اللجان
            $committees =  Committee::count();

            // 2️⃣ إرسال النتيجة
            return response()->json(
                $committees,
                200
            );
        } catch (\Throwable $e) {
            Log::error('خطأ في جلب عدد اللجان : ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب اللجان.'
            ], 500);
        }
    }
}
