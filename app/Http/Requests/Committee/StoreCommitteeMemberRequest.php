<?php

namespace App\Http\Requests\committee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommitteeMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    /**
     * قواعد التحقق من البيانات.
     */
    public function rules(): array
    {
        $request = $this;

        return [
            // 🔹 اسم العضو
            'name' => [
                'required', // يجب إدخال الاسم
                'string',   // يجب أن يكون نصاً
                'min:1',    // لا يقل عن حرف واحد
                // التحقق من أن الاسم فريد داخل جدول أعضاء اللجنة
                Rule::unique('committee_members', 'name')
                    ->where(
                        fn($query) =>
                        $query->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($request->name))])
                    ),
            ],

            // 🔹 الصفة أو المنصب داخل اللجنة
            'adjective' => 'required|string',

            // 🔹 رقم اللجنة المرتبطة بالعضو (يجب أن تكون موجودة فعلاً)
            'committeeId' => 'required|integer|exists:committee,id',

            // 🔹 المستخدم الذي أنشأ هذا السجل

            // 🔹 الحساب المرتبط (اختياري)
            'accountId' => 'nullable|integer|exists:users,id',
        ];
    }

    /**
     * الرسائل المخصصة للأخطاء.
     */
    public function messages(): array
    {
        return [
            // 🔹 name
            'name.required' => 'يرجى إدخال اسم العضو.',
            'name.string' => 'يجب أن يكون الاسم نصاً صحيحاً.',
            'name.min' => 'يجب أن يحتوي الاسم على حرف واحد على الأقل.',
            'name.unique' => 'هذا الاسم مستخدم مسبقاً في اللجنة.',

            // 🔹 adjective
            'adjective.required' => 'يرجى إدخال الصفة أو المنصب.',
            'adjective.string' => 'يجب أن تكون الصفة نصية.',

            // 🔹 committeeId
            'committeeId.required' => 'يرجى تحديد اللجنة.',
            'committeeId.integer' => 'معرّف اللجنة يجب أن يكون رقماً صحيحاً.',
            'committeeId.exists' => 'اللجنة المحددة غير موجودة.',

            // 🔹 createdBy
            // 'createdBy.required' => 'يرجى تحديد المستخدم الذي أنشأ العضو.',
            // 'createdBy.integer' => 'معرّف المستخدم يجب أن يكون رقماً صحيحاً.',
            // 'createdBy.exists' => 'المستخدم المحدد غير موجود.',

            // 🔹 accountId
            'accountId.integer' => 'معرّف الحساب يجب أن يكون رقماً صحيحاً.',
            'accountId.exists' => 'الحساب المحدد غير موجود.',
        ];
    }
}
