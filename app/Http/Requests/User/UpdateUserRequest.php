<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        $request = $this;

        return [
            // 2️⃣ اسم المستخدم
            'username' => [
                'required',
                'string',
                'min:3',
                Rule::unique('users', 'username')
                    ->ignore($this->route('userId'))
                    ->where(
                        fn($query) =>
                        $query->whereRaw('LOWER(TRIM(username)) = ?', [strtolower(trim($request->username))])
                    ),
            ],

            // 3️⃣ كلمة المرور
            // 'password' => [
            //     'required',
            //     'string',
            //     'min:8',
            //     'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            // ],

            // 4️⃣ البريد الإلكتروني
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($this->route('userId'))
                    ->where(
                        fn($query) =>
                        $query->whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($request->email))])
                    ),
            ],

            // 5️⃣ رقم الهاتف
            'phoneNumber' => [
                'required',
                // 'regex:/^0[0-9]{9}$/',
                Rule::unique('users', 'phoneNumber')->ignore($this->route('userId')),
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'username.required' => 'اسم المستخدم مطلوب.',
            'username.string'   => 'اسم المستخدم يجب أن يكون نصًا.',
            'username.min'      => 'اسم المستخدم يجب أن يحتوي على 3 أحرف على الأقل.',
            'username.unique'   => 'اسم المستخدم مستخدم بالفعل.',

            // 'password.required' => 'كلمة المرور مطلوبة.',
            // 'password.string'   => 'كلمة المرور يجب أن تكون نصًا.',
            // 'password.min'      => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
            // 'password.regex'    => 'كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل، رقم واحد، ورمز خاص واحد مثل @ أو # أو !.',

            'email.required'    => 'البريد الإلكتروني مطلوب.',
            'email.email'       => 'البريد الإلكتروني غير صالح.',
            'email.unique'      => 'هذا البريد الإلكتروني مستخدم بالفعل.',

            'phoneNumber.required' => 'رقم الهاتف مطلوب.',
            'phoneNumber.regex'    => 'رقم الهاتف غير صالح، يجب أن يبدأ بـ 0 ويليه 9 أرقام.',
            'phoneNumber.unique'   => 'رقم الهاتف مستخدم بالفعل.',

        ];
    }
}
