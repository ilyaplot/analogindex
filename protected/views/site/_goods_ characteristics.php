<div class="infoGoodItem-wp-settings" id="item2">
    <section class="infoGoodItem_content">
        <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Характеристики')?></h3>
        <div class="item-set-bl">
            <?php foreach ($product->getCharacteristics() as $catalog=>$items):?>
            
                <div class="item-set-bl_title"><?php echo $catalog;?></div>
                <?php foreach ($items as $characteristic):?>
                    <div class="item-set-bl_lineText clr">
                        <div class="flLeft"><span><?php echo $characteristic['characteristic_name'];?></span></div>
                        <div class="flRight"><span>
                           <?php echo $characteristic['value'] ?>
                        </span></div>
                    </div>
                <?php endforeach;?>
            <?php endforeach;?>
        </div>
    </section>
</div>
