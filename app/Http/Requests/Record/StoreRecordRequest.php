<?php

namespace App\Http\Requests\Record;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecordRequest extends FormRequest
{
    /**
     * السماح بتنفيذ الطلب.
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
            // 🔹 رقم المحضر — لا يتكرر في نفس (branchId + committeeId + year)
            'no' => [
                'required',
                'integer',
                'min:1',
                'max:2147483648',
                Rule::unique('record', 'no')
                    ->where(
                        fn($query) =>
                        $query->where('branchId', $request->branchId)
                            ->where('committeeId', $request->committeeId)
                            ->where('year', $request->year)
                    ),
            ],

            // 🔹 الرقم الإشاري — فريد بالكامل
            'referenceNumber' => [
                'required',
                'string',
                Rule::unique('record', 'referenceNumber')
                    ->where(
                        fn($query) =>
                        $query->whereRaw('LOWER(TRIM(referenceNumber)) = ?', [strtolower(trim($request->referenceNumber))])
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

            // 🔹 الوثيقة المرتبطة
            // 'docId' => 'required|integer|exists:file,id',

            // 🔹 المستخدم الذي أنشأ السجل
            // 'createdBy' => 'required|integer|exists:users,id',

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
            'no.max' => 'رقم المحضر يجب أن يكون اقل من 2147483648.',
            'no.unique' => 'رقم المحضر مسجل مسبقاً لنفس الفرع ونفس اللجنة ونفس السنة.',

            // 🔹 referenceNumber
            'referenceNumber.required' => 'يرجى إدخال الرقم الإشاري.',
            'referenceNumber.string' => 'يجب أن يكون الرقم الإشاري نصاً.',
            'referenceNumber.unique' => 'الرقم الإشاري مستخدم مسبقاً.',

            // 🔹 year
            'year.required' => 'يرجى إدخال السنة.',
            'year.integer' => 'السنة يجب أن تكون رقماً.',
            'year.min' => 'السنة غير صحيحة.',
            'year.max' => 'السنة غير منطقية.',

            // 🔹 conveningDate
            'conveningDate.required' => 'يرجى إدخال تاريخ انعقاد المحضر.',
            'conveningDate.integer' => 'تاريخ الانعقاج يجب أن يكون من النوع تاريخ .',

            // 🔹 branchId
            'branchId.required' => 'يرجى تحديد الفرع.',
            'branchId.integer' => 'معرّف الفرع يجب أن يكون رقماً.',
            'branchId.exists' => 'الفرع المحدد غير موجود.',

            // 🔹 committeeId
            'committeeId.required' => 'يرجى تحديد اللجنة.',
            'committeeId.integer' => 'معرّف اللجنة يجب أن يكون رقماً.',
            'committeeId.exists' => 'اللجنة المحددة غير موجودة.',

            // 🔹 docId
            'docId.required' => 'يرجى تحديد الوثيقة المرتبطة.',
            'docId.integer' => 'رقم الوثيقة يجب أن يكون صحيحاً.',
            'docId.exists' => 'الوثيقة المحددة غير موجودة.',

            // 🔹 createdBy
            'createdBy.required' => 'يرجى تحديد المستخدم الذي أنشأ السجل.',
            'createdBy.integer' => 'معرّف المستخدم يجب أن يكون رقماً.',
            'createdBy.exists' => 'المستخدم المحدد غير موجود.',

            // 🔹 desc
            'desc.string' => 'الوصف يجب أن يكون نصاً.',
            'desc.max' => 'الوصف طويل جداً.',
        ];
    }
}
