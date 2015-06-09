<link type="text/css" rel="stylesheet" href="/assets/css/all.css"/>
<div class="row">
    <div class="col s12">
        <?php
        $this->widget('LinkPager', array(
            'currentPage' => $pages->getCurrentPage(),
            'itemCount' => $pages->getItemCount(),
            'pageSize' => 15,
            'maxButtonCount' => 8,
            'header' => '',
            'firstPageLabel' => '<i class="mdi-av-fast-rewind"></i>',
            'lastPageLabel' => '<i class="mdi-av-fast-forward"></i>',
            'nextPageLabel' => '<i class="mdi-navigation-chevron-right"></i>',
            'prevPageLabel' => '<i class="mdi-navigation-chevron-left"></i>',
        ));
        ?>
    </div>
</div>
<div class="row">
    <div class="col s9 m9 l9">
        <?php
        foreach ($newsTags as $link):
            $item = $link->articles_data;
            ?>
            <div class="view_bl" itemscope itemtype="http://schema.org/NewsArticle">
                <div class="view_bl-textView">
                    <a href="<?php echo Yii::app()->createUrl("articles/index", ['type' => $item->type, 'link' => $item->link, 'id' => $item->id, 'language' => Language::getCurrentZone()]); ?>">
                        <h3 itemprop="name"><?php echo $item->title ?></h3>
                    </a>
                    <div class="view_bl-head-l flRight">
                        <date class="view_bl-date"><?php echo Yii::app()->dateFormatter->formatDateTime($item->created, 'long'); ?></date>
                        <span itemprop="datePublished" style="display: none;"><?php echo $item->created ?></span>
                    </div>
                    <?php if (!empty($item->preview_image->image_data)) : ?>
                        <a class="news-preview" href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $item->type, 'link' => $item->link, 'id' => $item->id, 'language' => Language::getCurrentZone()]); ?>">
                            <?php echo $item->preview_image->image_data->getHtml(NImages::SIZE_ARTICLE_PREVIEW, null, ['itemprop' => "image", 'class' => "news_preview"]) ?>
                        </a>
                    <?php endif; ?>
                    <span itemprop="description"><?php echo $item->description ?></span>...
                    <?php if (!empty($item->preview_image->image_data)) : ?>
                        <div style="clear: both; "></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col m3 s3 l3">
        <div class="center-align">
            <!-- materialize_analogindex_right -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-7891165885018162"
                 data-ad-slot="3509091535"
                 data-ad-format="auto"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12">
        <?php
        $this->widget('LinkPager', array(
            'currentPage' => $pages->getCurrentPage(),
            'itemCount' => $pages->getItemCount(),
            'pageSize' => 15,
            'maxButtonCount' => 8,
            'header' => '',
            'firstPageLabel' => '<i class="mdi-av-fast-rewind"></i>',
            'lastPageLabel' => '<i class="mdi-av-fast-forward"></i>',
            'nextPageLabel' => '<i class="mdi-navigation-chevron-right"></i>',
            'prevPageLabel' => '<i class="mdi-navigation-chevron-left"></i>',
        ));
        ?>
    </div>
</div>
