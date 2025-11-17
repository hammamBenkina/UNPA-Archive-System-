<?php

namespace App\Http\Requests\Committee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommitteeRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولًا لإرسال هذا الطلب.
     */
    public function authorize(): bool
    {
        // يمكن لاحقًا تقييدها حسب صلاحيات المستخدم
        return true;
    }

    /**
     * قواعد التحقق من البيانات.
     */
    public function rules(): array
    {
        return [
            'no' => [
                'required',
                'integer',
                'min:1',
                // 🔒 التحقق من أن رقم اللجنة والسنة معًا لا يتكرران
                Rule::unique('committee')
                    ->where(fn($query) => $query->where('yearOfEstablishment', $this->yearOfEstablishment)),
            ],

            'yearOfEstablishment' => [
                'required',
                'date',           // 👈 النوع تاريخ كامل
                'before_or_equal:today', // لا يمكن أن يكون تاريخ مستقبلي
            ],

            'isCurrent' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * الرسائل المخصصة للأخطاء.
     */
    public function messages(): array
    {
        return [
            'no.required' => 'رقم اللجنة مطلوب.',
            'no.integer'  => 'رقم اللجنة يجب أن يكون رقمًا صحيحًا.',
            'no.min'      => 'رقم اللجنة يجب أن يكون أكبر من صفر.',
            'no.unique'   => 'يوجد لجنة بنفس الرقم وسنة التأسيس بالفعل.',

            'yearOfEstablishment.required' => 'تاريخ التأسيس مطلوب.',
            'yearOfEstablishment.date'     => 'تاريخ التأسيس غير صالح.',
            'yearOfEstablishment.before_or_equal' => 'لا يمكن أن يكون تاريخ التأسيس في المستقبل.',

            'isCurrent.boolean' => 'قيمة اللجنة الحالية يجب أن تكون صحيحة أو خطأ (true أو false).',
        ];
    }
}
