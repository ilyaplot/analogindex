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
        <span itemprop="title"><?php echo Yii::t("goods", "Отзывы") ?></span>
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
<div class="news">
    <?php foreach ($reviews as $item):?>
    <div class="view_bl">
        <div class="view_bl-head clr">
            <div class="view_bl-head-l flRight">
                <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
            </div>
        </div>
        <div class="view_bl-textView">
            <h2><?php echo  $item->title ?></h2>
            <span><?php echo $item->getDescription()?></span>...
        </div>
        <div class="view_bl-replyLink"><?php echo Yii::t("main", 'Читать полностью')." : ". CHtml::link($item->title, Yii::app()->createAbsoluteUrl("reviews/index", ['goods'=>$product->brand_data->link."_".$product->link,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()])) ?></div>
    </div>
    <?php endforeach; ?>
</div>
