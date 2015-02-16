<h2><?php
$type = empty($type) ? 'news' : $type;
    switch($type) {
        case 'opinion':
            echo Yii::t("goods", "Отзывы по теме");
            break;
        case 'review':
            echo Yii::t("goods", "Обзоры по теме");
            break;
        case 'howto':
            echo Yii::t("goods", "Инструкции по теме");
            break;
        default:
            echo Yii::t("goods", "Новости по теме");
            break;
    }
    

?></h2>
<table style="margin-bottom: 15px;">
    <?php foreach ($newsTags as $tag): ?>
    <?php $news = $tag->articles_data; ?>
    <tr>
        <td style="padding-top: 12px;" itemscope itemtype="http://schema.org/NewsArticle">
            <span itemprop="datePublished" style="display: none;"><?php echo $news->created?></span>
            <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$news->type,'link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getZoneForLang($news->lang)]);?>" itemprop="url">
                <h3 itemprop="name"><?php echo $news->title ?></h3>
            </a>
            <small>
                <?php echo Yii::app()->dateFormatter->formatDateTime($news->created, 'long'); ?>
            </small>
            <?php if (!empty($news->preview_image->image_data)) :?>
            <a style="width: 130px; max-height: 130px; margin: 7px; text-align: center; display: table-cell; vertical-align: middle; float: left;" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$news->type,'link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getCurrentZone()]); ?>">
                <?php echo $news->preview_image->image_data->getHtml(NImages::SIZE_ARTICLE_PREVIEW);?>
            </a>
            <?php endif; ?>
            <p itemprop="description"><?php echo $news->description ?>...</p>
            <?php if (!empty($news->preview_image)) :?>
            <div style="clear: both;"></div>
            <?php endif;?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

