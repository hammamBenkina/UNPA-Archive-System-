<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
    public function rules(): array
    {
        $request = $this;

        return [
            'username' => [
                'required',
                'string',
                'min:3',
                Rule::unique('users', 'username')
                    ->where(
                        fn($query) =>
                        $query->whereRaw('LOWER(TRIM(username)) = ?', [strtolower(trim($request->username))])
                    ),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'email' => [

                'email',
                Rule::unique('users', 'email')
                    ->where(
                        fn($query) =>
                        $query->whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($request->email))])
                    ),
            ],
            'phoneNumber' => [

                'regex:/^0[0-9]{9}$/',
                Rule::unique('users', 'phoneNumber'),
            ],
            // 'createdBy' => 'required|integer|exists:users,id',
            'roleId' => 'required|integer|exists:role,id',
            'adjective' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.string'   => 'اسم المستخدم يجب أن يكون نصًا.',
            'username.min'      => 'اسم المستخدم يجب أن يحتوي على 3 أحرف على الأقل.',
            'username.unique'   => 'اسم المستخدم مستخدم بالفعل.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string'   => 'كلمة المرور يجب أن تكون نصًا.',
            'password.min'      => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
            'password.regex'    => 'كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل، رقم واحد، ورمز خاص واحد مثل @ أو # أو !.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email'    => 'البريد الإلكتروني غير صالح.',
            'email.unique'   => 'هذا البريد الإلكتروني مستخدم بالفعل.',

            'phoneNumber.required' => 'رقم الهاتف مطلوب.',
            'phoneNumber.regex'    => 'رقم الهاتف غير صالح، يجب أن يبدأ بـ 0 ويليه 9 أرقام.',
            'phoneNumber.unique'   => 'رقم الهاتف مستخدم بالفعل.',

            'createdBy.required' => 'معرّف المُنشئ مطلوب.',
            'createdBy.integer'  => 'معرّف المُنشئ يجب أن يكون رقمًا.',
            'createdBy.exists'   => 'المُنشئ المحدد غير موجود.',

            'roleId.required' => 'دور المستخدم مطلوب.',
            'roleId.integer'  => 'معرف الدور يجب أن يكون رقمًا.',
            'roleId.exists'   => 'الدور المحدد غير موجود.',

            'adjective.string' => 'الصفة يجب أن تكون نصًا.',
            'adjective.max'    => 'الصفة لا يمكن أن تتجاوز 255 حرفًا.',
        ];
    }
}
