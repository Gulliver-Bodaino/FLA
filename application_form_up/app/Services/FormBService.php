<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Models\FormBSetting;

use Auth;

use Log;

// 表画面のフォームB
class FormBService
{
    private $setting;

    public function __construct()
    {
        if (Auth::check()) {
            $this->setting = FormBSetting::where('id', 1)->firstOrFail();
        } else {
            $this->setting = FormBSetting::where('id', 1)->where('public', config('common.public.公開'))->firstOrFail();
        }
    }

    public function getSetting()
    {
        return $this->setting;
    }


}
