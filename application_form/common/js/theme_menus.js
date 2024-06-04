(function ($) {
  $(function(){
    //accordion_menu($btn,$menu,$close,type,anime)
    //hover_menu($hover_btn,$hover_area)
    //hover_menu2(hover_btn,hover_area)
    //tab_menu_box($tab_btn,$tab_menu)
    //tab_menu_list($tab_btn,$tab_menu)
    //slide_menu($tab_btn,$tab_menu,$close,right)

    /*例*/
    var $ac_btn=$("#header .sp_navi-btn");
    var $ac_menu=$("#header .navi");
    var $ac_close=$("");
    accordion_menu($ac_btn,$ac_menu,$ac_close,'sp',
      function(){sp_menu($ac_btn, $ac_menu);}
    );

  /*Q&Aリストのjsここから
    $(".qa_list dt").each(function(i){
      var $qa_btn   = $(this);
      var $qa_menu  = $(this).next('dd');
      var $qa_close  = $("");
      accordion_menu($qa_btn,$qa_menu,$qa_close,'both','');
    });
    //Q&Aリストのjsここまで*/
  });

  function sp_menu($btn){
    if($btn.hasClass('active')){
      open_anime($btn);
			$("body").addClass("navi_active");
    }else{
      close_anime($btn);
			$("body").removeClass("navi_active");
    }
  }

  var open_anime =function($btn){
    $btn.find('span:nth-child(1)').animate({'top': '11px'});
    $btn.find('span:nth-child(3)').animate({'bottom': '11px'});
    $btn.find('span:nth-child(2)').animate({'opacity': '0'},
      {'duration': 300,
        step: function (now) {
          $btn.find('span:nth-child(1)').css({ transform: 'rotate(' +parseInt(45- now*45) +'deg)' });
          $btn.find('span:nth-child(3)').css({ transform: 'rotate(' +parseInt(-45+now*45) +'deg)' });
        }
    });
  }
  var close_anime =function($btn){
    $btn.find('span:nth-child(1)').animate({'top': '0px'});
    $btn.find('span:nth-child(3)').animate({'bottom': '0px'});
    $btn.find('span:nth-child(2)').animate({'opacity': '1'},
      {'duration': 300,
        step: function (now) {
          $btn.find('span:nth-child(1)').css({ transform: 'rotate(' +parseInt(45- now*45) +'deg)' });
          $btn.find('span:nth-child(3)').css({ transform: 'rotate(' +parseInt(-45+now*45) +'deg)' });
        }
    });
  }


  function accordion_menu($btn,$menu,$close,type, callback){
    var w=$(window).width();

    $(window).on('load resize', function(){
      w=$(window).width();
      if((w>768 && type=='sp')||(w<=768 && type=='pc')){
        $btn.attr('style','');
        $btn.find('*').attr('style','');
        $btn.removeClass('active');
        $menu.attr('style','');
        $menu.find('*').attr('style','');
        $menu.removeClass('active');
      }
    });

    $btn.on('click', function(e){
      w=$(window).width();
      if((w<=768 && type=='sp')||(w>768 && type=='pc') || type=='both'){
        if($menu.hasClass('active')){
          $btn.removeClass('active');
          $menu.removeClass('active');
          $menu.slideUp('fast');
        }else{
          $btn.addClass('active');
          $menu.addClass('active');
          $menu.slideDown('slow');
        }
        callback();
      }
    });

    if($close!=''){
      $close.on('click', function(){
        if((w<=768 && type=='sp')||(w>768 && type=='pc') || type=='both'){
          $btn.removeClass('active');
          $menu.removeClass('active');
          $menu.slideUp('fast');
          callback();
        }
      });
    }
  }

  function hover_menu($hover_btn,$hover_area){
		$hover_btn.hover(function(){
				$hover_area.stop(true, true).slideDown('slow');
			},function(){
				$hover_area.stop(true, true).slideUp('slow');
			});
	}

  //箱ごと入れ替わる方式
  function tab_menu_box($tab_btn,$tab_menu){
    var num;
    $tab_btn.click(function() {
      num = $tab_btn.index(this);
      console.log(num);
      $tab_menu.removeClass('active');
      $tab_menu.eq(num).addClass('active');
      $tab_btn.removeClass('active');
      $(this).addClass('active')
    });
  }

  /*リストの一部が出たり消えたりする方式*/
  function tab_menu_list($tab_btn,$tab_menu){
    var cat;
    $tab_btn.click(function() {
      cat = $(this).attr('id');
      $tab_menu.each(function(i){
        if(cat=='all' || $(this).hasClass(cat)){
          if($(this).hasClass('hidden')){
            $(this).slideDown().removeClass('hidden');
          }
        }else{
          $(this).slideUp().addClass('hidden');
        }
      });
      $tab_btn.removeClass('active');
      $(this).addClass('active');
    });
  }

  function slide_menu($btn,$menu,$close,right, callback){
    var num;
    var scrollpos;
    var w
    $(window).on('load resize', function(){
      w=$(window).width();
      if(w>768){
        $menu.attr('style','');
        //$('#wrapper').removeClass('active').attr('style','');
      }
    });
    $btn.click(function() {
      if($(this).hasClass('active')){
        $(this).removeClass('active');
        $menu.animate({'right': right+'px'},{'duration': 300});
        $('#wrapper').removeClass('active').css({'top': 0});
        //window.scrollTo( 0 , scrollpos );
        callback();
      }else{
        $(this).addClass('active');
        $menu.animate({'right': '0px'},{'duration': 300});
        scrollpos = $(window).scrollTop();
        $('#wrapper').addClass('active');//.css({'top': -scrollpos});
        callback();
      }
    });
    $close.click(function() {
      if($btn.hasClass('active')){
        $btn.removeClass('active');
        $menu.animate({'right': right+'px'},{'duration': 300});
        $('#wrapper').removeClass('active').css({'top': 0});
        //window.scrollTo( 0 , scrollpos );
        callback();
      }
    });
  }
})(jQuery);
