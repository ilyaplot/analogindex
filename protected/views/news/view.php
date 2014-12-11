<div class="wp_col_fix clr">
    <div class="col-infoReview">
        <div class="news" itemscope itemtype="http://schema.org/NewsArticle">
            <span class="date"><?php echo Yii::app()->dateFormatter->formatDateTime($news->created, 'long');?></span>
            <?php if(!empty($news->tags)):?>
            <?php $tags = [];?>
            <?php foreach ($news->tags as $tag):?>
            <?php if(!empty($tag->tag_data)):?>
            <?php 
                $tags[$tag->tag_data->name] = $tag->tag_data->name;
                $this->addKeyword($tag->tag_data->name);
            ?>
            <?php endif;?>
            <?php endforeach;?>
            <?php $tags = implode(", ", $tags);?>
            <span style="display: none;" itemprop="keywords"><?php echo $tags?></span>
            <?php endif;?>
            <span style="display: none;" itemprop="dateCreated"><?php echo $news->created?></span>
            <h1 itemprop="name"><?php echo $news->title ?></h1>
            <?php if(!empty($tags)):?>
            <div>
                <?php $export->products($tags, Yii::app()->language); ?>
            </div>
            <hr />
            <?php endif;?>
            <div class="news-content" itemprop="articleBody"><?php echo $news->content ?></div>
            
            <?php if(!empty($tags)):?>
            <hr />
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
            <hr />
            <ul class="tags">
                <?php foreach ($news->tags as $tag):?>
                <?php if(!empty($tag->tag_data)):?>
                <li>
                    <a rel="tag" href="<?php echo Yii::app()->createAbsoluteUrl("tag/news", [
                        'language'=>  Language::getCurrentZone(),
                        'type'=>$tag->tag_data->type,
                        'tag'=>$tag->tag_data->link,
                    ])?>"><?=$tag->tag_data->name?></a>
                </li>
                <?php endif;?>
                <?php endforeach;?>
            </ul>
            <span><?php echo Yii::t("main", "Эта новость")?> : 
                <a href="<?php echo Yii::app()->createAbsoluteUrl("news/index", ['link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getCurrentZone()]);?>" itemprop="url"><?php echo Yii::app()->createAbsoluteUrl("news/index", ['link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getCurrentZone()]);?></a>
            </span><br />
            <?php echo Yii::t("main", "Источник")?>: <a target="_blank" href="<?php echo $news->source_url?>"><?php echo $news->source_url?></a>
            <br />
        </div>
    </div>

    <div class="col-sidebars">
        <div class="informer sidebar-informer">
            <?php 
                $widget_params = ['style' => 'inner'];
                if (!empty($widget_in)) {
                    $widget_params['in'] = $widget_in;
                } else {
                    $widget_params['type'] = 'pda';
                    $widget_params['limit'] = 20;
                }
            ?>
            <?php $this->widget('application.widgets.ListGoodsWidget', $widget_params); ?>
        </div>
    </div>
</div>

