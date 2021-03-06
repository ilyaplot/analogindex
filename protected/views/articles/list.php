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
<ul class="breadcrumbs breadcrumb">
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
        <span itemprop="title"><a itemprop="url" href="http://analogindex.<?php echo Language::getCurrentZone() ?>/"><?php echo Yii::t('main', 'Главная') ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
        <span itemprop="title"><a itemprop="url" href="<?php echo Yii::app()->createUrl("site/type", array("type" => $product->type_data->link)) ?>"><?php echo $product->type_data->name->name ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-2" itemref="breadcrumb-3">
        <span itemprop="title"><a itemprop="url" href="<?php
            echo Yii::app()->createUrl("site/brand", array(
                "link" => $product->brand_data->link,
                "language" => Language::getCurrentZone(),
                "type" => $product->type_data->link,
            ));
            ?>"><?php echo $product->brand_data->name ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-3" itemref="breadcrumb-4">
        <span itemprop="title"><a itemprop="url" href="<?php
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
        <span itemprop="title">
            <?php foreach ($types as $type): ?>
                <?php if ($type->link == $type_selected) : ?>
                    <a itemprop="url"
                       href="<?php
                       echo Yii::app()->createAbsoluteUrl("articles/list", array(
                           "product" => $product->link,
                           "type" => $type->link,
                           "brand" => $brand->link,
                           "language" => Language::getCurrentZone(),
                       ))
                       ?>"><?php echo $type->name ?></a>
                   <?php endif; ?>
               <?php endforeach; ?>
        </span>
    </li>
</ul>
<div class="manufacture-categories clr">
    <div class="mnf_logo">
        <?php if ($product->primary_image): ?>
            <a href="<?php
            echo Yii::app()->createUrl("site/goods", array(
                'link' => $product->link,
                'brand' => $product->brand_data->link,
                'type' => $product->type_data->link,
                'language' => Language::getCurrentZone()
            ))
            ?>">

                <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_LIST); ?>
            </a>
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
        <div class="mnf-catLiks clr">
            <?php foreach ($types as $type): ?>
                <?php if ($count = GoodsArticles::model()->getCount($product->id, $type->link)): ?>
                    <a <?php if ($type->link == $type_selected) : ?>
                            class="active"
                        <?php endif; ?>
                        href="<?php
                        echo Yii::app()->createAbsoluteUrl("articles/list", array(
                            "product" => $product->link,
                            "type" => $type->link,
                            "brand" => $brand->link,
                            "language" => Language::getCurrentZone(),
                        ))
                        ?>"><?php echo $type->name ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
$this->widget('LinkPager', array(
    'currentPage' => $pages->getCurrentPage(),
    'itemCount' => $pages->getItemCount(),
    'pageSize' => 15,
    'maxButtonCount' => 8,
    'header' => '',
    'htmlOptions' => array('class' => 'pagination'),
    'firstPageLabel' => Yii::t("main", "Первая"),
    'lastPageLabel' => Yii::t("main", "Последняя") . " (" . ceil($pages->getItemCount() / 15) . ")",
    'nextPageLabel' => Yii::t("main", "Следующая"),
    'prevPageLabel' => Yii::t("main", "Предыдущая"),
));
?>
<div class="news">
    <?php foreach ($news as $item): ?>
        <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
            <div class="view_bl-head clr">
                <div class="view_bl-head-l flRight">
                    <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long'); ?></date>
                    <span itemprop="datePublished" style="display: none;"><?php echo $item->created ?></span>
                </div>
            </div>
            <div class="view_bl-textView">
                <h2 itemprop="name"><?php echo $item->title ?></h2>
                <?php if (!empty($item->preview_image->image_data)) : ?>
                    <a class="news-preview" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $item->type, 'link' => $item->link, 'id' => $item->id, 'language' => Language::getCurrentZone()]); ?>">
                        <?php echo $item->preview_image->image_data->getHtml(NImages::SIZE_ARTICLE_PREVIEW, null, ['itemprop' => "image", 'class' => "news_preview"]); ?>
                    </a>
                <?php endif; ?>
                <span itemprop="description"><?php echo $item->description ?></span>...
                <?php if (!empty($item->preview_image->image_data)) : ?>
                    <div style="clear: both;"></div>
                <?php endif; ?>
            </div>
            <div class="view_bl-replyLink"><?php echo Yii::t("main", 'Читать полностью') ?> : <a itemprop="url" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $item->type, 'link' => $item->link, 'id' => $item->id, 'language' => Language::getCurrentZone()]); ?>"><?php echo $item->title ?></a></div>

        </div>
    <?php endforeach; ?>
</div>
<?php
$this->widget('LinkPager', array(
    'currentPage' => $pages->getCurrentPage(),
    'itemCount' => $pages->getItemCount(),
    'pageSize' => 15,
    'maxButtonCount' => 8,
    'header' => '',
    'htmlOptions' => array('class' => 'pagination'),
    'firstPageLabel' => Yii::t("main", "Первая"),
    'lastPageLabel' => Yii::t("main", "Последняя") . " (" . ceil($pages->getItemCount() / 15) . ")",
    'nextPageLabel' => Yii::t("main", "Следующая"),
    'prevPageLabel' => Yii::t("main", "Предыдущая"),
));
?>
