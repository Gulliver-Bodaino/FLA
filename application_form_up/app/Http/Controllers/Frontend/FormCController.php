<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\CheckFormC;

use App\Models\Setting;
use App\Models\FormCSetting;
use App\Models\FormCApplication;

use Log;
use App;
use Str;

use App\Services\FormCService;
use App\Services\SbpsService;

use Mail;
use App\Mail\FormCReplyMail;

class FormCController extends Controller
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
    public function form(Request $request, FormCService $formCService)
    {
        // フォームC　基本設定
        $setting = $formCService->getSetting();
//        if (App::environment('local')) {
            $old = [
                'member' => '1',
                'member_number' => '123456789',
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
                'address1' => 'テスト町１－２－３',
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
//        }

        $setting->member_fee_list = $formCService->getMemberFeeList();
        $setting->shop_fee_list = $formCService->getShopFeeList();
        $setting->seminar_venue_list = $formCService->getSeminarVenueList();
        $setting->academy_course_list = $formCService->getAcademyCourseList();

        $result = $formCService->calculate(old());

        $data = [
            'setting' => $setting,
            'subtotal_member'  => $result->subtotal_member,
            'subtotal_shop'    => $result->subtotal_shop,
            'subtotal_seminar' => $result->subtotal_seminar,
            'subtotal_academy' => $result->subtotal_academy,
            'total'            => $result->total,
        ];

        return view('frontend.form_c', $data);
    }

    public function credit(CheckFormC $request, FormCService $formCService)
    {
        // フォームC　基本設定
        $setting = $formCService->getSetting();

        $data = [
            'action' => route('form_c.confirm'),
            'check_credit_url' => route('form_c.check_credit'),
        ];

        return view('frontend.credit', $data);
    }

    public function confirm(CheckFormC $request, FormCService $formCService)
    {
        // フォームC　基本設定
        $setting = $formCService->getSetting();

        $setting->member_fee_list     = $request->filled('member_fee_id') ?     $formCService->getMemberFeeList($request->member_fee_id) : [];
        $setting->shop_fee_list       = $request->filled('shop_fee_id') ?       $formCService->getShopFeeList($request->shop_fee_id) : [];
        $setting->seminar_venue_list  = $request->filled('seminar_venue_id') ?  $formCService->getSeminarVenueList($request->seminar_venue_id) : [];
        $setting->academy_course_list = $request->filled('academy_course_id') ? $formCService->getAcademyCourseList($request->academy_course_id) : [];

        $result = $formCService->calculate($request->input());

        $data = [
            'setting' => $setting,
            'subtotal_member'  => $result->subtotal_member,
            'subtotal_shop'    => $result->subtotal_shop,
            'subtotal_seminar' => $result->subtotal_seminar,
            'subtotal_academy' => $result->subtotal_academy,
            'total'            => $result->total,
        ];

        return view('frontend.form_c_confirm', $data);
    }

    // クレジットカード決済処理かどうか？
    private function isCredit(Request $request)
    {
        return $request->filled('tokenKey');
    }

    public function send(CheckFormC $request, FormCService $formCService, SbpsService $sbpsSerice)
    {
        $action = $request->get('action', 'correct');
        if ($action !== 'send') {
            return redirect()->route('form_c.form')->withInput();
        }

        $result = $formCService->calculate($request->all());

        if ($this->isCredit($request)) {
            // 3DS認証要求
            $credit_key = (string) Str::uuid();
            $params = [
                'amount' => $result->total,
                'ok_return_url' => route('form_c.credit_ok', ['credit_key' => $credit_key]),
                'ng_return_url' => route('form_c.credit_ng', ['credit_key' => $credit_key])
            ];

            $response = $sbpsSerice->request3dsSecure($request, $params);

            if (!is_array($response)) {
                return redirect()->route('form_c.system_error')->with([
                    'error_message' => 'クレジットカードの3DS認証要求でシステムエラーが発生しました。'
                ]);
            }

            if ($response['res_result'] !== 'OK') {
                return redirect()->route('form_c.credit_error')->with($response);
            }
        }

        $merge = [
            'status' => $this->isCredit($request) ? config('common.status.決済処理中') : config('common.status.入金待ち'),
            'birthday' => sprintf('%04d-%02d-%02d', $request->birthday_year, $request->birthday_month, $request->birthday_day),
            'zip' => $request->zip1 . $request->zip2,
            'tel' => preg_replace('/[^0-9]/', '', $request->tel),
            'credit_key' => $credit_key ?? '',
            'sps_transaction_id' => $response['res_sps_transaction_id'] ?? '',
            'tds_authentication_id' => $response['res_tds_authentication_id'] ?? '',
        ];

        // 食アド会員
        if ($request->filled('member_fee_id')) {
            $member_fee = $formCService->getMemberFee($request->member_fee_id);
            if (is_object($member_fee)) {
                $merge['member_fee_name'] = $member_fee->name;
                $merge['member_fee_price'] = $member_fee->price;
            }
        } else {
            $merge['member_fee_id'] = null;
        }

        // 食アドのお店
        if ($request->filled('shop_fee_id')) {
            $shop_fee = $formCService->getShopFee($request->shop_fee_id);
            if (is_object($shop_fee)) {
                $merge['shop_fee_name'] = $shop_fee->name;
                $merge['shop_fee_price'] = $shop_fee->price;
            }
        } else {
            $merge['shop_fee_id'] = null;
        }

        // 食アドゼミナール
        if ($request->filled('seminar_venue_id')) {
            $seminar_venue_list = $formCService->getSeminarVenueList($request->seminar_venue_id);
            if (is_array($seminar_venue_list)) {
                $merge['seminar_venue_list'] = $seminar_venue_list;
                $merge['seminar_venue'] = json_encode($seminar_venue_list);
            }
        } else {
            $merge['seminar_venue'] = null;
        }

        // 食アドAcademy
        if ($request->filled('academy_course_id')) {
            $academy_course_list = $formCService->getAcademyCourseList($request->academy_course_id);
            if (is_array($academy_course_list)) {
                $merge['academy_course_list'] = $academy_course_list;
                $merge['academy_course'] = json_encode($academy_course_list);
            }
        } else {
            $merge['academy_course'] = null;
        }

        $merge['subtotal_member']  = $result->subtotal_member;
        $merge['subtotal_shop']    = $result->subtotal_shop;
        $merge['subtotal_seminar'] = $result->subtotal_seminar;
        $merge['subtotal_academy'] = $result->subtotal_academy;
        $merge['total']            = $result->total;
        $merge['tax']              = $result->tax;

        Log::debug($merge);

        // 申込データ保存
        $application = new FormCApplication();
        $request->merge($merge);
        if (!$application->fill($request->all())->save()) {
            return redirect()->route('form_c.system_error')->with([
                'error_message' => '申し込みデータを保存できませんでした。'
            ]);
        }

        if ($this->isCredit($request)) {
            // 3DS認証結果のredirect_urlへリダイレクト
            return redirect($response['redirect_url']);
        }

        $this->sendMail($formCService, $application);

        return redirect()->route('form_c.send_complete');
    }

    public function credit_ok(FormCService $formCService, SbpsService $sbpsSerice, $credit_key)
    {
        $applications = FormCApplication::where('credit_key', $credit_key)->get();

        if ($applications->count() !== 1) {
            return redirect()->route('form_c.system_error')->with([
                'error_message' => '申し込みデータの不整合が発生しました。'
            ]);
        }

        $application = $applications->first();

        // 決済要求
        $params = [
            'item_id' => 'form_c',
            'item_name' => 'フォーム C',
            'tax' => $application->tax,
            'amount' => $application->total,
            'tds_authentication_id' => $application->tds_authentication_id,
        ];
        $response = $sbpsSerice->request3dsSettlement($params);
        if (!is_array($response)) {
            return redirect()->route('form_c.system_error')->with([
                'error_message' => 'クレジットカードの決済要求でシステムエラーが発生しました。'
            ]);
        }
        if ($response['res_result'] !== 'OK') {
            return redirect()->route('form_c.credit_error')->with($response);
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
            return redirect()->route('form_c.system_error')->with([
                'error_message' => 'クレジットカードの確定要求でシステムエラーが発生しました。'
            ]);
        }
        if ($response['res_result'] !== 'OK') {
            return redirect()->route('form_c.credit_error')->with($response);
        }

        $application->status = config('common.status.申込確定');
        $application->credit_key = '';
        $application->save();

        $this->sendMail($formCService, $application);

        return redirect()->route('form_c.send_complete');
    }
    public function credit_ng($credit_key)
    {
        $application = FormCApplication::where('credit_key', $credit_key)->first();
        if ($application) {
            $application->credit_key = '';
            $application->save();    
        }

        return redirect()->route('form_c.system_error')->with([
            'error_message' => 'クレジットカードの3DS認証が通りませんでした。'
        ]);
    }

    private function sendMail(FormCService $formCService, FormCApplication $application)
    {
        // メール送信設定
        $mail = Setting::findOrFail(1)->mail;
        $mail = json_decode($mail);

        // 自動返信メール
        // フォームC　基本設定
        $setting = $formCService->getSetting();
        $replymail = json_decode($setting->replymail);

        // 表示するデータ
        $viewData = $application->toArray();

        $viewData['birthday_year']  = substr($application->birthday, 0, 4);
        $viewData['birthday_month'] = substr($application->birthday, 5, 2);
        $viewData['birthday_day']   = substr($application->birthday, 8, 2);

        $viewData['zip1']   = substr($application->zip, 0, 3);
        $viewData['zip2']   = substr($application->zip, 3, 4);

        $member = array_flip(config('common.member'));
        $gender = array_flip(config('common.gender'));
        $viewData['member'] = $member[$application->member] ?? '';
        $viewData['gender'] = $gender[$application->gender] ?? '';

        // 食アド会員
        if ($application->member_fee_id) {
            $member_fee = $formCService->getMemberFee($application->member_fee_id);
            if (is_object($member_fee)) {
                $viewData['member_fee_name'] = $member_fee->name;
                $viewData['member_fee_price'] = $member_fee->price;
            }
        }

        // 食アドのお店
        if ($application->shop_fee_id) {
            $shop_fee = $formCService->getShopFee($application->shop_fee_id);
            if (is_object($shop_fee)) {
                $viewData['shop_fee_name'] = $shop_fee->name;
                $viewData['shop_fee_price'] = $shop_fee->price;
            }
        }

        // 食アドゼミナール
        if ($application->seminar_venue) {
            $seminar_venue_id = [];
            foreach (json_decode($application->seminar_venue) as $row) {
                $seminar_venue_id[] = $row->id;
            }

            $seminar_venue_list = $formCService->getSeminarVenueList($seminar_venue_id);
            if (is_array($seminar_venue_list)) {
                $viewData['seminar_venue_list'] = $seminar_venue_list;
                $viewData['seminar_venue'] = json_encode($seminar_venue_list);
            }
        }

        // 食アドAcademy
        if ($application->academy_course) {
            $academy_course_id = [];
            foreach (json_decode($application->academy_course) as $row) {
                $academy_course_id[] = $row->id;
            }

            $academy_course_list = $formCService->getAcademyCourseList($academy_course_id);
            if (is_array($academy_course_list)) {
                $viewData['academy_course_list'] = $academy_course_list;
                $viewData['academy_course'] = json_encode($academy_course_list);
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
            Mail::send(new FormCReplyMail($params, $viewData));
            // 管理者へ
            // $params['sender_name'] = $application->sei . ' ' . $application->mei;
            // $params['from']        = $application->mailaddress;
            $params['to'] = $mail->from_address;
            Mail::send(new FormCReplyMail($params, $viewData));
        } else {
            // 管理者へ
            $params['to'] = $mail->from_address;
            Mail::send(new FormCReplyMail($params, $viewData));
        }
    }

    public function send_complete()
    {
        $data = [

        ];

        return view('frontend.send_complete', $data);
    }

    public function calculate(Request $request, FormCService $formCService)
    {
        $result = $formCService->calculate($request->all());
        $result->subtotal_member  = number_format($result->subtotal_member);
        $result->subtotal_shop    = number_format($result->subtotal_shop);
        $result->subtotal_seminar = number_format($result->subtotal_seminar);
        $result->subtotal_academy = number_format($result->subtotal_academy);
        $result->total            = number_format($result->total);
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
