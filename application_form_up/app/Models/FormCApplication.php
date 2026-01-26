<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormCApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'member',
        'member_number',
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
        'member_fee_id',
        'member_fee_name',
        'member_fee_price',
        'shop_fee_id',
        'shop_fee_name',
        'shop_fee_price',
        'seminar_venue',
        'academy_course',
        'subtotal_member',
        'subtotal_shop',
        'subtotal_seminar',
        'subtotal_academy',
        'total',
        'tax',
        'credit_key',
        'sps_transaction_id',
        'tds_authentication_id',
        'tracking_id',
    ];

}
