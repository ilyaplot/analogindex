<?php

class TestCommand extends CConsoleCommand
{
    public function beforeAction($action, $params)
    {
        date_default_timezone_set("Europe/Moscow");
        return parent::beforeAction($action, $params);
    }
    
    public function actionFilter()
    {
        //exit(0);
        $criteria = new CDbCriteria();
        $criteria->order = "id desc";
        $criteria->condition = "has_filtered = 0";
        $criteria->limit = 15;
        //$criteria->condition = 'id = 151121';
        $articles = Articles::model()->findAll($criteria);
        $filter = new ArticlesFilter();
        
        foreach($articles as $article) {
            echo date("Y-m-d H:i:s ").$article->id.PHP_EOL;
            $article = $filter->filter($article);
            $article->save();
        }
        //GoodsArticles::model()->filter();
        echo PHP_EOL;
    }
    
    public function actionFilterThread()
    {
        //exit(0);
        $criteria = new CDbCriteria();
        $criteria->condition = "has_filtered = 0 and id > ".rand(1,145330);
        $criteria->limit = 15;
        //$criteria->condition = 'id = 74170';
        $articles = Articles::model()->findAll($criteria);
        $filter = new ArticlesFilter();
        
        foreach($articles as $article) {
            echo date("Y-m-d H:i:s ").$article->id.PHP_EOL;
            $article = $filter->filter($article);
            $article->save();
        }
        //GoodsArticles::model()->filter();
        echo PHP_EOL;
    }

    
    public function actionSpec()
    {
        $model = new SpecificationsValues();
        $model->setAttribute('raw', 12);
    }
}
