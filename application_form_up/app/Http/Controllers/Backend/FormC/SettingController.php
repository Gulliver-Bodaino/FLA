<?php

namespace App\Http\Controllers\Backend\FormC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\FormCSetting;

use Log;
use File;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = FormCSetting::findOrFail(1);

        $setting->member_fee_list     = json_decode($setting->member_fee);
        $setting->shop_fee_list       = json_decode($setting->shop_fee);
        $setting->seminar_venue_list  = json_decode($setting->seminar_venue);
        $setting->academy_course_list = json_decode($setting->academy_course);

        $data = [
            'setting' => $setting,
        ];

        return view('backend.form_c.setting', $data);
    }

    public function basic(Request $request)
    {
        $setting = FormCSetting::findOrFail(1);
        $setting->public = $request->public;
        $setting->save();
        return response('', 204);
    }

    public function item(Request $request)
    {
        $setting = FormCSetting::findOrFail(1);

        //Log::debug($request->all());

        // 食アド会員
        $list = [];
        if ($request->filled('member_fee_id')) {
            $enabled = $request->filled('member_fee_enabled') ? array_values($request->member_fee_enabled) : [];
            foreach (array_keys($request->member_fee_id) as $i) {
                if (isset($request->member_fee_delete[$i]) && $request->member_fee_delete[$i] === '1') {
                    continue;
                }

                $member_fee = new \stdClass();
                $member_fee->id      = $request->member_fee_id[$i];
                $member_fee->enabled = in_array($member_fee->id, $enabled);
                $member_fee->name    = $request->member_fee_name[$i];
                $member_fee->price   = $request->member_fee_price[$i];

                $list[] = $member_fee;
            }
        }
        $setting->member_fee = json_encode($list);

        // 食アドのお店
        $list = [];
        if ($request->filled('shop_fee_id')) {
            $enabled = $request->filled('shop_fee_enabled') ? array_values($request->shop_fee_enabled) : [];
            foreach (array_keys($request->shop_fee_id) as $i) {
                if (isset($request->shop_fee_delete[$i]) && $request->shop_fee_delete[$i] === '1') {
                    continue;
                }

                $shop_fee = new \stdClass();
                $shop_fee->id      = $request->shop_fee_id[$i];
                $shop_fee->enabled = in_array($shop_fee->id, $enabled);
                $shop_fee->name    = $request->shop_fee_name[$i];
                $shop_fee->price   = $request->shop_fee_price[$i];

                $list[] = $shop_fee;
            }
        }
        $setting->shop_fee = json_encode($list);

        // 食アドゼミナール　会場
        $list = [];
        if ($request->filled('seminar_venue_id')) {
            $enabled = $request->filled('seminar_venue_enabled') ? array_values($request->seminar_venue_enabled) : [];
            foreach (array_keys($request->seminar_venue_id) as $i) {
                if (isset($request->seminar_venue_delete[$i]) && $request->seminar_venue_delete[$i] === '1') {
                    continue;
                }

                $seminar_venue = new \stdClass();
                $seminar_venue->id          = $request->seminar_venue_id[$i];
                $seminar_venue->enabled     = in_array($seminar_venue->id, $enabled);
                $seminar_venue->name        = $request->seminar_venue_name[$i];
                $seminar_venue->price_label = $request->seminar_venue_price_label[$i];
                $seminar_venue->price       = $request->seminar_venue_price[$i];

                $list[] = $seminar_venue;
            }
        }
        $setting->seminar_venue = json_encode($list);

        // 食アドAcademy　講座
        $list = [];
        if ($request->filled('academy_course_id')) {
            $enabled = $request->filled('academy_course_enabled') ? array_values($request->academy_course_enabled) : [];
            foreach (array_keys($request->academy_course_id) as $i) {
                if (isset($request->academy_course_delete[$i]) && $request->academy_course_delete[$i] === '1') {
                    continue;
                }

                $academy_course = new \stdClass();
                $academy_course->id          = $request->academy_course_id[$i];
                $academy_course->enabled     = in_array($academy_course->id, $enabled);
                $academy_course->name        = $request->academy_course_name[$i];
                $academy_course->price_label = $request->academy_course_price_label[$i];
                $academy_course->price       = $request->academy_course_price[$i];

                $list[] = $academy_course;
            }
        }
        $setting->academy_course = json_encode($list);

        $setting->seminar_enabled = $request->seminar_enabled ?? 0;
        $setting->academy_enabled = $request->academy_enabled ?? 0;
        $setting->academy_title   = $request->academy_title;

        $setting->save();

        return response('', 204);
    }


    public function replymail()
    {
        // メール送信設定
        $mail = Setting::findOrFail(1)->mail;
        $mail = json_decode($mail);

        // 自動返信メール設定
        $setting = FormCSetting::findOrFail(1);
        $replymail = json_decode($setting->replymail);

        $body = File::get(resource_path('views/emails/form_c.blade.php'));

        $data = [
            'mail' => $mail,
            'replymail' => $replymail,
            'body' => $body,
        ];

        return view('backend.form_c.replymail', $data);
    }

    public function update_replymail(Request $request)
    {
        $setting = FormCSetting::findOrFail(1);

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

        File::put(resource_path('views/emails/form_c.blade.php'), $request->body);

        return redirect()->route('backend.form_c.settings.replymail', ['saved' => 'on']);
    }

}
