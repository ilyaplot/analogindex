<div class="wp_col_fix clr">
    <div class="col-infoReview">
        <div class="news">
            <h1><?php echo $news->title ?></h1>
            <div class="news-content"><?php echo $news->content ?></div>
            <hr />
            <?php if(!empty($news->tags)):?>
            Тэги:
            <ul class="tags">
                <?php foreach ($news->tags as $tag):?>
                <?php if(!empty($tag->tag_data)):?>
                <li>
                    <a href="<?php echo Yii::app()->createAbsoluteUrl("tag/news", [
                        'language'=>  Language::getCurrentZone(),
                        'type'=>$tag->tag_data->type,
                        'tag'=>$tag->tag_data->link,
                    ])?>"><?=$tag->tag_data->name?></a>
                </li>
                <?php endif;?>
                <?php endforeach;?>
            </ul>
            <hr />
            <?php endif;?>
            Источник: <a target="_blank" href="<?php echo $news->source_url?>"><?php echo $news->source_url?></a>
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

