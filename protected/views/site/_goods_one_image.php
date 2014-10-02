<?php
    if (
        isset($product->primary_image)
        && isset($product->primary_image->image_data)
        && isset($product->primary_image->image_data->file_data)
        && isset($product->primary_image->image_data->size2_data)
    ):

?>
<div class="infoGoodItem-wp-photos_main" id="photo_main">
    <a class="big_image" data-lightbox="roadtrip" data-title="<?php echo $brand->name." ".$product->name ?>" 
       href="<?php echo Yii::app()->createUrl("files/image", array(
        'id'=>$product->primary_image->image_data->file_data->id,
        'name'=>$product->primary_image->image_data->file_data->name,
        'language'=>Language::getCurrentZone(),
        )); ?>">
        
        <img src="<?php echo Yii::app()->createUrl("files/image", array(
        'id'=>$product->primary_image->image_data->size2_data->id,
        'name'=>$product->primary_image->image_data->size2_data->name,
        'language'=>Language::getCurrentZone(),
        )); ?>" style="max-width: 510px; height: auto; max-height: 300px;"
        alt="<?php echo $brand->name." ".$product->name ?>" />
    </a>
</div>
<?php endif;?>