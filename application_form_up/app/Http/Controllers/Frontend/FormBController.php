<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\CheckFormB;

use App\Models\Setting;
use App\Models\FormBSetting;
use App\Models\FormBApplication;

use Log;
use App;

use Mail;
use App\Mail\FormBReplyMail;

use Auth;

use App\Services\FormBService;

class FormBController extends Controller
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

    public function form(Request $request, FormBService $formBService)
    {
        // フォームB　基本設定
        $setting = $formBService->getSetting();

        if (App::environment('local')) {
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
                'address1' => '岡山市',
                'address2' => 'テストビル',
                'workplace' => 'テスト勤務先',
                'department' => 'テスト部署',
                'tel' => '09011112222',
                'mailaddress' => 'okada@goozgoo.co.jp',
                'agree' => '1',
            ];
            $request->session()->flash('_old_input', $old);
        }

        $data = [
            'setting' => $setting,
        ];

        return view('frontend.form_b', $data);
    }

    // 確認画面
    public function confirm(CheckFormB $request, FormBService $formBService)
    {
        // フォームB　基本設定
        $setting = $formBService->getSetting();

        $data = [

        ];

        return view('frontend.form_b_confirm', $data);
    }

    // 送信
    public function send(CheckFormB $request, FormBService $formBService)
    {
        // フォームB　基本設定
        $setting = $formBService->getSetting();

        $action = $request->get('action', 'correct');
        if ($action !== 'send') {
            return redirect()->route('form_b.form')->withInput();
        }

        // メール送信設定
        $mail = Setting::findOrFail(1)->mail;
        $mail = json_decode($mail);

        // 自動返信メール
        $replymail = json_decode($setting->replymail);

        // 申込データ保存
        $application = new FormBApplication();
        $merge = [
            'status' => config('common.status.申込確定'),
            'birthday' => sprintf('%04d-%02d-%02d', $request->birthday_year, $request->birthday_month, $request->birthday_day),
            'zip' => $request->zip1 . $request->zip2,
            'tel' => preg_replace('/[^0-9]/', '', $request->tel),
        ];
        $request->merge($merge);
        $application->fill($request->all())->save();

        // 自動返信メール送信処理
        $viewData = [];
        foreach ($request->except(['_method', '_token']) as $key => $value) {
            $viewData[$key] = $value;
        }
        $answer = array_flip(config('common.answer'));
        $gender = array_flip(config('common.gender'));
        $viewData['answer1'] = $answer[$request->q1] ?? '';
        $viewData['answer2'] = $answer[$request->q2] ?? '';
        $viewData['gender']  = $gender[$request->gender] ?? '';

        $params = [
            'mail'        => $mail, // メール送信設定
            'sender_name' => $mail->from_name,
            'from'        => $mail->from_address,
            'cc'          => empty($replymail->cc_address)  ? [] : explode("\n", $replymail->cc_address),
            'bcc'         => empty($replymail->bcc_address) ? [] : explode("\n", $replymail->bcc_address),
            'subject'     => $replymail->subject,
        ];

        if ($request->filled('mailaddress')) {
            // 申込者へ
            $params['to'] = $request->mailaddress;
            Mail::send(new FormBReplyMail($params, $viewData));
            // 管理者へ
            // $params['sender_name'] = $request->sei . ' ' . $request->mei;
            // $params['from']        = $request->mailaddress;
            $params['to']          = $mail->from_address;
            Mail::send(new FormBReplyMail($params, $viewData));
        } else {
            // 管理者へ
            $params['to'] = $mail->from_address;
            Mail::send(new FormBReplyMail($params, $viewData));
        }

        $data = [

        ];

        return redirect()->route('form_b.send_complete');
    }

    // 送信完了
    public function send_complete()
    {

        $data = [

        ];

        return view('frontend.send_complete', $data);
    }

    // 金額計算

}
