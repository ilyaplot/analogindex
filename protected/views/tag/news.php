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
            $item = $link->news_data;
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
            <span itemprop="description"><?php echo $item->getDescription()?></span>...
        </div>
        <div class="view_bl-replyLink"><?php echo Yii::t("main", 'Читать полностью')?> : <a itemprop="url" href="<?php echo Yii::app()->createUrl("news/index", ['link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getCurrentZone()]); ?>"><?php echo $item->title?></a></div>
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
