<script type="text/javascript">
$(document).ready(function(){
    
    $("#photo_main img").click(function(){
        $(".infoGoodItem-wp-photos_all .slide .preview[data-preview='"+$(this).attr("src")+"']").trigger("click");
        return false;
    });
    
    $(".infoGoodItem-wp-photos_all .slide .preview").hover(function(){
        if ($("#photo_main img").attr("src") == $(this).attr("data-preview"))
            return;
        var src = $(this).attr("data-preview");
        $("#photo_main img").fadeOut(function() {
            $(this).attr("src", src).fadeIn();
        });
    }, function(){});
});
</script>
<?php echo $this->renderPartial("_goods_one_image", array("product"=>$product, "brand"=>$brand)) ?>

<div class="clear" ></div>
<div class="infoGoodItem-wp-photos_all">
    <?php
    $counter = 0;
    foreach ($product->images as $image):
        $counter++;
        if ($counter > 20)
            break;
        
        ?>
    <?php 
        if (!isset($image->image_data->size3_data) || !isset($image->image_data->file_data))
            continue;
    ?>
    <div class="slide">
        <a title="<?php echo $brand->name?> <?php echo $product->name?>" 
           href="<?php echo Yii::app()->createUrl("files/image", array(
                'id'=>$image->image_data->file_data->id,
                'name'=>$image->image_data->file_data->name,
                'language'=>Language::getCurrentZone(),
                )); ?>" data-lightbox="roadtrip">
            <img class="preview" src="<?php echo Yii::app()->createUrl("files/image", array(
                'id'=>$image->image_data->size3_data->id,
                'name'=>$image->image_data->size3_data->name,
                'language'=>Language::getCurrentZone(),
                )); ?>" 
                alt="<?php echo $brand->name." ".$product->name ?>" data-preview="<?php echo Yii::app()->createUrl("files/image", array(
                'id'=>$image->image_data->size2_data->id,
                'name'=>$image->image_data->size2_data->name,
                'language'=>Language::getCurrentZone(),
                )); ?>">
        </a>
    </div>
    <?php endforeach; ?>
</div>

