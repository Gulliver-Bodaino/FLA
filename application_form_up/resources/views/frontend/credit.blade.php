<!doctype html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1, user-scalable=0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="" />
<meta name="keywords" content="" />
<title>お支払い情報入力｜食の資格、食生活アドバイザー®｜FLAネットワーク協会</title>
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
    <form id="application_form" action="{{ $action }}" method="POST">
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
<input type="hidden" name="merchant_id" id="merchant_id" value="{{ config('common.sbps.merchant_id') }}">
<input type="hidden" name="service_id" id="service_id" value="{{ config('common.sbps.service_id') }}">
<input type="hidden" name="token" id="token" value="">
<input type="hidden" name="tokenKey" id="tokenKey" value="">
<input type="hidden" name="cardBrandCode" id="cardBrandCode" value="">
<input type="hidden" name="tds2infoToken" id="tds2infoToken" value="">
<input type="hidden" name="tds2infoTokenKey" id="tds2infoTokenKey" value="">
      <h1 class="application_form_main_title">お支払い情報入力</h1>
      <!--box start-->
      @if ($errors->has('token') || $errors->has('tokenKey'))
        <div class="error_box">クレジットカード決済代行会社との通信で問題が発生しています。</div>
      @endif
      @if ($errors->has('res_sps_transaction_id') || $errors->has('res_tracking_id'))
        <div class="error_box">クレジットカード情報の入力に誤りがあります。（カード番号を間違えている、有効期限を間違ている、セキュリティコードを間違えている等）</div>
      @endif
      <div id="credit_error" class="error_box"></div>
      <article id="credit" class="box">
        <div class="application_form_cont ">
          <h3 class="selection_box_tit">お支払情報<span class="hissu">(必須)</span></h3>
          <table class="main_table">
            <tbody>
              <tr>
                <th>カード名義<span class="hissu">（必須）</span></th>
                <td>
                  名 <input name="first_name" type="text" class="w40p" id="first_name" placeholder="例：TARO" value="{{ request('first_name') }}">
                  姓 <input name="last_name" type="text" class="w40p" id="last_name" placeholder="例：YAMADA" value="{{ request('last_name') }}">
                  <p>※ 半角英字で入力してください。</p>
                  <!-- <div class="error_box">必須項目です。</div> -->
                </td>
              </tr>
              <tr>
                <th>カード番号<span class="hissu">（必須）</span></th>
                <td>
                  <input name="cc_number" type="text" class="w95p" id="cc_number" placeholder="例：カード番号" maxlength="16" value="{{ request('cc_number') }}">
                  <!-- <div class="error_box">必須項目です。</div> -->
                </td>
              </tr>
              <tr>
                <th>有効期限<span class="hissu">（必須）</span></th>
                <td>
                  {{ Form::select('cc_month', ['' => '--'] + config('common.credit_card.month'), request('cc_month'), ['id' => 'cc_month', 'class' => 'w20p']) }}月
                  {{ Form::select('cc_year', ['' => '--'] + config('common.credit_card.year'), request('cc_year'), ['id' => 'cc_year', 'class' => 'w20p']) }}年
                  <!-- <div class="error_box">必須項目です。</div> -->
                </td>
              </tr>
              <tr>
                <th>セキュリティコード<span class="hissu">（必須）</span></th>
                <td>
                  <input name="security_code" type="text" class="w95p" id="security_code" placeholder="例：セキュリティ番号" maxlength="4" value="{{ request('security_code') }}">
                  <!-- <div class="error_box">必須項目です。</div> -->
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
      <!--box end--> 
      
      <!--box start-->
      <article id="total" class="box">
        <div class="total_btn align_c"><button type="button" id="confirm" class="link_btn" onclick="doSubmit();">内容確認へ</button></div>
      </article>
      <!--box end--> 
      
    </form>
    <!--main end--> 
  </div>
  <!--contents end--> 
  
  <!-- footer start --> 
  <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script> 
  <script src="{{ env('SBPS_SYSTEM_TOKEN_URL') }}"></script> 
  <script src="{{ env('SBPS_SYSTEM_TDS2INFO_TOKEN_URL') }}"></script>
  <!-- footer end --> 
  <script>
    $('#credit_error').hide();
    function doSubmit() {
      $('#confirm').prop('disabled', true);
      $('#credit_error').hide();

      let cc_expiration = '';
      if (document.getElementById('cc_year').value != '' && document.getElementById('cc_month').value != '') {
        cc_expiration = '20' + ('00' + document.getElementById('cc_year').value).slice(-2) + ('00' + document.getElementById('cc_month').value).slice(-2);
      }

      // ワンタイムトークン取得
      com_sbps_system.generateToken({
        "merchantId": document.getElementById('merchant_id').value,
        "serviceId": document.getElementById('service_id').value,
        "ccNumber": document.getElementById('cc_number').value,
        "ccExpiration": cc_expiration,
        "securityCode": document.getElementById('security_code').value
      }, afterGenerateToken);
      return false;
    }

    var afterGenerateToken = function(response) {
      if (response.result == "OK") {
        document.getElementById('token').value = response.tokenResponse.token;
        document.getElementById('tokenKey').value = response.tokenResponse.tokenKey;
        document.getElementById('cardBrandCode').value = response.tokenResponse.cardBrandCode;
        document.getElementById('cc_number').value = response.tokenResponse.maskedCcNumber;

        // カード利用者決済情報取得
        com_sbps_system_tds2.generateToken({
          "merchantId": document.getElementById('merchant_id').value,
          "serviceId": document.getElementById('service_id').value,
          "billingLastName": document.getElementById('last_name').value,
          "billingFirstName": document.getElementById('first_name').value,
          "billingPhone": document.getElementsByName('tel')[0].value
        }, afterGenerateTds2infoToken);

        return;
      } else {
        const errorCode = response.errorCode;
        const typeCode = errorCode.substr(0 ,2);
        const itemCode = errorCode.substr(2 ,3);
        let error_message = '';
        if (typeCode == '99') {
          error_message = 'クレジットカード決済代行会社でシステムエラーが発生しています。';
        } else if (itemCode == '003') {
          error_message = 'クレジットカード番号を正しく入力して下さい。';
        } else if (itemCode == '004') {
          error_message = 'クレジットカード有効期限を正しく選択して下さい。';
        } else if (itemCode == '005') {
          error_message = 'セキュリティーコードを正しく入力して下さい。';
        } else {
          error_message = 'クレジットカード決済代行会社との通信で問題が発生しています。';
        }
        $('#credit_error').text(error_message);
        $('#credit_error').show();
        $('#confirm').prop('disabled', false);
      }
    }

    var afterGenerateTds2infoToken = function(response) {
      if (response.result == "OK") {
        document.getElementById('tds2infoToken').value = response.tokenResponse.tds2infoToken;
        document.getElementById('tds2infoTokenKey').value = response.tokenResponse.tds2infoTokenKey;

        document.getElementById('first_name').value = '';
        document.getElementById('last_name').value = '';
        document.getElementById('cc_number').value = '';
        document.getElementById('cc_month').value = '';
        document.getElementById('cc_year').value = '';
        document.getElementById('security_code').value = '';

        document.getElementById('application_form').submit();

      } else {
        const errorCode = response.errorCode;
        const typeCode = errorCode.substr(0 ,2);
        const itemCode = errorCode.substr(2 ,3);
        let error_message = '';
        if (typeCode == '99') {
          error_message = 'クレジットカード決済代行会社でシステムエラーが発生しています。';
        /*
        } else if (itemCode == '003') {
          error_message = 'クレジットカード番号を正しく入力して下さい。';
        } else if (itemCode == '004') {
          error_message = 'クレジットカード有効期限を正しく選択して下さい。';
        } else if (itemCode == '005') {
          error_message = 'セキュリティーコードを正しく入力して下さい。';
        */
        } else {
          error_message = 'クレジットカード決済代行会社との通信で問題が発生しています。';
        }
        $('#credit_error').text(error_message);
        $('#credit_error').show();
        $('#confirm').prop('disabled', false);
      }
    }

  </script>
</div>
@if (Route::is('form_a.credit'))
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641v"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641v" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641v" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
@endif
@if (Route::is('form_c.credit'))
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641w"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641w" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641w" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
@endif
</body>
</html>
