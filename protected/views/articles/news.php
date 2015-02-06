<div class="wp_col_fix clr">
    <div class="infoGoodItem">
        <div class="col-infoReview" itemscope itemtype="http://schema.org/NewsArticle">
            <div class="infoReviewItem-title" style="top: 0px;">
                <div class="infoGoodItem-title-1">
                    <h1 itemprop="name"><?php echo $article->title ?></h1>
                </div>
                <div class="infoReviewItem-title-2 clr">
                    <div class="flLeft">
                        <span class="date"><?php echo Yii::app()->dateFormatter->formatDateTime($article->created, 'long'); ?></span>
                        <?php if (!empty($article->tags)): ?>
                            <?php $tags = []; ?>
                            <?php foreach ($article->tags as $tag): ?>
                                <?php if (!empty($tag->tag_data)): ?>
                                    <?php
                                    $tags[$tag->tag_data->name] = $tag->tag_data->name;
                                    $this->addKeyword($tag->tag_data->name);
                                    ?>
                               <?php endif; ?>
                            <?php endforeach; ?>
                            <?php $tags = implode(", ", $tags); ?>
                            <span style="display: none;" itemprop="keywords"><?php echo $tags ?></span>
                        <?php endif; ?>
                        <span style="display: none;" itemprop="dateCreated"><?php echo $article->created ?></span>

                        <?php if (!empty($tags)): ?>
                            <?php $export->productsfull($tags, Yii::app()->language); ?>
                        <?php endif; ?>

                    </div>
                    <div class="flRight">
                        <script type="text/javascript">
                            (function () {
                                if (window.pluso)
                                    if (typeof window.pluso.start == "function")
                                        return;
                                if (window.ifpluso == undefined) {
                                    window.ifpluso = 1;
                                    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                                    s.type = 'text/javascript';
                                    s.charset = 'UTF-8';
                                    s.async = true;
                                    s.src = ('https:' == window.location.protocol ? 'https' : 'http') + '://share.pluso.ru/pluso-like.js';
                                    var h = d[g]('body')[0];
                                    h.appendChild(s);
                                }
                            })();</script>
                        <div class="pluso" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=06" data-services="vkontakte,odnoklassniki,facebook,twitter,google" style="margin: 6px 0 0;"><div class="pluso-010011000101-06"><span class="pluso-wrap" style="background:transparent"><a href="http://analogindex.ru/pda/apple/iphone-6.html" title="ВКонтакте" class="pluso-vkontakte"></a><a href="http://analogindex.ru/pda/apple/iphone-6.html" title="Одноклассники" class="pluso-odnoklassniki"></a><a href="http://analogindex.ru/pda/apple/iphone-6.html" title="Facebook" class="pluso-facebook"></a><a href="http://analogindex.ru/pda/apple/iphone-6.html" title="Twitter" class="pluso-twitter"></a><a href="http://analogindex.ru/pda/apple/iphone-6.html" title="Google+" class="pluso-google"></a><a href="http://pluso.ru/" class="pluso-more"></a></span></div></div>
                    </div>
                </div>
            </div>
            <div class="news">

                <div class="news-content" itemprop="articleBody"><?php echo $article->content ?></div>
                <div style="clear: both;"></div>
                <?php if (!empty($tags)): ?>
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
                    <?php foreach ($article->tags as $tag): ?>
                        <?php if (!empty($tag->tag_data)): ?>
                            <li>
                                <a rel="tag" href="<?php
                                echo Yii::app()->createAbsoluteUrl("tag/news", [
                                    'language' => Language::getCurrentZone(),
                                    'type' => $tag->tag_data->type,
                                    'tag' => $tag->tag_data->link,
                                ])
                                ?>"><?= $tag->tag_data->name ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <span><?php echo Yii::t("main", "Эта новость") ?> : 
                    <a href="<?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getCurrentZone()]); ?>" itemprop="url"><?php echo Yii::app()->createAbsoluteUrl("articles/index", ['type' => $article->type, 'link' => $article->link, 'id' => $article->id, 'language' => Language::getCurrentZone()]); ?></a>
                </span><br />
                <?php echo Yii::t("main", "Источник") ?>: <a target="_blank" href="<?php echo $article->source_url ?>"><?php echo $article->source_url ?></a>
                <br />
                <?php echo $this->renderPartial("_comments") ?>
            </div>
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
        <?php if (!empty($tags)): ?>
            <?php echo $this->renderPartial('_topadvert', ['products' => $export->ProductsArray($tags, Yii::app()->language, 1)]); ?>
        <?php endif; ?>
    </div>
</div>

