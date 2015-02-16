<ul class="breadcrumbs breadcrumb">
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
        <span itemprop="title">
            <a itemprop="url" href="http://analogindex.<?php echo Language::getCurrentZone() ?>/">
                <?php echo Yii::t('main', 'Главная') ?>
            </a>
        </span>
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
        <span itemprop="title"><?php echo Yii::t("main", "Фотогалерея") ?></span>
    </li>
</ul>
<div class="wp_col_fix clr">
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
                ?>"><?php echo $product->brand_data->name . " " . $product->name ?></a>
            </div>
            <br />
            <small>
                <?php
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
    <style>
        #gallery .image {
            position: relative;
            width: 922px; /* for IE 6 */
            z-index: 1000;
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }
        #gallery .image img {
            max-width: 900px;
            max-height: 500px;
        }
        #gallery .button_next, #gallery .button_prev {
            cursor: pointer;
            position: absolute;
            top: 0px;
            height: 100%;
            width: 50px;
            z-index: 1100;
            background-color: rgba(200, 200, 200, 0.3);
            background-repeat: no-repeat;
            background-position-y: 50%;
        }

        #gallery .image .article {
            min-height: 50px;
            max-height: 150px;
            overflow: hidden;
            width: 792px;
            background-color: rgba(200, 200, 200, 0.1);
            text-align: left;
            font-size: 16px;
            margin-left: 50px;
            margin-right: 50px;
            padding: 15px;
        }

        #gallery .image .article p {
            font-size: 14px;
            font-style: italic;
        }

        #gallery .button_next:hover, #gallery .button_prev:hover {
            background-color: rgba(200, 200, 200, 0.7);
        }

        #gallery .button_next {
            right: 0px;
            background-image: url('/assets/img/next.png');
            background-position-x: 10px;
        }
        #gallery .button_prev {
            left: 0;
            background-image: url('/assets/img/prev.png');
            background-position-x: -10px;
        }

        .list table{
            border-collapse: collapse;
            border-spacing: 0;
            width:944px;
            height:100%;
            margin:0px;
            padding:0px;
            border:1px solid #cccccc !important;
        }

        .list td{
            vertical-align:middle;
            border:1px solid #cccccc;
            border-width:0px 1px 1px 0px;
            text-align:center;
            padding:2px;
        }.list tr:last-child td{
            border-width:0px 1px 0px 0px;
        }.list tr td:last-child{
            border-width:0px 0px 1px 0px;
        }.list tr:last-child td:last-child{
            border-width:0px 0px 0px 0px;
        }
        .list tr:first-child td{
            border:0px solid #cccccc;
            text-align:center;
            border-width:0px 0px 1px 1px;
        }
        .list tr:first-child td:first-child{
            border-width:0px 0px 1px 0px;
        }
        .list tr:first-child td:last-child{
            border-width:0px 0px 1px 1px;
        }

        #gallery table.list a {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
            width: 150px;
            height: 150px;
            padding: 1px;
            margin: 2px;
        }

        #loader {
            background-color: rgba(200, 200, 200, 0.7);
            position: absolute;
            width: 200px;
            height: 90px;
            text-align: center;
            top: 40%;
            left: 380px;
        }

        #loader img {
            width: 32px !important;
            height: 32px !important;
            max-width: 32px !important;
            max-height: 32px !important;
            min-width: 32px !important;
            min-height: 32px !important;
        }

        .gallery-pager {
            padding-top: 7px;
            padding-bottom: 7px;
            width: 790px;
            margin: auto;
        }

        .gallery-pager .pagination {
            margin: auto;
        }
    </style>
    <script type="text/javascript">
        function imageLoaded() {
            //document = document;
            var elem = document.getElementById('loader');
            elem.setAttribute('display', 'none');
            elem.remove();
        }
    </script>
    <div id="gallery">
        <div itemscope itemtype="http://schema.org/ImageObject" class="image">
            <div id="loader">
                <img onload="imageLoaded();" src="/assets/img/loading.gif" /> <?php echo Yii::t("gallery", 'Подождите, фотография загружается') ?>... 
            </div>

            <span itemprop="name" style="display: none"><?php echo $currentImage->image_data->title ?></span>
            <?php echo $currentImage->image_data->getHtml(NImages::SIZE_PRODUCT_BIG, null, ["itemprop" => "image"]); ?>
            <?php if ($currentImage->prev_url != null): ?>
                <a title="<?php echo Yii::t("gallery", 'Предыдущее фото') ?>" href="<?php echo $currentImage->prev_url ?>#gallery" class="button_prev"></a>
            <?php endif; ?>
            <?php if ($currentImage->next_url != null): ?>
                <a title="<?php echo Yii::t("gallery", 'Следующее фото') ?>" href="<?php echo $currentImage->next_url ?>#gallery" class="button_next"></a>
            <?php endif; ?>
            <?php if (!empty($currentImage->image_data->article) && !empty($currentImage->image_data->article->article_data(['cache' => 60 * 60, 'select' => 'title, description, link, id']))): ?>
                <div class="article">
                    <?php echo Yii::t("gallery", 'Фотография из') ?>
                    <?php Yii::app()->sourceLanguage = 'en';?>
                    <?php echo Yii::t("gallery", $currentImage->image_data->article->article_data->type) ?>: 
                    <?php Yii::app()->sourceLanguage = 'ru';?>
                    <a itemprop="associatedArticle" href="<?php echo $currentImage->image_data->article->article_data->url ?>">
                        <?php echo $currentImage->image_data->article->article_data->title ?>
                    </a>
                    <p itemprop="description"><?php echo $currentImage->image_data->article->article_data->description ?></p>
                </div>
            <?php endif; ?>

        </div>
        <div class="gallery-pager">
            <center>
                <?php
                $this->widget('LinkPager', array(
                    'currentPage' => $pages->getCurrentPage(),
                    'itemCount' => $pages->getItemCount(),
                    'pageSize' => Gallery::GALLERY_SIZE,
                    'maxButtonCount' => 8,
                    'header' => '',
                    'htmlOptions' => array('class' => 'pagination'),
                    'firstPageLabel' => Yii::t("main", "Первая"),
                    'lastPageLabel' => Yii::t("main", "Последняя") . " (" . ceil($pages->getItemCount() / Gallery::GALLERY_SIZE) . ")",
                    'nextPageLabel' => Yii::t("main", "Следующая"),
                    'prevPageLabel' => Yii::t("main", "Предыдущая"),
                ));
                ?>
            </center>
        </div>
        <table class="list">
            <?php foreach ($gallery as $list): ?>
                <tr>
                    <?php foreach ($list as $item): ?>
                        <td itemscope itemtype="http://schema.org/ImageObject">
                            <a itemtype="contentUrl" href="<?php echo $item->self_url ?>#gallery">
                                <?php echo $item->image_data->getHtml(NImages::SIZE_PRODUCT_GALLERY, null, ["itemtype" => "thumbnail"]); ?>
                            </a>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="gallery-pager">
            <center>
                <?php
                $this->widget('LinkPager', array(
                    'currentPage' => $pages->getCurrentPage(),
                    'itemCount' => $pages->getItemCount(),
                    'pageSize' => Gallery::GALLERY_SIZE,
                    'maxButtonCount' => 8,
                    'header' => '',
                    'htmlOptions' => array('class' => 'pagination'),
                    'firstPageLabel' => Yii::t("main", "Первая"),
                    'lastPageLabel' => Yii::t("main", "Последняя") . " (" . ceil($pages->getItemCount() / Gallery::GALLERY_SIZE) . ")",
                    'nextPageLabel' => Yii::t("main", "Следующая"),
                    'prevPageLabel' => Yii::t("main", "Предыдущая"),
                ));
                ?>
            </center>
        </div>
    </div>
</div>