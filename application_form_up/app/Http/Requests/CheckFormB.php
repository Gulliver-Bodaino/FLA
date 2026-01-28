<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\TelRule;

class CheckFormB extends FormRequest
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
            'agree' => 'required',
        ];

        return $rules;
    }
}
