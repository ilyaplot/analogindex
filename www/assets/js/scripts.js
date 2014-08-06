/*
  @ copyright (c) 2014
  @ by iShogun
*/





$(document).ready(function(){
    $(function(){
        $('.informer_currency-select').click(function(){
            $(this).parent().toggleClass('open');
            return false;
        });
    });

    $(function(){
        $('#nav-top>ul>li').hover(function(){
            $(this).addClass('active');
            $(this).children('ul').slideDown(200);
        }, function(){
            $(this).children('ul').slideUp(100);
            $(this).removeClass('active');
        });
    });
});


$(function(){
    $(".rating li a").hover(function(e){
       e.preventDefault();
       $(this).parent().addClass("full2");
       $(this).parent().prevAll().addClass("full2");
       $(this).parent().nextAll().addClass("empty");
    }, function(){
       $(this).parent().removeClass("full2");
       $(this).parent().prevAll().removeClass("full2");
       $(this).parent().nextAll().removeClass("empty");
    });
    
    $(".rating li a").on("click", function(e){
       e.preventDefault();
       $(this).parent().addClass("full");
       $(this).parent().prevAll().addClass("full");
       $(this).parent().nextAll().removeClass("full");
    });

    $(".rating2 li a").hover(function(e){
       e.preventDefault();
       $(this).parent().addClass("full2");
       $(this).parent().prevAll().addClass("full2");
       $(this).parent().nextAll().addClass("empty");
    }, function(){
       $(this).parent().removeClass("full2");
       $(this).parent().prevAll().removeClass("full2");
       $(this).parent().nextAll().removeClass("empty");
    });
    
    $(".rating2 li a").on("click", function(e){
       e.preventDefault();
       $('.GoodItemSetRating').attr('value', $(this).html())
       $(this).parent().addClass("full");
       $(this).parent().prevAll().addClass("full");
       $(this).parent().nextAll().removeClass("full");
    });

    $(function(){
      $('.item_photos_all').bxSlider({
        mode: 'vertical',
        slideWidth: 93,
        minSlides: 3,
        slideMargin: 10,
        moveSlides: 1,
        pager: false
      });
    });


    $(function(){

        // Определение позиции обекта от верхней границы сайта
        function offsetPosition(e) {
          return e.offset().top;
        }

      // Объявляем переменные и находим позицию блоков
        var bl0 = $('.infoGoodItem-title'),
            op0 = offsetPosition(bl0),
            bl1 = $('.col-infoPrices'),
            op1 = offsetPosition($('.col-infoPrices')),
            bl2 = $('.col-sidebars'),
            op2 = offsetPosition(bl2),
            wpcontent = $('.wp_col_fix'),
            op3 = offsetPosition(wpcontent),
            menu = $('.wpcontent_leftMenu');

          window.onscroll = function() {
            /*
            останавливаем плавающий блок, если тот
            достиг низа центральной колонки
          */

          if (window.pageYOffset > wpcontent.height() - bl2.height() + op3) {

            var bl2_topPos = (wpcontent.height() - bl2.height());

            // Фиксируем колонку с виджетами
            bl2.removeClass('prilip').addClass('stop');
            bl2.css('top', bl2_topPos + 'px');

            // Фиксируем колонку с ценами
            bl1.removeClass('prilip').addClass('stop');
            bl1.css('top', bl2_topPos + 'px');

            // Фиксируем титл
            bl0.removeClass('prilip');
            bl0.css('top', bl2_topPos + 'px');

            // Фиксируем меню
            menu.removeClass('prilip');
            menu.css('top', bl2_topPos + 100 + 'px');

          } else {        /* если блок не достиг конца блока */

            if(op2 < window.pageYOffset) 
            {
              bl2.addClass('prilip').removeClass('stop').css('top', 0);
              bl1.addClass('prilip').removeClass('stop').css('top', 0);
            } 
            else 
            {
              // Очищаем стили
              bl2.css('top', 0);
              bl2.removeClass('stop, prilip');

              bl1.css('top', 0);
              bl1.removeClass('stop, prilip');

            } // endif

            /**
              Блок с титлом */
             if (op0 <= window.pageYOffset) {
              bl0.addClass('prilip').css('top', 0);
              menu.addClass('prilip').css('top', 0);
             } else { 
              bl0.removeClass('prilip'); 
              menu.removeClass('prilip').removeAttr('style'); 
            }

          }
        }
    });

    // Left Menu fixed
    $('#fixedLeft-menu').on('click', 'li:not(.active)', function() {
      $(this).addClass('active').siblings().removeClass('active');
      return false;
    });

    $('#fixedLeft-menu>li>a').click(function(){
      $("html, body").animate({
            scrollTop: $($(this).attr("href")).offset().top -100 + "px"
        }, {
            duration: 500
        });
        return false;
    });

    $(function(){
      var bl1 = $('#item1'),
          bl2 = $('#item2'),
          bl3 = $('#item3'),
          bl4 = $('#item4'),
          bl5 = $('#item5'),
          bl6 = $('#item6'),
          bl7 = $('#item7'),
          $window    = $(window),
          topPadding = 100;

      $window.scroll(function() {

          if ($window.scrollTop() > bl1.offset().top+bl1.height()-topPadding) {
              $('#fixedLeft-menu li:nth-child(1)').removeClass('active');
          } else { $('#fixedLeft-menu li:nth-child(1)').addClass('active'); }

          if ($window.scrollTop() >= bl2.offset().top - topPadding && $window.scrollTop() < bl2.offset().top + (bl2.height() - topPadding)) {
              $('#fixedLeft-menu li:nth-child(2)').addClass('active');
          } else { $('#fixedLeft-menu li:nth-child(2)').removeClass('active');  }
          if ($window.scrollTop() >= bl3.offset().top - topPadding && $window.scrollTop() < bl3.offset().top + (bl3.height() - topPadding)) {
              $('#fixedLeft-menu li:nth-child(3)').addClass('active');
          } else { $('#fixedLeft-menu li:nth-child(3)').removeClass('active');  }
          if ($window.scrollTop() >= bl4.offset().top - topPadding && $window.scrollTop() < bl4.offset().top + (bl4.height() - topPadding)) {
              $('#fixedLeft-menu li:nth-child(4)').addClass('active');
          } else { $('#fixedLeft-menu li:nth-child(4)').removeClass('active'); }
          if ($window.scrollTop() >= bl5.offset().top - topPadding && $window.scrollTop() < bl5.offset().top + (bl5.height() - topPadding)) {
              $('#fixedLeft-menu li:nth-child(5)').addClass('active');
          } else { $('#fixedLeft-menu li:nth-child(5)').removeClass('active'); }
          if ($window.scrollTop() >= bl6.offset().top - topPadding && $window.scrollTop() < bl6.offset().top + (bl6.height() - topPadding)) {
              $('#fixedLeft-menu li:nth-child(6)').addClass('active');
          } else { $('#fixedLeft-menu li:nth-child(6)').removeClass('active'); }
          if ($window.scrollTop() >= bl7.offset().top - topPadding && $window.scrollTop() < bl7.offset().top + (bl7.height() - topPadding)) {
              $('#fixedLeft-menu li:nth-child(7)').addClass('active');
          } else { $('#fixedLeft-menu li:nth-child(7)').removeClass('active'); }
      });
  });


});