(function ($) {
  $(function(){
    lightbox();/*jquery.colorboxの設定*/
    slider();/*slickの設定*/
  });
  var lightbox = function(){
    $(".colorbox").colorbox();

    var w = $(window).width();
    if(w < 768) {
    		//SP判定
        $(".inlinebox").colorbox({
          inline:true,
          width:"80%",
          scrolling:true,
          opacity: 0.9,
          close:'閉じる',
          /*rel:'group'*/
        });
    }else{
    		//PC判定
        $(".inlinebox").colorbox({
          inline:true,
          width:980,
          scrolling:true,
          opacity: 0.9,
          close:'閉じる',
          /*rel:'group'*/
        });
    }
  }
  var slider = function(){
    $('.main-slider').slick({
         infinite: true,
         slidesToShow: 1,
         slidesToScroll: 1,
			fade: true,
			arrows: true,
         dots: false,
         autoplay:true,
         autoplaySpeed:5000,
    });
    $('.slider_box3, .slider3').slick({
         infinite: true,
         slidesToShow: 3,
         slidesToScroll: 1,
         arrows: true,
         dots: true,
         autoplay:true,
         autoplaySpeed:5000,
         responsive: [{
              breakpoint: 768,
                settings: {
                   slidesToShow: 2
                 }
             },{
         breakpoint: 480,
               settings: {
                  slidesToShow: 1
               }
            }
         ]
    });
    $('.slider_box4, .slider4').slick({
         infinite: true,
         slidesToShow: 4,
         slidesToScroll: 1,
         arrows: true,
         dots: false,
         autoplay:false,
         autoplaySpeed:5000,
         responsive: [{
              breakpoint: 768,
                settings: {
                   slidesToShow: 2
                 }
             },{
         breakpoint: 480,
               settings: {
                  slidesToShow: 1
               }
            }
         ]
    });
    $('.thumb-slider_main').slick({
         infinite: true,
         slidesToShow: 1,
         slidesToScroll: 1,
         arrows: false,
         fade: true,
         adaptiveHeight: true,
         asNavFor: '.thumb-slider_thumb' //サムネイルのクラス名
    });
    $('.thumb-slider_thumb').slick({
         infinite: true,
         slidesToShow: 5,
         slidesToScroll: 1,
     arrows: false,
         asNavFor: '.thumb-slider_main', //スライダー本体のクラス名
         focusOnSelect: true,
     draggable: true,
         adaptiveHeight: true,
     //vertical: true,
     responsive: [{
       breakpoint: 768,
         settings: {
           vertical:false
         }
     }]
    });
  }
})(jQuery);
