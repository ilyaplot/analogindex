<ul class="breadcrumbs breadcrumb">
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
        <a href="http://analogindex.<?php echo Language::getCurrentZone() ?>/"><?php echo Yii::t('main', 'Главная')?></a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
        <a href="<?php echo Yii::app()->createUrl("site/type", array("type"=>$product->type_data->link)) ?>"><?php echo $product->type_data->name->name?></a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-2" itemref="breadcrumb-3">
        <a href="<?php echo Yii::app()->createUrl("site/brand", array(
            "link"=>$product->brand_data->link, 
            "language"=>Language::getCurrentZone(),
            "type"=>$product->type_data->link,
        )); ?>"><?php echo $product->brand_data->name?></a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-3" itemref="breadcrumb-4">
        <a href="<?php echo Yii::app()->createUrl("site/goods", array(
            'link'=>$product->link, 
            'brand'=>$product->brand_data->link,
            'type'=>$product->type_data->link, 
            'language'=>Language::getCurrentZone(),
        ));?>">
            <?php echo $product->brand_data->name?> <?php echo $product->name?>
        </a>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" class="active" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-4">
        <?php echo Yii::t("main", "Обзор")?>: <?php echo $review->title?>
    </li>
</ul>
<div class="wp_col_fix clr">
    <div class="col-infoReview">
        <div class="manufacture-categories clr">
            <div class="mnf_logo">
                    <img src="<?php echo Yii::app()->createUrl("files/image", array(
                        'id'=>$product->primary_image->image_data->size3_data->id,
                        'name'=>$product->primary_image->image_data->size3_data->name,
                        'language'=>Language::getCurrentZone(),
                    )); ?>" alt="<?php echo $product->brand_data->name." ".$product->name ?>" />
            </div>
            <div class="mnf_clr">
                    <div class="mnf-name">
                            <span><?php echo $product->brand_data->name." ".$product->name ?></span>
                    </div>
            </div>
        </div>
        <div class="review">
            <h1><?php echo $review->title?></h1>
            <div class="review-content"><?php echo $review->content?></div>
            <hr />
            <br />
            <?php $this->widget('application.widgets.CommentsWidget.CommentsWidget', array("type"=>'review', 'id'=>$review->id)); ?>
        </div>    
    </div>
    
    <div class="col-sidebars">
        <div class="informer sidebar-informer">
        <?php $this->widget('application.widgets.ListGoodsWidget', array("type"=>'pda', 'limit'=>20, 'style'=>'inner')); ?>
        </div>
    </div>
</div>

