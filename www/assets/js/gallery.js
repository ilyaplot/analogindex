$(document).ready(function () 
	{

        $("#photo_main img").click(function () {
            $(".infoGoodItem-wp-photos_all .slide .preview[data-preview='" + $(this).attr("src") + "']").trigger("click");
            return false;
        });

        $(".infoGoodItem-wp-photos_all .slide .preview").hover(function () {
            if ($("#photo_main img").attr("src") == $(this).attr("data-preview"))
                return;
            var src = $(this).attr("data-preview");
            
            $("#photo_main img").fadeOut(function () {
                $(this).attr("src", src).fadeIn();
            });
            
        }, function () {
        });
    });