<?php

namespace App\Http\Controllers\Applicants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicants\StoreApplicantRequest;
use App\Models\Applicants;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApplicantsController extends Controller
{
    /**
     * 1️⃣ عرض جميع المتقدمين مع البحث والترتيب والتقسيم إلى صفحات
     */
    public function index(Request $request)
    {
        try {
            $query = Applicants::query();

            // 🔍 البحث
            if ($request->filled('searchKey')) {
                $search = $request->get('searchKey');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%")
                        ->orWhere('nationId', 'like', "%$search%");
                });
            }

            // 🔹 التصفية بالنوع
            if ($request->filled('type')) {
                $query->where('type', $request->get('type'));
            }

            // 🔹 الترتيب
            if ($request->has('sortBy') && $request->has('sortDir')) {
                $allowedSorts = ['name', 'type', 'email', 'created_at'];
                if (in_array($request->sortBy, $allowedSorts)) {
                    $query->orderBy(
                        $request->sortBy,
                        $request->boolean('sortDir') ? 'desc' : 'asc'
                    );
                }
            } else {
                $query->latest('id');
            }

            // 🔹 التقسيم إلى صفحات
            $applicants = $query->paginate(
                $request->get('perPage', 10),
                ['*'],
                'page',
                $request->get('page', 1)
            );

            return response()->json($applicants, 200);
        } catch (\Throwable $e) {
            Log::error('خطأ في جلب المتقدمين: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء تحميل بيانات المتقدمين.'], 500);
        }
    }



    /**
     * 2️⃣ إضافة متقدم جديد
     */
    public function store(StoreApplicantRequest $request)
    {
        try {
            $validated = $request->validated();

            $applicant = Applicants::create($validated);

            return response()->json([
                'message' => 'تمت إضافة المتقدم بنجاح.',
                'data' => $applicant,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء إضافة المتقدم: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء إضافة المتقدم.'], 500);
        }
    }

    /**
     * 3️⃣ عرض متقدم محدد
     */
    public function show(int $id)
    {
        try {
            $applicant = Applicants::findOrFail($id);
            return response()->json($applicant, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'المتقدم غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء عرض بيانات المتقدم: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء عرض بيانات المتقدم.'], 500);
        }
    }

    /**
     * 4️⃣ تحديث بيانات متقدم معين
     */
    public function update(StoreApplicantRequest $request, int $id)
    {
        try {
            $applicant = Applicants::findOrFail($id);
            $validated = $request->validated();

            $applicant->update($validated);

            return response()->json([
                'message' => 'تم تحديث بيانات المتقدم بنجاح.',
                'data' => $applicant,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'المتقدم غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء تحديث بيانات المتقدم: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء تحديث بيانات المتقدم.'], 500);
        }
    }

    /**
     * 5️⃣ حذف متقدم
     */
    public function destroy(int $id)
    {
        try {
            $applicant = Applicants::findOrFail($id);
            $applicant->delete();

            return response()->json(['message' => 'تم حذف المتقدم بنجاح.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'المتقدم غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء حذف المتقدم: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء حذف المتقدم.'], 500);
        }
    }

    /**
     * 6️⃣ جلب قائمة مختصرة (لاستخدامها في select)
     */
    public function listOfApplicants()
    {
        try {
            $list = Applicants::select('id', 'name', 'type')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json($list, 200);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء جلب قائمة المتقدمين: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء جلب القائمة.'], 500);
        }
    }

    /**
     * 7️⃣ إحصائية عدد المتقدمين
     */
    public function getApplicantsCount()
    {
        try {
            $count = Applicants::count();
            return response()->json($count, 200);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء حساب عدد المتقدمين: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء الحساب.'], 500);
        }
    }

    /**
     * 8️⃣ جلب أنواع الجهات التي يمكنها التقديم على الطلب
     */
    public function getApplicantsTypes(Request $request)
    {
        try {
            return response()->json(Applicants::$TYPES, 200);
        } catch (\Throwable $e) {
            Log::error('❌ خطأ أثناء جلب أنواع الجهات المتقدمة: ' . $e->getMessage());

            return response()->json([
                'message' => 'حدث خطأ أثناء جلب أنواع الجهات المتقدمة، يرجى المحاولة لاحقًا.'
            ], 500);
        }
    }
}
