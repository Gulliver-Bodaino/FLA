<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Log;

class DeleteUser extends FormRequest
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

    protected function prepareForValidation()
    {
        $merge = [
            'id' => $this->user,
        ];

        $this->merge($merge);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => [
                function ($attribute, $value, $fail) {
                    if ($value === '1') {
                        return $fail('#1 は削除できません。');
                    }
                }
            ],
        ];
    }
}
