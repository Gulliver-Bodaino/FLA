<!doctype html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1, user-scalable=0" />
<meta name="description" content="食の資格、食生活アドバイザー｜FLAネットワーク協会" />
<meta name="keywords" content="食,資格" />
<title>専用の払込取扱票請求フォーム（郵便局、ゆうちょ銀行の払込取扱票によるお支払い）｜食の資格、食生活アドバイザー®｜FLAネットワーク協会</title>
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
    <form id="application_form" action="{{ route('form_b.send') }}" method="POST">
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

      <h1 class="application_form_main_title">専用の払込取扱票請求フォーム<br>（郵便局、ゆうちょ銀行の払込取扱票によるお支払い）</h1>
      <!--box start-->
      <article id="person" class="box">
        <h2 class="application_form_tit">受験・合格講座・対策問題集申込フォーム</h2>
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
            </tbody>
          </table>
        </div>
      </article>
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
  <!-- <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script> -->
  <script>
    $('.link_btn').click(function(event) {
        $(':hidden[name=action]').val($(this).val());
        $(this).prop('disabled', true);
        $('#application_form').submit();
    });
  </script>
  <!-- footer end --> 
</div>
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641j"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641j" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641j" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
</body>
</html>
