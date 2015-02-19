<?php
$types = (object) [
            (object) [
                'link' => 'news',
                'name' => Yii::t("goods", 'Новости'),
            ],
            (object) [
                'link' => 'opinion',
                'name' => Yii::t("goods", 'Отзывы'),
            ],
            (object) [
                'link' => 'review',
                'name' => Yii::t("goods", 'Обзоры'),
            ],
            (object) [
                'link' => 'howto',
                'name' => Yii::t("goods", 'Инструкции'),
            ]
];
?>
<h3><?php echo Yii::t("goods", 'Упомянутые аппараты') ?></h3>
<?php foreach (array_chunk((array) $goods, 5) as $row): ?>
    <ul class="related-products-list">
        <?php foreach ($row as $product): ?>
            <li style="width: 100px; overflow: hidden;">
                <?php if (isset($product->primary_image)): ?>
                    <a class="title" href="<?php
                    echo Yii::app()->createAbsoluteUrl("site/goods", [
                        'link' => $product->link,
                        'brand' => $product->brand_data->link,
                        'type' => $product->type_data->link,
                        'language' => Language::getCurrentZone(),
                    ])
                    ?>">
                           <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_LIST); ?>
                    </a>
                <?php endif; ?>
                <a href="<?php
                echo Yii::app()->createAbsoluteUrl("site/goods", [
                    'link' => $product->link,
                    'brand' => $product->brand_data->link,
                    'type' => $product->type_data->link,
                    'language' => Language::getCurrentZone(),
                ])
                ?>">
                    <div><?php echo $product->brand_data->name ?> <?php echo $product->name ?></div>
                </a>
                <div>
                    <?php if ($count = $product->getGalleryCount()): ?>
                        <a href="<?php
                        echo Yii::app()->createAbsoluteUrl("gallery/product", [
                            'product' => $product->link,
                            'brand' => $product->brand_data->link,
                            'language' => Language::getCurrentZone(),
                        ])
                        ?>"><?php echo Yii::t("main", 'Фотогалерея'); ?></a>
                       <?php endif; ?>
                </div>
                <ul>
                    <?php foreach ($types as $type): ?>
                        <?php if ($count = GoodsArticles::model()->cache(60 * 60)->getCount($product->id, $type->link)): ?>
                            <li>
                                <a itemprop="url"
                                   href="<?php
                                   echo Yii::app()->createAbsoluteUrl("articles/list", array(
                                       "product" => $product->link,
                                       "type" => $type->link,
                                       "brand" => $product->brand_data->link,
                                       "language" => Language::getCurrentZone(),
                                   ))
                                   ?>"><?php echo $type->name ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </li>

        <?php endforeach; ?>
    </ul>
    <div style="clear: both;"></div>
<?php endforeach; ?>
<div style="clear: both;"></div>
