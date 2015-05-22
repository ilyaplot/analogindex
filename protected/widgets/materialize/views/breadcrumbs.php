<div class="row">
    <div class="col s12">
        <div class="center-align">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- materialize_analogindex_top -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-7891165885018162"
                 data-ad-slot="7160561934"
                 data-ad-format="auto"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>

    <div class="col s12 breadCr">
        <ul class="breadcrumb">
            <?php foreach ($items as $key => $item): ?>
            <li itemscope="" <?=($key > 0) ? 'itemprop="child"' : ''?> id="breadcrumb-<?=$key?>" <?= ($key + 1 == count($items)) ? 'class="active"' : ''?> itemtype="http://data-vocabulary.org/Breadcrumb" <?php if ($key + 1 < count($items)):?>itemref="breadcrumb-<?=($key + 1)?>"<?php endif;?>>
                <span itemprop="title">
                    <?php if (!empty($item->url)): ?>
                    <a itemprop="url" href="<?php echo $item->url ?>"><?php echo $item->title ?></a>
                    <?php else: ?>
                        <span><?=$item->title ?></span>
                        <a itemprop="url" href="<?=Yii::app()->request->pathInfo?>" style="display: none;"></a>
                    <?php endif;?>
                </span>
                <?php if ($key + 1 < count($items)): ?><span class="divider"> &gt; </span><?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>