<ul class="catalog_st1">
                    <?php $limit = 0;?>
                    <?php foreach ($goods as $item) :?>
                    <?php $limit++?>
                        <li><a href="<?php echo Yii::app()->createUrl("site/goods", array(
                                'link'=>$item->link, 
                                'brand'=>$brand->link,
                                'type'=>$item->type_data->link,
                                'language'=> Language::getCurrentZone()
                            ))?>">
                                <div class="catalog_st1-image">
                                    <?php if($image = $item->getPrimaryImage(Images::SIZE_BRAND)): ?>
                                    <img src="<?php echo Yii::app()->createUrl("files/image", array(
                                        'id'=>$image->resized_preview->file_data->id,
                                        'name'=>$image->resized_preview->file_data->name,
                                        'language'=>Language::getCurrentZone(),
                                        )); ?>" alt="<?php echo $brand->name?> <?php echo $item->name?>">
                                    <?php else :?>
                                    <img src="img/photo/s5.png" alt="<?php echo $brand->name?> <?php echo $item->name?>">
                                    <?php endif?>
                                </div>
                                <div class="catalog_st1-name"><?php echo $brand->name?> <?php echo $item->name?></div>
                        </a></li>
                        <?php if ($limit == 6): 
                            $limit = 0;?>
                        <br clear="both">
                        <?php endif;?>
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