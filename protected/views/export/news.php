<h2><?php echo Yii::t("goods", "Новости по теме");?></h2>
<table style="margin-bottom: 15px;">
    <?php foreach ($newsTags as $tag): ?>
    <?php $news = $tag->news_data; ?>
    <tr>
        <td style="padding-top: 12px;" itemscope itemtype="http://schema.org/NewsArticle">
            <span itemprop="datePublished" style="display: none;"><?php echo $news->created?></span>
            <a href="<?php echo Yii::app()->createAbsoluteUrl("news/index", ['link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getZoneForLang($news->lang)]);?>" itemprop="url">
                <h3 itemprop="name"><?php echo $news->title ?></h3>
            </a>
            <small>
                <?php echo Yii::app()->dateFormatter->formatDateTime($news->created, 'long'); ?>
            </small>
            <?php if (!empty($news->preview_image)) :?>
            <a style="width: 130px; max-height: 130px; margin: 7px; text-align: center; display: table-cell; vertical-align: middle; float: left;" href="<?php echo Yii::app()->createAbsoluteUrl("news/index", ['link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getCurrentZone()]); ?>">
                <img style="padding: 0px; margin: 0px;" itemprop="image" src="<?php echo $news->preview_image->getPreviewUrl()?>" alt="<?php echo $news->preview_image->alt?>"/>
            </a>
            <?php endif; ?>
            <p itemprop="description"><?php echo $news->getDescription() ?>...</p>
            <?php if (!empty($news->preview_image)) :?>
            <div style="clear: both;"></div>
            <?php endif;?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

