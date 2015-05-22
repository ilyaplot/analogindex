<link type="text/css" rel="stylesheet" href="/assets/css/all.css"/>
<div class="row content-wrapper">
    <div class="col s12 m12 l8 content-left">
        <div id="floatingToolbar">
            <div class="float-h">
                <span><?php echo $product->type_data->name->item_name ?> <?= $product->fullname ?></span>
                <h1 style="display: none;"><?= $product->type_data->name->item_name ?> <?= $product->fullname ?></h1>
            </div>
            <div class="float-rate">
                <span><!--Рейтинги: 0--></span>
            </div>
        </div>
        <div class="content-in">
            <div class="wpcontent_leftMenu">
                <menu id="fixedLeft-menu" class="hide-on-med-and-down">
                    <li class="active" data-id="item-main">
                        <a class="menu_lFix-item1" href="#item-main">
                            <i class="small mdi-maps-satellite"></i>
                        </a>
                    </li>

                    <li data-id="item-specifications">
                        <a class="menu_lFix-item2" href="#item-specifications">
                            <i class="small mdi-action-list"></i>
                        </a>
                    </li>

                    <li data-id="item-news">
                        <a class="menu_lFix-item3" href="#item-news">
                            <i class="small mdi-av-my-library-books"></i>
                        </a>
                    </li>

                    <li data-id="item-review">
                        <a class="menu_lFix-item5" href="#item-review">
                            <i class="small mdi-communication-message"></i>
                        </a>
                    </li>

                    <li data-id="item-opinion">
                        <a class="menu_lFix-item4" href="#item-opinion">
                            <i class="small mdi-action-receipt"></i>
                        </a>
                    </li>


                    <li data-id="item-howto">
                        <a class="menu_lFix-item6" href="#item-howto">
                            <i class="small mdi-action-settings"></i>
                        </a>
                    </li>

                    <li data-id="item-videos">
                        <a class="menu_lFix-item7" href="#item-videos">
                            <i class="small mdi-maps-local-movies"></i>
                        </a>
                    </li>

                    <li data-id="item-comments">
                        <a class="menu_lFix-item8" href="#item-comments">
                            <i class="small mdi-communication-forum"></i>
                        </a>
                    </li>

                </menu>
            </div>

            <!-- ГАЛЕРЕЯ НАЧАЛО ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <div class="infoGoodItem-wp-photos" id="item-main">
                <div class="infoGoodItem-wp-photos_main" id="photo_main">
                    <?php if (!empty($firstImage)): ?>
                        <a class="big_image" data-lightbox="roadtrip" data-title="<?= $product->fullname ?>" href="<?php echo $firstImage->createUrl(NImages::SIZE_PRODUCT_BIG); ?>">
                            <?php echo $firstImage->getHtml(NImages::SIZE_PRODUCT_PREVIEW); ?>
                        </a>
                    <?php else: ?>
                        <img style="width: 450px; height: auto;" src="/assets/img/no_photo.png">
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
                <div class="infoGoodItem-wp-photos_all">
                    <?php foreach ($images as $image): ?>
                        <div class="slide" itemscope="" itemtype="http://schema.org/ImageObject">
                            <a rel="lightbox" title="<?= $product->fullname ?>"  href="<?php echo $image->createUrl(NImages::SIZE_PRODUCT_BIG); ?>" data-lightbox="roadtrip">
                                <?=
                                $image->getHtml(NImages::SIZE_PRODUCT_LIST, null, [
                                    'class' => 'preview',
                                    'data-preview' => $image->createUrl(NImages::SIZE_PRODUCT_PREVIEW),
                                    'itemprop' => 'thumbnail'
                                ]);
                                ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="clear"></div>

                <?php if ($product->gallery_count > 0): ?>
                    <br/><a href="<?=
                    Yii::app()->createAbsoluteUrl("gallery/product", [
                        'product' => $product->link,
                        'brand' => $product->brand_data->link,
                        'language' => Language::getCurrentZone(),
                    ])
                    ?>"><?= Yii::t("main", 'Фотогалерея'); ?> (<?= $product->gallery_count ?>)</a>
                        <?php endif; ?>

            </div>
            <!-- ГАЛЕРЕЯ КОНЕЦ ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

            <!-- Описание товара++++++++++++++++++++++++++++++++++ -->
            <div class="infoGoodItem-wp-settings" id="item-specifications">
                <section class="infoGoodItem_content">
                    <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Характеристики') ?></h3>
                    <div class="item-set-bl">
                        <?php if (!empty($product->synonims)): ?>
                            <div class="item-set-bl_title"><?php echo Yii::t('goods', 'Другие наименования') ?></div>
                            <div class="item-set-bl_lineText clr">
                                <?php $synonims = [] ?>
                                <?php foreach ($product->synonims as $synonim): ?>
                                    <?php if ($synonim->visibled) : ?>
                                        <?php $synonims[] = $product->brand_data->name . " " . $synonim->name ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <div class="flRight">
                                    <span>
                                        <?php echo implode(", ", $synonims) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php foreach ($characteristics as $catalog => $items): ?>
                            <div class="item-set-bl_title"><?php echo $catalog; ?></div>
                            <?php foreach ($items as $characteristic): ?>
                                <div class="item-set-bl_lineText clr">
                                    <div class="flLeft"><span><?php echo $characteristic['characteristic_name']; ?></span></div>
                                    <div class="flRight"><span>
                                            <?php echo $characteristic['value'] ?>
                                        </span></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
            <!-- Описание товара КОНЕЦ+++++++++++++++++++++++++ -->

            <!-- НОВОСТИ ++++++++++++++++++++++++ -->
            <?php foreach ($relatedArticles as $type => $articles): ?>
                <?php if (empty($articles)) continue; ?>
                <div class="infoGoodItem-wp-news" id="item-<?= $type ?>">
                    <section class="infoGoodItem_content">
                        <?php Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en'; ?>
                        <h3 class="infoGoodItem-infoTitle"><?= $relatedCounts[$type]['title'] ?></h3>
                        <?php Yii::app()->sourceLanguage = (Yii::app()->language == 'en') ? 'ru' : 'en'; ?>
                        <div id="GoodItem-<?= $type ?>">
                            <div class="view_bl" >
                                <?php foreach ($articles as $article): ?>
                                    <div itemscope="" itemtype="http://schema.org/NewsArticle">
                                        <div class="view_bl-head clr">
                                            <div class="view_bl-head-l flRight">
                                                <div class="view_bl-date right">
                                                    <?= Yii::app()->dateFormatter->formatDateTime($article->created, 'long'); ?>
                                                </div>

                                                <span itemprop="datePublished" style="display: none;"><?= $article->created ?></span>
                                            </div>
                                        </div>
                                        <div class="view_bl-textView">
                                            <a href="<?= Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getZoneForLang($article->lang)]); ?>" itemprop="url">
                                                <h4 itemprop="name"><?= $article->title ?></h4>
                                            </a>
                                            <?php if (!empty($article->preview_image->image_data)) : ?>
                                                <a class="news-preview" href="<?= Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getZoneForLang($article->lang)]); ?>">
                                                    <img title="<?= $article->title ?>" itemprop="image" class="news_preview" src="<?= $article->preview_image->image_data->createUrl(NImages::SIZE_ARTICLE_PREVIEW); ?>" alt="<?= $article->title ?>">                                            
                                                </a>
                                            <?php endif; ?>
                                            <span itemprop="description"><?= $article->description ?></span>...
                                            <div style="clear: both;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <a href="<?php echo Yii::app()->createUrl("articles/list", ['type' => $type, 'brand' => $product->brand_data->link, 'product' => $product->link, 'language' => Language::getCurrentZone()]); ?>">
                                    <?= $relatedCounts[$type]['more'] ?> (<?= $relatedCounts[$type]['count'] ?>)...
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            <?php endforeach; ?>
            <!-- НОВОСТИ КОНЕЦ ++++++++++++++++++++++++ -->

            <!-- VIDEO +++++++++++++++++++++++++++++++++++++  -->
            <?php if (!empty($relatedVideos)): ?>
                <div class="infoGoodItem-wp-video" id="item-videos">
                    <section class="infoGoodItem_content views-list">
                        <h3 class="infoGoodItem-infoTitle"><?=Yii::t("main", "Видео");?></h3>
                        <?php foreach ($relatedVideos as $video): ?>
                        <?php if (empty($video->title)) continue; ?>
                            <h5><?= $video->title ?></h5>
                            <div class="video-container" itemscope="" itemtype="http://schema.org/VideoObject">
                                <div style="display: none;">
                                    <a itemprop="url" rel="nofollow" href="http://www.youtube.com/watch?v=YhHZq_Ta6a8"></a>
                                    <span itemprop="name"><?= $video->title ?></span>
                                    <span itemprop="description"><?= !empty($video->description) ? $video->description : 'video description' ?></span>
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
                    </section>
                </div>
            <?php endif; ?>

            <div id="item-comments">
                <hr />
                <?php
                include_once(Yii::getPathOfAlias('ext') . '/cackle_comments.php');
                $channel = Yii::app()->request->requestUri;
                $a = new Cackle(true, $channel);
                ?>
            </div>
            <!-- VIDEO КОНЕЦ +++++++++++++++++++++++++++++++++++++++++ -->

        </div> <!-- End of CONTENT-IN -->
    </div> <!-- End of CONTENT-LEFT col s12 m12 l8-->
    
    <div class="col s12 m12 l4 content-right">
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
        <!--
        <div id="floatingToolbar-right">
            <div class="widget-inner">
                <div class="widget-top">
                    Смартфоны
                    <i class="mdi-av-play-circle-outline right"></i>
                    <i class="mdi-action-alarm-add right"></i>
                    <i class="mdi-action-info-outline right"></i>
                </div>
                <div class="widget-middle">
                    <ul>
                        <li>
                            <div>
                                <div>
                                    <a href="#">
                                        <img title="1" src="images/zte-blade-l3.gif" alt="1" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#">ZTE Blade L3</a>
                                </div>
                            </div>

                            <div>
                                <div>0</div>
                                <div>0</div>
                            </div>

                        </li>
                        <li>
                            <div>
                                <div>
                                    <a href="#">
                                        <img title="1" src="images/zopo.png" alt="1" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#">ZTE Traderz 777</a>
                                </div>
                            </div>

                            <div>
                                <div>0</div>
                                <div>0</div>
                            </div>

                        </li>
                        <li>
                            <div>
                                <div>
                                    <a href="#">
                                        <img title="1" src="images/zte-blade-l3.gif" alt="1" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#">ZTE Blade L3</a>
                                </div>
                            </div>

                            <div>
                                <div>0</div>
                                <div>0</div>
                            </div>

                        </li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
                <div class="widget-bottom">
                    Bottom
                </div>
            </div>
            <div class="widget-inner">
                <div class="widget-top">
                    Смартфоны
                    <i class="mdi-av-play-circle-outline right"></i>
                    <i class="mdi-action-alarm-add right"></i>
                    <i class="mdi-action-info-outline right"></i>
                </div>
                <div class="widget-middle">
                    <ul>
                        <li>
                            <div>
                                <div>
                                    <a href="#">
                                        <img title="1" src="images/zte-blade-l3.gif" alt="1" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#">ZTE Blade L3</a>
                                </div>
                            </div>

                            <div>
                                <div>0</div>
                                <div>0</div>
                            </div>

                        </li>
                        <li>
                            <div>
                                <div>
                                    <a href="#">
                                        <img title="1" src="images/zopo.png" alt="1" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#">ZTE Traderz 777</a>
                                </div>
                            </div>

                            <div>
                                <div>0</div>
                                <div>0</div>
                            </div>

                        </li>
                        <li>
                            <div>
                                <div>
                                    <a href="#">
                                        <img title="1" src="images/zte-blade-l3.gif" alt="1" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#">ZTE Blade L3</a>
                                </div>
                            </div>

                            <div>
                                <div>0</div>
                                <div>0</div>
                            </div>

                        </li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
                <div class="widget-bottom">
                    Bottom
                </div>
            </div>

        </div>	-->	    	
    </div>

</div>
<!--  Конец контента -->
