(function ($) {
  /* ------------------------------- */
  $(function(){
    load_event();
	page_top();
	recipe_list();
	incude_fla();
  });
})(jQuery);

$(document).ready(function(e) {
$(window).resize();
        $('img[usemap]').rwdImageMaps();
      });

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
  $(".link_box").click(function(){
         if($(this).find("a").attr("target")=="_blank"){
             window.open($(this).find("a").attr("href"), '_blank');
         }else{
             window.location=$(this).find("a").attr("href");
         }
     return false;
  });
 $(".end_page_link li").autoHeight({column:4, clear:1});
  
  
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

var recipe_list = function(){
	$("#recipe_mat div dl dt").wrapInner("<span></span>");
	$("#recipe_mat div dl dd").after("<br />");
}

var incude_fla = function(){
	
	/* ヘッダー読み込み */
  $("#header_incbox").load("/include/header.inc", function() {
  
/* sp レシポンシブbtn start */
$(function($){
    var pull = $('#sp_navi_btn');  
    var menu = $('#sp_navi_box');  
    $(pull).on('click', function(e) { 
     $.when(
      $('#sp_navi_box').slideToggle()
      ).done(function(){ 
      $('#sp_navi_box').css('overflow', 'visible')
      });
    }); 
    
    $(window).resize(function(){  
        var w = $(window).width();
        // :hidden の代わりに css('display') で判定
        if(w < 641) {  
    	menu.attr('style', ''); 
    }  
    });
});
/* sp スマホtel end */
$(function(){
	var device = navigator.userAgent;
	if((device.indexOf('iPhone') > 0 && device.indexOf('iPad') == -1) || device.indexOf('iPod') > 0 || device.indexOf('Android') > 0){
		$(".tel_btn").wrap('<a href="tel:0120863593"></a>');
	}
});
/* sp スマホtel end */



	});
  
  
  $("#footer_incbox").load("/include/footer.inc");
  $("#side_incbox").load("/include/side.inc");
}

 