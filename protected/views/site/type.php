<link rel="stylesheet" href="/assets/css/filter.css" />
<script type="text/javascript" src="/assets/js/filter.js"></script>
<?php
$selected = array(
    "brands" => array(),
    "os" => array(),
);
?>

<div id="filter">
    <form action="" method="post">
        <?php
        echo $this->renderPartial("_filter_select_item", array(
            "id" => "brands",
            "title" => "Производители",
            "name" => "brands[]",
            "items" => $brands,
            "itemsSelected" => $brandsSelected,
        ));
        ?>

        <?php
        echo $this->renderPartial("_filter_select_item", array(
            "id" => "os",
            "title" => "Операционные системы",
            "name" => "os[]",
            "items" => $os,
            "itemsSelected" => $osSelected,
        ));
        ?>

        <?php
        echo $this->renderPartial("_filter_select_item", array(
            "id" => "screensizes",
            "title" => "Диагональ экрана",
            "name" => "screensizes[]",
            "items" => $screenSizes,
            "itemsSelected" => $screenSizesSelected,
        ));
        ?>

        <?php
        echo $this->renderPartial("_filter_select_item", array(
            "id" => "cores",
            "title" => "Количество ядер процессора",
            "name" => "cores[]",
            "items" => $cores,
            "itemsSelected" => $coresSelected,
        ));
        ?>

        <?php
        echo $this->renderPartial("_filter_select_item", array(
            "id" => "cpufreq",
            "title" => "Частота процессора",
            "name" => "cpufreq[]",
            "items" => $cpufreq,
            "itemsSelected" => $cpuFreqSelected,
        ));
        ?>

        <?php
        echo $this->renderPartial("_filter_select_item", array(
            "id" => "ram",
            "title" => "Размер оперативной памяти (RAM)",
            "name" => "ram[]",
            "items" => $ram,
            "itemsSelected" => $ramSelected,
        ));
        ?>


        <input type="submit" value="Отправить" />
    </form>
</div>

<ul class="search_result-bl clr">
                         <?php foreach ($goods as $key => $product): ?>
        <li>
            <div class="flLeft">
                <div class="search_result-id"><?php echo $key + 1 + $pages->getCurrentPage() * 10 ?>.</div>
                <div class="search_result-photo">
                    <a href="<?php echo Yii::app()->createUrl("site/goods", array('link' => $product->link, 'brand' => $product->brand_data->link, 'type' => $product->type_data->link, 'language' => Language::getCurrentZone())) ?>">
    <?php if (isset($product->primary_image->image_data->size3_data->id)): ?>
                            <img src="<?php
        echo Yii::app()->createUrl("files/image", array(
            'id' => $product->primary_image->image_data->size3_data->id,
            'name' => $product->primary_image->image_data->size3_data->name,
            'language' => Language::getCurrentZone(),
        ));
        ?>" alt="<?php echo $product->brand_data->name . " " . $product->name ?>" />
                        <?php else : ?>
                            <img src="/assets/img/photo/informers/1.png" alt="<?php echo $product->brand_data->name . " " . $product->name ?>" />
    <?php endif; ?>

                    </a>
                </div>
                <div class="search_result-desc">
                    <h2 class="search_result-nameItem">
                        <a href="<?php echo Yii::app()->createUrl("site/goods", array('link' => $product->link, 'brand' => $product->brand_data->link, 'type' => $product->type_data->link, 'language' => Language::getCurrentZone())) ?>">
    <?php echo $product->brand_data->name . " " . $product->name ?>
                        </a>
                    </h2>
                    <p class="search_result-text">
    <?php foreach ($product->getGeneralCharacteristics() as $characteristic): ?>
        <?php echo $characteristic['characteristic_name'] . ": " . $characteristic['value'] . PHP_EOL; ?><br />
    <?php endforeach; ?>
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="flRight">
                <div class="search_result-p_r">
                    <!--<div class="search_result-price">15 000 р.</div>
                    <div class="search_result-rating">
                        <ul class="rating">
                             <li class="full"><a href="#">1</a></li>
                             <li class=""><a href="#">2</a></li>
                             <li class=""><a href="#">3</a></li>
                             <li class=""><a href="#">4</a></li>
                             <li class=""><a href="#">5</a></li>
                          </ul>
                    </div>-->
                </div>
            </div>
        </li>

<?php endforeach; ?>
</ul>
<?php
$this->widget('LinkPager', array(
    'currentPage' => $pages->getCurrentPage(),
    'itemCount' => $pages->getItemCount(),
    'pageSize' => 10,
    'maxButtonCount' => 8,
    'header' => '',
    'htmlOptions' => array('class' => 'pagination'),
    'firstPageLabel' => Yii::t("main", "Первая"),
    'lastPageLabel' => Yii::t("main", "Последняя") . " (" . ceil($pages->getItemCount() / 10) . ")",
    'nextPageLabel' => Yii::t("main", "Следующая"),
    'prevPageLabel' => Yii::t("main", "Предыдущая"),
));
?>