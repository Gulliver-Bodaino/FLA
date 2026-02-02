<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\TelRule;
use App\Rules\FastVenueRule;

use Route;

class CheckFormA extends FormRequest
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
            'q1' => 'required',
            'q2' => 'required',
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
            // 受験会場
            'exam_venue_id' => 'required_with:exam_id',
            // 通学コース受講会場
            'normal_venue_id' => 'required_with:normal',
            // 速習コース講座
            'fast_course_id' => 'required_with:fast',
            // 速習コース受講会場
//            'fast_venue_id' => ['required_with:fast', 'array', 'size:2'],
            'fast_venue_id' => ['required_with:fast', 'array', new FastVenueRule],
            'agree' => 'required',
        ];

        if (Route::is('form_a.confirm')) {
            $rules['token'] = 'required';
            $rules['tokenKey'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'exam_venue_id.required_with' => '受験会場の選択が必須です。',
            'normal_venue_id.required_with' => '受講会場の選択が必須です。',
            'fast_course_id.required_with' => '講座の選択が必須です。',
            'fast_venue_id.required_with' => '受講会場の選択が必須です。',
            'fast_venue_id.size' => '2日間ご選択ください。',
            'token.required' => 'クレジットカード決済代行会社との通信で問題が発生しています。（トークン）',
            'tokenKey.required' => 'クレジットカード決済代行会社との通信で問題が発生しています。（トークンキー）',
        ];

        return $messages;
    }

}
