<!doctype html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1, user-scalable=0" />
<meta name="description" content="食の資格、食生活アドバイザー｜FLAネットワーク協会" />
<meta name="keywords" content="食,資格" />
<title>クレジットカード決済エラー｜食の資格、食生活アドバイザー®｜FLAネットワーク協会</title>
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
    <form id="application_form" action="" method="POST">

      <h1 class="application_form_main_title">
        @if (Route::is('form_a.system_error'))クレジットカード支払い希望者の『申込フォーム』@endif
        @if (Route::is('form_c.system_error'))食アド<sup>®</sup>会員関連・イベント関連の申込フォーム@endif
      </h1>
      <!--box start-->
      <article id="person" class="box">
        <p style="text-align: center;">
          {{ session('error_message') }}<br>
        </p>
        
      </article>
      <div class="box" style="text-align: center;">
        <a href="https://flanet.jp/">トップページへ戻る</a>
      </div>
      <!--box end--> 
      
    </form>
    <!--main end--> 
  </div>
  <!--contents end--> 
  
  <!-- footer start --> 
  <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script> 
  <!-- footer end --> 
</div>
</body>
</html>
