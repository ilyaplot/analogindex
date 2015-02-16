<!--<?php echo $product->id?>-->
<script type="text/javascript">
    /**
     * Возвращает строку для подписи к фото на нужном языке
     * @returns string
     */
    function AnalogindexLightboxLabel(a, b) {
        return"<?php echo Yii::t("main", "Фотография ") ?>" + a + " <?php echo Yii::t("main", " из ") ?> " + b;
    }
</script>
<script src="/assets/js/lightbox.js"></script>
<link href="/assets/css/lightbox.css" rel="stylesheet" />
<?php
$this->widget('application.widgets.BreadcrumbsWidget.BreadcrumbsWidget',['items'=>[
    [
        'url'=>'http://analogindex.'.Language::getCurrentZone().'/',
        'title'=>Yii::t('main', 'Главная'),
    ],
    [
        'url'=>Yii::app()->createAbsoluteUrl("site/type", array("type" => $type->link, "language" => Language::getCurrentZone())),
        'title'=>$type->name->name,
    ],
    [
        'url'=>Yii::app()->createAbsoluteUrl("site/brand", array(
            "link" => $brand->link,
            "language" => Language::getCurrentZone(),
            "type" => $type->link,
        )),
        'title'=>$brand->name,
    ],
    [
        'url'=>Yii::app()->createAbsoluteUrl("site/goods", array(
            "link" => $product->link,
            "brand" => $brand->link,
            "language" => Language::getCurrentZone(),
            "type" => $type->link,
        )),
        'title'=>$brand->name.' '.$product->name,
    ],
]]);
?>
<div class="wp_col_fix clr">
    <div class="col-infoGoods">
        <div class="infoGoodItem">
            <div class="infoGoodItem-title">
                <div class="infoGoodItem-title-1">
                    <h1><?php echo $product->type_data->name->item_name?> <?php echo $brand->name ?> <?php echo $product->name ?></h1>
                </div>
                <div class="infoGoodItem-title-2 clr">
                    <div class="flLeft">
                        <span class="infoGoodItem-title-2_name"><?php echo Yii::t('goods', 'Рейтинги') ?>:</span>
                        <ul class="infoGoodItem-title-2_list">
                            <li class="item1"></li>
                            <li class="item2"><?php echo $product->getRanking("antutu", 1, '%'); ?></li>
                            <li class="item<?php echo (!$ratingDisabled) ? 0 : 3 ?>" id="ratingResult">
                                <?php if (!$ratingDisabled) : ?>

                                    <?php
                                    $this->widget('application.widgets.StarRating', array(
                                        'name' => 'ratingAjax',
                                        'value' => isset($product->rating->value) ? round($product->rating->value * 2) : 0,
                                        'callback' => '
                                            function(){
                                                $.ajax({
                                                    type: "POST",
                                                    url: "' . Yii::app()->createUrl('ajax/ratingGoods', array("goods" => $product->id)) . '",
                                                    data: "' . Yii::app()->request->csrfTokenName . '=' . Yii::app()->request->getCsrfToken() . '&rate=" + $(this).val(),
                                                    success: function(msg){
                                                        $("#ratingResult").html(msg);
                                                        $(".infoGoodItem-title-2_list > .item0").removeClass("item0").addClass("item3");
                                                    }
                                                })
                                            }'
                                    ));
                                    ?> <?php echo isset($product->rating->value) ? "(" . round($product->rating->value, 1) . ")" : ''; ?>
                                <?php else: ?>
                                    <?php echo isset($product->rating->value) ? round($product->rating->value, 1) : ''; ?>
                                <?php endif; ?>
                            </li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <div class="flRight">
                        <script type="text/javascript">
    $(document).ready(function () {
        $('.item_photos_all .slide img').on('click', function () {
            $('.infoGoodItem-wp-photos_main img').attr('src', $(this).attr('src'));
        });
    });

    (function () {
        if (window.pluso)
            if (typeof window.pluso.start == "function")
                return;
        if (window.ifpluso == undefined) {
            window.ifpluso = 1;
            var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
            s.type = 'text/javascript';
            s.charset = 'UTF-8';
            s.async = true;
            s.src = ('https:' == window.location.protocol ? 'https' : 'http') + '://share.pluso.ru/pluso-like.js';
            var h = d[g]('body')[0];
            h.appendChild(s);
        }
    })();</script>
                        <div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=06" data-services="vkontakte,odnoklassniki,facebook,twitter,google" style="margin: 6px 0 0;"></div>
                    </div>
                </div>
            </div>
            <div class="infoGoodItem-wpcontent">
                <div class="wpcontent_leftMenu">
                    <menu id="fixedLeft-menu">
                        <li class="active"><a class="menu_lFix-item1" href="#item1"></a></li>
                        <li><a class="menu_lFix-item2" href="#item2"></a></li>
                        <li><a class="menu_lFix-item3" href="#item3"></a></li>
                        <li><a class="menu_lFix-item4" href="#item4"></a></li>
                        <li><a class="menu_lFix-item5" href="#item5"></a></li>
                        <li><a class="menu_lFix-item5" href="#item6"></a></li>
                        <li><a class="menu_lFix-item5" href="#item7"></a></li>
                    </menu>
                </div>
                <div class="wpcontent">
                    <div class="infoGoodItem-wp-photos" id="item1">
                        <?php if (!$product->gallery_count) : ?>
                            <div class="infoGoodItem-wp-photos_main">
                                <img style="width: 450px; height: auto;" src="/assets/img/no_photo.png">
                            </div>
                        <?php else: ?>
                            <?php $this->renderPartial("_goods_many_images", array("product" => $product, "brand" => $brand)) ?>
                        <?php endif; ?>
                        <div class="clear"></div>
                        <br />
                        <?php if($product->gallery_count > 0):?>
                         <a href="<?php echo Yii::app()->createAbsoluteUrl("gallery/product",[
                            'product'=>$product->link,
                            'brand'=>$product->brand_data->link,
                            'language'=>Language::getCurrentZone(),
                        ])?>"><?php echo Yii::t("main", 'Фотогалерея');?> (<?php echo $product->getGalleryCount() ?>)</a>
                        <?php endif;?>
                       
                    </div>
                    <?php $this->renderPartial("_goods_characteristics", array("product" => $product)) ?>
                    <div class="infoGoodItem-wp-news" id="item3">
                        <section class="infoGoodItem_content">
                            <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Новости') ?></h3>
                            <div id="GoodItem-news">
                                    <?php foreach ($news as $item):?>
                                    <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
                                        <div class="view_bl-head clr">
                                            <div class="view_bl-head-l flRight">
                                                <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
                                                <span itemprop="datePublished" style="display: none;"><?php echo $item->created?></span>
                                            </div>
                                        </div>
                                        <div class="view_bl-textView">
                                            <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]);?>" itemprop="url">
                                                <h2 itemprop="name"><?php echo  $item->title ?></h2>
                                            </a>
                                            <?php if (!empty($item->preview_image)) :?>
                                            <a class="news-preview" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>">
                                                <img itemprop="image" src="<?php echo $item->preview_image->getPreviewUrl()?>" class="news_preview" 
                                                     alt="<?php echo $item->preview_image->alt?>"/>
                                            </a>
                                            <?php endif; ?>
                                            <span itemprop="description"><?php echo $item->description?></span>...
                                            <?php if (!empty($item->preview_image)) :?>
                                            <div style="clear: both;"></div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php if ($news_count): ?>
                                <a href="<?php echo Yii::app()->createUrl("articles/list", ['type'=>'news','brand'=>$brand->link, 'product'=>$product->link, 'language'=>  Language::getCurrentZone()]);?>">
                                    <?php echo Yii::t("goods","Читать все новости");?> (<?php echo $news_count?>)...
                                </a>
                                <?php endif; ?>
                            </div>
                        </section>
                    </div>
                    <div class="infoGoodItem-wp-comments" id="item5">
                        <section class="infoGoodItem_content view-title">
                            <div class="infoGoodItem_title-2 clr">
                                <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Обзоры') ?></h3></div> 
                            </div>
                        </section>
                        
                        <section class="views-list">
                            <?php foreach ($reviews as $item):?>
                                <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
                                    <div class="view_bl-head clr">
                                        <div class="view_bl-head-l flRight">
                                            <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
                                            <span itemprop="datePublished" style="display: none;"><?php echo $item->created?></span>
                                        </div>
                                    </div>
                                    <div class="view_bl-textView">
                                        <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]);?>" itemprop="url">
                                            <h2 itemprop="name"><?php echo  $item->title ?></h2>
                                        </a>
                                        <?php if (!empty($item->preview_image)) :?>
                                        <a class="news-preview" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>">
                                            <img itemprop="image" src="<?php echo $item->preview_image->getPreviewUrl()?>" class="news_preview" 
                                                 alt="<?php echo $item->preview_image->alt?>"/>
                                        </a>
                                        <?php endif; ?>
                                        <span itemprop="description"><?php echo $item->description?></span>...
                                        <?php if (!empty($item->preview_image)) :?>
                                        <div style="clear: both;"></div>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php if ($reviews_count): ?>
                            <a href="<?php echo Yii::app()->createUrl("articles/list", ['type'=>'review','brand'=>$brand->link, 'product'=>$product->link, 'language'=>  Language::getCurrentZone()]);?>">
                                <?php echo Yii::t("goods","Читать все обзоры");?> (<?php echo $reviews_count?>)...
                            </a>
                            <?php endif; ?>
                        </section>
                    </div>
                    <div class="infoGoodItem-wp-comments" id="item5">
                        <section class="infoGoodItem_content view-title">
                            <div class="infoGoodItem_title-2 clr">
                                <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Отзывы') ?></h3></div> 
                            </div>
                        </section>
                        
                        <section class="views-list">
                            <?php foreach ($opinions as $item):?>
                                <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
                                    <div class="view_bl-head clr">
                                        <div class="view_bl-head-l flRight">
                                            <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
                                            <span itemprop="datePublished" style="display: none;"><?php echo $item->created?></span>
                                        </div>
                                    </div>
                                    <div class="view_bl-textView">
                                        <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]);?>" itemprop="url">
                                            <h2 itemprop="name"><?php echo  $item->title ?></h2>
                                        </a>
                                        <?php if (!empty($item->preview_image)) :?>
                                        <a class="news-preview" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>">
                                            <img itemprop="image" src="<?php echo $item->preview_image->getPreviewUrl()?>" class="news_preview" 
                                                 alt="<?php echo $item->preview_image->alt?>"/>
                                        </a>
                                        <?php endif; ?>
                                        <span itemprop="description"><?php echo $item->description?></span>...
                                        <?php if (!empty($item->preview_image)) :?>
                                        <div style="clear: both;"></div>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php if ($opinions_count): ?>
                            <a href="<?php echo Yii::app()->createUrl("articles/list", ['type'=>'opinion','brand'=>$brand->link, 'product'=>$product->link, 'language'=>  Language::getCurrentZone()]);?>">
                                <?php echo Yii::t("goods","Читать все отзывы");?> (<?php echo $opinions_count?>)...
                            </a>
                            <?php endif; ?>
                        </section>
                    </div>
                    <div class="infoGoodItem-wp-comments" id="item5">
                        <section class="infoGoodItem_content view-title">
                            <div class="infoGoodItem_title-2 clr">
                                <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Инструкции') ?></h3></div> 
                            </div>
                        </section>
                        
                        <section class="views-list">
                            <?php foreach ($howto as $item):?>
                                <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
                                    <div class="view_bl-head clr">
                                        <div class="view_bl-head-l flRight">
                                            <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
                                            <span itemprop="datePublished" style="display: none;"><?php echo $item->created?></span>
                                        </div>
                                    </div>
                                    <div class="view_bl-textView">
                                        <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]);?>" itemprop="url">
                                            <h2 itemprop="name"><?php echo  $item->title ?></h2>
                                        </a>
                                        <?php if (!empty($item->preview_image)) :?>
                                        <a class="news-preview" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>">
                                            <img itemprop="image" src="<?php echo $item->preview_image->getPreviewUrl()?>" class="news_preview" 
                                                 alt="<?php echo $item->preview_image->alt?>"/>
                                        </a>
                                        <?php endif; ?>
                                        <span itemprop="description"><?php echo $item->description?></span>...
                                        <?php if (!empty($item->preview_image)) :?>
                                        <div style="clear: both;"></div>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php if ($howto_count): ?>
                            <a href="<?php echo Yii::app()->createUrl("articles/list", ['type'=>'howto','brand'=>$brand->link, 'product'=>$product->link, 'language'=>  Language::getCurrentZone()]);?>">
                                <?php echo Yii::t("goods","Читать все инструкции");?> (<?php echo $howto_count?>)...
                            </a>
                            <?php endif; ?>
                        </section>
                    </div>
                    <div class="infoGoodItem-wp-comments" id="item5">
                        <section class="infoGoodItem_content view-title">
                            <div class="infoGoodItem_title-2 clr">
                                <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Видео') ?></h3></div> 
                            </div>
                        </section>
                        
                        <section class="views-list">
                            <div class="view_bl">
                                <?php foreach ($product->getVideos() as $video): ?>
                                    <?php echo $video; ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <?php foreach ($product->comments as $comment): ?>
                            <div><?php echo $comment->text ?></div>
                        <?php endforeach; ?>
                            <iframe id="trends" width="500" height="290" scrolling="no" src="http://www.google.com/trends/fetchComponent?hl=<?php echo Yii::app()->language?>&q=<?php echo urlencode($brand->name." ".$product->name)?>&cmpt=q&content=1&cid=TIMESERIES_GRAPH_0&export=5&w=500&h=330&date=today+12-m"></iframe>
                        <?php if (Yii::app()->user->checkAccess(Users::ROLE_USER)): ?>
                            <section class="infoGoodItem_content">
                                <div class="infoGoodItem_title-2 clr">
                                    <div class="flLeft"><h3 class="infoGoodItem-infoTitle">Ваш отзыв</h3></div>
                                </div>
                                <?php $this->widget('application.widgets.CommentsWidget.CommentsWidget', array("type" => 'goods', 'id' => $product->id)); ?>
                            </section>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="col-infoPrices">

        <div class="prices_inner">

            <div class="c_min-price">

                <div class="bl_min_price first">
                    <script type="text/topadvert">
                        load_event: page_load
                        feed_id: 11111
                        pattern_id: 7333
                        tech_model: <?php echo $brand->name . " " . $product->name ?>
                    </script>
                    <script type="text/javascript" charset="utf-8" defer="defer" async="async" src="http://loader.topadvert.ru/load.js"></script>
                </div>
            </div>

        </div>

    </div>

    <div class="col-sidebars">

        <div class="c_sidebars_inner">
            <?php $this->widget('application.widgets.ListGoodsWidget', array("type" => 'pda', 'limit' => 5, 'style' => 'inner')); ?>
        </div>

        <div class="informer sidebar-informer sidebar-informer-pr">
            <?php $this->widget('application.widgets.ListGoodsWidget', array("type" => 'e-book', 'limit' => 5, 'style' => 'inner')); ?>
        </div>

        <div class="informer sidebar-informer sidebar-informer-pr sidebar-informer-rt">
            <?php $this->widget('application.widgets.ListGoodsWidget', array("type" => 'tablet', 'limit' => 5, 'style' => 'inner')); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("document").ready(function(){
        //alert($(this).html());
    });
</script>