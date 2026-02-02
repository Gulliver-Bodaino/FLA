<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormBApplication extends Model
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
    ];

}
