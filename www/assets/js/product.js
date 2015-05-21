$(document).ready(function () {
        var items = [
            // id, object
            ['item-main', {}],
            ['item-specifications', {}],
            ['item-news', {}],
            ['item-review', {}],
            ['item-opinion', {}],
            ['item-howto', {}],
            ['item-videos', {}],
            ['item-comments', {}]
        ];

        if (screen.width > '992')
        {
            (function ($) {
                $.lockfixed("#fixedLeft-menu", {offset: {top: 100, bottom: 10}});
            })(jQuery);
        }

        // Left Menu fixed
        $('#fixedLeft-menu').on('click', 'li:not(.active)', function () {
            $(this).addClass('active').siblings().removeClass('active');
            return false;
        });

        $('#fixedLeft-menu>li>a').click(function () {
            if (typeof ($($(this).attr("href"))) == 'undefined') {
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
            var topPadding = 400;

            $(items).each(function (k, item) {
                if ($("#" + item[0]).length > 0) {
                    items[k][1] = $("#" + item[0]);
                } else {
                    $('#fixedLeft-menu>li[data-id="' + item[0] + '"]').remove();
                }

                if (typeof ($(items[k][1]).offset()) !== 'undefined') {

                    if ($window.scrollTop() > items[k][1].offset().top - topPadding && $window.scrollTop() < items[k][1].offset().top + (items[k][1].height() - topPadding)) {
                        $('#fixedLeft-menu li[data-id="' + item[0] + '"]').addClass('active');
                    } else {
                        $('#fixedLeft-menu li[data-id="' + item[0] + '"]').removeClass('active');
                    }

                }
            });

            $window.scroll(function () {
                $(items).each(function (k, item) {
                    if (typeof ($(item[1]).offset()) !== 'undefined') {

                        if ($window.scrollTop() > item[1].offset().top - topPadding && $window.scrollTop() < item[1].offset().top + (item[1].height() - topPadding)) {
                            $('#fixedLeft-menu li[data-id="' + item[0] + '"]').addClass('active');
                        } else {
                            $('#fixedLeft-menu li[data-id="' + item[0] + '"]').removeClass('active');
                        }

                    }
                });
            });
        });

    });