<div class="content">
    <div class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- analogindex_top -->
        <ins class="adsbygoogle"
                     style="display:inline-block;width:728px;height:90px"
                     data-ad-client="ca-pub-7891165885018162"
                     data-ad-slot="3491233138"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    <div class="row">
        <div class="col s12">
            <h1><?php echo $article->title ?></h1>
            <span class="date" style="font-style: italic;"><?php echo Yii::app()->dateFormatter->formatDateTime($article->created, 'long'); ?></span>
            <hr />
            <h3><?php echo Yii::t("goods", 'Упомянутые аппараты') ?></h3>
            <div class="row">
                <?php foreach ($widgets['related_products'] as $product): ?>
                    <div class="col s2">
                        <strong><a href="<?php
                            echo Yii::app()->createAbsoluteUrl("site/goods", [
                                'link' => $product->link,
                                'brand' => $product->brand_data->link,
                                'type' => $product->type_data->link,
                                'language' => Language::getCurrentZone(),
                            ])
                            ?>">
                                       <?php echo $product->brand_data->name ?> <?php echo $product->name ?>
                            </a>
                        </strong>
                        <?php if (!empty($product->primary_image)): ?>
                            <a href="<?php
                            echo Yii::app()->createAbsoluteUrl("site/goods", [
                                'link' => $product->link,
                                'brand' => $product->brand_data->link,
                                'type' => $product->type_data->link,
                                'language' => Language::getCurrentZone(),
                            ])
                            ?>">
                                   <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_LIST, null, ['class' => 'responsive-img']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($count = $product->getGalleryCount()): ?>
                            <br /><a href="<?php
                            echo Yii::app()->createAbsoluteUrl("gallery/product", [
                                'product' => $product->link,
                                'brand' => $product->brand_data->link,
                                'language' => Language::getCurrentZone(),
                            ])
                            ?>"><?php echo Yii::t("main", 'Фотогалерея'); ?></a>
                                 <?php endif; ?>


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
                        <?php foreach ($types as $type): ?>
                            <?php if ($count = GoodsArticles::model()->cache(60 * 60)->getCount($product->id, $type->link)): ?>
                                <br /><a itemprop="url"
                                         href="<?php
                                         echo Yii::app()->createAbsoluteUrl("articles/list", array(
                                             "product" => $product->link,
                                             "type" => $type->link,
                                             "brand" => $product->brand_data->link,
                                             "language" => Language::getCurrentZone(),
                                         ))
                                         ?>"><?php echo $type->name ?></a>
                                     <?php endif; ?>
                                 <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr />
            <div class="flow-text">
                <?php echo $article->content ?>
            </div>
            <hr />
            <style>
                .mc-comment-count {
                    display: none !important;
                }
            </style>
            <h3><?= Yii::t('main', 'Комментарии') ?></h3>
            <?php echo $this->renderPartial("_comments") ?>
            <hr />

            <?php if (!empty($widgets['related_trends'])): ?>
                <h3><?= Yii::t('main', 'Тренды') ?></h3>
                <?php foreach ($widgets['related_trends'] as $items): ?>
                    <br /><iframe style="border: 0;" width="500" height="290" scrolling="no" src="http://www.google.com/trends/fetchComponent?hl=<?= Yii::app()->language ?>&q=<?php echo implode(",", $items) ?>&cmpt=q&content=1&cid=TIMESERIES_GRAPH_0&export=5&w=500&h=330&date=today+12-m"></iframe>
                <?php endforeach; ?>
                <hr />
            <?php endif; ?>

            <?php if (!empty($widgets['related_videos'])): ?>
                <h3><?= Yii::t('main', 'Видео'); ?></h3>
                <?php foreach ($widgets['related_videos'] as $video): ?>
                    <div class="center-align">
                        <?php if (empty($video->title)): ?>
                            <h4><?= Yii::t("models", "Видео обзор"); ?> <?= $video->goods_data->brand_data->name . " " . $video->goods_data->name ?></h4>
                        <?php else: ?>
                            <h4><?= $video->title ?></h4>
                        <?php endif; ?>
                        <div itemprop="video" class="video-container" itemscope itemtype="http://schema.org/VideoObject">
                            <div style="display: none;">
                                <a itemprop="url" rel="nofollow" href="http://www.youtube.com/watch?v=<?= $video->link ?>"></a>
                                <span itemprop="name"><?= $video->title ?></span>
                                <span itemprop="description"><?= $video->description ?></span>
                                <meta itemprop="duration" content="<?= $video->duration ?>"/>
                                <meta itemprop="isFamilyFriendly" content="true"/>
                                <meta itemprop="uploadDate" content="<?= $video->date_added ?>"/>
                                <span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
                                    <img itemprop="contentUrl" src="<?= $video->thumbnail ?>"/>
                                    <meta itemprop="width" content="540"/>
                                    <meta itemprop="height" content="315"/>
                                </span>
                            </div>
                            <?= $video->getTemplate(); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr />
            <?php endif; ?>

            <?php if (!empty($widgets['related_compare'])): ?>
                <h3><?= Yii::t('goods', 'Упомянутые аппараты'); ?></h3>
                <?php foreach ($widgets['related_compare']['index'] as $index => $goods): ?>
                    <table class="responsive-table striped bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <?php foreach ($goods as $product): ?>
                                    <th style="text-align: center; vertical-align: middle;">
                                        <a href="<?php
                                        echo Yii::app()->createAbsoluteUrl("site/goods", [
                                            'link' => $product['model']->link,
                                            'brand' => $product['model']->brand_data->link,
                                            'type' => $product['model']->type_data->link,
                                            'language' => Language::getCurrentZone(),
                                        ])
                                        ?>">
                                               <?php if (isset($product['model']->primary_image)): ?>
                                                   <?php echo $product['model']->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_LIST); ?>
                                                <br />
                                            <?php endif; ?>
                                            <?= $product['name'] ?>
                                        </a>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($widgets['related_compare']['data'][$index] as $characteristic => $values): ?>
                                <tr>
                                    <th><?= $characteristic ?></th>
                                    <?php foreach ($values as $value): ?>
                                        <td><?= $value['value'] ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($article->getRelatedArticles(Articles::TYPE_NEWS))): ?>
                <h3><?=Yii::t("goods", "Новости по теме")?></h3>
                <div class="row">
                    <?php foreach ($article->getRelatedArticles(Articles::TYPE_NEWS) as $news): ?>
                        <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $news->type, 'link' => $news->link, 'id' => $news->id, 'language' => Language::getZoneForLang($news->lang)]); ?>" itemprop="url">
                            <h5 itemprop="name"><?php echo $news->title ?></h5>
                        </a>
                        <small>
                            <?php echo Yii::app()->dateFormatter->formatDateTime($news->created, 'long'); ?>
                        </small>
                        <?php if (!empty($news->preview_image->image_data)) : ?>
                            <a style="width: 130px; max-height: 130px; margin: 7px; text-align: center; display: table-cell; vertical-align: middle; float: left;" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $news->type, 'link' => $news->link, 'id' => $news->id, 'language' => Language::getCurrentZone()]); ?>">
                                <?php echo $news->preview_image->image_data->getHtml(NImages::SIZE_ARTICLE_PREVIEW); ?>
                            </a>
                        <?php endif; ?>
                        <p><?php echo $news->description ?>...</p>
                        <?php if (!empty($news->preview_image)) : ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <hr />
            <?php endif; ?>
                
            <?php if (!empty($article->getRelatedArticles(Articles::TYPE_REVIEW))): ?>
                <h3><?=Yii::t("goods", "Отзывы по теме")?></h3>
                <div class="row">
                    <?php foreach ($article->getRelatedArticles(Articles::TYPE_REVIEW) as $news): ?>
                        <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $news->type, 'link' => $news->link, 'id' => $news->id, 'language' => Language::getZoneForLang($news->lang)]); ?>" itemprop="url">
                            <h5 itemprop="name"><?php echo $news->title ?></h5>
                        </a>
                        <small>
                            <?php echo Yii::app()->dateFormatter->formatDateTime($news->created, 'long'); ?>
                        </small>
                        <?php if (!empty($news->preview_image->image_data)) : ?>
                            <a style="width: 130px; max-height: 130px; margin: 7px; text-align: center; display: table-cell; vertical-align: middle; float: left;" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $news->type, 'link' => $news->link, 'id' => $news->id, 'language' => Language::getCurrentZone()]); ?>">
                                <?php echo $news->preview_image->image_data->getHtml(NImages::SIZE_ARTICLE_PREVIEW); ?>
                            </a>
                        <?php endif; ?>
                        <p><?php echo $news->description ?>...</p>
                        <?php if (!empty($news->preview_image)) : ?>
                            <div style="clear: both;"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <hr />
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <!-- analogindex_bottom_responsive -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-7891165885018162"
             data-ad-slot="2503694332"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
</div>
