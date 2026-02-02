<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Models\FormASetting;

use Auth;

use Log;

// 表画面のフォームA
class FormAService
{
    private $setting;

    public function __construct()
    {
        if (Auth::check()) {
            $this->setting = FormASetting::where('id', 1)->firstOrFail();
        } else {
            $this->setting = FormASetting::where('id', 1)->where('public', config('common.public.公開'))->firstOrFail();
        }

        $exam = json_decode($this->setting->exam);
        $this->setting->exam = is_array($exam) ? $exam : [];

        $exam_venue = json_decode($this->setting->exam_venue);
        $this->setting->exam_venue = is_array($exam_venue) ? $exam_venue : [];

        $normal_venue = json_decode($this->setting->normal_venue);
        $this->setting->normal_venue = is_array($normal_venue) ? $normal_venue : [];

        $fast_course = json_decode($this->setting->fast_course);
        $this->setting->fast_course = is_array($fast_course) ? $fast_course : [];

        $fast_venue = json_decode($this->setting->fast_venue);
        $this->setting->fast_venue = is_array($fast_venue) ? $fast_venue : [];

        $workbook = json_decode($this->setting->workbook);
        $this->setting->workbook = is_array($workbook) ? $workbook : [];

    }

    public function getSetting()
    {
        return $this->setting;
    }

    // 検定試験
    public function getExam($exam_id)
    {
        if (empty($exam_id)) {
            return;
        }
        foreach ($this->setting->exam as $exam) {
            if (!$exam->enabled) continue;
            if ($exam->id != $exam_id) continue;
            return $exam;
        }

        return;
    }

    // 受験会場
    public function getExamVenue($exam_venue_id)
    {
        if (empty($exam_venue_id)) {
            return;
        }

        foreach ($this->setting->exam_venue as $exam_venue) {
            if (!$exam_venue->enabled) continue;
            if ($exam_venue->id != $exam_venue_id) continue;
            return $exam_venue;
        }

        return;
    }

    // 通学コース　受講料
    public function getNormalPrice()
    {
        return $this->setting->normal_price;
    }

    // 通学コース　受講会場
    public function getNormalVenue($normal_venue_id)
    {
        if (empty($normal_venue_id)) {
            return;
        }

        foreach ($this->setting->normal_venue as $normal_venue) {
            if (!$normal_venue->enabled) continue;
            if ($normal_venue->id != $normal_venue_id) continue;
            return $normal_venue;
        }

        return;
    }

    // 速習コース　講座
    public function getFastCourse($fast_course_id)
    {
        if (empty($fast_course_id)) {
            return;
        }

        foreach ($this->setting->fast_course as $fast_course) {
            if (!$fast_course->enabled) continue;
            if ($fast_course->id != $fast_course_id) continue;
            return $fast_course;
        }

        return;
    }

    // 速習コース　受講会場リスト
    public function getFastVenueList($fast_venue_id)
    {
        if (empty($fast_venue_id)) {
            return;
        }

        $city = array_flip(config('common.city'));

        $list = [];

        foreach ($this->setting->fast_venue as $fast_venue) {
            if (!$fast_venue->enabled) continue;
            if (!in_array($fast_venue->id, $fast_venue_id)) continue;

            $fast_venue->city_name = $city[$fast_venue->city] ?? '';

            $list[] = $fast_venue;
        }

        return $list;
    }

    // 科目別 過去問題集
    public function getWorkbook($workbook_id)
    {
        if (empty($workbook_id)) {
            return;
        }
        foreach ($this->setting->workbook as $workbook) {
            if (!$workbook->enabled) continue;
            if ($workbook->id != $workbook_id) continue;
            return $workbook;
        }

        return;
    }

    // 金額計算
    public function calculate($input)
    {
        Log::debug($input);

        $subtotal1 = 0; // 試験と講座
        $subtotal2 = 0; // 問題集
        $total = 0;
        $tax = 0;

        // 検定試験
        $exam_id = $input['exam_id'] ?? '';
        foreach ($this->setting->exam as $exam) {
            if (!$exam->enabled) continue;
            if ($exam->id == $exam_id) {
                $subtotal1 += $exam->price;
                $total += $exam->price;
                $tax += $this->tax($exam->price);
                break;
            }
        }

        // 通学コース　講座
        $normal = $input['normal'] ?? '';
        if ($normal == 1) {
            $subtotal1 += $this->setting->normal_price;
            $total += $this->setting->normal_price;
            $tax += $this->tax($this->setting->normal_price);
        }

        // 速習コース　講座
        $fast = $input['fast'] ?? '';
        if ($fast == 1) {
            $fast_course_id = $input['fast_course_id'] ?? '';
            foreach ($this->setting->fast_course as $fast_course) {
                if (!$fast_course->enabled) continue;
                if ($fast_course->id == $fast_course_id) {
                    $subtotal1 += $fast_course->price;
                    $total += $fast_course->price;
                    $tax += $this->tax($fast_course->price);
                    break;
                }
            }
        }

        // 科目別 過去問題集
        $workbook_id = $input['workbook_id'] ?? '';
        foreach ($this->setting->workbook as $workbook) {
            if (!$workbook->enabled) continue;
            if ($workbook->id == $workbook_id) {
                $subtotal2 += $workbook->price;
                $total += $workbook->price;
                $tax += $this->tax($workbook->price);
                break;
            }
        }

        $result = new \stdClass();
        $result->subtotal1 = $subtotal1;
        $result->subtotal2 = $subtotal2;
        $result->total     = $total;
        $result->tax       = $tax;

        return $result;
    }

    private function tax($price)
    {
        return round($price * 10 / 110);
    }
}
