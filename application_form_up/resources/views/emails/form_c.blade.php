FLAネットワーク協会です。
食アド®会員関連、イベント関連のお申込ありがとうございました。
下記お申込内容となります。

郵便局でのお支払いをご希望の方には専用の払込取扱票を
お送りいたします。
※会員様でゼミナールのお申込みの場合は、参加証をお送りいたします。

なお本メールは自動返信メール専用となっており、返信はできません。
---------------------------------------------------------------------------
[食生活アドバイザー会員]
{{ $member }}
{{ $member_number }}

[氏名]
{{ $sei }} {{ $mei }}

[フリガナ]
{{ $sei_kana }} {{ $mei_kana }}

[生年月日]
{{ $birthday_year }}年 {{ $birthday_month }}月 {{ $birthday_day }}日

[性別]
{{ $gender }}

[住所]
〒 {{ $zip1 }}-{{ $zip2 }}
{{ $pref }}
{{ $city ?? '' }}
{{ $address1 }}
{{ $address2 }}
{{ $workplace }}
{{ $department }}

[電話番号]
{{ $tel }}

[メールアドレス]
{{ $mailaddress }}

[食アド会員]
@if ($member_fee_id)
{{ $member_fee_name }}
{{ number_format($member_fee_price) }}円
@endif

[食アドのお店]
@if ($shop_fee_id)
{{ $shop_fee_name }}
{{ number_format($shop_fee_price) }}円
@endif

[食アドゼミナール]
@if ($seminar_venue)
@foreach ($seminar_venue_list as $seminar_venue)
・{{ $seminar_venue->name }}　{{ $seminar_venue->price_label }}
@endforeach
@endif

[食アドAcademy]
@if ($academy_course)
@foreach ($academy_course_list as $academy_course)
・{{ $academy_course->name }}　{{ $academy_course->price_label }}
@endforeach
@endif

[お支払い合計]
{{ number_format($total) }}円
---------------------------------------------------------------------------