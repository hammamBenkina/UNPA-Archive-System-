<?php

namespace App\Http\Requests\Applicants;

use App\Models\Applicants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicantRequest extends FormRequest
{
    /**
     * تحديد السماح بتنفيذ الطلب.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق من البيانات.
     */
    public function rules(): array
    {
        $request = $this;

        return [
            // 🔹 الاسم (فريد)
            'name' => [
                'required',
                'string',
                'min:1',
                Rule::unique('applicants', 'name')
                    ->where(
                        fn($query) =>
                        $query->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($request->name))])
                    ),
            ],

            // 🔹 نوع المتقدم (مثلاً: فرد، مؤسسة، جهة حكومية...)
            'type' => [
                'required',
                'string',
                Rule::in(Applicants::$TYPES),
            ],

            // 🔹 رقم الهاتف
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9+\-\s]+$/',
                'min:6',
                'max:20',
            ],

            // 🔹 البريد الإلكتروني
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            // 🔹 رقم الهوية الوطنية
            'nationId' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    /**
     * الرسائل المخصصة للأخطاء.
     */
    public function messages(): array
    {
        return [
            // 🔹 name
            'name.required' => 'يرجى إدخال اسم المتقدم.',
            'name.string' => 'يجب أن يكون الاسم نصاً صحيحاً.',
            'name.min' => 'يجب أن يحتوي الاسم على حرف واحد على الأقل.',
            'name.unique' => 'هذا الاسم مستخدم مسبقاً.',

            // 🔹 type
            'type.required' => 'يرجى تحديد نوع المتقدم.',
            'type.string' => 'يجب أن يكون النوع نصياً.',
            'type.in' => 'النوع المحدد غير صالح. الأنواع المسموح بها هي: مواطن، شركة خاصة، مكتب هندسي، جهة حكومية، مطور عقاري، نشاط تجاري، أو أخرى.',

            // 🔹 phone
            'phone.string' => 'يجب أن يكون رقم الهاتف نصاً.',
            'phone.regex' => 'صيغة رقم الهاتف غير صحيحة.',
            'phone.min' => 'رقم الهاتف قصير جداً.',
            'phone.max' => 'رقم الهاتف طويل جداً.',

            // 🔹 email
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.max' => 'البريد الإلكتروني طويل جداً.',

            // 🔹 nationId
            'nationId.string' => 'رقم الهوية يجب أن يكون نصاً.',
            'nationId.max' => 'رقم الهوية طويل جداً.',
        ];
    }
}
