<div class="bzd-affiliate-catalog">
    <div class="bzd-affiliate-catalog-item" style="width:100px;">
        <?php foreach($items as $item):?>
            <div class="bzd-affiliate-catalog-item-image">
                <a href="<?=$item['url']?>">
                    <img src="<?=$item['picture']?>" alt="<?=$item['name']?>" title="<?=$item['name']?>" width=100 height=100>
                </a>
            </div>
            <div class="bzd-affiliate-catalog-item-name">
                <a href="<?=$item['url']?>"><?=$item['name']?></a>
            </div>
        <div class="bzd-affiliate-catalog-item-price"><span><?=$item['price']?></span> р.</div>
        <?php endforeach;?>
    </div>
</div>