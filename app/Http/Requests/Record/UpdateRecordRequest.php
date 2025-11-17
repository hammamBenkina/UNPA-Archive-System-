<?php

namespace App\Http\Requests\Record;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecordRequest extends FormRequest
{
    /**
     * السماح بتنفيذ الطلب.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق.
     */
    public function rules(): array
    {
        $request = $this;
        $recordId = $this->route('id'); // أو حسب اسم الباراميتر في الـ route

        return [

            /**
             * 🔹 رقم المحضر — فريد في نفس (branchId + committeeId + year)
             * مع تجاهل السجل الحالي عند التعديل
             */
            'no' => [
                'required',
                'integer',
                'min:1',
                'max:2147483648',
                Rule::unique('record', 'no')
                    ->ignore($recordId)
                    ->where(
                        fn($query) =>
                        $query->where('branchId', $request->branchId)
                            ->where('committeeId', $request->committeeId)
                            ->where('year', $request->year)
                    ),
            ],

            /**
             * 🔹 الرقم الإشاري — فريد بالكامل
             * مع تجاهل السجل الحالي
             */
            'referenceNumber' => [
                'required',
                'string',
                Rule::unique('record', 'referenceNumber')
                    ->ignore($recordId)
                    ->where(
                        fn($query) =>
                        $query->whereRaw(
                            'LOWER(TRIM(referenceNumber)) = ?',
                            [strtolower(trim($request->referenceNumber))]
                        )
                    ),
            ],

            // 🔹 السنة
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),

            // 🔹 تاريخ الانعقاد
            'conveningDate' => 'required|date',

            // 🔹 الفرع
            'branchId' => 'required|integer|exists:branch,id',

            // 🔹 اللجنة
            'committeeId' => 'required|integer|exists:committee,id',

            // 🔹 الوصف
            'desc' => 'nullable|string|max:5000',
        ];
    }

    /**
     * الرسائل المخصصة للأخطاء.
     */
    public function messages(): array
    {
        return [

            // 🔹 no
            'no.required' => 'يرجى إدخال رقم المحضر.',
            'no.integer' => 'رقم المحضر يجب أن يكون رقماً.',
            'no.min' => 'رقم المحضر يجب أن يكون أكبر من صفر.',
            'no.max' => 'رقم المحضر يجب أن يكون أقل من 2147483648.',
            'no.unique' => 'رقم المحضر مسجل مسبقاً لنفس الفرع ونفس اللجنة ونفس السنة.',

            // 🔹 referenceNumber
            'referenceNumber.required' => 'يرجى إدخال الرقم الإشاري.',
            'referenceNumber.string' => 'الرقم الإشاري يجب أن يكون نصاً.',
            'referenceNumber.unique' => 'الرقم الإشاري مستخدم مسبقاً.',

            // 🔹 year
            'year.required' => 'يرجى إدخال السنة.',
            'year.integer' => 'السنة يجب أن تكون رقماً.',
            'year.min' => 'السنة غير صحيحة.',
            'year.max' => 'السنة غير منطقية.',

            // 🔹 conveningDate
            'conveningDate.required' => 'يرجى إدخال تاريخ انعقاد المحضر.',
            'conveningDate.date' => 'تاريخ الانعقاد يجب أن يكون من النوع تاريخ.',

            // 🔹 branchId
            'branchId.required' => 'يرجى تحديد الفرع.',
            'branchId.integer' => 'معرّف الفرع يجب أن يكون رقماً.',
            'branchId.exists' => 'الفرع المحدد غير موجود.',

            // 🔹 committeeId
            'committeeId.required' => 'يرجى تحديد اللجنة.',
            'committeeId.integer' => 'معرّف اللجنة يجب أن يكون رقماً.',
            'committeeId.exists' => 'اللجنة المحددة غير موجودة.',

            // 🔹 desc
            'desc.string' => 'الوصف يجب أن يكون نصاً.',
            'desc.max' => 'الوصف طويل جداً.',
        ];
    }
}
