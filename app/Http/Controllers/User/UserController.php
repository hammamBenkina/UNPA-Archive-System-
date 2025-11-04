<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;


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
                'message' => 'حدث خطأ أثناء إنشاء المستخدم.',
            ], 500);
        }

        // ✅ إعادة استجابة منظمة
        return response()->json([
            'message' => 'تمت إضافة المستخدم بنجاح',
            'user'    => $user,
        ], 201);
    }


    public function login(Request $request){
        return 'this is login function';
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
