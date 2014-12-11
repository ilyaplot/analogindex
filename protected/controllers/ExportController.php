<?php
class ExportController extends CController
{
    public static $export;
    public function beforeAction($action)
    {
        self::$export = new Export();
        return parent::beforeAction($action);
    }

    public function actionNews($tags, $lang, $limit)
    {
        echo self::$export->News($tags, $lang, $limit);
    }
    
    public function actionReviews($tags, $lang, $limit = 10)
    {
        echo self::$export->Reviews($tags, $lang, $limit);
    }
    
    public function actionVideos($tags, $lang)
    {
        echo self::$export->Videos($tags, $lang);
    }

    
    public function actionCompare($tags, $lang, $limit=20)
    {
        echo self::$export->Compare($tags, $lang, $limit);
    }
    
    public function actionProducts($tags, $lang, $limit=20)
    {
        echo self::$export->Products($tags, $lang, $limit);
    }
    
    public function actionTrends($tags, $lang, $limit=20)
    {
        echo self::$export->Trends($tags, $lang, $limit);
    }
}

