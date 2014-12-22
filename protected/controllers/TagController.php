<?php

class TagController extends Controller
{
    public function actionReviews($type, $tag)
    {
        
        
        $criteria = new CDbCriteria();
        $criteria->condition = "type = :type and link = :link and disabled = 0";
        $criteria->params = ['type'=>$type, 'link'=>$tag];
        $tag = Tags::model()->find($criteria);
        if (!$tag) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        }
        $criteria = new CDbCriteria();
        $criteria->condition = "t.tag = :tag and review_data.lang = :lang";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.review';
        
        $reviewsTags = ReviewsTags::model()->with(['review_data'])->findAll($criteria);
        $this->render("reviews", ['reviewsTags'=>$reviewsTags, 'tag'=>$tag]);
        
    }
    
    public function actionNews($type, $tag)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "type = :type and link = :link and disabled = 0";
        $criteria->params = ['type'=>$type, 'link'=>$tag];
        $tag = Tags::model()->find($criteria);
        if (!$tag) {
            Yii::app()->request->redirect("/", true, 302);
            exit();
        }
        $criteria = new CDbCriteria();
        $criteria->condition = "t.tag = :tag and news_data.lang = :lang";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.news';
        $criteria->order = "news_data.created desc";
        $newsCount = NewsTags::model()->with(['news_data'])->count($criteria);
        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);
        $newsTags = NewsTags::model()->with(['news_data'])->findAll($criteria);
        $this->render("news", ['newsTags'=>$newsTags, 'tag'=>$tag, 'pages'=>$pages]);
    }
}