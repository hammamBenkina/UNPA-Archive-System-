<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Committee\StoreCommitteeMemberRequest;
use App\Models\CommitteeMember;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CommitteeMemberController extends Controller
{
    /**
     * 1️⃣ عرض جميع الأعضاء مع خيارات البحث والترتيب والتقسيم إلى صفحات
     */
    public function index(Request $request)
    {
        try {
            // 1️⃣ إنشاء الاستعلام الأساسي
            $membersQuery = CommitteeMember::query();

            // 2️⃣ البحث
            if ($request->filled('searchKey')) {
                $searchKey = $request->get('searchKey');
                $membersQuery->where(function ($query) use ($searchKey) {
                    $query->where('name', 'like', "%$searchKey%")
                        ->orWhere('adjective', 'like', "%$searchKey%");
                });
            }

            // التصفية بالدور
            if ($request->filled('committeeId')) {
                $membersQuery->where('committeeId', $request->get('committeeId'));
            }

            // 3️⃣ الترتيب
            if ($request->has('sortBy') && $request->has('sortDir')) {
                $allowedSorts = ['name', 'adjective', 'committeeId'];
                if (in_array($request->sortBy, $allowedSorts)) {
                    $membersQuery->orderBy(
                        $request->sortBy,
                        $request->boolean('sortDir') ? 'desc' : 'asc'
                    );
                }
            } else {
                // 🔹 ترتيب افتراضي بالأحدث
                $membersQuery->latest('id');
            }

            // 4️⃣ التقسيم إلى صفحات
            $members = $membersQuery
                ->with(['committee:id,no,yearOfEstablishment', 'creator:id,username'])
                ->paginate(
                    $request->get('perPage', config('request.pagination.per_page', 10)),
                    ['*'],
                    'page',
                    $request->get('page', 1)
                );

            // 5️⃣ الإرجاع
            return response()->json($members, 200);
        } catch (\Throwable $e) {
            Log::error('خطأ في جلب أعضاء اللجان: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء تحميل بيانات الأعضاء.'], 500);
        }
    }

    /**
     * 2️⃣ إضافة عضو جديد
     */
    public function store(StoreCommitteeMemberRequest $request)
    {
        try {
            // 1️⃣ التحقق من صحة البيانات
            $validated = $request->validated();

            // 2️⃣ إكمال البيانات
            $validated['createdBy'] = auth()->user()->id;
            $validated['about'] = $validated['about'] ?? null;
            $validated['accountId'] = $validated['accountId'] ?? null;


            // 3️⃣ إنشاء العضو
            $member = CommitteeMember::create($validated);

            // 4️⃣ الإرجاع
            return response()->json([
                'message' => 'تمت إضافة العضو بنجاح.',
                'data' => $member
            ], 201);
        } catch (\Throwable $e) {
            return $e->getMessage();
            Log::error('خطأ أثناء إضافة عضو: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء إضافة العضو.'], 500);
        }
    }

    /**
     * 3️⃣ عرض عضو محدد
     */
    public function show(int $id)
    {
        try {
            // 1️⃣ البحث عن العضو
            $member = CommitteeMember::with(['committee:id,no,yearOfEstablishment', 'creator:id,username'])
                ->findOrFail($id);

            // 2️⃣ الإرجاع
            return response()->json($member, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'العضو غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء عرض بيانات العضو: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء عرض بيانات العضو.'], 500);
        }
    }

    /**
     * 4️⃣ تحديث بيانات عضو معين
     */
    public function update(StoreCommitteeMemberRequest $request, int $id)
    {
        try {
            // 1️⃣ البحث عن العضو
            $member = CommitteeMember::findOrFail($id);

            // 2️⃣ التحقق من البيانات
            $validated = $request->validated();

            // 3️⃣ تحديث البيانات
            $member->update($validated);

            // 4️⃣ الإرجاع
            return response()->json([
                'message' => 'تم تحديث بيانات العضو بنجاح.',
                'data' => $member
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'العضو غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء تحديث بيانات العضو: ' . $e->getMessage());

            return response()->json(['message' => 'حدث خطأ أثناء تحديث بيانات العضو.'], 500);
        }
    }


    /**
     * 5️⃣ حذف عضو معين
     */
    public function destroy(int $id)
    {
        try {
            // 1️⃣ البحث عن العضو
            $member = CommitteeMember::findOrFail($id);

            // 2️⃣ الحذف
            $member->delete();

            // 3️⃣ الإرجاع
            return response()->json(['message' => 'تم حذف العضو بنجاح.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'العضو غير موجود.'], 404);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء حذف العضو: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء حذف العضو.'], 500);
        }
    }

    /**
     * 6️⃣ عرض جميع الأعضاء المختصرين (للاستخدام في select مثلاً)
     */
    public function listOfAllMembers()
    {
        try {
            $members = CommitteeMember::select('id', 'name', 'adjective', 'committeeId')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json($members, 200);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء جلب قائمة الأعضاء: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء جلب الأعضاء.'], 500);
        }
    }

    /**
     * 7️⃣ جلب عدد الأعضاء الإجمالي
     */
    public function getMembersCount()
    {


        try {
            $count = CommitteeMember::count();
            return response()->json($count, 200);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء حساب عدد الأعضاء: ' . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء حساب عدد الأعضاء.'], 500);
        }
    }

    /**
     * 8️⃣ تعديل رقم اللجنة الخاصة بعضو محدد
     */
    public function changeCommitteeId(Request $request, int $id)
    {
        try {
            // 1️⃣ التحقق من وجود العضو
            $member = CommitteeMember::findOrFail($id);

            // 2️⃣ تحديث رقم اللجنة
            $member->committeeId = $request->committeeId;

            // 3️⃣ حفظ التعديلات في قاعدة البيانات
            $member->save();

            // 4️⃣ إعادة استجابة ناجحة
            return response()->json([
                'message' => 'تم تعديل رقم اللجنة بنجاح.',
                'member'  => $member
            ], 200);
        } catch (\Throwable $e) {
            // 🔴 تسجيل الخطأ في السجلات
            Log::error('حدث خطأ أثناء تعديل رقم اللجنة: ' . $e->getMessage());

            // 🔴 إعادة استجابة بخطأ داخلي
            return response()->json([
                'message' => 'حدث خطأ أثناء تعديل رقم اللجنة.'
            ], 500);
        }
    }
}
