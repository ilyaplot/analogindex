<div id="floatingToolbar-right">
    <div class="widget-inner">
        <div class="widget-top">
            <?= $type->name->name ?>
            <i class="mdi-av-play-circle-outline right"></i>
            <i class="mdi-action-alarm-add right"></i>
            <i class="mdi-action-info-outline right"></i>
        </div>
        <div class="widget-middle">
            <ul>
                <?php foreach ($data as $product): ?>
                    <li>
                        <div>
                            <div>
                                <?php if (isset($product->primary_image)): ?>
                                    <a class="title" href="<?= $product->url ?>">
                                        <?php echo $product->primary_image->image_data->getHtml(NImages::SIZE_PRODUCT_WIDGET); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="<?= $product->url ?>"><?= $product->fullname ?></a>
                            </div>
                        </div>

                        <div>
                            <div>0</div>
                            <div>0</div>
                        </div>

                    </li>
                <?php endforeach; ?>
                <li></li>
                <li></li>
            </ul>
        </div>
        <div class="widget-bottom">
            <!-- bottom -->
        </div>
    </div>

</div>