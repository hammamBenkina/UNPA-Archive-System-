<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Accessibility
{

    public function handle(Request $request, Closure $next, $module = null, $action = null): Response
    {
        // 1️⃣ التحقق من وجود token في الـ header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'رمز الوصول مفقود'], 401);
        }


        // 2️⃣ التحقق من صحة الـ token وجلب المستخدم
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return response()->json(['message' => 'رمز الوصول غير صالح أو منتهي الصلاحية'], 401);
        }

        $user = $accessToken->tokenable; // المستخدم المرتبط بالـ token



        // 3️⃣ تحديد صلاحيات كل نوع مستخدم
        $accessibility = [

            'admin' => [
                'applicants' => ['read',  'export', 'report',],
                'applicants_requests' => ['read',  'export', 'report',],
                'applicant_request_reason' => ['read', 'export', 'report',],
                'branch' => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'committee' => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'committee_members' => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'file' => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'record' => ['read',  'export', 'report',],
                'record_item' => ['read',  'export', 'report',],
                'record_item_picture' => ['read',  'export', 'report',],
                'role' => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'users' => ['create', 'edit', 'read', 'delete', 'export', 'report', 'changeActiveStatus'],
                'user_history' => ['create', 'edit', 'read', 'delete', 'export', 'report'],
            ],

            'data_entry' => [
                'applicants' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'applicants_requests' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'applicant_request_reason' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'branch' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'committee' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'committee_members' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'file' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'record' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'record_item' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
                'record_item_picture' => ['create', 'edit', 'read', 'delete', 'export', 'report',],
            ],

            'viewer' => [
                'applicants' => ['read', 'report'],
                'applicants_requests' => ['read', 'report'],
                'applicant_request_reason' => ['read', 'report'],
                'branch' => ['read', 'report'],
                'committee' => ['read', 'report'],
                'committee_members' => ['read', 'report'],
                'file' => ['read', 'report'],
                'record' => ['read', 'report'],
                'record_item' => ['read', 'report'],
                'record_item_picture' => ['read', 'report'],

            ]

        ];


        // 4️⃣ جلب دور المستخدم (مثلاً من عمود role في جدول users)
        $role = $user->role->name; // إذا لم يكن له دور نعتبره viewer افتراضياً


        // 5️⃣ التحقق من أن العملية المطلوبة موجودة ضمن صلاحيات الدور
        if ($module && $action) {

            $allowedActions = $accessibility[$role][$module] ?? [];

            if (!in_array($action, $allowedActions)) {
                return response()->json([
                    'message' => "ليس لديك الصلاحية لتنفيذ '$action' على '$module'."
                ], 403);
            }
        }

        // 6️⃣ تمرير الطلب في حال السماح
        return $next($request);
    }
}
