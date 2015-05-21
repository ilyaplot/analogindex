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
            <li itemscope="" <?=($key > 0) ? 'itemprop="child"' : ''?> id="breadcrumb-<?=$key?>" <?= ($key + 1 == count($items)) ? 'class="active"' : ''?> itemtype="http://data-vocabulary.org/Breadcrumb" itemref="breadcrumb-<?=($key + 1)?>">
                <span itemprop="title">
                    <?php if (!empty($item->url)): ?>
                        <a itemprop="url" href="<?php echo $item->url ?>"><?php echo $item->title ?></a>
                    <?php else: ?>
                        <?php echo $item->title ?>
                    <?php endif;?>
                </span>
                <?php if ($key + 1 < count($items)): ?><span class="divider">/</span><?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>