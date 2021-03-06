<div class="news">
    <?php foreach ($news as $item):?>
    <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
        <div class="view_bl-head clr">
            <div class="view_bl-head-l flRight">
                <date>Created: <?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long');?></date>
                <span itemprop="datePublished" style="display: none;"><?php echo $item->created?></span>
            </div>
        </div>
        <div class="view_bl-textView">
            <h2 itemprop="name"><?php echo  $item->id.": ". $item->title ?></h2>
            <span itemprop="description"><?php echo $item->getDescription()?></span>...
            <div>Filtered: <?php echo $item->filtered?></div>
        </div>
        <div class="view_bl-replyLink"><?php echo Yii::t("main", 'Не фильтрованый контент')?> : <a target="_blank" itemprop="url" href="<?php echo Yii::app()->createAbsoluteUrl("news/index", ['link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getZoneForLang($item->lang), 'debug'=>true]); ?>"><?php echo $item->title?></a></div>
        <div class="view_bl-replyLink"><?php echo Yii::t("main", 'Фильтрованый')?> : <a target="_blank" itemprop="url" href="<?php echo Yii::app()->createAbsoluteUrl("news/index", ['link'=>$item->link, 'id'=>$item->id, 'language'=>  Language::getZoneForLang($item->lang)]); ?>"><?php echo $item->title?></a></div>
        
    </div>
    <?php endforeach; ?>
</div>
