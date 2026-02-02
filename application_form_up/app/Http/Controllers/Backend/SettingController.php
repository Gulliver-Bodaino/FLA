<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\SaveSettingMail;

use App\Models\Setting;

use Log;

class SettingController extends Controller
{

    public function mail()
    {
        $setting = Setting::findOrFail(1);

        $mail = json_decode($setting->mail);

        $data = [
            'mail' => $mail,
        ];

        return view('backend.setting.mail', $data);
    }

    public function update_mail(SaveSettingMail $request)
    {
        $setting = Setting::findOrFail(1);

        $mail = new \stdClass();

        $fields = [
            'from_name',
            'from_address',
            'mailers_smtp_host',
            'mailers_smtp_port',
            'mailers_smtp_username',
            'mailers_smtp_password',
        ];
        foreach ($fields as $field) {
            $mail->$field = $request->$field;
        }

        $setting->mail = json_encode($mail);
        $setting->save();

        return redirect()->route('backend.settings.mail', ['saved' => 'on']);
    }




}
