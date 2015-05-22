<link rel="stylesheet" href="/assets/css/style_list.css" />
<div class="row content-wrapper">
    <div class="col s12">
        <div class="col s12 m3 l2 hide-on-med-and-down">
            <!-- Описание++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <div class="mnf_logo">
                <a href="<?=Yii::app()->createAbsoluteUrl('site/brand', ['link'=>$brand->link, 'language'=>Language::getCurrentZone()])?>">
                    <img title="htmlentities($brand->name)" src="<?=$brand->getLogoUrl() ?>" alt="<?=  htmlentities($brand->name) ?>" />
                </a>
            </div>
        </div>
        <div class="col s12 m9 l10">
            <div class="mnf_clr">
                <div class="mnf-name">
                    <a href="<?=Yii::app()->createAbsoluteUrl('site/brand', ['link'=>$brand->link, 'language'=>Language::getCurrentZone()])?>"><?=$brand->name?></a>
                </div>
                <br />
                <div class="bl_sort-goods">
                     <a<?php echo (!isset($type_selected->link)) ? ' class="active"' : '' ?> href="<?php
                        echo Yii::app()->createAbsoluteUrl("site/brand", array(
                            "link" => $brand->link,
                            "language" => Language::getCurrentZone(),
                        ))
                        ?>"><?php echo Yii::t("main", "Все типы товаров") ?></a>
                    <?php foreach ($brand->getTypes() as $type): ?>
                        <a<?php echo (isset($type_selected->link) && ($type->link == $type_selected->link)) ? ' class="active"' : '' ?> href="<?php
                        echo Yii::app()->createAbsoluteUrl("site/brand", array(
                            "link" => $brand->link,
                            "type" => $type->link,
                            "language" => Language::getCurrentZone(),
                        ))
                        ?>"><?php echo $type->name->name ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 gallery">
                <div class="gallery-title">
                    <div>
                        <div class="catalogList-sortLinks">
                            <a class="sorting-cols active" href="#">колонками</a>
                            <a class="sorting-rows" href="#">списком</a>
                        </div>
                    </div>
                </div>
                <?php foreach ($goods as $key => $product): ?>
                    <div class="col s6 m6 l4 item-cat" itemscope="" itemtype="http://schema.org/ImageObject">
                        <a href="<?= $product->url ?>" class="center-align">
                            <?php if ($product->primary_image): ?>
                                <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_GALLERY, null, ['itemprop' => 'thumbnail']); ?>
                            <?php else : ?>
                                <img itemprop="thumbnail" src="/assets/img/photo/informers/1.png" alt="<?php echo $product->fullname ?>" />
                            <?php endif; ?>
                        </a>
                        <br />
                        <div class="item-subscribe">
                            <ul>
                                <li><a href="<?= $product->url ?>" itemprop="contentUrl"><?= $product->fullname ?></a></li>
                            </ul>
                        </div>
                        <div class="left-align">
                            <ul>
                                <?php foreach ($product->getGeneralCharacteristics() as $characteristic): ?>
                                    <li><strong><?= $characteristic['characteristic_name'] ?>:</strong> <?= $characteristic['value']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
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
                    'pageSize' => 12,
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
<!--            
            <div class="manufacture-categories clr">
<?php if ($brand->logo): ?>
                    <div class="mnf_logo">
                        <img src="<?php echo $brand->getLogoUrl() ?>" alt="<?php echo htmlspecialchars($brand->name); ?>">
                    </div>
<?php endif; ?>
                <div class="mnf_clr">
                        <div class="mnf-name">
                                <span><?php echo $brand->name ?></span>
                        </div>
                        <div class="mnf-catLiks clr">
                            <a<?php echo (!isset($type_selected->link)) ? ' class="active"' : '' ?> href="<?php
echo Yii::app()->createAbsoluteUrl("site/brand", array(
    "link" => $brand->link,
    "language" => Language::getCurrentZone(),
))
?>"><?php echo Yii::t("main", "Все типы товаров") ?></a>
<?php foreach ($brand->getTypes() as $type): ?>
                                    <a<?php echo (isset($type_selected->link) && ($type->link == $type_selected->link)) ? ' class="active"' : '' ?> href="<?php
    echo Yii::app()->createAbsoluteUrl("site/brand", array(
        "link" => $brand->link,
        "type" => $type->link,
        "language" => Language::getCurrentZone(),
    ))
    ?>"><?php echo $type->name->name ?></a>
<?php endforeach; ?>
                        </div>
                </div>
        </div>
        <div class="bl_sort-goods">
                <span class="sort-goods-text">Сортировка:</span>
                <a href="#" class="link-sorting">по рейтингу</a>
                <a href="#" class="link-sorting">по цене</a>
                <a href="#" class="link-sorting active">по наименованию</a>
        </div>
        <div class="bl_catalogList">
                <div class="bl_catalogList-top clr">
                        <div class="flLeft"><h2 class="bl_catalogList-title"><?php echo isset($type_selected->name) ? $type_selected->name->name : Yii::t("main", "Все типы товаров") ?></h2></div>
                        <div class="flRight">

                        </div>
                </div>

        </div>
-->
