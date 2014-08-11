<?php 
    $bigImage = $goods->getPrimaryImage(Images::SIZE_PREVIEW);
    if ($bigImage):
?>
<div class="infoGoodItem-wp-photos_main" id="photo_main">
    <a data-lightbox="image-1" data-title="<?php echo $goods->brand_data->name." ".$goods->name ?>" href="<?php echo Yii::app()->createUrl("files/image", array(
        'id'=>$bigImage->file_data->id,
        'name'=>$bigImage->file_data->name,
        'language'=>Language::getCurrentZone(),
        )); ?>"><img src="<?php echo Yii::app()->createUrl("files/image", array(
        'id'=>$bigImage->file_data->id,
        'name'=>$bigImage->file_data->name,
        'language'=>Language::getCurrentZone(),
        )); ?>" style="max-width: 510px; height: auto;"
        alt="<?php echo $goods->brand_data->name." ".$goods->name ?>" /></a>
</div>
<?php endif; ?>