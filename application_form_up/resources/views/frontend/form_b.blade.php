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
    <form id="application_form" action="{{ route('form_b.confirm') }}" method="POST">
      @method('POST')
      @csrf
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
                  @error('pref')<div class="error_box">{{ $message }}</div>@enderror
                  @error('city')<div class="error_box">{{ $message }}</div>@enderror
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
        <div class="total_btn align_c"><button type="submit" class="link_btn">確認画面へ</button></div>
      </article>
      <!--box end--> 
      
    </form>
    <!--main end--> 
  </div>
  <!--contents end--> 
  
  <!-- footer start --> 
  <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script> 
  <!-- footer end --> 
</div>
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641i"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641i" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641i" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
</body>
</html>
