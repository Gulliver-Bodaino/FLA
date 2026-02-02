<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Models\FormASetting;

use Log;

class CalculatorService
{
    // フォームAの金額計算
//    public function formA(Request $request)
    public function formA($input)
    {
//        Log::debug($request->all());

        $subtotal1 = 0; // 試験と講座
        $subtotal2 = 0; // 問題集
        $total = 0;

        $setting = FormASetting::where('id', 1)->where('public', config('common.public.公開'))->firstOrFail();

        // 検定試験
        $exam_id = $input['exam_id'] ?? '';
        foreach (json_decode($setting->exam) as $exam) {
            if (!$exam->enabled) continue;
            if ($exam->id == $exam_id) {
                $subtotal1 += $exam->price;
                $total += $exam->price;
                break;
            }
        }

        // 通学コース　講座
        $normal = $input['normal'] ?? '';
        if ($normal == 1) {
            $subtotal1 += $setting->normal_price;
            $total += $setting->normal_price;
        }

        // 速習コース　講座
        $fast_course_id = $input['fast_course_id'] ?? '';
        foreach (json_decode($setting->fast_course) as $fast_course) {
            if (!$fast_course->enabled) continue;
            if ($fast_course->id == $fast_course_id) {
                $subtotal1 += $fast_course->price;
                $total += $fast_course->price;
                break;
            }
        }

        // 科目別 過去問題集
        $workbook_id = $input['workbook_id'] ?? '';
        foreach (json_decode($setting->workbook) as $workbook) {
            if (!$workbook->enabled) continue;

            if ($workbook->id == $workbook_id) {
                $subtotal2 += $workbook->price;
                $total += $workbook->price;
                break;
            }
        }

        $result = new \stdClass();
        $result->subtotal1 = $subtotal1;
        $result->subtotal2 = $subtotal2;
        $result->total = $total;

        return $result;
    }
}
