<?php

class TagController extends Controller
{
    public function actionReviews($type, $tag)
    {
        
        
        $criteria = new CDbCriteria();
        $criteria->condition = "type = :type and link = :link";
        $criteria->params = ['type'=>$type, 'link'=>$tag];
        $tag = Tags::model()->find($criteria);
        if (!$tag) {
            throw new CHttpException(404);
        }
        $criteria = new CDbCriteria();
        $criteria->condition = "tag = :tag";
        $criteria->params = ['tag'=>$tag->id];
        $criteria->group = 'review';
        
        echo $tag->name."<br />".PHP_EOL;
        $reviewsTags = ReviewsTags::model()->findAll($criteria);
        foreach ($reviewsTags as $link) {
            $review = $link->review_data;
            if (empty($review->goods_data->brand_data->link))
                continue;
            
            echo '<a href="'.Yii::app()->createUrl("site/review", array("goods" => $review->goods_data->brand_data->link . "-" . $review->goods_data->link, "link" => $review->link, "id" => $review->id, "language" => Language::getCurrentZone())) .'" class="link-replyView">'.$review->title.'</a><br />';
            
        }
    }
}