$(document).ready(function () {
    var items = [
        // id, object
        ['item-main', {}],
        ['item-specifications', {}],
        ['item-news', {}],
        ['item-opinion', {}],
        ['item-review', {}],
        ['item-howto', {}],
        ['item-videos', {}],
        ['item-comments', {}]
    ];

    if (screen.width > '992')
    {
        (function($) {
            $.lockfixed("#fixedLeft-menu", {offset: {top: 100, bottom: 10}});
        })(jQuery);
    }

    // Left Menu fixed
    $('#fixedLeft-menu').on('click', 'li:not(.active)', function () {
        $(this).addClass('active').siblings().removeClass('active');
        return false;
    });

    $('#fixedLeft-menu>li>a').click(function () {
        if (typeof($($(this).attr("href")).offset().top) == 'undefined') {
            return false;
        }
        
        $("html, body").animate({
            scrollTop: $($(this).attr("href")).offset().top - 100 + "px"
        }, {
            duration: 500
        });
        return false;
    });

    $(function () {
        
        var $window = $(window);
        var topPadding = 100;
        
        $(items).each(function(k, item){
            var container = $("#"+item[0]);
            if (container.length()) {
                items[k][1] = container;
            } else {
                $('#fixedLeft-menu>li[data-id="'+item[0]+'"]').remove();
            }
        });

        $window.scroll(function () {
            $(items).each(function(k, item){
                if (typeof (item[1].offset()) !== 'undefined') {
                    if ($window.scrollTop() > item[1].offset().top + item[1].height() - topPadding) {
                        $('#fixedLeft-menu>li[data-id="'+item[0]+'"]').removeClass('active');
                    } else {
                        $('#fixedLeft-menu>li[data-id="'+item[0]+'"]').addClass('active');
                    }
                }
            });
            /**
            if (typeof (bl7.offset()) !== 'undefined') {
                if ($window.scrollTop() >= bl7.offset().top - topPadding && $window.scrollTop() < bl7.offset().top + (bl7.height() - topPadding)) {
                    $('#fixedLeft-menu li:nth-child(7)').addClass('active');
                } else {
                    $('#fixedLeft-menu li:nth-child(7)').removeClass('active');
                }
            }
            **/

        });
    });

});