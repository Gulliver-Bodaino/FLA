(function ($) {
  /* ------------------------------- */
  $(function(){
    top_main();
  });
})(jQuery);

var top_main = function(){
	$(".bxslider").bxSlider({
			mode: "fade",
			auto: true,
			//pause:スライドしてから次のスライドまでの待ち時間の設定箇所。
			pause: 6000,
			controls: false,
			infiniteLoop: true,	
			pager: false
	});
}

 