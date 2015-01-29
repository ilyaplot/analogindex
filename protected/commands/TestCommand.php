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
        $criteria = new CDbCriteria();
        $criteria->order = "id desc";
        $articles = Articles::model()->findAll($criteria);
        $filter = new ArticlesFilter();
        
        foreach($articles as $article) {
            echo ".";
            $article = $filter->filter($article);
            $article->save();
        }
        GoodsArticles::model()->filter();
        echo PHP_EOL;
    }
}
