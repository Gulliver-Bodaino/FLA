(function ($) {
  /* ------------------------------- */
  $(function(){
    load_event();
    page_top();
    mobile();
		lightbox();
  });
})(jQuery);

/*image rollover*/
var load_event = function(){
  $('a>img[src*="-out-"],input[src*="-out-"]').each(function(){
    var $$ = $(this);
    $$.mouseover(function(){ $(this).attr('src', $(this).attr('src').replace(/-out-/,'-on-')) });
    $$.mouseout (function(){
      if ( $(this).attr('wws') != 'current' ) { $(this).attr('src', $(this).attr('src').replace(/-on-/,'-out-')) }
    });
  });

  $('a[subwin]').die('click').click(subwin_func);
  $("img.btnover").hover(function(){$(this).stop().fadeTo("fast", 0.5);},function(){$(this).stop().fadeTo("fast", 1);});
  /*$("#aaa").autoHeight({column:1, clear:1});*/
  $(".link_box").click(function(){
         if($(this).find("a").attr("target")=="_blank"){
             window.open($(this).find("a").attr("href"), '_blank');
         }else{
             window.location=$(this).find("a").attr("href");
         }
     return false;
  });
}

/*sub window*/
var subwin_func = function () {
  var $$ = $(this);
  var param = $$.attr('subwin').split(/\D+/);
  var w = param[0] || 300;
  var h = param[1] || 300;
  var s = ($$.attr('subwin').match(/slim/))?'no':'yes';
  var r = ($$.attr('subwin').match(/fix/) )?'no':'yes';
  var t = $$.attr('target') || '_blank' ;
  window.open( $$.attr('href'), t, "resizable="+r+",scrollbars="+s+",width="+w+",height="+h ).focus();
  return false;
}


var page_top = function () {$(document).ready(function(){var topBtn = $('#pagetop'); topBtn.hide();$(window).scroll(function () {if ($(this).scrollTop() > 300) {topBtn.fadeIn();} else {topBtn.fadeOut();}});});}


var mobile = function(){
var ua = navigator.userAgent;
if(ua.indexOf('iPhone') != -1 || ua.indexOf('iPad') != -1 || ua.indexOf('Android') != -1){
/*iPhone & iPad start */

/*iPhone & iPad end */
}

/* リサイズ処理 */
$(window).on('load resize', function(){
// 処理を記載
});
}

  var lightbox = function(){
    $(".colorbox").colorbox();

    var w = $(window).width();
    if(w < 768) {
    		//SP判定
        $(".inlinebox").colorbox({
          iframe:true,
          width:"80%",
          scrolling:true,
          opacity: 0.9,
          close:'閉じる',
          /*rel:'group'*/
        });
    }else{
    		//PC判定
        $(".inlinebox").colorbox({
          iframe:true,
          width:980,
          scrolling:true,
          opacity: 0.9,
          close:'閉じる',
          /*rel:'group'*/
        });
    }
  }