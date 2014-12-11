<?php foreach ($reviewsTags as $link) {
            $review = $link->review_data;
            if (empty($review->goods_data->brand_data->link))
                continue;
            
            echo '<a href="'.Yii::app()->createUrl("reviews/index", array("goods" => $review->goods_data->brand_data->link . "-" . $review->goods_data->link, "link" => $review->link, "id" => $review->id, "language" => Language::getCurrentZone())) .'" class="link-replyView">'.$review->title.'</a><br />';
            
        }
        ?>