<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Типы товаров</li>
                    <li<?php
                        if (!isset($filters['controller']['type']) || $filters['controller']['type'] == null )
                            echo ' class="active" ';
                    ?>><a href="<?php echo Yii::app()->createUrl("admin/goods")?>"> * Все</a></li>
                    <?php foreach ($types as $type):?>
                    <li<?php
                        if (isset($filters['controller']['type']) && $filters['controller']['type'] == $type->id )
                            echo ' class="active" ';
                    ?>><a href="<?php echo Yii::app()->createUrl("admin/goods", array("type"=>$type->id, 'language'=>Yii::app()->language))?>"><?php echo $type->name->name?></a></li>
                    <?php endforeach;?>
                    <li class="nav-header">Производители</li>
                    <?php 
                        $brandsUrl = $filters['controller'];
                        unset($brandsUrl['brand']);
                    ?>
                    <li<?php
                        if (!isset($filters['controller']['brand']) || $filters['controller']['brand'] == null )
                            echo ' class="active" ';
                    ?>><a href="<?php echo Yii::app()->createUrl("admin/goods", $brandsUrl)?>"> * Все</a></li>
                    <?php 
                    foreach ($brands as $brand):?>
                    <li<?php
                        if (isset($filters['controller']['brand']) && $filters['controller']['brand'] == $brand->id )
                            echo ' class="active" ';
                    ?>><a href="<?php echo Yii::app()->createUrl("admin/goods", array_merge($filters['controller'], array("brand"=>$brand->id)))?>"><?php echo $brand->name?></a></li>
                    <?php endforeach;?>
                </ul>
            </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
            <div class="well">
                <form action="/admin/goods.html" method="get">
                    <div class="input-append">
                        <input class="span12" name="search" value="<?php echo isset($filters['controller']['search']) ? $filters['controller']['search'] : ''?>" placeholder="Наименование" type="text">
                        <?php foreach ($filters['controller'] as $name=>$filter):?>
                        <?php if ($name !== 'search'): ?>
                        <input type="hidden" name="<?php echo $name?>" value="<?php echo $filter?>" />
                        <?php endif; ?>
                        <?php endforeach;?>
                        <input class="btn" type="submit" value="Найти" />
                    </div>
                </form>
                <?php if (!empty($filters["view"])):?>
                Применены фильтры:
                <?php foreach ($filters['view'] as $name=>$filter):?>
                <span class="label"><?php echo $name.": ".$filter?></span>
                <?php endforeach;?>
                <?php endif;?>
                <br />Всего товаров: <?php echo $goodsCount ?>
            </div>
            <?php if (!empty($goods)):?>
            <?php 
                $this->widget('BootstrapLinkPager', array(
                    'currentPage'=>$pages->getCurrentPage(),
                    'itemCount'=>$goodsCount,
                    'pageSize'=>25,
                    'maxButtonCount'=>8,
                    'header'=>'',
                    'htmlOptions'=>array('class'=>'pagination'),
                    'firstPageLabel'=>"<<",
                    'lastPageLabel'=>">>",
                    'nextPageLabel'=>">",
                    'prevPageLabel'=>"<",
                ));
            ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Производитель</th>
                        <th>Наименование</th>
                        <th>Тип</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($goods as $item):?>
                    <tr>
                        <td><?php echo $item->id?></td>
                        <td><?php echo $item->brand_data->name?></td>
                        <td><?php echo $item->name ?></td>
                        <td><?php echo $item->type_data->name->name ?></td>
                        <td>
                            <a class="btn btn-mini" target="_blank" href="<?php
                                echo Yii::app()->createUrl("admin/editgoods", array("id"=>$item->id))
                            ?>"><i class="icon-edit"></i> Редактировать</a><br />
                            <a class="btn btn-mini" target="_blank" href="<?php
                                echo Yii::app()->createUrl("site/goods", array(
                                    "type"=>$item->type_data->link,
                                    "language"=>Yii::app()->language,
                                    "brand"=>$item->brand_data->link,
                                    "link"=>$item->link,
                                ));
                            ?>"><i class="icon-eye-open"></i> Открыть страницу</a><br />
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
            <?php 
                $this->widget('BootstrapLinkPager', array(
                    'currentPage'=>$pages->getCurrentPage(),
                    'itemCount'=>$goodsCount,
                    'pageSize'=>25,
                    'maxButtonCount'=>8,
                    'header'=>'',
                    'htmlOptions'=>array('class'=>'pagination'),
                    'firstPageLabel'=>"<<",
                    'lastPageLabel'=>">>",
                    'nextPageLabel'=>">",
                    'prevPageLabel'=>"<",
                ));
            ?>
            <?php else: ?>
            <div class="well">
                <h5>Ничего не найдено...</h5>
            </div>
            <?php endif;?>
        </div>
    </div>

    <hr>

</div>