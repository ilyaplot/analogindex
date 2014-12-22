<?php
class ExportController extends CController
{
    public static $export;
    public function beforeAction($action)
    {
        self::$export = new Export();
        return parent::beforeAction($action);
    }

    /**
     * Возвращает новости по тэгам
     * @param string $tags тэги через запятую
     * @param string $lang язык ru|en
     * @param int $limit Количество новостей
     * 
     * @example http://analogindex.ru/export/news?tags=tag1,tag2,tag+3&lang=ru&limit=10
     */
    public function actionNews($tags, $lang, $limit)
    {
        echo self::$export->News($tags, $lang, $limit);
    }
    
    /**
     * Отзывы по аппаратам, к которым привязаны тэги
     * @param string $tags Тэги через запятую
     * @param string $lang язык ru|en
     * @param int $limit Количество отзывов
     * 
     * @example http://analogindex.ru/export/reviews?tags=tag1,tag2,tag+3&lang=ru&limit=10
     */
    public function actionReviews($tags, $lang, $limit = 10)
    {
        echo self::$export->Reviews($tags, $lang, $limit);
    }
    
    /**
     * Видео по аппаратам, к которым привязаны тэги
     * @param string $tags Тэги через запятую
     * @param string $lang язык ru|en
     * 
     * @example http://analogindex.ru/export/videos?tags=tag1,tag2,tag+3&lang=ru
     */
    public function actionVideos($tags, $lang)
    {
        echo self::$export->Videos($tags, $lang);
    }

    /**
     * Технические характеристики аппаратов по тэгам
     * @param string $tags Тэги через запятую
     * @param string $lang язык ru|en
     * @param int $limit Максимальное количество устройств
     * 
     * @example http://analogindex.ru/export/compare?tags=tag1,tag2,tag+3&lang=ru
     */
    public function actionCompare($tags, $lang, $limit=20)
    {
        echo self::$export->Compare($tags, $lang, $limit);
    }
    
    /**
     * Упоминаемые аппараты по тэгам
     * @param string $tags Тэги через запятую
     * @param string $lang язык ru|en
     * @param int $limit Максимальное количество устройств
     * 
     * @example http://analogindex.ru/export/products?tags=tag1,tag2,tag+3&lang=ru
     */
    public function actionProducts($tags, $lang, $limit=20)
    {
        echo self::$export->Products($tags, $lang, $limit);
    }
    
    /**
     * Google Trends для аппаратов по тэгам
     * @param string $tags Тэги через запятую
     * @param string $lang язык ru|en
     * @param int $limit Максимальное количество устройств
     */
    public function actionTrends($tags, $lang, $limit=20)
    {
        echo self::$export->Trends($tags, $lang, $limit);
    }
}

