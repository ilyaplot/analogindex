<h3><?php echo Yii::t("goods", 'Упомянутые аппараты')?></h3>
<?php foreach ($goods as $product):?>
<div style="float: left; margin: 3px; margin-bottom: 2px; padding: 3px; text-align: left; height: 45px;">
    <a href="<?php echo Yii::app()->createAbsoluteUrl("site/goods", [
        'link'=>$product->link, 
        'brand'=>$product->brand_data->link,
        'type'=>$product->type_data->link, 
        'language'=>Language::getCurrentZone(),
    ])?>">
        <?php if (isset($product->primary_image->image_data->size3_data->id)): ?>
            <img style="float:left; margin-right: 4px; max-width: 40px; max-height: 47px;" 
                 src="<?php echo Yii::app()->createAbsoluteUrl("files/image", array(
            'id'=>$product->primary_image->image_data->size3_data->id,
            'name'=>$product->primary_image->image_data->size3_data->name,
            'language'=>Language::getCurrentZone(),
            )); ?>" alt="<?php echo $product->brand_data->name ." ". $product->name?>" 
            title="<?php echo $product->brand_data->name ." ". $product->name?>" /> 
        <?php endif; ?>
            <div style="margin-top: 4px; min-width: 90px;"><?php echo $product->brand_data->name?> <?php echo $product->name?></div>
    </a>
</div>
<?php endforeach; ?>
<div style="clear: both; height: 20px;"></div>