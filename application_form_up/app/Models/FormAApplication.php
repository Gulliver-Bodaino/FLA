<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormAApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'q1',
        'q2',
        'sei',
        'mei',
        'sei_kana',
        'mei_kana',
        'birthday',
        'gender',
        'zip',
        'pref',
        'city',
        'address1',
        'address2',
        'workplace',
        'department',
        'tel',
        'mailaddress',
        'job',
        'exam_id',
        'exam_name',
        'exam_price',
        'exam_venue_id',
        'exam_venue_name',
        'normal',
        'normal_price',
        'normal_venue_id',
        'normal_venue_city',
        'normal_venue_city_name',
        'normal_venue_name',
        'normal_venue_schedule',
        'fast',
        'fast_course_id',
        'fast_course_name',
        'fast_course_price',
        'fast_venue',
        'workbook_id',
        'workbook_name',
        'workbook_price',
        'subtotal1',
        'subtotal2',
        'total',
        'tax',
        'credit_key',
        'sps_transaction_id',
        'tds_authentication_id',
        'tracking_id',
    ];

}
