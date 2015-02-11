<ul class="catalog_st1">
    <?php $limit = 0; ?>
    <?php foreach ($goods as $item) : ?>
        <?php $limit++ ?>
        <li><a href="<?php
            echo Yii::app()->createUrl("site/goods", array(
                'link' => $item->link,
                'brand' => $brand->link,
                'type' => $item->type_data->link,
                'language' => Language::getCurrentZone()
            ))
            ?>">
                <div class="catalog_st1-image">
                    <?php if ($item->primary_image): ?>
                        <?php echo $item->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_BRAND); ?>
                    <?php else : ?>
                        <img src="/assets/img/no-image-available.jpg" alt="<?php echo $brand->name ?> <?php echo $item->name ?>">
                    <?php endif ?>
                </div>
                <div class="catalog_st1-name"><?php echo $brand->name ?> <?php echo $item->name ?></div>
            </a></li>
        <?php
        if ($limit == 6):
            $limit = 0;
            ?>
            <br clear="both">
        <?php endif; ?>
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