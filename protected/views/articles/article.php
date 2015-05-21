<link type="text/css" rel="stylesheet" href="/assets/css/all.css"/>
<div class="row content-wrapper">
    <div class="col s12 m12 l8 content-left">
        <div class="content-in">
            <div class="ref-items">
                <div class="news" itemscope="" itemtype="http://schema.org/NewsArticle">
                    <h1><?php echo $article->title ?></h1>
                    <span class="date"><?php echo Yii::app()->dateFormatter->formatDateTime($article->created, 'long'); ?></span>
                    <?php if (!empty($widgets['related_products'])): ?>
                        <hr />
                        <h5><?= Yii::t("goods", 'Упомянутые аппараты') ?></h5>
                        <?php foreach ($widgets['related_products'] as $product): ?>
                            <ul class="ref-list">
                                <li>
                                    <ul>
                                        <li>
                                            <?php if (isset($product->primary_image)): ?>
                                                <a class="title" href="<?= $product->url ?>">
                                                    <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_LIST); ?>
                                                    <br /><strong><?= $product->fullname ?></strong>
                                                </a>
                                            <?php endif; ?>
                                        </li>
                                        <?php if ($product->getGalleryCount()): ?>
                                            <li>
                                                <a href="<?=
                                                Yii::app()->createAbsoluteUrl("gallery/product", [
                                                    'product' => $product->link,
                                                    'brand' => $product->brand_data->link,
                                                    'language' => Language::getCurrentZone(),
                                                ])
                                                ?>"><?php echo Yii::t("main", 'Фотогалерея'); ?></a>
                                            </li>
                                        <?php endif; ?>
                                        <!-- @todo: Добавить ссылки на articles-->
                                    </ul>
                                </li>
                            </ul>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div><!-- There was DIV -->
            </div>
            <div class="simple-content" itemprop="articleBody">
                <hr />
                <?= $article->content ?>
            </div>
            <div id="item-comments">
                <hr />
                <?php
                include_once(Yii::getPathOfAlias('ext') . '/cackle_comments.php');
                $channel = Yii::app()->request->requestUri; 
                $a = new Cackle(true,$channel);
                ?>
            </div>
            <div class="table">
                <hr />
                <!-- Compare table-->
            </div> <!-- END of div class=table -->
            <div class="video-content">
                <?php if (!empty($widgets['related_videos'])): ?>
                    <?php foreach ($widgets['related_videos'] as $video): ?>
                        <h5><?= $video->title ?></h5>
                        <div itemprop="video" class="video-container" itemscope="" itemtype="http://schema.org/VideoObject">
                            <div style="display: none;">
                                <a itemprop="url" rel="nofollow" href="http://www.youtube.com/watch?v=YhHZq_Ta6a8"></a>
                                <span itemprop="name"><?= $video->title ?></span>
                                <span itemprop="description"><?= $video->description ?></span>
                                <span itemprop="duration"><?= $video->duration ?></span>
                                <meta itemprop="isFamilyFriendly" content="true"/>
                                <meta itemprop="uploadDate" content="<?= $video->date_added ?>"/>
                                <span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
                                    <img itemprop="contentUrl" src="<?= $video->thumbnail ?>"/>
                                    <meta itemprop="width" content="480"/>
                                    <meta itemprop="height" content="360"/>
                                </span>
                            </div>

                            <?= $video->getTemplate(); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div> <!-- END of VIDEO Content -->
            <?php foreach ($article->related as $type=>$relatedNews):?>
            <?php if (!empty($relatedNews)):?>
            <div>
                <hr />
                <h3><?php 
                    Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en';
                    echo Yii::t('articles', $type . '-related');
                    Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en'; 
                ?></h3>
                <table style="margin-bottom: 15px;">
                    <tbody>
                        <?php foreach ($relatedNews as $news):?>

                        <tr>
                            <td style="padding-top: 12px;" itemscope itemtype="http://schema.org/NewsArticle">
                                <span itemprop="datePublished" style="display: none;"><?php echo $news->created?></span>
                                <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$news->type,'link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getZoneForLang($news->lang)]);?>" itemprop="url">
                                    <h3 itemprop="name"><?php echo $news->title ?></h3>
                                </a>
                                <small>
                                    <?php echo Yii::app()->dateFormatter->formatDateTime($news->created, 'long'); ?>
                                </small>
                                <?php if (!empty($news->preview_image->image_data)) :?>
                                <a style="width: 130px; max-height: 130px; margin: 7px; text-align: center; display: table-cell; vertical-align: middle; float: left;" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$news->type,'link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getCurrentZone()]); ?>">
                                    <?php echo $news->preview_image->image_data->getHtml(NImages::SIZE_ARTICLE_PREVIEW);?>
                                </a>
                                <?php endif; ?>
                                <p itemprop="description"><?php echo $news->description ?>...</p>
                                <?php if (!empty($news->preview_image)) :?>
                                <div style="clear: both;"></div>
                                <?php endif;?>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <?php endif;?>
            <?php endforeach;?>
            <div>
                <?php if (!empty($widgets['related_trends'])): ?>
                    <?php foreach ($widgets['related_trends'] as $trend): ?>
                        <div class="video-container"><iframe style="border: 0px;" width="500" height="290" scrolling="no" src="<?= $trend ?>"></iframe></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div>
                <ul class="tags">
                    <?php foreach ($article->tags as $tag): ?>
                        <?php if (!empty($tag->tag_data)): ?>
                            <li>
                                <a rel="tag" href="<?=
                                Yii::app()->createAbsoluteUrl("tag/news", [
                                    'language' => Language::getCurrentZone(),
                                    'type' => $tag->tag_data->type,
                                    'tag' => $tag->tag_data->link,
                                ])
                                ?>"><?= $tag->tag_data->name ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="source">
                <span>
                    <?= Yii::t("main", "Эта новость") ?>: 
                    <a href="<?= Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getCurrentZone()]); ?>" itemprop="url"><?= Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getCurrentZone()]); ?></a>
                </span>
                <span>
                    <br/>
                    <?= Yii::t("main", "Источник") ?>: 
                    <a target="_blank" href="<?php echo $article->source_url ?>"><?php echo $article->source_url ?></a>
                </span>
                <br/>
            </div>

        </div> <!-- End of CONTENT-IN -->

    </div> <!-- End of CONTENT-LEFT col s12 m12 l8-->

    <div class="col s12 m12 l4 content-right">

        <div id="floatingToolbar-right">
            <div class="widget-inner">
                <div class="widget-top">
                    <?= Yii::t("goods", 'Упомянутые аппараты') ?>
                    <i class="mdi-av-play-circle-outline right"></i>
                    <i class="mdi-action-alarm-add right"></i>
                    <i class="mdi-action-info-outline right"></i>
                </div>
                <div class="widget-middle">
                    <ul>
                        <?php foreach ($widgets['related_products'] as $product): ?>
                            <li>
                                <div>
                                    <div>
                                        <?php if (isset($product->primary_image)): ?>
                                            <a class="title" href="<?= $product->url ?>">
                                                <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_WIDGET); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <a href="<?= $product->url ?>"><?= $product->fullname ?></a>
                                    </div>
                                </div>

                                <div>
                                    <div>0</div>
                                    <div>0</div>
                                </div>

                            </li>
                        <?php endforeach; ?>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
                <div class="widget-bottom">
                    <!-- bottom -->
                </div>
            </div>
            <div class="center-align">
                <!-- materialize_analogindex_right -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-7891165885018162"
                     data-ad-slot="3509091535"
                     data-ad-format="auto"></ins>
                <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>	

    </div>
</div>