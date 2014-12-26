<ul class="breadcrumbs breadcrumb">
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-1">
        <span itemprop="title"><a itemprop="url" href="http://analogindex.<?php echo Language::getCurrentZone() ?>/"><?php echo Yii::t('main', 'Главная') ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-1" itemref="breadcrumb-2">
        <span itemprop="title"><a itemprop="url" href="<?php echo Yii::app()->createUrl("site/type", array("type" => $product->type_data->link)) ?>"><?php echo $product->type_data->name->name ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-2" itemref="breadcrumb-3">
        <span itemprop="title"><a itemprop="url" href="<?php
        echo Yii::app()->createUrl("site/brand", array(
            "link" => $product->brand_data->link,
            "language" => Language::getCurrentZone(),
            "type" => $product->type_data->link,
        ));
        ?>"><?php echo $product->brand_data->name ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-3" itemref="breadcrumb-4">
        <span itemprop="title"><a itemprop="url" href="<?php
        echo Yii::app()->createUrl("site/goods", array(
            'link' => $product->link,
            'brand' => $product->brand_data->link,
            'type' => $product->type_data->link,
            'language' => Language::getCurrentZone(),
        ));
        ?>">
               <?php echo $product->brand_data->name ?> <?php echo $product->name ?>
        </a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" class="active" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-4">
        <span itemprop="title"><?php echo Yii::t("main", "Обзор") ?>: <?php echo $review->title ?></span>
    </li>
</ul>
<div class="wp_col_fix clr">
    <div class="col-infoReview" itemprop="review" itemscope itemtype="http://schema.org/Review">
        <div class="manufacture-categories clr">
            <div class="mnf_logo">
                <a href="<?php
                echo Yii::app()->createUrl("site/goods", array(
                    'link' => $product->link,
                    'brand' => $product->brand_data->link,
                    'type' => $product->type_data->link,
                    'language' => Language::getCurrentZone()
                ))
                ?>">
                       <?php if ($product->primary_image): ?>
                        <img src="<?php
                        echo Yii::app()->createUrl("files/image", array(
                            'id' => $product->primary_image->image_data->size3_data->id,
                            'name' => $product->primary_image->image_data->size3_data->name,
                            'language' => Language::getCurrentZone(),
                        ));
                        ?>" alt="<?php echo $product->brand_data->name . " " . $product->name ?>" /></a>
                    <?php endif; ?>
            </div>
            <div class="mnf_clr">
                <div class="mnf-name">
                    <a href="<?php
                    echo Yii::app()->createUrl("site/goods", array(
                        'link' => $product->link,
                        'brand' => $product->brand_data->link,
                        'type' => $product->type_data->link,
                        'language' => Language::getCurrentZone()
                    ))
                    ?>"><span itemprop="itemReviewed"><?php echo $product->brand_data->name . " " . $product->name ?></span></a>
                </div>
                <div class="item<?php echo (!$ratingDisabled) ? 0 : 3 ?>" id="ratingResult">

                    <?php if (!$ratingDisabled) : ?>

                        <?php
                        $this->widget('application.widgets.StarRating', array(
                            'name' => 'ratingAjax',
                            'maxRating' => 10,
                            'starCount' => 5,
                            'value' => isset($product->rating->value) ? round($product->rating->value * 2) : 0,
                            'resetValue' => false,
                            'cssFile' => '/assets/css/rating.css',
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
                        ?> <?php echo isset($product->rating->value) ? "(<span itemprop='reviewRating'>" . round($product->rating->value, 1) . "</span>)" : ''; ?>
                    <?php else: ?>
                    <span itemprop='reviewRating'><?php echo isset($product->rating->value) ? round($product->rating->value, 1) : ''; ?></span>
                    <?php endif; ?>
                </div>
                <br />
                <small>
                    <?php
                    $characteristics = $product->getCharacteristics($product->generalCharacteristics);
                    $characteristicsLinks = new CharacteristicsLinks($characteristics);
                    $characteristics = $characteristicsLinks->getCharacteristics($product->type_data->link);
                    ?>
                    <?php foreach ($characteristics as $catalog): ?>
                        <?php foreach ($catalog as $characteristic): ?>
                            <?php echo $characteristic['characteristic_name'] . ": " . $characteristic['value'] . PHP_EOL; ?><br />
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </small>

            </div>
        </div>
        <div class="review">
            <h1 itemprop="name"><?php echo $review->title ?></h1>
            
            <?php if(!empty($review->tags)):?>
                <?php $tags = [];?>
                <?php foreach ($review->tags as $tag):?>
                <?php if(!empty($tag->tag_data)):?>
                <?php 
                    $tags[$tag->tag_data->name] = $tag->tag_data->name;
                    $this->addKeyword($tag->tag_data->name);
                ?>
                <?php endif;?>
                <?php endforeach;?>
                <?php $tags = implode(", ", $tags);?>
            <?php endif;?>
            <?php if(!empty($tags)):?>
            <div>
                <?php $export->products($tags, Yii::app()->language); ?>
            </div>
            <hr />
            <?php endif;?>
            
            <div class="review-content" itemprop="reviewBody"><?php echo $review->content ?></div>
            <?php if(!empty($tags)):?>
            
            <hr   />
            <div>
                <?php $export->trends($tags, Yii::app()->language, 20); ?>
            </div>
            <div>
                <?php $export->videos($tags, Yii::app()->language); ?>
            </div>

            <div>
                <?php $export->compare($tags, Yii::app()->language, 20); ?>
            </div>
            
            <div>
                <?php $export->news($tags, Yii::app()->language, 10); ?>
            </div>
            
            <div>
                <?php $export->reviews($tags, Yii::app()->language, 10); ?>
            </div>
            <?php endif; ?>
            <ul class="tags">
                <?php $tags = [];?>
                <?php foreach ($review->tags as $tag):?>
                <?php $tags[$tag->tag_data->name] = $tag->tag_data->name ;?>
                <li>
                    <a rel="tag" href="<?php echo Yii::app()->createUrl("tag/reviews", [
                        'language'=>  Language::getCurrentZone(),
                        'type'=>$tag->tag_data->type,
                        'tag'=>$tag->tag_data->link,
                    ])?>"><?=$tag->tag_data->name?></a>
                </li>
                <?php endforeach;?>
            </ul>
            <?php if(!empty($tags)):?>
            <span itemprop="keywords" style="display: none;"><?php echo implode(", ", $tags)?></span>
            <?php endif;?>
            <hr />
            <br />
            <?php foreach ($review->comments as $comment): ?>
                <div><?php echo $comment->text ?></div>
            <?php endforeach; ?>
            <?php $this->widget('application.widgets.CommentsWidget.CommentsWidget', array("type" => 'reviews', 'id' => $review->id)); ?>
        </div>    
    </div>

    <div class="col-sidebars">
        <div class="informer sidebar-informer">
            <?php $this->widget('application.widgets.ListGoodsWidget', array("type" => 'pda', 'limit' => 20, 'style' => 'inner')); ?>
        </div>
    </div>
</div>

