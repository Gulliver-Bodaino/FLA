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
<title>食アド®会員関連・イベント関連の申込フォーム｜食の資格、食生活アドバイザー®｜FLAネットワーク協会</title>
<link rel="stylesheet" type="text/css" href="{{ asset('common/css/import.css') }}" media="all" />
<script type="text/javascript" src="{{ asset('common/js/common.js') }}" charset="utf-8"></script>
</head>
<body>
<div id="wrapper" class="top"><a name="page_top" id="page_top"></a> 
  
  <!--navi start-->
  <p id="application_form_logo"><img src="./images/common/logo.gif"></p>
  <!--navi end-->
  <div id="main-vis"></div>
  <!--contents start--> 
  <a name="content_top" id="content_top"></a> 
  <!--contents start-->
  <div id="contents"> 
    <!--main start-->
    <form id="application_form" action="{{ route('form_c.credit') }}" method="POST">
      @method('POST')
      @csrf
      <input type="hidden" name="shoku-ad" value="">
      <h1 class="application_form_main_title">食アド<sup>®</sup>会員関連・イベント関連の申込フォーム</h1>
      <!--box start-->
      <article id="person" class="box">
        <h2 class="application_form_tit">申込フォーム</h2>
        <div class="application_form_cont m_b40">
          <h3 class="selection_box_tit">個人情報入力</h3>
          @if ($errors->any())<div class="error_box">入力エラーがあります。</div>@endif
          <table class="main_table">
            <tbody>
              <tr>
                <th>食生活アドバイザー会員<span class="hissu">（必須）</span></th>
                <td>
                  <dl class="form_in_box">
                    <dt>
                      <lable><input name="member" type="radio" value="1"@if(old('member', '') == '1') checked @endif>食生活アドバイザー会員</lable>
                      ：会員番号または登録番号をご記入ください。</dt>
                    <dd><input name="member_number" type="text" class="w95p" id="member_number" value="{{ old('member_number', '') }}" placeholder="会員番号または登録番号" value=""><br>※番号が不明の場合は未記入でも可</dd>
                    <dt>
                      <lable><input name="member" type="radio" value="0"@if(old('member', '') == '0') checked @endif>非会員</lable>
                    </dt>
                  </dl>
                  @error('member')<div class="error_box">{{ $message }}</div>@enderror
                </td>
              </tr>
            </tbody>
          </table>
          <table class="main_table">
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
            </tbody>
          </table>
        </div>
      </article>
      <!--box end--> 
      @error('shoku-ad')<div class="error_box">{{ $message }}</div>@enderror
      <!--box start-->
      <article id="pass" class="box">
        <div class="application_form_cont"> 
          <!--selection_box start-->
          <div class="selection_box">
            <p class="m_b15 align_c">下記のご希望の項目にチェックを入れください。</p>
            <p class="m_b15 align_c">※ 会員の方でゼミナールのみ申込みされる場合は「郵便局支払いへ」からお手続きください。</p>
            <h3 class="selection_box_tit">食アド会員関連</h3>
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <p class="selection_list_tit">食アド会員　年会費</p>
              <ul class="selection_list layout_box3">
                @foreach ($setting->member_fee_list as $member_fee)
                <li>
                  <label>
                  <input type="radio" name="member_fee_id" class="cal" value="{{ $member_fee->id }}"@if(old('member_fee_id', '') == $member_fee->id) checked @endif>
                  <p class="selection_detal">{{ $member_fee->name }}<br>{{ number_format($member_fee->price) }}円（税込）</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="https://shoku-ad.jp/member/" target="_blank">詳細はこちらをご確認ください</a></p>
              <div class="subtotal_box">小計：<span class="subtotal_box_price"><span id="subtotal_member">{{ number_format($subtotal_member) }}</span>円</span></div>
            </div>
            <!--selection_list_box end--> 
            
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <p class="selection_list_tit">食アドのお店 年会費</p>
              <ul class="selection_list layout_box3">
                @foreach ($setting->shop_fee_list as $shop_fee)
                <li>
                  <label>
                  <input type="radio" name="shop_fee_id" class="cal" value="{{ $shop_fee->id }}"@if(old('shop_fee_id', '') == $shop_fee->id) checked @endif>
                  <p class="selection_detal">{{ $shop_fee->name }}<br>{{ number_format($shop_fee->price) }}円（税込）</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16"><a href="https://shoku-ad.jp/store/" target="_blank">食アドのお店について</a></p>
              <div class="subtotal_box">小計：<span class="subtotal_box_price"><span id="subtotal_shop">{{ number_format($subtotal_shop) }}</span>円</span></div>
            </div>
            <!--selection_list_box end--> 
            
          </div>
          <!--selection_box start--> 
          
          <!--selection_box start-->
          @if ($setting->seminar_enabled)
          <div class="selection_box"> 
            
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <h3 class="selection_box_tit">イベント関連</h3>
              <p class="selection_list_tit">食アドゼミナール</p>
              <ul class="selection_list layout_box3">
                @foreach ($setting->seminar_venue_list as $seminar_venue)
                <li>
                  <label>
                  <input type="checkbox" name="seminar_venue_id[]" class="chk" value="{{ $seminar_venue->id }}"@if(in_array($seminar_venue->id, old('seminar_venue_id', []))) checked @endif>
                  <p class="selection_detal">{{ $seminar_venue->name }}<br>{{ $seminar_venue->price_label }}</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="https://shoku-ad.jp/seminar/" target="_blank">詳細はこちらをご確認ください</a></p>
            </div>
            <!--selection_list_box end-->
            <div class="subtotal_box">小計：<span class="subtotal_box_price"><span id="subtotal_seminar">{{ number_format($subtotal_seminar) }}</span>円</span></div>
          </div>
          @endif
          <!--selection_box start--> 
          
          <!--selection_box start-->
          @if ($setting->academy_enabled)
          <div class="selection_box"> 
            
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <h3 class="selection_box_tit">{{ $setting->academy_title }}</h3>
              <ul class="selection_list layout_box3">
                @foreach ($setting->academy_course_list as $academy_course)
                <li>
                  <label>
                  <input type="checkbox" name="academy_course_id[]" class="chk" value="{{ $academy_course->id }}"@if(in_array($academy_course->id, old('academy_course_id', []))) checked @endif>
                  <p class="selection_detal">{{ $academy_course->name }}<br>{{ $academy_course->price_label }}</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="https://shoku-ad.jp/academy/" target="_blank">詳細はこちらをご確認ください</a></p>
            </div>
            <!--selection_list_box end-->
            <div class="subtotal_box">小計：<span class="subtotal_box_price"><span id="subtotal_academy">{{ number_format($subtotal_academy) }}</span>円</span></div>
          </div>
          @endif
          <!--selection_box start--> 
          
        </div>
      </article>
      <!--box end--> 
      
      <!--box start-->
      <article id="privacy" class="box">
        @include('frontend.privacy_c')
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
        <div class="total_btn align_c">
          <button type="submit" class="link_btn">クレジットカード入力画面へ</button>
          <button type="button" class="link_btn" id="post_office_payment" data-action="{{ route('form_c.confirm') }}">郵便局 支払いへ</button>
          <!-- <a href="#" class="link_btn">クレジットカード入力画面へ</a>
          <a href="#" class="link_btn">郵便局 支払いへ</a> -->
        </div>
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
    $('button#post_office_payment').click(function(event) {
      $('form#application_form').attr('action', $(this).data('action'));
      $('form#application_form').submit();
    });

    var checked;
    $('.cal').mouseup(function(event) {
      checked = $(this).prop('checked') ? false : true;
    });
    $('.cal, .chk').click(function(event) {
      const url = '{{ route('form_c.calculate') }}';
      const fd = new FormData($('form#application_form').get(0));

      if ($(this).attr('class') == 'cal') {
        if (checked) {
//          alert($(this).attr('name') + '：ON');
        } else if ($(this).attr('class') == 'cal') {
//          alert($(this).attr('name') + '：OFF');
          fd.set($(this).attr('name'), '');
        }
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
        $('#subtotal_member').text(data.subtotal_member);
        $('#subtotal_shop').text(data.subtotal_shop);
        $('#subtotal_seminar').text(data.subtotal_seminar);
        $('#subtotal_academy').text(data.subtotal_academy);
        $('#pay_total').text(data.total);
      }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('料金の計算に失敗しました。');
      }).always(function(jqXHR, textStatus, errorThrown) {

      });
    });
  </script>
</div>
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641o"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641o" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641o" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
</body>
</html>
