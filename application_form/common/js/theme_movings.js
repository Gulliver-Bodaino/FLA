(function ($) {
  $(function(){
    links();/*リンク関連の設定*/
    hovers();/*ホバー関連の設定*/
    clicks();/*クリック関連の設定*/
    scrolls();/*スクロール関連の設定*/
    animation();/*アニメーション関連の設定*/
    page_top();
  });
  function links(){
    /* $("セレクタ").link_box()もしくは
      $("セレクタのwrapper class > *").link_box()で
      リンクボックス化
      内包する<a>タグのリンク先を勝手に拾ってくる*/
    $(".js_link-box").link_box();
    $(".js_link-box_wrp > *").link_box();
  }
  function hovers(){
    /* $("セレクタ").mhover()もしくは
      $("セレクタのwrapperクラス > *").mhover()で
      ホバー時に光らせる
       $("セレクタ").mhover() = $.mhover_num(0.5,1)*/
    /* $("セレクタ").mhover_num(ホバー時の透明度,元の透明度)*/
    /* $("セレクタ").mhover_num_target(内包する光らせたい対象,ホバー時の透明度,元の透明度)*/
      $(".link-btn").mhover();
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

      /*例
      $("#mhn_target_exm1").mhover_num_target('dt',0.5,1);
      $("#mhn_target_exm2").mhover_num_target('dd',0.5,1);*/
  }
  function clicks(){
    /* <p>や<span>のオプションにhref="電話番号"がある場合、
      スマホ使用時電話番号のリンクがつく*/
    $('.js_tel-btn').tel_btn();
    
    /*クリックするとラジオ解除*/
const radioButtons = document.querySelectorAll('input[type="radio"]');
const clearRadioButton = (radioButton) => {
  setTimeout(func =()=>{
    radioButton.checked = false;
  },100)
}

radioButtons.forEach(radioButton => {
  let queryStr = 'label[for="' + radioButton.id + '"]'
  let label = document.querySelector(queryStr)

  radioButton.addEventListener("mouseup", func=()=>{
    if(radioButton.checked){
      clearRadioButton(radioButton)
    }
  });

  if(label){
    label.addEventListener("mouseup", func=()=>{
      if(radioButton.checked){
        clearRadioButton(radioButton)
      }
    });
  }

});
    
    
  }
  function scrolls(){
    /* 例）固定したいパーツまで進むと固定したいパーツがactiveになる
    var navTop;
    var winTop;
    $(window).on('load resize',function(){
      navTop=$('固定したいパーツ').offset().top;
    });

    $(window).on('scroll',function(){
      winTop=$(this).scrollTop();
      if(winTop>=navTop){ $('固定したいパーツ').addClass('active');}
      else{ $('固定したいパーツ').removeClass('active');}
    });*/
    var navTop;
    var winTop;
    $(window).on('load resize',function(){
      if($('.top #main').offset()!=undefined){
        navTop=parseInt($('.top #main').offset().top - 50);
      }else{
        navTop=0;
      }
    });

    $(window).on('load scroll',function(){
      winTop=$(this).scrollTop();
      if(winTop>=navTop){ $('#header').addClass('upper');}
      else{ $('#header').removeClass('upper');}
    });
  }
  function animation(){
    /*$('animationをつけたいセレクタのwrapperクラス').on('inview', function(event, isInView) {
      if (isInView) {$(this).find('animationをつけたいセレクタ').addClass('fadeInLeftBig');}
      else{$(this).find('animationをつけたいセレクタ').removeClass('fadeInLeftBig');}
    });*/
    /*inview.jsを使ったアニメーション例
    $('#animation-exam').on('inview', function(event, isInView) {
      if (isInView) {$(this).find('#animation-exam_inview').addClass('fadeInLeftBig');}
      else{$(this).find('#animation-exam_inview').removeClass('fadeInLeftBig');}
    });*/
    /*hoverを使ったアニメーション例
    $('#animation-exam').hover(function() {
        $(this).find('#animation-exam_hover').addClass('bounceIn');
      },function() {
        $(this).find('#animation-exam_hover').removeClass('bounceIn');
    });*/
  }

    var page_top = function () {
    $(document).ready(function(){
      var topBtn = $('.footer_pagetop');
      topBtn.hide();
      $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
          topBtn.fadeIn();
        } else {
          topBtn.fadeOut();
        }
      });
    });
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
