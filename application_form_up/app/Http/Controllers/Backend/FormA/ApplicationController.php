<?php

namespace App\Http\Controllers\Backend\FormA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FormAApplication;

use Log;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $application = FormAApplication::query();

        if ($request->filled('id')) $application->where('id', $request->id);
        if ($request->filled('start_date')) {
            $start_date = $request->start_date . ' 00:00:00';
            $application->where('created_at', '>=', $start_date);
        }
        if ($request->filled('end_date')) {
            $end_date = $request->end_date . ' 23:59:59';
            $application->where('created_at', '<=', $end_date);
        }
        if ($request->filled('status')) $application->where('status', $request->status);
        if ($request->filled('tracking_id')) $application->where('tracking_id', $request->tracking_id);
        if ($request->filled('sei')) {
            $sei = $request->sei;
            $application->where(function ($query) use ($sei) {
                $value = '%' . $sei . '%';
                $query->where('sei', 'LIKE', $value)
                    ->orWhere('sei_kana', 'LIKE', $value);
            });
        }
        if ($request->filled('mei')) {
            $mei = $request->mei;
            $application->where(function ($query) use ($mei) {
                $value = '%' . $mei . '%';
                $query->where('mei', 'LIKE', $value)
                    ->orWhere('mei_kana', 'LIKE', $value);
            });
        }
        if ($request->filled('tel')) $application->where('tel', 'LIKE', '%' . $request->tel . '%');
        if ($request->filled('memo')) $application->where('memo', 'LIKE', '%' . $request->memo . '%');


        $application->orderBy('id', 'DESC');

        $applications = $application->paginate(100);

        $status = array_flip(config('common.status'));

        $applications->transform(function($application, $key) use ($status) {
            $application->status_name = $status[$application->status] ?? '';
            return $application;
        });

        $data = [
            'applications' => $applications,
        ];

        return view('backend.form_a.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        var_dump($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $application = FormAApplication::findOrFail($id);

        $answer = array_flip(config('common.answer'));
        $gender = array_flip(config('common.gender'));
        $job    = array_flip(config('common.job'));
        $application->answer1 = $answer[$application->q1] ?? '';
        $application->answer2 = $answer[$application->q2] ?? '';
        $application->gender  = $gender[$application->gender] ?? '';
        $application->job     = $job[$application->job] ?? '';
        
        $application->fast_venue_list = json_decode($application->fast_venue);

        $data = [
            'application' => $application,
        ];

        return view('backend.form_a.detail', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $application = FormAApplication::findOrFail($id);
        $application->status = $request->status;
        $application->memo = $request->memo;
        $application->save();

        return redirect()->route('backend.form_a.applications.index', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function fastVenueList($fast_venue)
    {
        $fast_venue_list = json_decode($fast_venue);
        if (!(is_array($fast_venue_list) && count($fast_venue_list) > 0)) {
            return '';
        }

        $list = [];
        foreach ($fast_venue_list as $row) {
            $list[] = $row->city_name . ' ' . $row->name . ' ' . $row->schedule;
        }

        return implode("、", $list);
    }

    public function download_csv(Request $request)
    {
        $columns = [
            'created_at|substr($value, 0, 10)' => '申込日',
            'created_at|substr($value, 11, 8)' => '申込時間',
            'status|array_flip(config("common.status"))[$value] ?? ""' => 'ステータス',
            'tracking_id' => 'トラッキングID',
            'q1|array_flip(config("common.answer"))[$value] ?? ""' => '過去に食生活アドバイザーの願書請求をしたことがありますか？',
            'q2|array_flip(config("common.answer"))[$value] ?? ""' => '過去に食生活アドバイザーの受験をしたことがありますか？',
            'sei' => '氏名（姓）',
            'mei' => '氏名（名）',
            'sei_kana' => 'フリガナ（姓）',
            'mei_kana' => 'フリガナ（名）',
            'birthday' => '生年月日',
            'gender|array_flip(config("common.gender"))[$value] ?? ""' => '性別',
            'zip' => '郵便番号',
            'pref' => '都道府県',
            'city' => '市区町村',
            'address1' => '住所',
            'address2' => 'ビル名等',
            'workplace' => '勤務先',
            'department' => '部署名',
            'tel' => '電話番号',
            'mailaddress' => 'メールアドレス',
            'job' => '職業コード',
            'job|array_flip(config("common.job"))[$value] ?? ""' => '職業名',
            'exam_count' => '受験回',
            'exam_name' => '検定試験',
            'exam_venue_name|preg_match("/(\d+)/", $value, $matches) ? $matches[0] : ""' => '受験会場コード',
            'exam_venue_name' => '受験会場',
            'normal|array_flip(config("common.normal"))[$value] ?? ""' => '通学コース',
            'normal_code' => '通学コースコード',
            'normal_venue_city_name' => '受講会場の都市',
            'normal_venue_name' => '受講会場',
            'normal_venue_schedule' => '日程',
            'fast|array_flip(config("common.fast"))[$value] ?? ""' => '速習コース',
            'fast_course_name' => '講座',
            'fast_code' => '速習コースコード',
            'fast_venue|$this->fastVenueList($value)' => '受講会場',
            'workbook_name' => '科目別 過去問題集',
            'total' => 'お支払い合計',
        ];

        $callback = function() use ($request, $columns) {
             $stream = fopen('php://output', 'w');

            fwrite($stream, "\xEF\xBB\xBF");

            // ヘッダー行
            fputcsv($stream, array_values($columns));

            // データ
            $application = FormAApplication::query();

            if ($request->filled('id')) $application->where('id', $request->id);
            if ($request->filled('start_date')) {
                $start_date = $request->start_date . ' 00:00:00';
                $application->where('created_at', '>=', $start_date);
            }
            if ($request->filled('end_date')) {
                $end_date = $request->end_date . ' 23:59:59';
                $application->where('created_at', '<=', $end_date);
            }
            if ($request->filled('status')) $application->where('status', $request->status);
            if ($request->filled('tracking_id')) $application->where('tracking_id', $request->tracking_id);
            if ($request->filled('name')) {
                $name = $request->name;
                $application->where(function ($query) use ($name) {
                    $value = '%' . $name . '%';
                    $query->where('sei', 'LIKE', $value)
                        ->orWhere('mei', 'LIKE', $value)
                        ->orWhere('sei_kana', 'LIKE', $value)
                        ->orWhere('mei_kana', 'LIKE', $value);
                });
            }
            if ($request->filled('tel')) $application->where('tel', 'LIKE', '%' . $request->tel . '%');
            if ($request->filled('memo')) $application->where('memo', 'LIKE', '%' . $request->memo . '%');
    
    
            $application->orderBy('id', 'DESC');
    
            // cursor()メソッドで１レコードずつストリームに流す
            foreach ($application->cursor() as $row) {
                $values = [];

                foreach ($columns as $def => $column) {
                    $filters = explode('|', $def);
                    $field = array_shift($filters);
                    $value = $row->$field ?? '';
                    foreach ($filters as $filter) {
                        $value = eval('return ' . $filter . ';');
                    }
                    $values[] = $value;
                }

                fputcsv($stream, $values);
            }
            fclose($stream);
        };

        $filename = sprintf('%s-form_a.csv', date('YmdHis'));

        $header = [
            'Content-Type' => 'application/octet-stream',
        ];

        return response()->streamDownload($callback, $filename, $header);
    }

}
