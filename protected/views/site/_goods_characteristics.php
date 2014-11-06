<?php
$characteristics = $product->getCharacteristics();
$characteristicsLinks = new CharacteristicsLinks($characteristics);
$characteristics = $characteristicsLinks->getCharacteristics($product->type_data->link);
?><div class="infoGoodItem-wp-settings" id="item2">
    <section class="infoGoodItem_content">
        <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Характеристики')?></h3>
        <div class="item-set-bl">
            <?php if (!empty($product->synonims)):?>
                <div class="item-set-bl_title"><?php echo Yii::t('goods', 'Другие наименования')?></div>
                    <div class="item-set-bl_lineText clr">
                    <?php $synonims = [] ?>
                    <?php foreach ($product->synonims as $synonim):?>
                        <?php if ($synonim->visibled) :?>
                        <?php $synonims[] = $product->brand_data->name." ".$synonim->name?>
                        <?php endif;?>
                    <?php endforeach;?>
                    <div class="flRight">
                        <span>
                            <?php echo implode(", ", $synonims)?>
                        </span>
                    </div>
                </div>
            <?php endif;?>
            <?php foreach ($characteristics as $catalog=>$items):?>
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
