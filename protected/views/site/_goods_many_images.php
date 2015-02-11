<script type="text/javascript">
    $(document).ready(function () {

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
</script>
<?php
$images = array_map(function($value) {
    return $value->image_data;
}, $product->cache(60*5)->gallery([
    'limit' => Goods::IMAGES_LIMIT, 
    'with' => ['image_data'],
    'order' => 'gallery.id asc',
    ]));
$firstImage = reset($images);
//$images = array_chunk($images, 5);
?>



<div class="infoGoodItem-wp-photos_main" id="photo_main">
    <a class="big_image" data-lightbox="roadtrip" data-title="<?php echo $brand->name . " " . $product->name ?>" 
       href="<?php echo $firstImage->createUrl(NImages::SIZE_PRODUCT_BIG); ?>">
<?php echo $firstImage->getHtml(NImages::SIZE_PRODUCT_PREVIEW); ?>
    </a>
</div>
<div class="clear" ></div>
<div class="infoGoodItem-wp-photos_all">
<?php foreach ($images as $image): ?>
        <div class="slide">
            <a rel="lightbox" title="<?php echo $brand->name ?> <?php echo $product->name ?>" 
               href="<?php echo $image->createUrl(NImages::SIZE_PRODUCT_BIG); ?>" data-lightbox="roadtrip">
                   <?php echo $image->getHtml(NImages::SIZE_PRODUCT_LIST, null, [
                       'class' => 'preview',
                       'data-preview' => $image->createUrl(NImages::SIZE_PRODUCT_PREVIEW),
                   ]); ?>
            </a>
        </div>
<?php endforeach; ?>
</div>
