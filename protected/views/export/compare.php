<h2 style="margin-top: 15px;"><?php echo Yii::t("goods", 'Технические характеристики');?></h2>
<?php foreach ($goodsIndex as $index=>$goods): ?>
<table class="compare striped">
    <thead>
        <tr>
            <th></th>
            <?php foreach($goods as $product):?>
                <th style="text-align: center; vertical-align: middle;">
                    <a href="<?php echo Yii::app()->createAbsoluteUrl("site/goods", [
                        'link'=>$product['model']->link, 
                        'brand'=>$product['model']->brand_data->link,
                        'type'=>$product['model']->type_data->link, 
                        'language'=>Language::getCurrentZone(),
                    ])?>">
                        <?php if (isset($product['model']->primary_image)): ?>
                            <?php echo $product['model']->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_LIST);?>
                        <br />
                        <?php endif; ?>
                        <?=$product['name']?>
                    </a>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data[$index] as $characteristic=>$values):?>
        <tr>
            <th><?=$characteristic?></th>
            <?php foreach ($values as $value): ?>
            <td><?=$value['value']?></td>
            <?php endforeach;?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endforeach; ?>