<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Models\FormCSetting;

use Auth;

use Log;

// 表画面のフォームC
class FormCService
{
    private $setting;

    public function __construct()
    {
        if (Auth::check()) {
            $this->setting = FormCSetting::where('id', 1)->firstOrFail();
        } else {
            $this->setting = FormCSetting::where('id', 1)->where('public', config('common.public.公開'))->firstOrFail();
        }

        $member_fee = json_decode($this->setting->member_fee);
        $this->setting->member_fee = is_array($member_fee) ? $member_fee : [];

        $shop_fee = json_decode($this->setting->shop_fee);
        $this->setting->shop_fee = is_array($shop_fee) ? $shop_fee : [];

        $seminar_venue = json_decode($this->setting->seminar_venue);
        $this->setting->seminar_venue = is_array($seminar_venue) ? $seminar_venue : [];

        $academy_course = json_decode($this->setting->academy_course);
        $this->setting->academy_course = is_array($academy_course) ? $academy_course : [];
    }

    public function getSetting()
    {
        return $this->setting;
    }

    // 食アド会員
    public function getMemberFeeList($member_fee_id = null)
    {
        $list = [];

        foreach ($this->setting->member_fee as $member_fee) {
            if (!$member_fee->enabled) continue;
            if (empty($member_fee_id)) {
                $list[] = $member_fee;
            } else if ($member_fee->id == $member_fee_id) {
                $list[] = $member_fee;
            }
        }

        return $list;
    }
    public function getMemberFee($member_fee_id)
    {
        if (empty($member_fee_id)) {
            return;
        }
        foreach ($this->setting->member_fee as $member_fee) {
            if (!$member_fee->enabled) continue;
            if ($member_fee->id != $member_fee_id) continue;
            return $member_fee;
        }

        return;
    }

    // 食アドのお店
    public function getShopFeeList($shop_fee_id = null)
    {
        $list = [];

        foreach ($this->setting->shop_fee as $shop_fee) {
            if (!$shop_fee->enabled) continue;
            if (empty($shop_fee_id)) {
                $list[] = $shop_fee;
            } else if ($shop_fee->id == $shop_fee_id) {
                $list[] = $shop_fee;
            }
        }

        return $list;
    }
    public function getShopFee($shop_fee_id)
    {
        if (empty($shop_fee_id)) {
            return;
        }
        foreach ($this->setting->shop_fee as $shop_fee) {
            if (!$shop_fee->enabled) continue;
            if ($shop_fee->id != $shop_fee_id) continue;
            return $shop_fee;
        }

        return;
    }


    // 食アドゼミナール
    public function getSeminarVenueList($seminar_venue_id = null)
    {
        $list = [];

        foreach ($this->setting->seminar_venue as $seminar_venue) {
            if (!$seminar_venue->enabled) continue;
            if (empty($seminar_venue_id)) {
                $list[] = $seminar_venue;
            } else if (in_array($seminar_venue->id, $seminar_venue_id)) {
                $list[] = $seminar_venue;
            }
        }

        return $list;
    }

    // 食アドAcademy
    public function getAcademyCourseList($academy_course_id = null)
    {
        $list = [];

        foreach ($this->setting->academy_course as $academy_course) {
            if (!$academy_course->enabled) continue;
            if (empty($academy_course_id)) {
                $list[] = $academy_course;
            } else if (in_array($academy_course->id, $academy_course_id)) {
                $list[] = $academy_course;
            }
        }

        return $list;
    }


    // 金額計算
    public function calculate($input)
    {
        Log::debug($input);

        $subtotal_member = 0;
        $subtotal_shop = 0;
        $subtotal_seminar = 0;
        $subtotal_academy = 0;
        $total = 0;
        $tax = 0;

        $member_fee_id = $input['member_fee_id'] ?? '';
        foreach ($this->setting->member_fee as $member_fee) {
            if (!$member_fee->enabled) continue;
            if ($member_fee->id == $member_fee_id) {
                $subtotal_member += $member_fee->price;
                $total += $member_fee->price;
                $tax += $this->tax($member_fee->price);
                break;
            }
        }

        $shop_fee_id = $input['shop_fee_id'] ?? '';
        foreach ($this->setting->shop_fee as $shop_fee) {
            if (!$shop_fee->enabled) continue;
            if ($shop_fee->id == $shop_fee_id) {
                $subtotal_shop += $shop_fee->price;
                $total += $shop_fee->price;
                $tax += $this->tax($shop_fee->price);
                break;
            }
        }

        $seminar_venue_id = $input['seminar_venue_id'] ?? [];
        foreach ($this->setting->seminar_venue as $seminar_venue) {
            if (!$seminar_venue->enabled) continue;
            if (in_array($seminar_venue->id, $seminar_venue_id)) {
                $subtotal_seminar += $seminar_venue->price;
                $total += $seminar_venue->price;
                $tax += $this->tax($seminar_venue->price);
            }
        }

        $academy_course_id = $input['academy_course_id'] ?? [];
        foreach ($this->setting->academy_course as $academy_course) {
            if (!$academy_course->enabled) continue;
            if (in_array($academy_course->id, $academy_course_id)) {
                $subtotal_academy += $academy_course->price;
                $total += $academy_course->price;
                $tax += $this->tax($academy_course->price);
            }
        }

        $result = new \stdClass();
        $result->subtotal_member = $subtotal_member;
        $result->subtotal_shop = $subtotal_shop;
        $result->subtotal_seminar = $subtotal_seminar;
        $result->subtotal_academy = $subtotal_academy;
        $result->total = $total;
        $result->tax   = $tax;

        return $result;
    }

    private function tax($price)
    {
        return round($price * 10 / 110);
    }

}
