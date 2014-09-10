<script type="text/javascript">
    /**
     * Возвращает строку для подписи к фото на нужном языке
     * @returns string
     */
    function AnalogindexLightboxLabel(a,b){
        return"<?php echo Yii::t("main", "Фотография ")?>"+a+" <?php echo Yii::t("main", " из ")?> "+b
    };
</script>
<script src="/assets/js/lightbox.js"></script>
<link href="/assets/css/lightbox.css" rel="stylesheet" />
<ul class="breadcrumbs breadcrumb">
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
        <a href="http://analogindex.<?php echo Language::getCurrentZone() ?>/"><?php echo Yii::t('main', 'Главная')?></a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
        <a href="<?php echo Yii::app()->createUrl("site/type", array("type"=>$type->link)) ?>"><?php echo $type->name->name?></a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-2" itemref="breadcrumb-3">
        <a href="<?php echo Yii::app()->createUrl("site/brand", array(
            "link"=>$brand->link, 
            "language"=>Language::getCurrentZone(),
            "type"=>$type->link,
        )); ?>"><?php echo $brand->name?></a>
        <span class="divider">/</span>
    </li>
    <li class="active" itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-3">
        <?php echo $brand->name?> <?php echo $product->name?>
    </li>
</ul>
<div class="wp_col_fix clr">
    <div class="col-infoGoods">
        <div class="infoGoodItem">
            <div class="infoGoodItem-title">
                <div class="infoGoodItem-title-1">
                    <h1><?php echo $brand->name?> <?php echo $product->name?></h1>
                </div>
                <div class="infoGoodItem-title-2 clr">
                    <div class="flLeft">
                        <span class="infoGoodItem-title-2_name"><?php echo Yii::t('goods', 'Рейтинги')?>:</span>
                        <ul class="infoGoodItem-title-2_list">
                            <li class="item1"></li>
                            <li class="item2"></li>
                            <li class="item3"><?php echo isset($product->rating->value) ? round($product->rating->value,1) : '';?></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <div class="flRight">
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $('.item_photos_all .slide img').on('click', function(){
                                $('.infoGoodItem-wp-photos_main img').attr('src', $(this).attr('src'));
                            });
                        });

                        (function() {
                        if (window.pluso)if (typeof window.pluso.start == "function") return;
                        if (window.ifpluso==undefined) { window.ifpluso = 1;
                            var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                            s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
                            s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
                            var h=d[g]('body')[0];
                            h.appendChild(s);
                    }})();</script>
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
                        <?php echo $this->renderPartial("_goods_one_image", array("product"=>$product, "brand"=>$brand)) ?>
                    <?php else: ?>
                        <?php $this->renderPartial("_goods_many_images", array("product"=>$product, "brand"=>$brand)) ?>
                    <?php endif; ?>
                    <div class="clear"></div>
                </div>
                <?php $this->renderPartial("_goods_ characteristics", array("product"=>$product))?>
                <div class="infoGoodItem-wp-news" id="item3">
                    <section class="infoGoodItem_content">
                        <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Новости')?></h3>
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
                            <h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Обновления и прошивки')?></h3>
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
                            <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'Отзывы')?></h3></div> 
                            <div class="flRight"><a href="#" class="btn-link_st1" title=""><?php echo Yii::t('goods', "Написать отзыв") ?></a></div>
                        </div>
                    </section>
                    <section class="views-list">
                        <?php foreach($product->getVideos() as $video):?>
                        <?php echo $video; ?>
                        <?php endforeach; ?>
                        
                        <?php foreach ($reviews as $review): ?>
                        <div class="view_bl">
                            <div class="view_bl-head clr">
                                <div class="view_bl-head-r flLeft">
                                    <div class="view_bl-avatar"><img src="/assets/img/photo/avatar_view1.png"></div>
                                    <div class="view_bl-h2">
                                        <div class="view_bl-h2_name">Аноним</div>
                                        <div class="view_bl-h2_rating">
                                            <ul class="rating_like-s" title="Оценка - 5">
                                                <li><span class="icon-like"></span></li>
                                                <li><span class="icon-like"></span></li>
                                                <li><span class="icon-like"></span></li>
                                                <li><span class="icon-like"></span></li>
                                                <li><span class="icon-like"></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="view_bl-head-l flRight">
                                    <date class="view_bl-date">24 февраля 2014 в 19:05</date>
                                </div>
                            </div>
                            <div class="view_bl-textView">
                                <h2><?php echo $review['title']?></h2>
                                <?php echo $this->getWords($review['content']) ?>...
                            </div>
                            <div class="view_bl-replyLink"><a href="reviews/Array" class="link-replyView">Читать полностью...</a></div>
                        </div>

                        <?php endforeach; ?>
                    </section>
                </div>

                <div class="infoGoodItem-wp-comments" id="item6">
                    <section class="infoGoodItem_content view-title">
                        <div class="infoGoodItem_title-2 clr">
                            <div class="flLeft"><h3 class="infoGoodItem-infoTitle"><?php echo Yii::t('goods', 'FAQ')?></h3></div> 
                        </div>
                    </section>

                    <section class="views-list">
                        <?php foreach ($product->faq as $question): ?>
                        <div class="view_bl">
                            <div class="view_bl-textView">
                                <h2><?php echo $question['question']?></h2>
                                <?php echo $question['answer']?>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    </section>

                </div>


                <div class="infoGoodItem-wp-new_comment" id="item7">
                    <?php if (Yii::app()->user->checkAccess(Users::ROLE_USER)): ?>
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
                                            <img src="/assets/img/photo/avatar_view3.png" height="40" width="40">
                                        </div>
                                        <div class="view_read-h2">
                                            <div class="view_r-name"><?php echo Yii::app()->user->getState("name")?></div>
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
                    <?php endif;?>
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
        <!--
        <div class="prices_inner">
        <div class="c_min-price">
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
        </div>
        </div>
        -->
    </div>

    <div class="col-sidebars">
        <!--
        <div class="c_sidebars_inner">
        <div class="informer sidebar-informer">
            <div class="informer-top">
                <div class="informer-t-left"><span class="informer-title">Планшеты</span></div>
                <div class="informer-t-right">
                    <nav id="informer-top-menu">
                        <ul>
                            <li><a href="#" class="informer-icon-rating"></a></li>
                            <li id="price_li_informer"><a href="#" class="informer-icon-price"></a>
                                <div class="informer-curr-bl">
                                    <a href="#" class="informer_currency-select cur-rub"><span class="drpd_arrow-informer"></span></a>
                                    <ul>
                                        <li><a href="#" class="cur-dol"></a></li>
                                        <li><a href="#" class="cur-eur"></a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="informer-c">
                <ul class="informer-listGoods">
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/1.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Samsung Galaxy Tab 2 10.1 P5100 16Gb</span></a></div>
                        <div class="informer-listGoods_rating">100</div>
                        <div class="informer-listGoods_price">36000</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/2.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Apple iPad mini 16Gb Wi-Fi</span></a></div>
                        <div class="informer-listGoods_rating">99</div>
                        <div class="informer-listGoods_price">36000</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/3.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Samsung Galaxy Note 8.0 N5100 16Gb</span></a></div>
                        <div class="informer-listGoods_rating">97</div>
                        <div class="informer-listGoods_price">36000</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/2.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Apple iPad mini 16Gb Wi-Fi</span></a></div>
                        <div class="informer-listGoods_rating">99</div>
                        <div class="informer-listGoods_price">36000</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/2.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Apple iPad mini 16Gb Wi-Fi</span></a></div>
                        <div class="informer-listGoods_rating">99</div>
                        <div class="informer-listGoods_price">36000</div>
                    </li>
                </ul>
            </div>
            <div class="informer-bottom">
                <div class="informer-b-left"><img src="/assets/img/small/logo.png" height="17" width="79"></div>
                <div class="informer-b-right">
                    <ul id="informer-b-r-links" class="clr">
                        <li><a href="#" class="informer-link-share" title="Поделиться"></a>
                        <div class="submenu">
                            <nav class="clr">
                                <a href="#" class="share_block-fb" title="Facebook"></a>
                                <a href="#" class="share_block-gp" title="Google+"></a>
                                <a href="#" class="share_block-tw" title="Twitter"></a>
                                <a href="#" class="share_block-vk" title="Вконтакте"></a>
                            </nav>
                        </div></li>
                        <li><a href="#" class="informer-link-send" title="Рассказать"></a></li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class="informer sidebar-informer sidebar-informer-pr">
            <div class="informer-top">
                <div class="informer-t-left"><span class="informer-title">Производительность</span></div>
                <div class="informer-t-right"></div>
                <div class="clear"></div>
            </div>
            <div class="informer-c">
                <ul class="informer-listGoods">
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/1.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Samsung Galaxy Tab 2 10.1 P5100 16Gb</span></a></div>
                        <div class="informer-listGoods_like">100</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/2.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Apple iPad mini 16Gb Wi-Fi</span></a></div>
                        <div class="informer-listGoods_like">99</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/3.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Samsung Galaxy Note 8.0 N5100 16Gb</span></a></div>
                        <div class="informer-listGoods_like">97</div>
                    </li>
                </ul>
            </div>
            <div class="informer-bottom">
                <div class="informer-b-left"><img src="/assets/img/small/logo.png" height="17" width="79"></div>
                <div class="informer-b-right">
                    <ul id="informer-b-r-links" class="clr">
                        <li><a href="#" class="informer-link-share" title="Поделиться"></a>
                        <div class="submenu">
                            <nav class="clr">
                                <a href="#" class="share_block-fb" title="Facebook"></a>
                                <a href="#" class="share_block-gp" title="Google+"></a>
                                <a href="#" class="share_block-tw" title="Twitter"></a>
                                <a href="#" class="share_block-vk" title="Вконтакте"></a>
                            </nav>
                        </div></li>
                        <li><a href="#" class="informer-link-send" title="Рассказать"></a></li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class="informer sidebar-informer sidebar-informer-pr sidebar-informer-rt">
            <div class="informer-top">
                <div class="informer-t-left"><span class="informer-title">Оценка пользователей</span></div>
                <div class="informer-t-right"></div>
                <div class="clear"></div>
            </div>
            <div class="informer-c">
                <ul class="informer-listGoods">
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/1.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Samsung Galaxy Tab 2 10.1 P5100 16Gb</span></a></div>
                        <div class="informer-listGoods_like">100</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/2.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Apple iPad mini 16Gb Wi-Fi</span></a></div>
                        <div class="informer-listGoods_like">99</div>
                    </li>
                    <li>
                        <div class="informer-listGoods_photo"><a href="#"><img src="/assets/img/photo/informers/3.png" height="37" width="30"></a></div>
                        <div class="informer-listGoods_desc"><a href="#"><span>Samsung Galaxy Note 8.0 N5100 16Gb</span></a></div>
                        <div class="informer-listGoods_like">97</div>
                    </li>
                </ul>
            </div>
            <div class="informer-bottom">
                <div class="informer-b-left"><img src="/assets/img/small/logo.png" height="17" width="79"></div>
                <div class="informer-b-right">
                    <ul id="informer-b-r-links" class="clr">
                        <li><a href="#" class="informer-link-share" title="Поделиться"></a>
                        <div class="submenu">
                            <nav class="clr">
                                <a href="#" class="share_block-fb" title="Facebook"></a>
                                <a href="#" class="share_block-gp" title="Google+"></a>
                                <a href="#" class="share_block-tw" title="Twitter"></a>
                                <a href="#" class="share_block-vk" title="Вконтакте"></a>
                            </nav>
                        </div></li>
                        <li><a href="#" class="informer-link-send" title="Рассказать"></a></li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        </div>
        -->
    </div>
</div>