<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\TelRule;

class CheckFormC extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'member' => 'required',
            'sei' => 'required',
            'mei' => 'required',
            'sei_kana' => 'required',
            'mei_kana' => 'required',
            'birthday_year' => 'required',
            'birthday_month' => 'required',
            'birthday_day' => 'required',
            'gender' => 'required',
            'zip1' => ['required', 'numeric', 'digits:3'],
            'zip2' => ['required', 'numeric', 'digits:4'],
            'pref' => 'required',
            'city' => 'required',
            'address1' => 'required',
            'tel' => ['required', new TelRule],
            'mailaddress' => ['nullable', 'email:rfc,dns'],
            'agree' => 'required',
            'shoku-ad' => 'required_without_all:member_fee_id,shop_fee_id,seminar_venue_id,academy_course_id',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'member' => '会員の選択',
            'shoku-ad' => '食アド・イベント関連の選択',
        ];
    }

    public function messages()
    {
        $messages = [
            'shoku-ad.required_without_all' => '食アド・イベント関連をいずれか一つ選択して下さい。',
        ];

        return $messages;
    }

}
