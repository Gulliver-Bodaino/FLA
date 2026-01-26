<?php

namespace App\Http\Controllers\Backend\FormB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\FormBSetting;

use Log;
use File;

class SettingController extends Controller
{
    public function index()
    {
        $setting = FormBSetting::findOrFail(1);

        $data = [
            'setting' => $setting,
        ];

        return view('backend.form_b.setting', $data);
    }

    public function basic(Request $request)
    {
        $setting = FormBSetting::findOrFail(1);
        $setting->public = $request->public;
        $setting->save();
        return response('', 204);
    }

    public function replymail()
    {
        // メール送信設定
        $mail = Setting::findOrFail(1)->mail;
        $mail = json_decode($mail);

        // 自動返信メール設定
        $setting = FormBSetting::findOrFail(1);
        $replymail = json_decode($setting->replymail);

        $body = File::get(resource_path('views/emails/form_b.blade.php'));

        $data = [
            'mail' => $mail,
            'replymail' => $replymail,
            'body' => $body,
        ];

        return view('backend.form_b.replymail', $data);
    }

    public function update_replymail(Request $request)
    {
        $setting = FormBSetting::findOrFail(1);

        $replymail = new \stdClass();

        $fields = [
            'cc_address',
            'bcc_address',
            'subject',
//            'body',
        ];
        foreach ($fields as $field) {
            $replymail->$field = $request->$field;
        }

        $setting->replymail = json_encode($replymail);
        $setting->save();

        File::put(resource_path('views/emails/form_b.blade.php'), $request->body);

        return redirect()->route('backend.form_b.settings.replymail', ['saved' => 'on']);
    }


}
