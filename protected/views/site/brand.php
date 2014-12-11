            <ul class="breadcrumbs breadcrumb">
                <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
                    <span itemprop="title"><a itemprop="url" href="http://analogindex.<?php echo Language::getCurrentZone() ?>/"><?php echo Yii::t('main', 'Главная')?></a></span>
                    <span class="divider">/</span>
                </li>
                <?php if ($type_selected):?>
                <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
                    <span itemprop="title"><?php echo $type_selected->name->name?></span>
                    <span class="divider">/</span>
                </li>
                <?php else: ?>
                <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
                    <span itemprop="title"><?php echo Yii::t("main", "Производители")?></span>
                    <span class="divider">/</span>
                </li>
                <?php endif;?>
                <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-2">
                    <span itemprop="title"><?php echo $brand->name?></span>
                </li>
            </ul>
            <div class="manufacture-categories clr">
                <div class="mnf_logo">
                        <img src="img/photo/samsung.png" alt="">
                </div>
                <div class="mnf_clr">
                        <div class="mnf-name">
                                <span><?php echo $brand->name?></span>
                        </div>
                        <div class="mnf-catLiks clr">
                            <a<?php echo (!isset($type_selected->link)) ? ' class="active"' : ''?> href="<?php echo Yii::app()->createAbsoluteUrl("site/brand", array(
                                    "link"=>$brand->link,
                                    "language"=>Language::getCurrentZone(),
                                ))?>"><?php echo Yii::t("main", "Все типы товаров")?></a>
                            <?php foreach ($brand->getTypes() as $type):?>
                                <a<?php echo (isset($type_selected->link) && ($type->link == $type_selected->link)) ? ' class="active"' : ''?> href="<?php echo Yii::app()->createAbsoluteUrl("site/brand", array(
                                    "link"=>$brand->link,
                                    "type"=>$type->link,
                                    "language"=>Language::getCurrentZone(),
                                ))?>"><?php echo $type->name->name?></a>
                            <?php endforeach;?>
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
                        <div class="flLeft"><h2 class="bl_catalogList-title"><?php echo isset($type_selected->name) ? $type_selected->name->name : Yii::t("main","Все типы товаров") ?></h2></div>
                        <div class="flRight">
                                <div class="catalogList-sortLinks">
                                        <a class="sorting-cols<?php echo ($view['id'] == 1) ? ' active' : ''?>" href="<?php echo $this->createUrl($this->route, array_merge($_GET, array("view"=>1, "page"=>1)))?>">колонками</a>
                                        <a class="sorting-rows<?php echo ($view['id'] == 2) ? ' active' : ''?>" href="<?php echo $this->createUrl($this->route, array_merge($_GET, array("view"=>2, "page"=>1)))?>">списком</a>
                                </div>
                        </div>
                </div>
                <?php echo $this->renderPartial($view['template'], array("goods"=>$goods, "brand"=>$brand, "pages"=>$pages, "view"=>$view));?>
                
        </div>
