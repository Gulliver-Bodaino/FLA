(function ($) {
  /* ------------------------------- */
  $(function(){
    load_event();
	page_top();
	recipe_list();
	incude_fla();
    hovers();/*ホバー関連の設定*/
	  links();
  });

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
  
  
  $("#footer_incbox").load("/include/footer_202511.inc");
  $("#side_incbox").load("/include/side.inc");
}

  function links(){
    /* $("セレクタ").link_box()もしくは
      $("セレクタのwrapper class > *").link_box()で
      リンクボックス化
      内包する<a>タグのリンク先を勝手に拾ってくる*/
    $(".js_link-box").link_box();
    $(".js_link-box_wrp > *").link_box();
  }
  function hovers(){
      $(".link_btn").mhover();
      $(".js_hvr-btn").mhover();
      $(".js_hvr-btn_wrp > *").mhover();


      $('.imgline_wrp .layout_line li').hover(
        function(){
          var idx = $(this).index();
          $('.imgline_wrp .layout_imgline li.active').removeClass('active');
          $('.imgline_wrp .layout_imgline li:nth-child('+parseInt(idx+1)+')').addClass('active');
        },
        function(){}
      );

  }
 

  $.fn.mhover =function(){
    $(this).mhover_num(0.5,1);
  }
  $.fn.mhover_num =function(num1,num2){
    var w;
    $(window).on('load resize', function(){w=$(window).width();});
    $(this).hover(
      function(){if(w>768){
        $(this).stop().fadeTo("fast",num1);}},
      function(){if(w>768){
        $(this).stop().fadeTo("fast",num2);}}
    );
  }
  $.fn.mhover_num_target =function(target,num1,num2){
    var w;
    $(window).on('load resize', function(){w=$(window).width();});
    $(this).hover(
      function(){if(w>768){
         var $target = $(this).find(target);
        $target.stop().fadeTo("fast",num1);}},
      function(){if(w>768){
        var $target = $(this).find(target);
        $target.stop().fadeTo("fast",num2);}}
    );
  }
  $.fn.link_box =function(){
    $(this).click(function(e){
           if($(this).find("a").attr("target")=="_blank"){
               window.open($(this).find("a").attr("href"), '_blank');
           }else{
               window.location=$(this).find("a").attr("href");
           }
       return false;
    });
  }
  $.fn.tel_btn =  function(){
    var device = navigator.userAgent;
    var tel;
    if((device.indexOf('iPhone') > 0 && device.indexOf('iPad') == -1) || device.indexOf('iPod') > 0 || device.indexOf('Android') > 0){
      tel = $(this).attr("href");
      $(this).wrap('<a href="tel:'+tel+'"></a>');
    }
  };
	
	
		
	
})(jQuery);