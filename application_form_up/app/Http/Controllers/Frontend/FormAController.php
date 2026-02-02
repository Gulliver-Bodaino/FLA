<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\CheckFormA;

use App\Models\Setting;
use App\Models\FormASetting;
use App\Models\FormAApplication;

use Log;
use App;
use Str;
use Validator;

use Auth;

use App\Services\FormAService;
use App\Services\SbpsService;

use Mail;
use App\Mail\FormAReplyMail;

class FormAController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function form(Request $request, FormAService $formAService)
    {
        // フォームA　基本設定
        $setting = $formAService->getSetting();

        //if (App::environment('local')) {
            $old = [
                'q1' => '1',
                'q2' => '0',
                'sei' => 'てすと',
                'mei' => 'たろう',
                'sei_kana' => 'テスト',
                'mei_kana' => 'タロウ',
                'birthday_year' => '2000',
                'birthday_month' => '1',
                'birthday_day' => '2',
                'gender' => '1',
                'zip1' => '123',
                'zip2' => '4567',
                'pref' => '岡山県',
                'city' => '岡山市',
                'address1' => 'テスト町１−２−３',
                'address2' => 'テストビル',
                'workplace' => 'テスト勤務先',
                'department' => 'テスト部署',
                'tel' => '09011112222',
                'mailaddress' => 'user@testkey.net',
                'job' => '17',
                'agree' => '1',
                'exam_id' => '1',
                'exam_venue_id' => '1',
                'normal' => '1',
                'normal_venue_id' => '1',
                'fast' => '1',
                'fast_course_id' => '3',
                'fast_venue_id' => ['2', '3'],
                'workbook_id' => '1',
            ];
            //$request->session()->flash('_old_input', $old);
        //}

        // 検定試験
        $exam_list = [];
        foreach ($setting->exam as $exam) {
            if (!$exam->enabled) continue;
            $exam_list[] = $exam;
        }
        $setting->exam_list = $exam_list;

        // 受験会場
        $exam_venue_list = [];
        foreach ($setting->exam_venue as $exam_venue) {
            if (!$exam_venue->enabled) continue;
            $exam_venue_list[] = $exam_venue;
        }
        $setting->exam_venue_list = $exam_venue_list;

        $city = array_flip(config('common.city'));

        // 通学コース　受講会場
        $normal_venue_list = [];
        foreach ($setting->normal_venue as $normal_venue) {
            if (!$normal_venue->enabled) continue;
            $normal_venue->city_name = $city[$normal_venue->city] ?? '';
            $normal_venue_list[] = $normal_venue;
        }
        $setting->normal_venue_list = $normal_venue_list;

        // 速習コース　講座
        $fast_course_list = [];
        foreach ($setting->fast_course as $fast_course) {
            if (!$fast_course->enabled) continue;
            $fast_course_list[] = $fast_course;
        }
        $setting->fast_course_list = $fast_course_list;

        // 速習コース　受講会場
        $fast_venue_list = [];
        foreach ($setting->fast_venue as $fast_venue) {
            if (!$fast_venue->enabled) continue;

            $city_id = sprintf('%02d', $fast_venue->city);
            $city_name = $city[$fast_venue->city] ?? '';
            $venue_name = trim($fast_venue->name);
            $key = $city_id . '_' . $city_name . '_' . $venue_name;
            if (!isset($fast_venue_list[$key])) {
                $fast_venue_list[$key] = [];
            }
            $fast_venue_list[$key][] = $fast_venue;

        }
        ksort($fast_venue_list);
        $setting->fast_venue_list = $fast_venue_list;

        // 科目別 過去問題集
        $workbook_list = [];
        foreach ($setting->workbook as $workbook) {
            if (!$workbook->enabled) continue;
            $workbook_list[] = $workbook;
        }
        $setting->workbook_list = $workbook_list;

        Log::debug(old());
        $result = $formAService->calculate(old());
        Log::debug(var_export($result, true));

        $data = [
            'setting' => $setting,
            'subtotal1' => $result->subtotal1,
            'subtotal2' => $result->subtotal2,
            'total' => $result->total,
        ];

        return view('frontend.form_a', $data);
    }

    public function credit(CheckFormA $request, FormAService $formAService)
    {
        // フォームA　基本設定
        $setting = $formAService->getSetting();

        $data = [
            'action' => route('form_a.confirm'),
            'check_credit_url' => route('form_a.check_credit'),
        ];

        return view('frontend.credit', $data);
    }

    // 決済要求
    public function check_credit(CheckFormA $request, FormAService $formAService, SbpsService $sbpsSerice)
    {
        // フォームA　基本設定
        $setting = $formAService->getSetting();

        $result = $formAService->calculate($request->all());

        $params = [
            'item_id' => 'form_a',
            'item_name' => 'フォーム A',
            'tax' => $result->tax,
            'amount' => $result->total,
        ];

        return $sbpsSerice->requestSettlement($request, $params);
    }

    // 確認画面
    public function confirm(CheckFormA $request, FormAService $formAService, SbpsService $sbpsSerice)
    {
        // フォームA　基本設定
        $setting = $formAService->getSetting();

        // 検定試験
        $exam_list = [];
        foreach ($setting->exam as $exam) {
            if (!$exam->enabled) continue;
            if (empty($request->exam_id)) continue;
            if ($exam->id != $request->exam_id) continue;
            $exam_list[] = $exam;
        }
        $setting->exam_list = $exam_list;

        // 受験会場
        $exam_venue_list = [];
        foreach ($setting->exam_venue as $exam_venue) {
            if (!$exam_venue->enabled) continue;
            if (empty($request->exam_venue_id)) continue;
            if ($exam_venue->id != $request->exam_venue_id) continue;
            $exam_venue_list[] = $exam_venue;
        }
        $setting->exam_venue_list = $exam_venue_list;

        $city = array_flip(config('common.city'));

        // 通学コース　受講会場
        $normal_venue_list = [];
        foreach ($setting->normal_venue as $normal_venue) {
            if (!$normal_venue->enabled) continue;
            if (empty($request->normal_venue_id)) continue;
            if ($normal_venue->id != $request->normal_venue_id) continue;
            $normal_venue->city_name = $city[$normal_venue->city] ?? '';
            $normal_venue_list[] = $normal_venue;
        }
        $setting->normal_venue_list = $normal_venue_list;

        // 速習コース　講座
        $fast_course_list = [];
        foreach ($setting->fast_course as $fast_course) {
            if (!$fast_course->enabled) continue;
            if (empty($request->fast_course_id)) continue;
            if ($fast_course->id != $request->fast_course_id) continue;
            $fast_course_list[] = $fast_course;
        }
        $setting->fast_course_list = $fast_course_list;

        // 速習コース　受講会場
        $fast_venue_list = [];
        foreach ($setting->fast_venue as $fast_venue) {
            if (!$fast_venue->enabled) continue;
            if (empty($request->fast_venue_id)) continue;
            if (!in_array($fast_venue->id, $request->fast_venue_id)) continue;

            $city_id = sprintf('%02d', $fast_venue->city);
            $city_name = $city[$fast_venue->city] ?? '';
            $venue_name = trim($fast_venue->name);
            $key = $city_id . '_' . $city_name . '_' . $venue_name;
            if (!isset($fast_venue_list[$key])) {
                $fast_venue_list[$key] = [];
            }
            $fast_venue_list[$key][] = $fast_venue;

        }
        ksort($fast_venue_list);
        $setting->fast_venue_list = $fast_venue_list;

        // 科目別 過去問題集
        $workbook_list = [];
        foreach ($setting->workbook as $workbook) {
            if (!$workbook->enabled) continue;
            if (empty($request->workbook_id)) continue;
            if ($workbook->id != $request->workbook_id) continue;
            $workbook_list[] = $workbook;
        }
        $setting->workbook_list = $workbook_list;

        $result = $formAService->calculate($request->input());

        $data = [
            'setting' => $setting,
            'subtotal1' => $result->subtotal1,
            'subtotal2' => $result->subtotal2,
            'total' => $result->total,
        ];

        return view('frontend.form_a_confirm', $data);
    }

    public function send(CheckFormA $request, FormAService $formAService, SbpsService $sbpsSerice)
    {
        $action = $request->get('action', 'correct');
        if ($action !== 'send') {
            return redirect()->route('form_a.form')->withInput();
        }

        $result = $formAService->calculate($request->all());

        // 3DS認証要求
        $credit_key = (string) Str::uuid();
        $params = [
            'amount' => $result->total,
            'ok_return_url' => route('form_a.credit_ok', ['credit_key' => $credit_key]),
            'ng_return_url' => route('form_a.credit_ng', ['credit_key' => $credit_key])
        ];

        $response = $sbpsSerice->request3dsSecure($request, $params);

        if (!is_array($response)) {
            return redirect()->route('form_a.system_error')->with([
                'error_message' => 'クレジットカードの3DS認証要求でシステムエラーが発生しました。'
            ]);
        }

        if ($response['res_result'] !== 'OK') {
            return redirect()->route('form_a.credit_error')->with($response);
        }

        $merge = [
            'status' => config('common.status.決済処理中'),
            'birthday' => sprintf('%04d-%02d-%02d', $request->birthday_year, $request->birthday_month, $request->birthday_day),
            'zip' => $request->zip1 . $request->zip2,
            'tel' => preg_replace('/[^0-9]/', '', $request->tel),
            'credit_key' => $credit_key ?? '',
            'sps_transaction_id' => $response['res_sps_transaction_id'] ?? '',
            'tds_authentication_id' => $response['res_tds_authentication_id'] ?? '',
        ];

        // 検定試験
        if ($request->filled('exam_id')) {
            $exam = $formAService->getExam($request->exam_id);
            if (is_object($exam)) {
                $merge['exam_name'] = $exam->name;
                $merge['exam_price'] = $exam->price;
            }
        } else {
            $merge['exam_id'] = null;
        }

        // 受験会場
        if ($request->filled('exam_venue_id')) {
            $exam_venue = $formAService->getExamVenue($request->exam_venue_id);
            if (is_object($exam_venue)) {
                $merge['exam_venue_name'] = $exam_venue->name;
            }
        }

        $city = array_flip(config('common.city'));

        // 通学コース
        if ($request->filled('normal')) {
            $merge['normal_price'] = $formAService->getNormalPrice();

            // 受講会場
            if ($request->filled('normal_venue_id')) {
                $normal_venue = $formAService->getNormalVenue($request->normal_venue_id);
                if (is_object($normal_venue)) {
                    $merge['normal_venue_city'] = $normal_venue->city;
                    $merge['normal_venue_city_name'] = $city[$normal_venue->city] ?? '';
                    $merge['normal_venue_name'] = $normal_venue->name;
                    $merge['normal_venue_schedule'] = $normal_venue->schedule;
                }
            }
        } else {
            $merge['normal'] = null;
        }

        // 速習コース
        if ($request->filled('fast')) {
            // 講座
            if ($request->filled('fast_course_id')) {
                $fast_course = $formAService->getFastCourse($request->fast_course_id);
                if (is_object($fast_course)) {
                    $merge['fast_course_name'] = $fast_course->name;
                    $merge['fast_course_price'] = $fast_course->price;
                }
            }

            // 受講会場
            if ($request->filled('fast_venue_id')) {
                $fast_venue_list = $formAService->getFastVenueList($request->fast_venue_id);
                if (is_array($fast_venue_list)) {
                    $merge['fast_venue_list'] = $fast_venue_list;
                    $merge['fast_venue'] = json_encode($fast_venue_list);
                }
            }
        } else {
            $merge['fast'] = null;
        }

        // 科目別 過去問題集
        if ($request->filled('workbook_id')) {
            $workbook = $formAService->getWorkbook($request->workbook_id);
            if (is_object($workbook)) {
                $merge['workbook_name'] = $workbook->name;
                $merge['workbook_price'] = $workbook->price;
            }
        } else {
            $merge['workbook_id'] = null;
        }

        $merge['subtotal1'] = $result->subtotal1;
        $merge['subtotal2'] = $result->subtotal2;
        $merge['total']     = $result->total;
        $merge['tax']       = $result->tax;
        
        Log::debug($merge);

        // 申込データ保存
        $application = new FormAApplication();
        $request->merge($merge);
        if (!$application->fill($request->all())->save()) {
            return redirect()->route('form_a.system_error')->with([
                'error_message' => '申し込みデータを保存できませんでした。'
            ]);
        }

        // 3DS認証結果のredirect_urlへリダイレクト
        return redirect($response['redirect_url']);
    }

    public function credit_ok(FormAService $formAService, SbpsService $sbpsSerice, $credit_key)
    {
        $applications = FormAApplication::where('credit_key', $credit_key)->get();

        if ($applications->count() !== 1) {
            return redirect()->route('form_a.system_error')->with([
                'error_message' => '申し込みデータの不整合が発生しました。'
            ]);
        }

        $application = $applications->first();

        // 決済要求
        $params = [
            'item_id' => 'form_a',
            'item_name' => 'フォーム A',
            'tax' => $application->tax,
            'amount' => $application->total,
            'tds_authentication_id' => $application->tds_authentication_id,
        ];
        $response = $sbpsSerice->request3dsSettlement($params);
        if (!is_array($response)) {
            return redirect()->route('form_a.system_error')->with([
                'error_message' => 'クレジットカードの決済要求でシステムエラーが発生しました。'
            ]);
        }
        if ($response['res_result'] !== 'OK') {
            return redirect()->route('form_a.credit_error')->with($response);
        }

        $application->sps_transaction_id = $response['res_sps_transaction_id'];
        $application->tracking_id = $response['res_tracking_id'];

        // 確定要求
        $params = [
            'sps_transaction_id' => $application->sps_transaction_id,
            'tracking_id' => $application->tracking_id,
        ];
        $response = $sbpsSerice->requestConfirm($params);
        if (!is_array($response)) {
            return redirect()->route('form_a.system_error')->with([
                'error_message' => 'クレジットカードの確定要求でシステムエラーが発生しました。'
            ]);
        }
        if ($response['res_result'] !== 'OK') {
            return redirect()->route('form_a.credit_error')->with($response);
        }

        $application->status = config('common.status.申込確定');
        $application->credit_key = '';
        $application->save();

        $this->sendMail($formAService, $application);

        return redirect()->route('form_a.send_complete');
    }
    public function credit_ng($credit_key)
    {
        $application = FormAApplication::where('credit_key', $credit_key)->first();
        if ($application) {
            $application->credit_key = '';
            $application->save();    
        }

        return redirect()->route('form_a.system_error')->with([
            'error_message' => 'クレジットカードの3DS認証が通りませんでした。'
        ]);
    }

    private function sendMail(FormAService $formAService, FormAApplication $application)
    {
        // メール送信設定
        $mail = Setting::findOrFail(1)->mail;
        $mail = json_decode($mail);

        // 自動返信メール
        // フォームA　基本設定
        $setting = $formAService->getSetting();
        $replymail = json_decode($setting->replymail);

        // 表示するデータ
        $viewData = $application->toArray();

        $viewData['birthday_year']  = substr($application->birthday, 0, 4);
        $viewData['birthday_month'] = substr($application->birthday, 5, 2);
        $viewData['birthday_day']   = substr($application->birthday, 8, 2);

        $viewData['zip1']   = substr($application->zip, 0, 3);
        $viewData['zip2']   = substr($application->zip, 3, 4);

        $answer = array_flip(config('common.answer'));
        $gender = array_flip(config('common.gender'));
        $job    = array_flip(config('common.job'));
        $viewData['answer1'] = $answer[$application->q1] ?? '';
        $viewData['answer2'] = $answer[$application->q2] ?? '';
        $viewData['gender']  = $gender[$application->gender] ?? '';
        $viewData['job']     = $job[$application->job] ?? '';

        // 検定試験
        if ($application->exam_id) {
            $exam = $formAService->getExam($application->exam_id);
            if (is_object($exam)) {
                $viewData['exam_name'] = $exam->name;
                $viewData['exam_price'] = $exam->price;
            }
        }

        // 受験会場
        if ($application->exam_venue_id) {
            $exam_venue = $formAService->getExamVenue($application->exam_venue_id);
            if (is_object($exam_venue)) {
                $viewData['exam_venue_name'] = $exam_venue->name;
            }
        }

        $city = array_flip(config('common.city'));

        // 通学コース
        if ($application->normal) {
            $viewData['normal_price'] = $formAService->getNormalPrice();

            // 受講会場
            if ($application->normal_venue_id) {
                $normal_venue = $formAService->getNormalVenue($application->normal_venue_id);
                if (is_object($normal_venue)) {
                    $viewData['normal_venue_city'] = $normal_venue->city;
                    $viewData['normal_venue_city_name'] = $city[$normal_venue->city] ?? '';
                    $viewData['normal_venue_name'] = $normal_venue->name;
                    $viewData['normal_venue_schedule'] = $normal_venue->schedule;
                }
            }
        }

        // 速習コース
        $viewData['fast_venue_list'] = [];
        if ($application->fast) {
            // 講座
            if ($application->fast_course_id) {
                $fast_course = $formAService->getFastCourse($application->fast_course_id);
                if (is_object($fast_course)) {
                    $viewData['fast_course_name'] = $fast_course->name;
                    $viewData['fast_course_price'] = $fast_course->price;
                }
            }

            // 受講会場
            if ($application->fast_venue_id) {
                $fast_venue_list = $formAService->getFastVenueList($application->fast_venue_id);
                if (is_array($fast_venue_list)) {
                    $viewData['fast_venue_list'] = $fast_venue_list;
                    $viewData['fast_venue'] = json_encode($fast_venue_list);
                }
            }
        }

        // 科目別 過去問題集
        if ($application->workbook_id) {
            $workbook = $formAService->getWorkbook($application->workbook_id);
            if (is_object($workbook)) {
                $viewData['workbook_name'] = $workbook->name;
                $viewData['workbook_price'] = $workbook->price;
            }
        }

        $params = [
            'mail'        => $mail, // メール送信設定
            'sender_name' => $mail->from_name,
            'from'        => $mail->from_address,
            'cc'          => empty($replymail->cc_address)  ? [] : explode("\n", $replymail->cc_address),
            'bcc'         => empty($replymail->bcc_address) ? [] : explode("\n", $replymail->bcc_address),
            'subject'     => $replymail->subject,
        ];

        if (!empty($application->mailaddress)) {
            // 申込者へ
            $params['to'] = $application->mailaddress;
            Mail::send(new FormAReplyMail($params, $viewData));
            // 管理者へ
            // $params['sender_name'] = $application->sei . ' ' . $application->mei;
            // $params['from']        = $application->mailaddress;
            $params['to'] = $mail->from_address;
            Mail::send(new FormAReplyMail($params, $viewData));
        } else {
            // 管理者へ
            $params['to'] = $mail->from_address;
            Mail::send(new FormAReplyMail($params, $viewData));
        }
    }

    public function send_complete()
    {
        $data = [

        ];

        return view('frontend.send_complete', $data);
    }

    public function calculate(Request $request, FormAService $formAService)
    {
        $result = $formAService->calculate($request->all());
        $result->subtotal1 = number_format($result->subtotal1);
        $result->subtotal2 = number_format($result->subtotal2);
        $result->total = number_format($result->total);
        return $result;
    }

    public function credit_error()
    {
        return view('frontend.credit_error');
    }

    public function system_error()
    {
        return view('frontend.system_error');
    }

}
