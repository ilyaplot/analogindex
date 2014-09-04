<ul class="catalog_st2">
    <?php foreach ($goods as $item):?>
<li>
        <div class="flLeft catalog_st2-l">
                <div class="flLeft catalog_st2-l-image"><a href="<?php echo Yii::app()->createUrl("site/goods", array(
                                'link'=>$item->link, 
                                'brand'=>$brand->link,
                                'type'=>$item->type_data->link,
                                'language'=> Language::getCurrentZone()
                            ))?>">
                        <?php if($image = $item->getPrimaryImage(Images::SIZE_BRAND)): ?>
                                    <img src="<?php echo Yii::app()->createUrl("files/image", array(
                                        'id'=>$image->resized_preview->file_data->id,
                                        'name'=>$image->resized_preview->file_data->name,
                                        'language'=>Language::getCurrentZone(),
                                        )); ?>" alt="<?php echo $brand->name?> <?php echo $item->name?>">
                                    <?php else :?>
                                    <img src="img/photo/s5.png" alt="<?php echo $brand->name?> <?php echo $item->name?>">
                                    <?php endif?></a></div>
                <div class="catalog_st2-l-info">
                        <div class="catalog_st2-l-info-title"><a href="<?php echo Yii::app()->createUrl("site/goods", array(
                                'link'=>$item->link, 
                                'brand'=>$brand->link,
                                'type'=>$item->type_data->link,
                                'language'=> Language::getCurrentZone()
                            ))?>"><?php echo $brand->name?> <?php echo $item->name?></a></div>
                        <div class="catalog_st2-l-info-desc">Операционная система iOS экран 9.7", 2048x1536, емкостный, мультитач встроенная память 128 Гб еспроводная связь Wi-Fi, Bluetooth, 3G, LTE навигация GPS вес 478 г тыловая камера 5 млн пикс.</div>
                </div>
                <div class="clear"></div>
        </div>
        <div class="catalog_st2-r">
                <div class="catalog_st2-r_price">
                        <span>24 900</span>
                                                <div class="informer-curr-bl">
                                                        <a href="#" class="informer_currency-select cur-rub"><span class="drpd_arrow-informer"></span></a>
                                                        <ul>
                                                                <li><a href="#" class="cur-dol"></a></li>
                                                                <li><a href="#" class="cur-eur"></a></li>
                                                        </ul>
                                                </div>
                </div>
                <div class="catalog_st2-r_set1"><span class="icon"></span>94</div>
                <div class="catalog_st2-r_set2"><span class="icon"></span>91</div>
                <div class="catalog_st2-r_set3"><span class="icon"></span>95</div>
        </div>
</li>
<?php endforeach;?>
</ul>
<?php 
                    $this->widget('LinkPager', array(
                        'currentPage'=>$pages->getCurrentPage(),
                        'itemCount'=>$pages->getItemCount(),
                        'pageSize'=>$view['limit'],
                        'maxButtonCount'=>8,
                        'header'=>'',
                        'htmlOptions'=>array('class'=>'pagination'),
                        'firstPageLabel'=>Yii::t("main", "Первая"),
                        'lastPageLabel'=>Yii::t("main", "Последняя")." (".ceil($pages->getItemCount()/$view['limit']).")",
                        'nextPageLabel'=>Yii::t("main", "Следующая"),
                        'prevPageLabel'=>Yii::t("main", "Предыдущая"),
                    ));
                ?>