<div class="infoGoodItem-wp-settings" id="item2">
    <section class="infoGoodItem_content">
        <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Характеристики')?></h3>
        <div class="item-set-bl">
            <?php if ($goods->synonims):?>
            <div class="item-set-bl_title"><?php echo Yii::t("goods", "Общие");?></div>
                <?php if ($goods->synonims):?>
                <div class="item-set-bl_lineText clr">
                    <div class="flLeft"><span><?php echo Yii::t("goods", "Другие названия");?></span></div>
                    <div class="flRight"><span>
                       <?php foreach ($goods->synonims as $synonim):?>
                            <?php echo $goods->brand_data->name." ".$synonim->name."<br />";?>
                       <?php endforeach; ?>
                    </span></div>
                </div>
                <?php endif;?>
            <?php endif;?>
            <?php /** foreach ($data['characteristics'] as $characteristic): ?>
            <div class="item-set-bl_title">Общие</div>
            
            <div class="item-set-bl_lineText clr">
                <div class="flLeft"><span><?php echo htmlspecialchars($characteristic['name']) ?></span></div>
                <div class="flRight"><span><?php echo htmlspecialchars($characteristic['value']) ?></span></div>
            </div>
        <?php endforeach; **/ ?>
        </div>
    </section>
</div>