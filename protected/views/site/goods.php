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
<ul class="breadcrumbs breadcrumb">
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
        <a href="http://analogindex.<?php echo Language::getCurrentZone() ?>/"><?php echo Yii::t('main', 'Главная') ?></a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
        <a href="<?php echo Yii::app()->createUrl("site/type", array("type" => $type->link, "language" => Language::getCurrentZone())) ?>"><?php echo $type->name->name ?></a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-2" itemref="breadcrumb-3">
        <a href="<?php
        echo Yii::app()->createUrl("site/brand", array(
            "link" => $brand->link,
            "language" => Language::getCurrentZone(),
            "type" => $type->link,
        ));
        ?>"><?php echo $brand->name ?></a>
        <span class="divider">/</span>
    </li>
    <li class="active" itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-3">
        <?php echo $brand->name ?> <?php echo $product->name ?>
    </li>
</ul>
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
                        <?php if (empty($product->images)) : ?>
                            <div class="infoGoodItem-wp-photos_main">
                                <img style="width: 450px; height: auto;" src="/assets/img/no_photo.png">
                            </div>
                        <?php elseif (count($product->images) == 2): ?>
                            <?php echo $this->renderPartial("_goods_one_image", array("product" => $product, "brand" => $brand)) ?>
                        <?php else: ?>
                            <?php $this->renderPartial("_goods_many_images", array("product" => $product, "brand" => $brand)) ?>
                        <?php endif; ?>
                        <div class="clear"></div>
                    </div>
                    <?php $this->renderPartial("_goods_characteristics", array("product" => $product)) ?>
                    <div class="infoGoodItem-wp-news" id="item3">
                        <section class="infoGoodItem_content">
                            <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Новости') ?></h3>
                            <ul id="GoodItem-news">
                                <!--
                                <li>
                                    <div class="newsGood-name"><span class="count">1.</span>Обзор планшета iPad mini</div>
                                    <div class="newsGoods-link"><a href="#">http://www.mobile-review.com/ipad-mini.html</a></div>
                                </li>
                                -->
                            </ul>
                        </section>
                    </div>
                    <div class="infoGoodItem-wp-updates" id="item4">
                        <section class="infoGoodItem_content">
                            <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Обновления и прошивки') ?></h3>
                            <ul id="GoodItem-updates">
                                <!--
                                <li>
                                    <div class="newsGood-name"><span class="count">9.</span>Что купить: новый Nexus 7 или iPad Mini</div>
                                    <div class="newsGoods-link"><a href="#">http://www.mobile-review.com/articles/2012/apple-ipad-mini.html</a></div>
                                </li>
                                -->
                            </ul>
                        </section>
                    </div>
                    <div class="infoGoodItem-wp-comments" id="item5">
                        <section class="infoGoodItem_content view-title">
                            <div class="infoGoodItem_title-2 clr">
                                <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Отзывы') ?></h3></div> 
                                <!--<div class="flRight"><a href="#" class="btn-link_st1" title=""><?php echo Yii::t('goods', "Написать отзыв") ?></a></div>-->
                            </div>
                        </section>
                        <section class="views-list">
                            <div class="view_bl">
                                <?php foreach ($product->getVideos() as $video): ?>
                                    <?php echo $video; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php foreach ($product->reviews as $review): ?>
                                <div class="view_bl">
                                    <div class="view_bl-head clr">
                                        <div class="view_bl-head-r flLeft">
                                            <div class="view_bl-avatar"><img src="/assets/img/photo/avatar_view.png"></div>
                                            <div class="view_bl-h2">
                                                <div class="view_bl-h2_name">Аноним</div>
                                                <!--<div class="view_bl-h2_rating">
                                                    <ul class="rating_like-s" title="Оценка - 5">
                                                        <li><span class="icon-like"></span></li>
                                                        <li><span class="icon-like"></span></li>
                                                        <li><span class="icon-like"></span></li>
                                                        <li><span class="icon-like"></span></li>
                                                        <li><span class="icon-like"></span></li>
                                                    </ul>
                                                </div>-->
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="view_bl-head-l flRight">
                                            <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->format('d MMMM yyyy в hh:mm', $review->created); ?></date>
                                        </div>
                                    </div>
                                    <div class="view_bl-textView">
                                        <h2><?php echo $review->title ?></h2>
                                        <?php echo $review->preview ?>...
                                    </div>
                                    <div class="view_bl-replyLink"><a href="<?php echo Yii::app()->createUrl("site/review", array("goods" => $brand->link . "-" . $product->link, "link" => $review->link, "id" => $review->id, "language" => Language::getCurrentZone())) ?>" class="link-replyView">Читать полностью...</a></div>
                                </div>

                            <?php endforeach; ?>
                        </section>
                        <?php foreach ($product->comments as $comment): ?>
                            <div><?php echo $comment->text ?></div>
                        <?php endforeach; ?>
                        <?php if (Yii::app()->user->checkAccess(Users::ROLE_USER)): ?>
                            <section class="infoGoodItem_content">
                                <div class="infoGoodItem_title-2 clr">
                                    <div class="flLeft"><h3 class="infoGoodItem-infoTitle">Ваш отзыв</h3></div>
                                </div>
                                <?php $this->widget('application.widgets.CommentsWidget.CommentsWidget', array("type" => 'goods', 'id' => $product->id)); ?>
                            </section>
                        <?php endif; ?>
                    </div>

                    <div class="infoGoodItem-wp-comments" id="item6">
                        <section class="infoGoodItem_content view-title">
                            <div class="infoGoodItem_title-2 clr">
                                <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'FAQ') ?></h3><small><?php echo $product->id?></small></div> 
                            </div>
                        </section>

                        <section class="views-list">
                            <?php foreach ($product->faq as $question): ?>
                                <div class="view_bl">
                                    <div class="view_bl-textView">
                                        <h2><?php echo $question['question'] ?></h2>
                                        <?php echo $question['answer'] ?>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </section>

                    </div>

                    <!--
            <div class="infoGoodItem-wp-new_comment" id="item6">
                <section class="infoGoodItem_content">
                    <div class="infoGoodItem_title-2 clr">
                        <div class="flLeft"><h3 class="infoGoodItem-infoTitle">Ваш отзыв</h3></div>
                        <div class="flRight"><a href="#" class="link-st3" title="">Посмотреть отзывы</a></div>
                    </div>
                    <div class="view_read-bl">
                        <form action="#" method="post">
                            <div class="view_read-head">
                                <div class="clr">
                                    <div class="view_read-avatar">
                                        <img src="/assets/img/photo/avatar_view2.png" height="40" width="40">
                                    </div>
                                    <div class="view_read-h2">
                                        <div class="view_r-name">Анна Аннова</div>
                                        <div class="view_r-setRating">
                                            <div>Оцените товар:</div>
                                            <ul class="rating2">
                                                <li><a href="#">1</a></li>
                                                <li><a href="#">2</a></li>
                                                <li><a href="#">3</a></li>
                                                <li><a href="#">4</a></li>
                                                <li><a href="#">5</a></li>
                                            </ul>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="view_read-text">
                                <textarea name="view_text" class="textarea-st3"></textarea>
                            </div>
                            <div class="view_read-bottom clr">
                                <div class="view_read-bottom-left flLeft">
                                    <div class="view_r_b-replytext">
                                        Вы отвечаете на комментарий:<br>
                                        «Открыл, взял в руки и понял - ОНО. Честно говоря  с...»
                                    </div>
                                    <div class="view_r_b-linkOff"><a href="#" class="link-st3">Отменить</a></div>
                                </div>
                                <div class="flRight"><input type="submit" class="btn_submit2" value="Отправить" name="submit_readView"></div>
                            </div>
                            <input type="hidden" name="GoodItemSetRating" class="GoodItemSetRating" value="">
                        </form>
                    </div>
                </section>
            </div>
                    -->
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
                <!--
                <div class="bl_min_price first">
                    <div class="bl_min_price-name">
                        <span>"М-Видео"</span>
                    </div>
                    <div class="bl_min_price-price">
                        <span class="price_c_minimum">10 000</span>
                    </div>
                    <div class="bl_min_price-buy">
                        <a href="#" class="link-btn_buy-big">Купить</a>
                    </div>
                </div>
                <div class="bl_min_price">
                    <div class="bl_min_price-name">
                        <span>"Связной"</span>
                    </div>
                    <div class="bl_min_price-price">
                        <span class="price_c_other">11 500</span>
                    </div>
                    <div class="bl_min_price-buy">
                        <a href="#" class="link-btn_buy-small">Купить</a>
                    </div>
                </div>
                <div class="bl_min_price">
                    <div class="bl_min_price-name">
                        <span>"Связной"</span>
                    </div>
                    <div class="bl_min_price-price">
                        <span class="price_c_other">12 000</span>
                    </div>
                    <div class="bl_min_price-buy">
                        <a href="#" class="link-btn_buy-small">Купить</a>
                    </div>
                </div>
                <div class="bl_min_price">
                    
                    <div class="bl_min_price-name">
                        <span>"Евросеть"</span>
                    </div>
                    <div class="bl_min_price-price">
                        <span class="price_c_other">13 000</span>
                    </div>
                    <div class="bl_min_price-buy">
                        <a href="#" class="link-btn_buy-small">Купить</a>
                    </div>
                </div>
                <div class="bl_min_price">
                    <div class="bl_min_price-name">
                        <span>"Альт"</span>
                    </div>
                    <div class="bl_min_price-price">
                        <span class="price_c_other">13 500</span>
                    </div>
                    <div class="bl_min_price-buy">
                        <a href="#" class="link-btn_buy-small">Купить</a>
                    </div>
                </div>
                -->
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