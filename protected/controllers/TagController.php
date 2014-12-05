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
        $criteria->condition = "t.tag = :tag and review_data.lang = :lang";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.review';
        
        echo $tag->name."<br />".PHP_EOL;
        $reviewsTags = ReviewsTags::model()->with(['review_data'])->findAll($criteria);
        foreach ($reviewsTags as $link) {
            $review = $link->review_data;
            if (empty($review->goods_data->brand_data->link))
                continue;
            
            echo '<a href="'.Yii::app()->createUrl("site/review", array("goods" => $review->goods_data->brand_data->link . "-" . $review->goods_data->link, "link" => $review->link, "id" => $review->id, "language" => Language::getCurrentZone())) .'" class="link-replyView">'.$review->title.'</a><br />';
            
        }
    }
    
    public function actionNews($type, $tag)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "type = :type and link = :link";
        $criteria->params = ['type'=>$type, 'link'=>$tag];
        $tag = Tags::model()->find($criteria);
        if (!$tag) {
            throw new CHttpException(404);
        }
        $criteria = new CDbCriteria();
        $criteria->condition = "t.tag = :tag and news_data.lang = :lang";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.news';
        
        echo $tag->name."<br />".PHP_EOL;
        
        $newsTags = NewsTags::model()->with(['news_data'])->findAll($criteria);
        foreach ($newsTags as $link) {
            $news = $link->news_data;
            $link = Yii::app()->createAbsoluteUrl("news/index", ['link'=>$news->link, 'id'=>$news->id, 'language'=>  Language::getCurrentZone()]);
            echo CHtml::link($news->title, $link)."<br/ >".PHP_EOL;
        }
    }
}