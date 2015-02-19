<?php 
    $this->widget('LinkPager', array(
        'currentPage'=>$pages->getCurrentPage(),
        'itemCount'=>$pages->getItemCount(),
        'pageSize'=>15,
        'maxButtonCount'=>8,
        'header'=>'',
        'htmlOptions'=>array('class'=>'pagination'),
        'firstPageLabel'=>Yii::t("main", "Первая"),
        'lastPageLabel'=>Yii::t("main", "Последняя")." (".ceil($pages->getItemCount()/15).")",
        'nextPageLabel'=>Yii::t("main", "Следующая"),
        'prevPageLabel'=>Yii::t("main", "Предыдущая"),
    ));
?>
<div class="news">
    <?php
        foreach ($newsTags as $link):
            $item = $link->articles_data;
        ?>
    <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
        <div class="view_bl-head clr">
            <div class="view_bl-head-l flRight">
                <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
                <span itemprop="datePublished" style="display: none;"><?php echo $item->created?></span>
            </div>
        </div>
        <div class="view_bl-textView">
            <h2 itemprop="name"><?php echo  $item->title ?></h2>
            <?php if (!empty($item->preview_image->image_data)) :?>
            <a class="news-preview" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>">
                <?php echo $item->preview_image->image_data->getHtml(NImages::SIZE_ARTICLE_PREVIEW, null, ['itemprop'=>"image", 'class'=>"news_preview"]) ?>
            </a>
            <?php endif; ?>
            <span itemprop="description"><?php echo $item->description?></span>...
            <?php if (!empty($item->preview_image->image_data)) :?>
            <div style="clear: both; "></div>
            <?php endif; ?>
        </div>
        <div class="view_bl-replyLink"><?php echo Yii::t("main", 'Читать полностью')?> : <a itemprop="url" href="<?php echo Yii::app()->createUrl("articles/index", ['type'=>$item->type,'link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>"><?php echo $item->title?></a></div>
    </div>
    <?php endforeach; ?>
</div>
<?php 
    $this->widget('LinkPager', array(
        'currentPage'=>$pages->getCurrentPage(),
        'itemCount'=>$pages->getItemCount(),
        'pageSize'=>15,
        'maxButtonCount'=>8,
        'header'=>'',
        'htmlOptions'=>array('class'=>'pagination'),
        'firstPageLabel'=>Yii::t("main", "Первая"),
        'lastPageLabel'=>Yii::t("main", "Последняя")." (".ceil($pages->getItemCount()/15).")",
        'nextPageLabel'=>Yii::t("main", "Следующая"),
        'prevPageLabel'=>Yii::t("main", "Предыдущая"),
    ));
?>
