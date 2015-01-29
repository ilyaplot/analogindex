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
        ?>"><?php echo $brand->name ?></a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-3" itemref="breadcrumb-4">
        <span itemprop="title"><a itemprop="url" href="<?php
        echo Yii::app()->createUrl("site/goods", array(
            'link' => $product->link,
            'brand' => $brand->link,
            'type' => $product->type_data->link,
            'language' => Language::getCurrentZone(),
        ));
        ?>">
               <?php echo $brand->name ?> <?php echo $product->name ?>
        </a></span>
        <span class="divider">/</span>
    </li>
    <li itemprop="child" class="active" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" id="breadcrumb-4">
        <span itemprop="title"><?php echo Yii::t("main", "Фотогалерея") ?></span>
    </li>
</ul>
<div class="wp_col_fix clr">
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
                ?>"><?php echo $product->brand_data->name . " " . $product->name ?></a>
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
    <style>
#gallery .image {
    position: relative;
    width: 922px; /* for IE 6 */
    z-index: 1000;
    text-align: center;
    display: table-cell;
    vertical-align: middle;
}
#gallery .image img {
    max-width: 900px;
    max-height: 500px;
}
#gallery .button_next, #gallery .button_prev {
    cursor: pointer;
    position: absolute;
    top: 0px;
    height: 100%;
    width: 50px;
    z-index: 1100;
    background-color: rgba(200, 200, 200, 0.3);
    background-repeat: no-repeat;
    background-position-y: 50%;
}

#gallery .image .article {
    min-height: 50px;
    max-height: 150px;
    overflow: hidden;
    width: 792px;
    background-color: rgba(200, 200, 200, 0.1);
    text-align: left;
    font-size: 16px;
    margin-left: 50px;
    margin-right: 50px;
    padding: 15px;
}

#gallery .image .article p {
    font-size: 14px;
    font-style: italic;
}

#gallery .button_next:hover, #gallery .button_prev:hover {
    background-color: rgba(200, 200, 200, 0.7);
}

#gallery .button_next {
    right: 0px;
    background-image: url('/assets/img/next.png');
    background-position-x: 10px;
}
#gallery .button_prev {
    left: 0;
    background-image: url('/assets/img/prev.png');
    background-position-x: -10px;
}

.list table{
    border-collapse: collapse;
    border-spacing: 0;
	width:944px;
	height:100%;
	margin:0px;
    padding:0px;
    border:1px solid #cccccc !important;
}

.list td{
	vertical-align:middle;
	border:1px solid #cccccc;
	border-width:0px 1px 1px 0px;
	text-align:center;
	padding:2px;
}.list tr:last-child td{
	border-width:0px 1px 0px 0px;
}.list tr td:last-child{
	border-width:0px 0px 1px 0px;
}.list tr:last-child td:last-child{
	border-width:0px 0px 0px 0px;
}
.list tr:first-child td{
	border:0px solid #cccccc;
	text-align:center;
	border-width:0px 0px 1px 1px;
}
.list tr:first-child td:first-child{
	border-width:0px 0px 1px 0px;
}
.list tr:first-child td:last-child{
	border-width:0px 0px 1px 1px;
}

#gallery table.list a {
    text-align: center;
    display: table-cell;
    vertical-align: middle;
    width: 150px;
    height: 150px;
    padding: 1px;
    margin: 2px;
}

#loader {
    background-color: rgba(200, 200, 200, 0.7);
    position: absolute;
    width: 200px;
    height: 90px;
    text-align: center;
    top: 40%;
    left: 380px;
}

#loader img {
    width: 32px !important;
    height: 32px !important;
    max-width: 32px !important;
    max-height: 32px !important;
    min-width: 32px !important;
    min-height: 32px !important;
}

    </style>
    <script type="text/javascript">
    function imageLoaded() {
        document = document;
        var elem = document.getElementById('loader');
        elem.setAttribute('display', 'none');
        elem.remove();
    }
    </script>
    <div id="gallery">
        <div class="image">
            <div id="loader">
                <img onload="imageLoaded();" src="/assets/img/loading.gif" /> <?php echo Yii::t("gallery", 'Подождите, фотография загружается')?>... 
            </div>
            <img src="<?php echo $image->src?>" alt="<?php echo $image->alt?>" title="<?php echo $image->alt?>"/>
            <?php if ($image->page > 0):?>
            <a title="<?php echo Yii::t("gallery",'Предыдущее фото')?>" href="<?php 
            echo Yii::app()->createAbsoluteUrl("gallery/product", [
                'brand'=>$brand->link,
                'product'=>$product->link,
                'page'=>$image->page,
                'language'=>  Language::getCurrentZone(),
            ])?>#gallery" class="button_prev"></a>
            <?php endif;?>
            <?php if ($image->page+1 < $countImages):?>
                <a title="<?php echo Yii::t("gallery",'Следующее фото')?>" href="<?php 
            echo Yii::app()->createAbsoluteUrl("gallery/product", [
                'brand'=>$brand->link,
                'product'=>$product->link,
                'page'=>$image->page+2,
                'language'=>  Language::getCurrentZone(),
            ])?>#gallery" class="button_next"></a>
            <?php endif;?>
            <?php if(!empty($image->article)):?>
            <div class="article">
                <?php echo Yii::t("gallery", 'Фотография из')?> <?php echo Yii::t("gallery", $image->article->type_name)?>: <a href="<?php echo $image->article->url?>"><?php echo $image->article->title?></a>
                <p><?php echo $image->article->description?></p>
            </div>
            <?php endif;?>
        </div>
        <table class="list">
            <?php foreach($gallery as $list): ?>
            <tr>
                <?php foreach($list as $item):?>
                <td<?php echo ($item->active) ? ' class="active"' : ''?>>
                    <a href="<?php 
                        echo Yii::app()->createAbsoluteUrl("gallery/product", [
                            'brand'=>$brand->link,
                            'product'=>$product->link,
                            'page'=>$item->page+1,
                            'language'=>  Language::getCurrentZone(),
                        ])?>#gallery">
                    <img src="<?php echo $item->preview_src?>" alt="<?php echo $item->alt?>" title="<?php echo $item->alt?>" />
                    </a>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
</div>