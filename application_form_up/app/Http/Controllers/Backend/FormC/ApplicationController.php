<?php

namespace App\Http\Controllers\Backend\FormC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FormCApplication;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $application = FormCApplication::query();

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

        return view('backend.form_c.index', $data);

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $application = FormCApplication::findOrFail($id);

        $member = array_flip(config('common.member'));
        $gender = array_flip(config('common.gender'));
        $application->member = $member[$application->member] ?? '';
        $application->gender = $gender[$application->gender] ?? '';
        
        $application->seminar_venue_list = json_decode($application->seminar_venue);
        $application->academy_course_list = json_decode($application->academy_course);

        $data = [
            'application' => $application,
        ];

        return view('backend.form_c.detail', $data);

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
        $application = FormCApplication::findOrFail($id);
        $application->status = $request->status;
        $application->memo = $request->memo;
        $application->save();

        return redirect()->route('backend.form_c.applications.index', ['id' => $id]);
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

    private function seminarVenueList($seminar_venue)
    {
        $seminar_venue_list = json_decode($seminar_venue);
        if (!(is_array($seminar_venue_list) && count($seminar_venue_list) > 0)) {
            return '';
        }

        $list = [];
        foreach ($seminar_venue_list as $row) {
            $list[] = $row->name;
        }

        return implode("、", $list);
    }

    private function academyCourseList($academy_course)
    {
        $academy_course_list = json_decode($academy_course);
        if (!(is_array($academy_course_list) && count($academy_course_list) > 0)) {
            return '';
        }

        $list = [];
        foreach ($academy_course_list as $row) {
            $list[] = $row->name;
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
            'member|array_flip(config("common.answer"))[$value] ?? ""' => '食生活アドバイザー会員',
            'member_number' => '会員番号または登録番号',
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
            'member_fee_name' => '食アド会員',
            'shop_fee_name' => '食アドのお店',
            'seminar_venue|$this->seminarVenueList($value)' => '食アドゼミナール',
            'academy_course|$this->academyCourseList($value)' => '食アドAcademy',
            'tracking_id|$value ? "クレジットカード決済" : "郵便局支払い"' => '支払方法',
            'total' => 'お支払い合計',
        ];

        $callback = function() use ($request, $columns) {
             $stream = fopen('php://output', 'w');

            fwrite($stream, "\xEF\xBB\xBF");

            // ヘッダー行
            fputcsv($stream, array_values($columns));

            // データ
            $application = FormCApplication::query();

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

        $filename = sprintf('%s-form_c.csv', date('YmdHis'));

        $header = [
            'Content-Type' => 'application/octet-stream',
        ];

        return response()->streamDownload($callback, $filename, $header);
    }


}
