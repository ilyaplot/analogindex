<div class="wp_col_fix clr">
    <div class="col-infoReview">
        <div class="news" itemscope itemtype="http://schema.org/NewsArticle">
            <span class="date"><?php echo Yii::app()->dateFormatter->formatDateTime($article->created, 'long');?></span>
            <?php if(!empty($article->tags)):?>
            <?php $tags = [];?>
            <?php foreach ($article->tags as $tag):?>
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
            <span style="display: none;" itemprop="dateCreated"><?php echo $article->created?></span>
            <h1 itemprop="name"><?php echo $article->title ?></h1>
            <?php if(!empty($tags)):?>
            <div>
                <?php $export->productsfull($tags, Yii::app()->language); ?>
            </div>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <hr />
            <?php endif;?>
            <div class="news-content" itemprop="articleBody"><?php echo $article->content ?></div>
            <div style="clear: both;"></div>
            <?php  echo $this->renderPartial("_comments")?>
            <br />
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
            <div style="clear: both;"></div>
            <hr />
            <ul class="tags">
                <?php foreach ($article->tags as $tag):?>
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
                <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$article->type,'link'=>$article->link, 'id'=>$article->id, 'language'=>  Language::getCurrentZone()]);?>" itemprop="url"><?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type'=>$article->type,'link'=>$article->link, 'id'=>$article->id, 'language'=>  Language::getCurrentZone()]);?></a>
            </span><br />
            <?php echo Yii::t("main", "Источник")?>: <a target="_blank" href="<?php echo $article->source_url?>"><?php echo $article->source_url?></a>
            <br />
            
        </div>
    </div>

    <div class="col-sidebars">
        <?php if (!empty($tags)): ?>
        <?php echo $this->renderPartial('_topadvert', ['products'=>$export->ProductsArray($tags, Yii::app()->language, 1)]); ?>
        <?php endif;?>
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

