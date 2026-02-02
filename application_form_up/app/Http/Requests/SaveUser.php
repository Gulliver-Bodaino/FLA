<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Route;

use Log;

class SaveUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
//            'name' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'max:255', Rule::unique('users')->ignore($this->user)],
            'name' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'password' => ['required', 'string', 'alpha_num', 'min:8'],
        ];

        /*
        if (Route::is('backend.members.*')) {
            // 管理画面のルールを調整
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('members')->ignore($this->member)];
            unset($rules['email_confirm']);
            unset($rules['agree']);
            if (Route::is('backend.members.update')) {
                $rules['password'] = ['nullable', 'string', 'alpha_num', 'min:8'];
            }
        } else {
            // 表画面のルールを調整
            unset($rules['password']);
            unset($rules['kind']);
        }
        */
        if (Route::is('backend.users.update')) {
            $rules['password'] = ['nullable', 'string', 'alpha_num', 'min:8'];
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'アカウント名',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
        ];
    }

    public function messages()
    {
        return [
//            'name.regex' => 'アカウント名は半角英数字で入力して下さい。',
//            'name.unique' => '登録済みのアカウント名です。',
            'email.unique' => '登録済みのメールアドレスです。',
        ];
    }
}
