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
  <p id="application_form_logo"><img src="{{ asset('images/common/logo.gif') }}"></p>
  <!--navi end-->
  <div id="main-vis"></div>
  <!--contents start--> 
  <a name="content_top" id="content_top"></a> 
  <!--contents start-->
  <div id="contents"> 
    <!--main start-->
    <form id="application_form" action="{{ route('form_c.send') }}" method="POST">
      @method('POST')
      @csrf
@foreach (request()->except(['_method', '_token']) as $key => $value)
  @if (is_array($value))
    @foreach ($value as $val)
      <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
    @endforeach
  @else
    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
  @endif
@endforeach
<input type="hidden" name="action" value="">

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
                <td><dl class="form_in_box">
                  @if (request('member') == 1)
                    <dt>
                      <lable>食生活アドバイザー会員</lable>
                    </dt>
                    <dd>会員番号または登録番号：{{ request('member_number') }}</dd>
                  @endif
                  @if (request('member') == 0)
                    <dt>
                      <lable>非会員</lable>
                    </dt>
                    @endif
                  </dl></td>
              </tr>
            </tbody>
          </table>
          <table class="main_table">
            <tbody>
              <tr>
                <th>氏名<span class="hissu">（必須）</span></th>
                <td>
                  {{ request('sei') }}
                  {{ request('mei') }}
                </td>
              </tr>
              <tr>
                <th>フリガナ<span class="hissu">（必須）</span></th>
                <td>
                  {{ request('sei_kana') }}
                  {{ request('mei_kana') }}
                </td>
              </tr>
              <tr>
                <th>生年月日<span class="hissu">（必須）</span></th>
                <td>
                  {{ request('birthday_year') }}年
                  {{ request('birthday_month') }}月
                  {{ request('birthday_day') }}日
                </td>
              </tr>
              <tr>
                <th>性別<span class="hissu">（必須）</span></th>
                <td>
                  {{ array_flip(config('common.gender'))[request('gender')] ?? '' }}
                </td>
              </tr>
              <tr>
                <th>住所<span class="hissu">（必須）</span></th>
                <td>
                  <dl class="form_in_box">
                    <dd>〒 {{ request('zip1') }}-{{ request('zip2') }}</dd>
                    <dd>{{ request('pref') }}</dd>
                    <dd>{{ request('city') }}</dd>
                    <dd>{{ request('address1') }}</dd>
                    <dd>{{ request('address2') }}</dd>
                  </dl>
                  <p class="m_b10">勤務先・部署名</p>
                  <dl class="form_in_box">
                    <dd>{{ request('workplace') }}</dd>
                    <dd>{{ request('department') }}</dd>
                  </dl>
                </td>
              </tr>
              <tr>
                <th>電話番号<span class="hissu">（必須）</span></th>
                <td>
                  {{ request('tel') }}
                </td>
              </tr>
              <tr>
                <th>メールアドレス</th>
                <td>
                  {{ request('mailaddress') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
      <!--box end--> 
      
      <!--box start-->
      <article id="pass" class="box">
        <div class="application_form_cont"> 
          <!--selection_box start-->
          <div class="selection_box">
            <p class="m_b15 align_c">下記のご希望の項目にチェックを入れください。</p>
            <h3 class="selection_box_tit">食アド会員関連</h3>
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <p class="selection_list_tit">食アド会員　年会費</p>
              <ul class="selection_list layout_box3">
                @foreach ($setting->member_fee_list as $member_fee)
                <li>
                  <label>
                  <p class="selection_detal">{{ $member_fee->name }}<br>{{ number_format($member_fee->price) }}円（税込）</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="http://www.flanet.jp/kaiin/" target="_blank">詳細はこちらをご確認ください</a></p>
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
                  <p class="selection_detal">{{ $shop_fee->name }}<br>{{ number_format($shop_fee->price) }}円（税込）</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16"><a href="http://www.flanet.jp/event/index.html" target="_blank">食アドのお店について</a></p>
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
                  <p class="selection_detal">{{ $seminar_venue->name }}<br>{{ $seminar_venue->price_label }}</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="http://www.flanet.jp/academy/" target="_blank">詳細はこちらをご確認ください</a></p>
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
                  <p class="selection_detal">{{ $academy_course->name }}<br>{{ $academy_course->price_label }}</p>
                  </label>
                </li>
                @endforeach
              </ul>
              <p class="align_c txt16 other_link"><a href="http://www.flanet.jp/academy/" target="_blank">詳細はこちらをご確認ください</a></p>
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
          レ 個人情報の取扱いについて同意する
        </p>
      </article>
      <!--box end--> 
      
      <!--box start-->
      <article id="total" class="box">
        <div class="subtotal_box">お支払い合計：<span class="subtotal_box_price"><span id="pay_total">{{ number_format($total) }}</span>円</span></div>
      </article>
      <!--box end--> 

      <!--box start-->
      <article id="total" class="box">
        <div class="total_btn align_c"><button type="button" class="link_btn" value="correct" style="background-color: #666;">入力内容を修正する</button></div>
      </article>
      <article id="total" class="box">
        <div class="total_btn align_c"><button type="button" class="link_btn" value="send">送信する</button></div>
      </article>
      <!--box end--> 
      
    </form>
    <!--main end--> 
  </div>
  <!--contents end--> 
  
  <!-- footer start --> 
  <!-- <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>  -->
  <!-- footer end -->
  <script>
    $('.link_btn').click(function(event) {
        $(':hidden[name=action]').val($(this).val());
        $(this).prop('disabled', true);
        $('#application_form').submit();
    });
  </script>
</div>
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641p"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641p" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641p" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
</body>
</html>
