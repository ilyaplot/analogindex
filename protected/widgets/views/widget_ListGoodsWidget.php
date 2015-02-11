<div class="informer">
    <div class="informer-top">
        <div class="informer-t-left">
            <span class="informer-title"><?php echo $type->name->name?></span>
        </div>
        <div class="informer-t-right">
            <nav id="informer-top-menu">
                <ul>
                    <li>
                        <a href="#" class="informer-icon-rating"></a>
                    </li>
                    <li class="active" id="like_li_informer">
                        <a href="#" class="informer-icon-likes"></a>
                    </li>
                    <li id="price_li_informer">
                        <a href="#" class="informer-icon-price"></a>
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
            <?php foreach ($data as $goods): ?>
            <li>
                <div class="informer-listGoods_photo">
                    <a href="<?php echo Yii::app()->createAbsoluteUrl("site/goods", array(
                        'link'=>$goods->link, 
                        'brand'=>$goods->brand_data->link,
                        'type'=>$goods->type_data->link, 
                        'language'=>Language::getCurrentZone(),
                    ));?>">
                        <?php if (!empty($goods->primary_image->image_data)):?>
                            <?php echo $goods->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_WIDGET); ?>
                        <?php else :?>
                            <img src="/assets/img/photo/informers/1.png" style="height:37px; width: 30px;" alt="<?php echo $goods->brand_data->name." ".$goods->name ?>" />
                        <?php endif;?>
                    </a>
                </div>
                <div class="informer-listGoods_desc">
                    <a href="<?php echo Yii::app()->createAbsoluteUrl("site/goods", array(
                        'link'=>$goods->link, 
                        'brand'=>$goods->brand_data->link,
                        'type'=>$type->link,
                        'language'=> Language::getCurrentZone()
                    ))?>">
                        <span><?php echo $goods->brand_data->name." ".$goods->name ?></span>
                    </a>
                </div>
                <div class="informer-listGoods_rating"><?php echo $goods->getRanking("antutu", 0, '%');?></div>
                <div class="informer-listGoods_like"><?php echo isset($goods->rating->value) ? round($goods->rating->value,1) : 0;?></div>
                <div class="informer-listGoods_price">0</div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="informer-bottom">
        <div class="informer-b-left">
            <img src="/assets/img/small/logo.png" height="17" width="79" alt="" />
        </div>
        <div class="informer-b-right">
            <ul id="informer-b-r-links" class="clr">
                <li>
                    <a href="#" class="informer-link-share" title="Поделиться"></a>
                    <div class="submenu">
                        <nav class="clr">
                            <a href="#" class="share_block-fb" title="Facebook"></a>
                            <a href="#" class="share_block-gp" title="Google+"></a>
                            <a href="#" class="share_block-tw" title="Twitter"></a>
                            <a href="#" class="share_block-vk" title="Вконтакте"></a>
                        </nav>
                    </div>
                </li>
                <li>
                    <a href="#" class="informer-link-send" title="Рассказать"></a>
                </li>
            </ul>
        </div>
    <div class="clear"></div>
    </div>
</div>