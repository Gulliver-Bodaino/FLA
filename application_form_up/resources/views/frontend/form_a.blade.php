<!doctype html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1, user-scalable=0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="食の資格、食生活アドバイザー｜FLAネットワーク協会" />
<meta name="keywords" content="食,資格" />
<title>クレジットカード支払い希望者の『申込フォーム』｜食の資格、食生活アドバイザー®｜FLAネットワーク協会</title>
<link rel="stylesheet" type="text/css" href="{{ asset('common/css/import.css') }}" media="all" />
<script type="text/javascript" src="{{ asset('common/js/common.js') }}" charset="utf-8"></script>
</head>
<body>
<div id="wrapper" class="top"><a name="page_top" id="page_top"></a> 
  
  <!--navi start-->
  <p id="application_form_logo"><img src="{{ asset('images/common/logo.gif') }}"></p>
  <!--navi end-->
  <div id="main-vis"></div>
  <!--contents start--> 
  <a name="content_top" id="content_top"></a> 
  <!--contents start-->
  <div id="contents"> 
    <!--main start-->
    <form id="application_form" action="{{ route('form_a.credit') }}" method="POST">
      @method('POST')
      @csrf
      <h1 class="application_form_main_title">クレジットカード支払い希望者の『申込フォーム』</h1>
      <!--box start-->
      <article id="person" class="box">
        <h2 class="application_form_tit">申込フォーム</h2>
        <div class="application_form_cont m_b40">
          <h3 class="selection_box_tit">個人情報入力<span class="hissu">(必須)</span></h3>
          @if ($errors->any())<div class="error_box">入力エラーがあります。</div>@endif
          <table class="main_table">
            <tbody>
              <tr>
                <th>ご質問<span class="hissu">（必須）</span></th>
                <td>
                  <dl class="form_in_box">
                    <dt>過去に食生活アドバイザーの専用の払込取扱票請求をしたことがありますか？</dt>
                    <dd>
                      <lable><input type="radio" name="q1" value="1"@if(old('q1', '') == '1') checked @endif>はい</lable>
                      <lable><input type="radio" name="q1" value="0"@if(old('q1', '') == '0') checked @endif>いいえ</lable>
                      @error('q1')<div class="error_box">必須項目です。</div>@enderror
                    </dd>
                    <dt>過去に食生活アドバイザーの受験をしたことがありますか？</dt>
                    <dd>
                      <lable><input type="radio" name="q2" value="1"@if(old('q2', '') == '1') checked @endif>はい</lable>
                      <lable><input type="radio" name="q2" value="0"@if(old('q2', '') == '0') checked @endif>いいえ</lable>
                      @error('q2')<div class="error_box">必須項目です。</div>@enderror
                    </dd>
                  </dl>
                </td>
              </tr>
            </tbody>
          </table>
          <table class="main_table m_b60">
            <tbody>
              <tr>
                <th>氏名<span class="hissu">（必須）</span></th>
                <td>
                  <input name="sei" type="text" class="w30p" id="name01-01" placeholder="姓" value="{{ old('sei', '') }}">
                  <input name="mei" type="text" class="w30p" id="name01-02" placeholder="名" value="{{ old('mei', '') }}">
                  @error('sei')<div class="error_box">{{ $message }}</div>@enderror
                  @error('mei')<div class="error_box">{{ $message }}</div>@enderror
                </td>
              </tr>
              <tr>
                <th>フリガナ<span class="hissu">（必須）</span></th>
                <td>
                  <input name="sei_kana" type="text" class="w30p" id="name02-01" placeholder="セイ" value="{{ old('sei_kana', '') }}">
                  <input name="mei_kana" type="text" class="w30p" id="name02-02" placeholder="メイ" value="{{ old('mei_kana', '') }}">
                  @error('sei_kana')<div class="error_box">{{ $message }}</div>@enderror
                  @error('mei_kana')<div class="error_box">{{ $message }}</div>@enderror
                </td>
              </tr>
              <tr>
                <th>生年月日<span class="hissu">（必須）</span></th>
                <td>
                  {{ Form::select('birthday_year', ['' => '--'] + config('common.birthday.year'), old('birthday_year', ''), ['class' => 'w20p']) }}年
                  {{ Form::select('birthday_month', ['' => '--'] + config('common.birthday.month'), old('birthday_month', ''), ['class' => 'w20p']) }}月
                  {{ Form::select('birthday_day', ['' => '--'] + config('common.birthday.day'), old('birthday_day', ''), ['class' => 'w20p']) }}日
                  @error('birthday_year')<div class="error_box">{{ $message }}</div>@enderror
                  @error('birthday_month')<div class="error_box">{{ $message }}</div>@enderror
                  @error('birthday_day')<div class="error_box">{{ $message }}</div>@enderror
                </td>
              </tr>
              <tr>
                <th>性別<span class="hissu">（必須）</span></th>
                <td>
                  @foreach (array_flip(config('common.gender')) as $id => $name)
                  <input type="radio" name="gender" value="{{ $id }}" id="gender{{ $id }}"@if (old('gender', '') == $id) checked @endif>
                  <label for="gender{{ $id }}">{{ $name }}</label>
                  @endforeach
                  @error('gender')<div class="error_box">{{ $message }}</div>@enderror
                </td>
              </tr>
              <tr>
                <th>住所<span class="hissu">（必須）</span></th>
                <td>
                  <dl class="form_in_box">
                    <dd>〒 <input name="zip1" value="{{ old('zip1', '') }}" type="text" size="8" id="zip1" class="w10p" maxlength="3">-<input name="zip2" class="w10p" value="{{ old('zip2', '') }}" type="text" id="zip2" maxlength="4"onkeyup="AjaxZip3.zip2addr('zip1','zip2','pref','city','address1');"></dd>
                    <dd><input name="pref" type="text" class="w95p" id="pref" placeholder="例：東京都" value="{{ old('pref', '') }}"></dd>
                    <dd><input name="city" type="text" class="w95p" id="city" placeholder="例：新宿区" value="{{ old('city', '') }}"></dd>
                    <dd><input name="address1" type="text" class="w95p" id="address1" placeholder="例：西新宿７−１５−１０" value="{{ old('address1', '') }}"></dd>
                    <dd><input name="address2" type="text" class="w95p" id="address2" placeholder="例：大山ビル２階" value="{{ old('address2', '') }}"></dd>
                  </dl>
                  @error('zip1')<div class="error_box">{{ $message }}</div>@enderror
                  @error('zip2')<div class="error_box">{{ $message }}</div>@enderror
                  @error('city')<div class="error_box">{{ $message }}</div>@enderror
                  @error('pref')<div class="error_box">{{ $message }}</div>@enderror
                  @error('address1')<div class="error_box">{{ $message }}</div>@enderror
                  <p class="m_b10">会社宛てにご送付希望の方は勤務先・部署名のご入力も忘れずにお願いします。</p>
                  <dl class="form_in_box">
                    <dd><input name="workplace" type="text" class="w95p" id="workplace" placeholder="例：ＦＬＡネットワーク協会" value="{{ old('workplace', '') }}"></dd>
                    <dd><input name="department" type="text" class="w95p" id="department" placeholder="例：検定事務局" value="{{ old('department', '') }}"></dd>
                  </dl>
                </td>
              </tr>
              <tr>
                <th>電話番号<span class="hissu">（必須）</span></th>
                <td>
                  <input name="tel" type="text" class="w95p" id="tel" placeholder="例：0120-86-3593" value="{{ old('tel', '') }}">
                  @error('tel')<div class="error_box">{{ $message }}</div>@enderror
                </td>
              </tr>
              <tr>
                <th>メールアドレス</th>
                <td>
                  <input name="mailaddress" type="text" class="w95p" id="mailaddress" placeholder="例：taro@flanet.jp" value="{{ old('mailaddress', '') }}">
                  @error('mailaddress')<div class="error_box">{{ $message }}</div>@enderror
                  <p>メール受信制限をされている方はnoreply@flanet.jpからのメール受信を許可してください。</p>
                </td>
              </tr>
              <tr>
                <th>職業</th>
                <td>
                  <ul class="layout_box3">
                  @foreach (array_flip(config('common.job')) as $id => $name)
                    <li>
                      <label><input type="radio" name="job" value="{{ $id }}"@if (old('job', '') == $id) checked @endif>{{ $name }}</label>
                    </li>
                  @endforeach
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="selection_box"> 
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <p class="align_c m_b35">下記のご希望の項目にチェックを入れてください。</p>
              <h3 class="selection_box_tit">検定試験の申込</h3>
              <ul class="selection_list layout_box3">
                @foreach ($setting->exam_list as $exam)
                <li>
                  <label>
                  <input type="radio" name="exam_id" class="cal" value="{{ $exam->id }}"@if(old('exam_id', '') == $exam->id) checked @endif>
                  <p class="selection_detal">{{ $exam->name }}<br>{{ number_format($exam->price) }} 円</p>
                  </label>
                </li>
                @endforeach
              </ul>
            </div>
            <!--selection_list_box end--> 
          </div>
          <div class="selection_box"> 
            <p class="selection_list_main_tit">受験会場選択</p>
            <p class="m_b15 align_c">※検定試験の申込にチェックを入れた場合、受験会場の選択が必須となります。</p>
            @error('exam_venue_id')<div class="error_box">{{ $message }}</div>@enderror
            <ul class="selection_list layout_box4">
              @foreach ($setting->exam_venue_list as $exam_venue)
              <li>
                <label>
                <input type="radio" name="exam_venue_id" class="exam" value="{{ $exam_venue->id }}"@if(old('exam_venue_id', '') == $exam_venue->id) checked @endif>
                <p class="selection_detal">{{ $exam_venue->name }}</p>
                </label>
              </li>
              @endforeach
            </ul>
          </div>
          
        </div>
      </article>
      <!--box end--> 
      
      <!--box start-->
      @if ($setting->normal_enabled || $setting->fast_enabled)
      <article id="pass" class="box">
        <h2 class="application_form_tit">合格講座@if($setting->normal_enabled)『通学コース』@endif @if($setting->fast_enabled)『速習コース』@endifのお申し込み</h2>
        <div class="application_form_cont"> 
          <!--selection_box start-->
          @if ($setting->normal_enabled)
          <div class="selection_box"> 
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <h3 class="selection_box_tit">合格講座：『通学コース』 受講申込（2日間でじっくり学ぶコースです）</h3>
              <ul class="selection_list">
                <li>
                  <label>
                  <input type="radio" name="normal" class="cal" value="1"@if (old('normal', '') == 1) checked @endif>
                  <p class="selection_detal">2級講座<br>{{ number_format($setting->normal_price) }} 円</p>
                  </label>
                </li>
              </ul>
            </div>
            <!--selection_list_box end--> 
            
            <!--selection_list_box start-->
            <div class="selection_list_box">
             <p class="selection_list_main_tit">通学コース受講会場</p>
              <p class="align_c m_b15">※通学コースを申込む場合、会場の選択が必須となります。</p>
              @error('normal_venue_id')<div class="error_box">{{ $message }}</div>@enderror
              <ul class="selection_list layout_box3">
              @foreach ($setting->normal_venue_list as $normal_venue)
                <li>
                  <label>
                  <input type="radio" name="normal_venue_id" class="normal" value="{{ $normal_venue->id }}"@if (old('normal_venue_id', '') == $normal_venue->id) checked @endif>
                  <p class="selection_detal">{{ $normal_venue->city_name }}<br>{{ $normal_venue->name }}<br>{{ $normal_venue->schedule }}</p>
                  </label>
                </li>
              @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="http://www.flanet.jp/pass/" target="_blank">合格講座─『通学コース』について</a></p>
            </div>
            <!--selection_list_box end--> 
          </div>
          @endif
          <!--selection_box start--> 
          
          <!--selection_box start-->
          @if ($setting->fast_enabled)
          <div class="selection_box"> 
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <h3 class="selection_box_tit">合格講座：『速習コース』 受講申込（1日で完結する集中コースです）</h3>
              <ul class="selection_list">
                <li>
                  <label>
                  <input type="radio" name="fast" class="cal" value="1"@if (old('fast', '') == 1) checked @endif>
                  <p class="selection_detal">合格講座：<br>『速習コース』 受講申込</p>
                  </label>
                </li>
              </ul>
            </div>
            <!--selection_list_box end--> 
            
            <!--selection_list_box start-->
            <div class="selection_list_box">
             <p class="selection_list_main_tit">講座一覧</p>
              <p class="align_c m_b15">※速習コースを申込む場合、講座の選択が必須となります。</p>
              @error('fast_course_id')<div class="error_box">{{ $message }}</div>@enderror
              <ul class="selection_list layout_box3">
              @foreach ($setting->fast_course_list as $fast_course)
                <li>
                  <label>
                  <input type="radio" name="fast_course_id" class="fast cal" value="{{ $fast_course->id }}"@if (old('fast_course_id', '') == $fast_course->id) checked @endif>
                  <p class="selection_detal">{{ $fast_course->name }}<br>{{ number_format($fast_course->price) }} 円</p>
                  </label>
                </li>
              @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="http://www.flanet.jp/pass/" target="_blank">合格講座─『速習コース』について</a></p>
            </div>
            <!--selection_list_box end--> 
          </div>
          <!--selection_box start--> 
          
          <!--selection_box start-->
          <div class="selection_box">
            <p class="selection_list_main_tit">速習コース受講会場</p>
            <p class="m_b15 align_c">※3級・2級講座は受講日が2日間になります。下記会場より必ず2日間ご選択ください。</p>
            @error('fast_venue_id')<div class="error_box">{{ $message }}</div>@enderror
            <!--selection_list_box start-->
            @php
              $old_key = '';
            @endphp
            @foreach ($setting->fast_venue_list as $key => $fast_venue_list)
              @if ($old_key != $key)
              @if (!$loop->first)</ul></div>@endif
              <div class="selection_list_box">
                <p class="selection_list_tit">
                  @php
                    $pieces = explode('_', $key);
                    $city_name  = $pieces[1] ?? '';
                    $venue_name = $pieces[2] ?? '';
                  @endphp
                  【{{ $city_name }}】{{ $venue_name }}
                </p>
                <ul class="selection_list layout_box4">
              @endif
                @foreach ($fast_venue_list as $fast_venue)
                  <li>
                    <label>
                    <input type="checkbox" name="fast_venue_id[]" class="fast" value="{{ $fast_venue->id }}"@if (in_array($fast_venue->id, old('fast_venue_id', []))) checked @endif>
                    <p class="selection_detal">{{ $fast_venue->schedule }}</p>
                    </label>
                  </li>
                @endforeach
              @if ($loop->last)</ul></div>@endif
            @endforeach

            <!--selection_list_box end--> 
            <div class="attention">※ご希望の会場にチェックを入れてください。<br>※いずれも1日で完結します。<br>※同じ日に２級・3級を同時に受講することはできません。</div>
            <div class="subtotal_box">小計：<span class="subtotal_box_price"><span id="subtotal1">{{ number_format($subtotal1) }}</span>円</span></div>
          </div>
          @endif
          <!--selection_box start--> 
          
        </div>
      </article>
      @endif
      <!--box end--> 
      
      <!--box start-->
      @if (count($setting->workbook_list) > 0)
      <article id="subject" class="box">
        <h2 class="application_form_tit">『対策問題集』ご購入のお申し込み</h2>
        <div class="application_form_cont"> 
          <!--selection_box start-->
          <div class="selection_box">
            <h3 class="selection_box_tit">対策問題集 購入申込</h3>
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <ul class="selection_list layout_box3">
              @foreach ($setting->workbook_list as $workbook)
                <li>
                  <label>
                  <input type="radio" name="workbook_id" class="cal" value="{{ $workbook->id }}"@if (old('workbook_id', '') == $workbook->id) checked @endif>
                  <p class="selection_detal">{{ $workbook->name }}<br>{{ number_format($workbook->price) }} 円</p>
                  </label>
                </li>
              @endforeach
              </ul>
            </div>
            <!--selection_box start-->
            <p class="align_c txt16"><a href="http://www.flanet.jp/shoseki/past.html" target="_blank">『対策問題集』について</a></p>
            <div class="subtotal_box">小計：<span class="subtotal_box_price"><span id="subtotal2">{{ number_format($subtotal2) }}</span>円</span></div>
          </div>
          <!--selection_box start--> 
        </div>
      </article>
      @endif
      <!--box end--> 
      
      <!--box start-->
      <article id="privacy" class="box">
        @include('frontend.privacy_ab')
        <p class="privacy_link">
          <input name="agree" type="checkbox" value="1"@if (old('agree', '') == 1) checked @endif>
          個人情報の取扱いについて同意する
          @error('agree')<div class="error_box">{{ $message }}</div>@enderror
        </p>
      </article>
      <!--box end-->
      
      <!--box start-->
      <article id="total" class="box">
        <div class="subtotal_box">お支払い合計：<span class="subtotal_box_price"><span id="pay_total">{{ number_format($total) }}</span>円</span></div>
        <div class="total_btn align_c"><button type="submit" class="link_btn">クレジットカード入力画面へ</button></div>
      </article>
      <!--box end--> 
      
    </form>
    <!--main end--> 
  </div>
  <!--contents end--> 
  
  <!-- footer start --> 
  <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script> 
  <!-- footer end --> 
  <script>
    if ($('input[name=exam_id]:checked').length > 0) {
      $('.exam').prop('disabled', false);
    } else {
      $('.exam').prop('disabled', true);
    }
    if ($('input[name=normal]').prop('checked')) {
      $('.normal').prop('disabled', false);
    } else {
      $('.normal').prop('disabled', true);
    }
    if ($('input[name=fast]').prop('checked')) {
      $('.fast').prop('disabled', false);
    } else {
      $('.fast').prop('disabled', true);
    }

    var checked;
    $('.cal').mouseup(function(event) {
      checked = $(this).prop('checked') ? false : true;
    });
    $('.cal').click(function(event) {
      const url = '{{ route('form_a.calculate') }}';
      const fd = new FormData($('form#application_form').get(0));

      if (checked) {
        // ON
        if ($(this).attr('name') == 'exam_id') $('.exam').prop('disabled', false);
        if ($(this).attr('name') == 'normal') $('.normal').prop('disabled', false);
        if ($(this).attr('name') == 'fast') $('.fast').prop('disabled', false);
      } else {
        // OFF
        if ($(this).attr('name') == 'exam_id') $('.exam').prop('disabled', true);
        if ($(this).attr('name') == 'normal') $('.normal').prop('disabled', true);
        if ($(this).attr('name') == 'fast') $('.fast').prop('disabled', true);
        fd.set($(this).attr('name'), '');
      }

      $.ajax({
          type: 'POST',
          url: url,
          processData: false,
          contentType: false,
          data: fd,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      }).done(function(data, textStatus, jqXHR) {
        $('#subtotal1').text(data.subtotal1);
        $('#subtotal2').text(data.subtotal2);
        $('#pay_total').text(data.total);
      }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('料金の計算に失敗しました。');
      }).always(function(jqXHR, textStatus, errorThrown) {

      });
    });


</script>
</div>
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641l"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641l" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641l" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
</body>
</html>
