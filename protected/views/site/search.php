<link rel="stylesheet" href="/assets/css/style_list.css" />
<div class="row content-wrapper">
    <div class="col s12">
        <!--<div class="row">
            <div class="col s12 sort-by">
                <div class="bl_sort-goods">
                    <span class="sort-goods-text">Сортировка:</span>
                    <a href="#" class="">по рейтингу</a>
                    <a href="#" class="l">по цене</a>
                    <a href="#" class="activated">по наименованию</a>
                </div>
            </div>
        </div>-->
        <div class="row">
            <div class="col s12 gallery">
                <div class="gallery-title">
                    <h2><?=Yii::t("main", "Поиск");?></h2>
                    <div>
                        <div class="catalogList-sortLinks">
                            <a class="sorting-cols active" href="#">колонками</a>
                            <a class="sorting-rows" href="#">списком</a>
                        </div>
                    </div>
                </div>
                <?php foreach ($goods as $key => $product): ?>
                <div class="col s6 m6 l4 item-cat" itemscope="" itemtype="http://schema.org/ImageObject">
                    <a href="<?=$product->url?>" class="center-align">
                        <?php if ($product->primary_image): ?>
                            <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_GALLERY, null, ['itemprop'=>'thumbnail']); ?>
                        <?php else : ?>
                            <img itemprop="thumbnail" src="/assets/img/photo/informers/1.png" alt="<?php echo $product->fullname ?>" />
                        <?php endif; ?>
                    </a>
                    <br />
                    <div class="item-subscribe">
                        <ul>
                            <li><a href="<?=$product->url?>" itemprop="contentUrl"><?=$product->fullname?></a></li>
                        </ul>
                    </div>
                    <div class="left-align">
                        <ul>
                            <?php foreach ($product->getGeneralCharacteristics() as $characteristic): ?>
                            <li><strong><?=$characteristic['characteristic_name']?>:</strong> <?=$characteristic['value']; ?></li>
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
