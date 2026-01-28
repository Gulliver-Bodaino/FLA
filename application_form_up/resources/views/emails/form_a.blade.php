FLAネットワーク協会 食生活アドバイザー検定事務局です。
お申込みありがとうございました。

今回のお申込みは第55回食生活アドバイザー検定試験に関するお申込みです。

下記お申込み内容となります。

なお本メールは自動返信メール専用となっており、返信はできません。


---------------------------------------------------------------------------
[ご質問]
過去に食生活アドバイザーの願書請求をしたことがありますか？
{{ $answer1 }}

過去に食生活アドバイザーの受験をしたことがありますか？
{{ $answer2 }}

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

[職業]
{{ $job }}

[検定試験]
@if ($exam_id)
{{ $exam_name }}
{{ number_format($exam_price) }}円
{{ $exam_venue_name }}
@endif

[通学コース]
@if ($normal)
2級講座
{{ number_format($normal_price) }}円
{{ $normal_venue_city_name }}
{{ $normal_venue_name }}
{{ $normal_venue_schedule }}
@endif

[速習コース]
@if ($fast)
{{ $fast_course_name }}
{{ number_format($fast_course_price) }}円
@foreach ($fast_venue_list as $fast_venue)
・{{ $fast_venue->city_name }}　{{ $fast_venue->name }}　{{ $fast_venue->schedule }}
@endforeach
@endif

[科目別 過去問題集]
@if ($workbook_id)
{{ $workbook_name }}
{{ number_format($workbook_price) }}円
@endif

[お支払い合計]
{{ number_format($total) }}円
---------------------------------------------------------------------------