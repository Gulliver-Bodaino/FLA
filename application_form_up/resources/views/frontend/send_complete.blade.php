<!doctype html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1, user-scalable=0" />
<meta name="description" content="食の資格、食生活アドバイザー｜FLAネットワーク協会" />
<meta name="keywords" content="食,資格" />
<title>送信完了｜食の資格、食生活アドバイザー®｜FLAネットワーク協会</title>
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

      <h1 class="application_form_main_title">
        @if (Route::is('form_a.send_complete'))クレジットカード支払い希望者の『申込フォーム』@endif
        @if (Route::is('form_b.send_complete'))願書請求フォーム<br>（郵便局、ゆうちょ銀行の払込取扱票によるお支払い）@endif
        @if (Route::is('form_c.send_complete'))会員関連・イベント関連の申込フォーム@endif
      </h1>
      <!--box start-->
      <article id="person" class="box">
        <p style="text-align: center;">送信完了しました。</p>
        
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
@if (Route::is('form_a.send_complete'))
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641n"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641n" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641n" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
@endif
@if (Route::is('form_b.send_complete'))
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641k"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641k" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641k" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
@endif
@if (Route::is('form_c.send_complete'))
<!--shinobi1--><script type="text/javascript" src="//xa.shinobi.jp/ufo/19079641q"></script><noscript><a href="//xa.shinobi.jp/bin/gg?19079641q" target="_blank"><img src="//xa.shinobi.jp/bin/ll?19079641q" border="0"></a><br><span style="font-size:9px"><img style="margin:0;vertical-align:text-bottom;" src="//img.shinobi.jp/tadaima/fj.gif" width="19" height="11"> </span></noscript><!--shinobi2-->
@endif
</body>
</html>
