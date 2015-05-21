
$(document).ready(function(){



  if (screen.width > '992') 
  {
    (function($) 
      {
        $.lockfixed("#fixedLeft-menu",{offset: {top: 100, bottom: 10}});
      })(jQuery);
  }

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
            if(typeof(bl1.offset()) !== 'undefined') {
                if ($window.scrollTop() > bl1.offset().top+bl1.height()-topPadding) {
                    $('#fixedLeft-menu li:nth-child(1)').removeClass('active');
                } else { $('#fixedLeft-menu li:nth-child(1)').addClass('active'); }
            }
            if(typeof(bl2.offset()) !== 'undefined') {
                if ($window.scrollTop() >= bl2.offset().top - topPadding && $window.scrollTop() < bl2.offset().top + (bl2.height() - topPadding)) {
                    $('#fixedLeft-menu li:nth-child(2)').addClass('active');
                } else { $('#fixedLeft-menu li:nth-child(2)').removeClass('active');  }
            }
            if(typeof(bl3.offset()) !== 'undefined') {
                if ($window.scrollTop() >= bl3.offset().top - topPadding && $window.scrollTop() < bl3.offset().top + (bl3.height() - topPadding)) {
                    $('#fixedLeft-menu li:nth-child(3)').addClass('active');
                } else { $('#fixedLeft-menu li:nth-child(3)').removeClass('active');  }
            }
            if(typeof(bl4.offset()) !== 'undefined') {
                if ($window.scrollTop() >= bl4.offset().top - topPadding && $window.scrollTop() < bl4.offset().top + (bl4.height() - topPadding)) {
                    $('#fixedLeft-menu li:nth-child(4)').addClass('active');
                } else { $('#fixedLeft-menu li:nth-child(4)').removeClass('active'); }
            }
            if(typeof(bl5.offset()) !== 'undefined') {
                if ($window.scrollTop() >= bl5.offset().top - topPadding && $window.scrollTop() < bl5.offset().top + (bl5.height() - topPadding)) {
                    $('#fixedLeft-menu li:nth-child(5)').addClass('active');
                } else { $('#fixedLeft-menu li:nth-child(5)').removeClass('active'); }
            }
            if(typeof(bl6.offset()) !== 'undefined') {
                if ($window.scrollTop() >= bl6.offset().top - topPadding && $window.scrollTop() < bl6.offset().top + (bl6.height() - topPadding)) {
                    $('#fixedLeft-menu li:nth-child(6)').addClass('active');
                } else { $('#fixedLeft-menu li:nth-child(6)').removeClass('active'); }
            }
            if(typeof(bl7.offset()) !== 'undefined') {
                if ($window.scrollTop() >= bl7.offset().top - topPadding && $window.scrollTop() < bl7.offset().top + (bl7.height() - topPadding)) {
                    $('#fixedLeft-menu li:nth-child(7)').addClass('active');
                } else { $('#fixedLeft-menu li:nth-child(7)').removeClass('active'); }
            }
            
        });
  });
      
});