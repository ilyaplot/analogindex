<table>
    <?php foreach ($products as $product): ?>
    <tr>
        <th colspan="2" style="text-align: left;">
            <h2><?php echo Yii::t("goods", "Отзывы о") . " " . $product->brand_data->name . " " . $product->name ?></h2>
        </th>
    </tr>
    <tr>
        <td rowspan="<?php echo count($product->revs) ?>" style="text-align: center; vertical-align: middle !important; padding: 5px;">
            <a href="<?php
            echo Yii::app()->createAbsoluteUrl("site/goods", [
                'link' => $product->link,
                'brand' => $product->brand_data->link,
                'type' => $product->type_data->link,
                'language' => Language::getCurrentZone(),
            ])
            ?>">
                <?php if (isset($product->primary_image->image_data->size3_data)): ?>
                    <img src="<?php
                    echo Yii::app()->createAbsoluteUrl("files/image", array(
                        'id' => $product->primary_image->image_data->size3_data->id,
                        'name' => $product->primary_image->image_data->size3_data->name,
                        'language' => Language::getCurrentZone(),
                    ));
                    ?>" alt="<?php echo $product->brand_data->name . " " . $product->name ?>" 
                         title="<?php echo $product->brand_data->name . " " . $product->name ?>" /><br />
            <?php endif; ?>
            <?php echo $product->brand_data->name . " " . $product->name ?></a>
        </td>
        <td>
            <?php
            $review = $product->revs[0];
            unset($product->revs[0]);
            ?>
            <a href="<?php
            echo Yii::app()->createAbsoluteUrl("reviews/index", [
                "goods" => $product->brand_data->link . "-" . $product->link,
                "link" => $review->link,
                "id" => $review->id,
                "language" => Language::getCurrentZone()
                    ]
            )
            ?>">
    <?php echo $review->title ?>
            </a>
            <br /><?php echo Yii::app()->dateFormatter->formatDateTime($review->created, 'long'); ?><br />
            <?php echo $review->preview ?>...
        </td>
    </tr>
            <?php foreach ($product->revs as $review): ?>
        <tr>
            <td style="padding-top: 12px;">
                <a href="<?php
                       echo Yii::app()->createAbsoluteUrl("reviews/index", [
                           "goods" => $product->brand_data->link . "-" . $product->link,
                           "link" => $review->link,
                           "id" => $review->id,
                           "language" => Language::getCurrentZone()
                               ]
                       )
                       ?>">
        <?php echo $review->title ?>
                </a>
                <br /><small><?php echo Yii::app()->dateFormatter->formatDateTime($review->created, 'long'); ?></small><br />
                    <?php echo $review->preview ?>...
            </td>

        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="2" style="padding-top: 12px;">
            <a href="<?php echo Yii::app()->createAbsoluteUrl("reviews/list", ['brand' => $product->brand_data->link, 'product' => $product->link, 'language' => Language::getCurrentZone()]); ?>">
    <?php echo Yii::t("goods", "Читать все отзывы") . " " . $product->brand_data->name . " " . $product->name; ?> (<?php echo count($product->reviews) ?>)...
            </a>
        </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
<?php endforeach; ?>
</table>


