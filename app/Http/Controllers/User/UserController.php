<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $usersQuery = User::query();


        // البحث بالكلمات
        if ($request->filled('searchKey')) {
            $searchKey = $request->get('searchKey');
            $usersQuery->where(function ($query) use ($searchKey) {
                $query->where('username', 'like', "%$searchKey%")
                    ->orWhere('email', 'like', "%$searchKey%")
                    ->orWhere('phoneNumber', 'like', "%$searchKey%");
            });
        }


        // التصفية بالدور
        if ($request->filled('roleId')) {
            $usersQuery->where('roleId', $request->get('roleId'));
        }


        // الترتيب
        if ($request->has('sortBy') && $request->has('sortDir')) {
            $allowedSorts = ['username', 'phoneNumber', 'email'];
            if (in_array($request->sortBy, $allowedSorts)) {
                $usersQuery->orderBy($request->sortBy, $request->boolean('sortDir') ? 'desc' : 'asc');
            }
        }


        // العلاقات والصفحات
        $users = $usersQuery
            ->with(['role:id,name'])
            ->paginate(
                $request->get('perPage', config('request.pagination.per_page')),
                ['*'],
                'page',
                $request->get('page', 1)
            );

        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //

        // ✅ التحقق من البيانات باستخدام الـ Form Request
        $validated = $request->validated();

        // ✅ تشفير كلمة المرور قبل الحفظ

        // ✅ ضبط القيم الافتراضية في حال لم تُرسل من الواجهة
        $validated['createdBy'] = $validated['createdBy'] ?? NULL; // أو null
        $validated['active']    = 1;

        // ✅ إنشاء المستخدم
        $user = User::create($validated);

        // ✅ التأكد من نجاح العملية
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء المستخدم.',
            ], 500);
        }

        // ✅ إعادة استجابة منظمة
        return response()->json([
            'status' => true,
            'message' => 'تمت إضافة المستخدم بنجاح',
            'user'    => $user,
        ], 201);
    }



    public function login(Request $request){

        // 1️⃣ التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:users,username',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.exists' => 'اسم المستخدم غير موجود.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }


        // 2️⃣ تحديد مفتاح المحاولة الفريدة لكل مستخدم (لمنع محاولات التخمين)
        $key = 'login_attempts:' . $request->ip() . ':' . strtolower($request->username);


        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'status' => false,
                'message' => "تم تجاوز عدد المحاولات، يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }


        // 3️⃣ البحث عن المستخدم
        $user = User::where('username', $request->username)->first();


        // 4️⃣ التحقق من كلمة المرور
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); // حفظ محاولة فاشلة لمدة 60 ثانية
            return response()->json([
                'status' => false,
                'message' => 'بيانات الدخول غير صحيحة.',
            ], 401);
        }


        // ✅ في حال نجاح الدخول، نعيد تعيين المحاولات
        RateLimiter::clear($key);


        // 5️⃣ التحقق من حالة الحساب
        if (!$user->active) {
            return response()->json([
                'status' => false,
                'message' => 'الحساب غير مفعل. يرجى التواصل مع الدعم.',
            ], 403);
        }


        // 6️⃣ (اختياري) السماح بتسجيل دخول من جهاز واحد فقط
        // لحذف التوكنات القديمة (في حال أردت هذا السلوك)
        $user->tokens()->delete();


        // 7️⃣ إنشاء توكن جديد مع صلاحية زمنية محددة (اختياري)
        $token = $user->createToken('auth_token')->plainTextToken;


        // 8️⃣ حفظ بيانات الدخول الأخيرة (للتتبع الأمني)
        // $user->update([
        //     'last_login_at' => Carbon::now(),
        //     'last_login_ip' => $request->ip(),
        // ]);


        // 9️⃣ إرجاع البيانات
        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phoneNumber' => $user->phoneNumber,
                'last_login_at' => $user->last_login_at,
            ],
            'token' => $token,
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
