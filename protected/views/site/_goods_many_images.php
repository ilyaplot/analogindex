<script type="text/javascript">
$(document).ready(function(){
    
    $("#photo_main img").click(function(){
        $(".infoGoodItem-wp-photos_all .slide .preview[data-preview='"+$(this).attr("src")+"']").trigger("click");
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
<?php echo $this->renderPartial("_goods_one_image", array("goods"=>$goods)) ?>
<div class="clear" ></div>
<div class="infoGoodItem-wp-photos_all">
    <?php foreach ($goods->images as $image): ?>
    <?php if (!isset($image->image_data->resized_preview->file_data->id)) continue;?>
    <?php if (!isset($image->image_data->resized_list->file_data->id)) continue;?>
    <div class="slide">
        <a title="<?php echo $goods->brand_data->name?> <?php echo $goods->name?>" href="<?php echo Yii::app()->createUrl("files/image", array(
                'id'=>$image->image_data->file_data->id,
                'name'=>$image->image_data->file_data->name,
                'language'=>Language::getCurrentZone(),
                )); ?>" data-lightbox="roadtrip">
            <img class="preview" src="<?php echo Yii::app()->createUrl("files/image", array(
                'id'=>$image->image_data->resized_list->file_data->id,
                'name'=>$image->image_data->resized_list->file_data->name,
                'language'=>Language::getCurrentZone(),
                )); ?>" 
                alt="<?php echo $goods->brand_data->name." ".$goods->name ?>" data-preview="<?php echo Yii::app()->createUrl("files/image", array(
                'id'=>$image->image_data->resized_preview->file_data->id,
                'name'=>$image->image_data->resized_preview->file_data->name,
                'language'=>Language::getCurrentZone(),
                )); ?>">
        </a>
    </div>
    <?php endforeach; ?>
</div>
