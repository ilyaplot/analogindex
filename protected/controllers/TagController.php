<?php

class TagController extends Controller
{    
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
        $criteria->condition = "t.tag = :tag and articles_data.lang = :lang and articles_data.type = 'news'";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.article';
        $criteria->order = "articles_data.created desc";
        $newsCount = ArticlesTags::model()->with(['articles_data'])->count($criteria);
        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);
        $newsTags = ArticlesTags::model()->cache(60*60*2)->with(['articles_data'])->findAll($criteria);
        $this->render("news", ['newsTags'=>$newsTags, 'tag'=>$tag, 'pages'=>$pages]);
    }
    
    public function actionOpinion($type, $tag)
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
        $criteria->condition = "t.tag = :tag and articles_data.lang = :lang and articles_data.type = 'opinion'";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.article';
        $criteria->order = "articles_data.created desc";
        $newsCount = ArticlesTags::model()->with(['articles_data'])->count($criteria);
        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);
        $newsTags = ArticlesTags::model()->cache(60*60*2)->with(['articles_data'])->findAll($criteria);
        $this->render("news", ['newsTags'=>$newsTags, 'tag'=>$tag, 'pages'=>$pages]);
    }
    
    public function actionHowto($type, $tag)
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
        $criteria->condition = "t.tag = :tag and articles_data.lang = :lang and articles_data.type = 'howto'";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.article';
        $criteria->order = "articles_data.created desc";
        $newsCount = ArticlesTags::model()->with(['articles_data'])->count($criteria);
        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);
        $newsTags = ArticlesTags::model()->cache(60*60*2)->with(['articles_data'])->findAll($criteria);
        $this->render("news", ['newsTags'=>$newsTags, 'tag'=>$tag, 'pages'=>$pages]);
    }
    
    public function actionReview($type, $tag)
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
        $criteria->condition = "t.tag = :tag and articles_data.lang = :lang and articles_data.type = 'review'";
        $criteria->params = ['tag'=>$tag->id, 'lang'=>Yii::app()->language];
        $criteria->group = 't.article';
        $criteria->order = "articles_data.created desc";
        $newsCount = ArticlesTags::model()->with(['articles_data'])->count($criteria);
        $pages = new CPagination($newsCount);
        $pages->setPageSize(15);
        $pages->applyLimit($criteria);
        $newsTags = ArticlesTags::model()->cache(60*60*2)->with(['articles_data'])->findAll($criteria);
        $this->render("news", ['newsTags'=>$newsTags, 'tag'=>$tag, 'pages'=>$pages]);
    }
}