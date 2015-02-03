<ul class="breadcrumbs breadcrumb">
    <?php foreach ($items as $key=>$item):?>
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
        <a <?php if ($key+1 == count($items)):?>class="active" <?php endif;?>itemprop="url" href="<?php echo $item->url?>">
             <span itemprop="title"><?php echo $item->title?></span>
        </a>
        <?php if ($key+1 < count($items)):?><span class="divider">/</span><?php endif;?>
    </li>
    <?php endforeach; ?>
</ul>