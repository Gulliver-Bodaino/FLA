<?php

namespace App\Http\Controllers\Backend\FormA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\FormASetting;

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
        $setting = FormASetting::findOrFail(1);

        // 検定試験
        $setting->exam_list = json_decode($setting->exam);

        // 受験会場
        $setting->exam_venue_list = json_decode($setting->exam_venue);

        $city = array_flip(config('common.city'));

        // 通学コース　受講会場
        $normal_venue_list = json_decode($setting->normal_venue);
        foreach ($normal_venue_list as $normal_venue) {
            $normal_venue->city_name = $city[$normal_venue->city] ?? '';
        }
        $setting->normal_venue_list = $normal_venue_list;

        // 速習コース　講座
        $setting->fast_course_list = json_decode($setting->fast_course);

        // 速習コース　受講会場
        $fast_venue_list = json_decode($setting->fast_venue);
        foreach ($fast_venue_list as $fast_venue) {
            $fast_venue->city_name = $city[$fast_venue->city] ?? '';
        }
        $setting->fast_venue_list = $fast_venue_list;

        // 科目別 過去問題集
        $setting->workbook_list = json_decode($setting->workbook);

        $data = [
            'setting' => $setting,
        ];

        return view('backend.form_a.setting', $data);
    }

    public function basic(Request $request)
    {
        $setting = FormASetting::findOrFail(1);
        $setting->public = $request->public;
        $setting->save();
        return response('', 204);
    }

    public function item(Request $request)
    {
        $setting = FormASetting::findOrFail(1);

        //Log::debug($request->all());

        // 検定試験
        $list = [];
        if ($request->filled('exam_id')) {
            $enabled = $request->filled('exam_enabled') ? array_values($request->exam_enabled) : [];
            foreach (array_keys($request->exam_id) as $i) {
                if (isset($request->exam_delete[$i]) && $request->exam_delete[$i] === '1') {
                    continue;
                }

                $exam = new \stdClass();
                $exam->id      = $request->exam_id[$i];
                $exam->enabled = in_array($exam->id, $enabled);
                $exam->name    = $request->exam_name[$i];
                $exam->price   = $request->exam_price[$i];

                $list[] = $exam;
            }
        }
        $setting->exam = json_encode($list);

        // 受験会場
        $list = [];
        if ($request->filled('exam_venue_id')) {
            $enabled = $request->filled('exam_venue_enabled') ? array_values($request->exam_venue_enabled) : [];
            foreach (array_keys($request->exam_venue_id) as $i) {
                if (isset($request->exam_venue_delete[$i]) && $request->exam_venue_delete[$i] === '1') {
                    continue;
                }

                $exam_venue = new \stdClass();
                $exam_venue->id      = $request->exam_venue_id[$i];
                $exam_venue->enabled = in_array($exam_venue->id, $enabled);
                $exam_venue->name    = $request->exam_venue_name[$i];

                $list[] = $exam_venue;
            }
        }
        $setting->exam_venue = json_encode($list);

        // 通学コース　受講会場
        $list = [];
        if ($request->filled('normal_venue_id')) {
            $enabled = $request->filled('normal_venue_enabled') ? array_values($request->normal_venue_enabled) : [];
            foreach (array_keys($request->normal_venue_id) as $i) {
                if (isset($request->normal_venue_delete[$i]) && $request->normal_venue_delete[$i] === '1') {
                    continue;
                }

                $normal_venue = new \stdClass();
                $normal_venue->id       = $request->normal_venue_id[$i];
                $normal_venue->enabled  = in_array($normal_venue->id, $enabled);
                $normal_venue->city     = $request->normal_venue_city[$i];
                $normal_venue->name     = $request->normal_venue_name[$i];
                $normal_venue->schedule = $request->normal_venue_schedule[$i];

                $list[] = $normal_venue;
            }
        }
        $setting->normal_venue = json_encode($list);

        // 速習コース　講座
        $list = [];
        if ($request->filled('fast_course_id')) {
            $enabled = $request->filled('fast_course_enabled') ? array_values($request->fast_course_enabled) : [];
            foreach (array_keys($request->fast_course_id) as $i) {
                if (isset($request->fast_course_delete[$i]) && $request->fast_course_delete[$i] === '1') {
                    continue;
                }

                $fast_course = new \stdClass();
                $fast_course->id      = $request->fast_course_id[$i];
                $fast_course->enabled = in_array($fast_course->id, $enabled);
                $fast_course->name    = $request->fast_course_name[$i];
                $fast_course->price   = $request->fast_course_price[$i];
                $fast_course->days    = $request->fast_course_days[$i];

                $list[] = $fast_course;
            }
        }
        $setting->fast_course = json_encode($list);

        // 速習コース　受講会場
        $list = [];
        if ($request->filled('fast_venue_id')) {
            $enabled = $request->filled('fast_venue_enabled') ? array_values($request->fast_venue_enabled) : [];
            foreach (array_keys($request->fast_venue_id) as $i) {
                if (isset($request->fast_venue_delete[$i]) && $request->fast_venue_delete[$i] === '1') {
                    continue;
                }

                $fast_venue = new \stdClass();
                $fast_venue->id       = $request->fast_venue_id[$i];
                $fast_venue->enabled  = in_array($fast_venue->id, $enabled);
                $fast_venue->city     = $request->fast_venue_city[$i];
                $fast_venue->name     = $request->fast_venue_name[$i];
                $fast_venue->schedule = $request->fast_venue_schedule[$i];

                $list[] = $fast_venue;
            }
        }
        $setting->fast_venue = json_encode($list);

        // 科目別 過去問題集
        $list = [];
        if ($request->filled('workbook_id')) {
            $enabled = $request->filled('workbook_enabled') ? array_values($request->workbook_enabled) : [];
            foreach (array_keys($request->workbook_id) as $i) {
                if (isset($request->workbook_delete[$i]) && $request->workbook_delete[$i] === '1') {
                    continue;
                }

                $workbook = new \stdClass();
                $workbook->id      = $request->workbook_id[$i];
                $workbook->enabled = in_array($workbook->id, $enabled);
                $workbook->name    = $request->workbook_name[$i];
                $workbook->price   = $request->workbook_price[$i];

                $list[] = $workbook;
            }
        }
        $setting->workbook = json_encode($list);

        $setting->normal_enabled = $request->normal_enabled ?? 0;
        $setting->normal_price   = $request->normal_price;
        $setting->fast_enabled   = $request->fast_enabled ?? 0;

        $setting->save();

        return response('', 204);
    }

    public function replymail()
    {
        // メール送信設定
        $mail = Setting::findOrFail(1)->mail;
        $mail = json_decode($mail);

        // 自動返信メール設定
        $setting = FormASetting::findOrFail(1);
        $replymail = json_decode($setting->replymail);

        $body = File::get(resource_path('views/emails/form_a.blade.php'));

        $data = [
            'mail' => $mail,
            'replymail' => $replymail,
            'body' => $body,
        ];

        return view('backend.form_a.replymail', $data);
    }

    public function update_replymail(Request $request)
    {
        $setting = FormASetting::findOrFail(1);

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

        File::put(resource_path('views/emails/form_a.blade.php'), $request->body);

        return redirect()->route('backend.form_a.settings.replymail', ['saved' => 'on']);
    }


}
