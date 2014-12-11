<ul class="breadcrumbs breadcrumb">
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
        <span itemprop="title"><a href="http://analogindex.<?php echo Language::getCurrentZone() ?>/"><?php echo Yii::t('main', 'Главная') ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
        <span itemprop="title"><a href="<?php echo Yii::app()->createUrl("site/type", array("type" => $product->type_data->link)) ?>"><?php echo $product->type_data->name->name ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-2" itemref="breadcrumb-3">
        <span itemprop="title"><a href="<?php
        echo Yii::app()->createUrl("site/brand", array(
            "link" => $product->brand_data->link,
            "language" => Language::getCurrentZone(),
            "type" => $product->type_data->link,
        ));
        ?>"><?php echo $product->brand_data->name ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-3" itemref="breadcrumb-4">
        <span itemprop="title"><a href="<?php
        echo Yii::app()->createUrl("site/goods", array(
            'link' => $product->link,
            'brand' => $product->brand_data->link,
            'type' => $product->type_data->link,
            'language' => Language::getCurrentZone(),
        ));
        ?>">
               <?php echo $product->brand_data->name ?> <?php echo $product->name ?>
            </a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" class="active" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-4">
        <span itemprop="title"><?php echo Yii::t("goods", "Новости") ?></span>
    </li>
</ul>
<div class="manufacture-categories clr">
            <div class="mnf_logo">
                <a href="<?php
                echo Yii::app()->createUrl("site/goods", array(
                    'link' => $product->link,
                    'brand' => $product->brand_data->link,
                    'type' => $product->type_data->link,
                    'language' => Language::getCurrentZone()
                ))
                ?>">
                       <?php if ($product->primary_image): ?>
                        <img src="<?php
                        echo Yii::app()->createUrl("files/image", array(
                            'id' => $product->primary_image->image_data->size3_data->id,
                            'name' => $product->primary_image->image_data->size3_data->name,
                            'language' => Language::getCurrentZone(),
                        ));
                        ?>" alt="<?php echo $product->brand_data->name . " " . $product->name ?>" /></a>
                    <?php endif; ?>
            </div>
            <div class="mnf_clr">
                <div class="mnf-name">
                    <a href="<?php
                    echo Yii::app()->createUrl("site/goods", array(
                        'link' => $product->link,
                        'brand' => $product->brand_data->link,
                        'type' => $product->type_data->link,
                        'language' => Language::getCurrentZone()
                    ))
                    ?>"><span itemprop="itemReviewed"><?php echo $product->brand_data->name . " " . $product->name ?></span></a>
                </div>
                <small>
                    <?php
                    $characteristics = $product->getCharacteristics($product->generalCharacteristics);
                    $characteristicsLinks = new CharacteristicsLinks($characteristics);
                    $characteristics = $characteristicsLinks->getCharacteristics($product->type_data->link);
                    ?>
                    <?php foreach ($characteristics as $catalog): ?>
                        <?php foreach ($catalog as $characteristic): ?>
                            <?php echo $characteristic['characteristic_name'] . ": " . $characteristic['value'] . PHP_EOL; ?><br />
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </small>

            </div>
        </div>
<?php 
    $this->widget('LinkPager', array(
        'currentPage'=>$pages->getCurrentPage(),
        'itemCount'=>$pages->getItemCount(),
        'pageSize'=>15,
        'maxButtonCount'=>8,
        'header'=>'',
        'htmlOptions'=>array('class'=>'pagination'),
        'firstPageLabel'=>Yii::t("main", "Первая"),
        'lastPageLabel'=>Yii::t("main", "Последняя")." (".ceil($pages->getItemCount()/15).")",
        'nextPageLabel'=>Yii::t("main", "Следующая"),
        'prevPageLabel'=>Yii::t("main", "Предыдущая"),
    ));
?>
<div class="news">
    <?php foreach ($news as $item):?>
    <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
        <div class="view_bl-head clr">
            <div class="view_bl-head-l flRight">
                <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
                <span itemprop="datePublished" style="display: none;"><?php echo $item->created?></span>
            </div>
        </div>
        <div class="view_bl-textView">
            <h2 itemprop="name"><?php echo  $item->title ?></h2>
            <span itemprop="description"><?php echo $item->getDescription()?></span>...
        </div>
        <div class="view_bl-replyLink"><?php echo Yii::t("main", 'Читать полностью')?> : <a itemprop="url" href="<?php echo Yii::app()->createUrl("news/index", ['link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>"><?php echo $item->title?></a></div>
    </div>
    <?php endforeach; ?>
</div>
<?php 
    $this->widget('LinkPager', array(
        'currentPage'=>$pages->getCurrentPage(),
        'itemCount'=>$pages->getItemCount(),
        'pageSize'=>15,
        'maxButtonCount'=>8,
        'header'=>'',
        'htmlOptions'=>array('class'=>'pagination'),
        'firstPageLabel'=>Yii::t("main", "Первая"),
        'lastPageLabel'=>Yii::t("main", "Последняя")." (".ceil($pages->getItemCount()/15).")",
        'nextPageLabel'=>Yii::t("main", "Следующая"),
        'prevPageLabel'=>Yii::t("main", "Предыдущая"),
    ));
?>
