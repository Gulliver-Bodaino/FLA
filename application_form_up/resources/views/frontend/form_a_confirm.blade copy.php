<!doctype html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1, user-scalable=0" />
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
    <form id="application_form" action="{{ route('form_a.send') }}" method="POST">
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
                    <dt>過去に食生活アドバイザーの願書請求をしたことがありますか？</dt>
                    <dd>
                      {{ array_flip(config('common.answer'))[request('q1')] ?? '' }}
                    </dd>
                    <dt>過去に食生活アドバイザーの受験をしたことがありますか？</dt>
                    <dd>
                      {{ array_flip(config('common.answer'))[request('q2')] ?? '' }}
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
              <tr>
                <th>職業</th>
                <td>
                  {{ array_flip(config('common.job'))[request('job')] ?? '' }}
                </td>
              </tr>
            </tbody>
          </table>

          <div class="selection_box"> 
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <h3 class="selection_box_tit">検定試験の申込</h3>
              <ul class="selection_list layout_box3">
                @foreach ($setting->exam_list as $exam)
                <li>
                  <label>
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
            <ul class="selection_list layout_box4">
            @if (request('exam_id'))
              @foreach ($setting->exam_venue_list as $exam_venue)
              <li>
                <label>
                <p class="selection_detal">{{ $exam_venue->name }}</p>
                </label>
              </li>
              @endforeach
            @endif
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
                @if (request('normal') == 1)
                <li>
                  <label>
                  <p class="selection_detal">2級講座<br>{{ number_format($setting->normal_price) }} 円</p>
                  </label>
                </li>
                @endif
              </ul>
            </div>
            <!--selection_list_box end--> 
            
            <!--selection_list_box start-->
            <div class="selection_list_box">
             <p class="selection_list_main_tit">通学コース受講会場</p>
              <ul class="selection_list layout_box3">
              @if (request('normal') == 1)
              @foreach ($setting->normal_venue_list as $normal_venue)
                <li>
                  <label>
                  <p class="selection_detal">{{ $normal_venue->city_name }}<br>{{ $normal_venue->name }}<br>{{ $normal_venue->schedule }}</p>
                  </label>
                </li>
              @endforeach
              @endif
              </ul>
              <p class="align_c txt16 other_link"><a href="http://www.flanet.jp/pass/education.html" target="_blank">合格講座─『通学コース』について</a></p>
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
                @if (request('fast') == 1)
                <li>
                  <label>
                  <p class="selection_detal">合格講座：<br>『速習コース』 受講申込</p>
                  </label>
                </li>
                @endif
              </ul>
            </div>
            <!--selection_list_box end--> 
            
            <!--selection_list_box start-->
            <div class="selection_list_box">
             <p class="selection_list_main_tit">講座一覧</p>
              <ul class="selection_list layout_box3">
              @if (request('fast') == 1)
              @foreach ($setting->fast_course_list as $fast_course)
                <li>
                  <label>
                  <p class="selection_detal">{{ $fast_course->name }}<br>{{ number_format($fast_course->price) }} 円</p>
                  </label>
                </li>
              @endforeach
              @endif
              </ul>
              <p class="align_c txt16 other_link"><a href="http://www.flanet.jp/pass/" target="_blank">合格講座─『速習コース』について</a></p>
            </div>
            <!--selection_list_box end--> 
          </div>
          <!--selection_box start--> 
          
          <!--selection_box start-->
          <div class="selection_box">
            <p class="selection_list_main_tit">速習コース受験会場</p>
            <!--selection_list_box start-->
            @if (request('fast') == 1)
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
                    <p class="selection_detal">{{ $fast_venue->schedule }}</p>
                    </label>
                  </li>
                @endforeach
              @if ($loop->last)</ul></div>@endif
            @endforeach
            @endif
            <!--selection_list_box end--> 
            <div class="subtotal_box">小計：<span class="subtotal_box_price">{{ number_format($subtotal1) }}円</span></div>
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
        <h2 class="application_form_tit">『科目別 過去問題集』ご購入のお申し込み</h2>
        <div class="application_form_cont"> 
          <!--selection_box start-->
          <div class="selection_box">
            <h3 class="selection_box_tit">科目別 過去問題集 購入申込</h3>
            <!--selection_list_box start-->
            <div class="selection_list_box">
              <ul class="selection_list layout_box3">
              @foreach ($setting->workbook_list as $workbook)
                <li>
                  <label>
                  <p class="selection_detal">{{ $workbook->name }}<br>{{ number_format($workbook->price) }} 円</p>
                  </label>
                </li>
              @endforeach
              </ul>
            </div>
            <!--selection_box start-->
            <p class="align_c txt16"><a href="http://www.flanet.jp/shoseki/past.html" target="_blank">『科目別 過去問題集』について</a></p>
            <div class="subtotal_box">小計：<span class="subtotal_box_price">{{ number_format($subtotal2) }}円</span></div>
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
          レ 個人情報の取扱いについて同意する
        </p>
      </article>
      <!--box end--> 

      <!--box start-->
      <article id="total" class="box">
        <div class="subtotal_box">お支払い合計：<span class="subtotal_box_price">{{ number_format($total) }}円</span></div>
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
  <script>
    $('.link_btn').click(function(event) {
        $(':hidden[name=action]').val($(this).val());
        $(this).prop('disabled', true);
        $('#application_form').submit();
    });
  </script>
  <!-- footer end --> 
</div>
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641m"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641m" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641m" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
</body>
</html>
