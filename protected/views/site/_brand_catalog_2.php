<ul class="catalog_st2">
    <?php foreach ($goods as $item): ?>
        <li>
            <div class="flLeft catalog_st2-l">
                <div class="flLeft catalog_st2-l-image"><a href="<?php
                    echo Yii::app()->createUrl("site/goods", array(
                        'link' => $item->link,
                        'brand' => $brand->link,
                        'type' => $item->type_data->link,
                        'language' => Language::getCurrentZone()
                    ))
                    ?>">
                        <?php if ($item->primary_image): ?>
                            <?php echo $item->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_BRAND); ?>
                        <?php else : ?>
                            <img src="/assets/img/no-image-available.jpg" alt="<?php echo $brand->name ?> <?php echo $item->name ?>">
                        <?php endif ?></a></div>
                <div class="catalog_st2-l-info">
                    <div class="catalog_st2-l-info-title"><a href="<?php
                        echo Yii::app()->createUrl("site/goods", array(
                            'link' => $item->link,
                            'brand' => $brand->link,
                            'type' => $item->type_data->link,
                            'language' => Language::getCurrentZone()
                        ))
                        ?>"><?php echo $brand->name ?> <?php echo $item->name ?></a></div>
                    <div class="catalog_st2-l-info-desc">
    <?php foreach ($item->getGeneralCharacteristics() as $characteristic): ?>
        <?php echo $characteristic['characteristic_name'] . ": " . $characteristic['value'] . PHP_EOL; ?><br />
    <?php endforeach; ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="catalog_st2-r">
                <!--
                    <div class="catalog_st2-r_price">
                            <span>24 900</span>
                                                    <div class="informer-curr-bl">
                                                            <a href="#" class="informer_currency-select cur-rub"><span class="drpd_arrow-informer"></span></a>
                                                            <ul>
                                                                    <li><a href="#" class="cur-dol"></a></li>
                                                                    <li><a href="#" class="cur-eur"></a></li>
                                                            </ul>
                                                    </div>
                    </div>
                    <div class="catalog_st2-r_set1"><span class="icon"></span>94</div>
                    <div class="catalog_st2-r_set2"><span class="icon"></span>91</div>
                    <div class="catalog_st2-r_set3"><span class="icon"></span>95</div>
                -->
            </div>
        </li>
<?php endforeach; ?>
</ul>
<?php
$this->widget('LinkPager', array(
    'currentPage' => $pages->getCurrentPage(),
    'itemCount' => $pages->getItemCount(),
    'pageSize' => $view['limit'],
    'maxButtonCount' => 8,
    'header' => '',
    'htmlOptions' => array('class' => 'pagination'),
    'firstPageLabel' => Yii::t("main", "Первая"),
    'lastPageLabel' => Yii::t("main", "Последняя") . " (" . ceil($pages->getItemCount() / $view['limit']) . ")",
    'nextPageLabel' => Yii::t("main", "Следующая"),
    'prevPageLabel' => Yii::t("main", "Предыдущая"),
));
?>