<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class SaveSettingMail extends FormRequest
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
            'from_address' => ['required', 'email:rfc,dns'],
            'mailers_smtp_host' => ['required'],
            'mailers_smtp_port' => ['required', 'numeric'],
            'mailers_smtp_username' => ['required'],
            'mailers_smtp_password' => ['required'],
        ];

        return $rules;
    }
}
