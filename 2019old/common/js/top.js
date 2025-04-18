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
			pause: 4000,
			controls: false,
			infiniteLoop: true,	
			pager: false
	});
}

 