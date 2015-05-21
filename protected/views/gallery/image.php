<link rel="stylesheet" href="/assets/css/style_gallery.css" />
<!-- Контент Начало-->
<div class="row content-wrapper">
    <div class="col s12">
        <div class="col s12 m3 l2 hide-on-med-and-down">
            <!-- Описание++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <div class="mnf_logo">
                <?php if ($product->primary_image): ?>
                    <a href="<?=
                    Yii::app()->createUrl("site/goods", array(
                        'link' => $product->link,
                        'brand' => $product->brand_data->link,
                        'type' => $product->type_data->link,
                        'language' => Language::getCurrentZone()
                    ))
                    ?>">
                    <?= $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_LIST); ?>
                    </a>
<?php endif; ?>
            </div>
        </div>
        <div class="col s12 m9 l10">
            <div class="mnf_clr">
                <div class="mnf-name">
                    <a href="<?=
                    Yii::app()->createUrl("site/goods", array(
                        'link' => $product->link,
                        'brand' => $product->brand_data->link,
                        'type' => $product->type_data->link,
                        'language' => Language::getCurrentZone()
                    ))
                    ?>"><?= $product->fullname ?>
                    </a>
                </div>

                <br />

                <small>
                    <?php foreach ($characteristics as $catalog): ?>
                        <?php foreach ($catalog as $characteristic): ?>
                            <?= $characteristic['characteristic_name'] . ": " . $characteristic['value'] . PHP_EOL; ?><br />
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </small>
            </div>
        </div>

        <div class="col s12">
            <div class="row tttt">
                <div class="col s3 m1 l1 nav-arrow">
                    <?php if ($currentImage->prev_url != null): ?>
                        <a title="<?php echo Yii::t("gallery", 'Предыдущее фото') ?>" href="<?php echo $currentImage->prev_url ?>#gallery" class="button_prev"><i class="mdi-hardware-keyboard-arrow-left medium"></i></a>
                    <?php endif; ?>
                </div>
                <div class="col s6 m10 l10" id="slider">
                    <div class="slider-wrapper">
                        <div itemscope="" itemtype="http://schema.org/ImageObject" class="image-big" id="gallery">
                            <span itemprop="name" style="display: none"><?= $currentImage->image_data->title ?></span>
                                <?= $currentImage->image_data->getHtml(NImages::SIZE_PRODUCT_BIG, null, ["itemprop" => "image"]); ?>

                                <?php if (!empty($currentImage->image_data->article) && !empty($currentImage->image_data->article->article_data(['cache' => 60 * 60, 'select' => 'title, description, link, id']))): ?>
                                <div class="article hide-on-med-and-down">
                                        <?php echo Yii::t("gallery", 'Фотография из') ?>
                                        <?php Yii::app()->sourceLanguage = 'en'; ?>
                                        <?php echo Yii::t("gallery", $currentImage->image_data->article->article_data->type) ?>: 
                                <?php Yii::app()->sourceLanguage = 'ru'; ?>
                                    <a itemprop="associatedArticle" href="<?php echo $currentImage->image_data->article->article_data->url ?>">
                                <?php echo $currentImage->image_data->article->article_data->title ?>
                                    </a>
                                    <p itemprop="description"><?php echo $currentImage->image_data->article->article_data->description ?></p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <div class="col s3 m1 l1 nav-arrow">
                    <?php if ($currentImage->next_url != null): ?>
                        <a title="<?php echo Yii::t("gallery", 'Следующее фото') ?>" href="<?php echo $currentImage->next_url ?>#gallery" class="button_next"><i class="mdi-hardware-keyboard-arrow-right medium"></i></a>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col s12">

                <?php
                $this->widget('LinkPager', array(
                    'currentPage' => $pages->getCurrentPage(),
                    'itemCount' => $pages->getItemCount(),
                    'pageSize' => Gallery::GALLERY_SIZE,
                    'maxButtonCount' => 8,
                    'header' => '',
                    'firstPageLabel' => '<i class="mdi-av-fast-rewind"></i>',
                    'lastPageLabel' => '<i class="mdi-av-fast-forward"></i>',
                    'nextPageLabel' => '<i class="mdi-navigation-chevron-right"></i>',
                    'prevPageLabel' => '<i class="mdi-navigation-chevron-left"></i>',
                ));
                ?>
                
            </div>
        </div>
        <div class="row">
            <div class="col s12 gallery center-align">
                <?php foreach ($gallery as $item): ?>
                <div class="col s6 m4 l2 center-align valign-wrapper z-depth-1" itemtype="http://schema.org/ImageObject" style="display: table-cell !important; vertical-align: middle; text-align: center; height: 120px; overflow: hidden;">
                    <a itemtype="contentUrl" class="valign-wrapper" style="height: 112px;" href="<?= $item->self_url ?>#gallery">
                        <?=$item->image_data->getHtml(NImages::SIZE_PRODUCT_GALLERY, null, ["itemtype" => "thumbnail", 'class'=>'valign']); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <?php
                $this->widget('LinkPager', array(
                    'currentPage' => $pages->getCurrentPage(),
                    'itemCount' => $pages->getItemCount(),
                    'pageSize' => Gallery::GALLERY_SIZE,
                    'maxButtonCount' => 8,
                    'header' => '',
                    'firstPageLabel' => '<i class="mdi-av-fast-rewind"></i>',
                    'lastPageLabel' => '<i class="mdi-av-fast-forward"></i>',
                    'nextPageLabel' => '<i class="mdi-navigation-chevron-right"></i>',
                    'prevPageLabel' => '<i class="mdi-navigation-chevron-left"></i>',
                ));
                ?>
            </div>
        </div>

    </div>
</div>