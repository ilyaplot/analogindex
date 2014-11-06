<?php
class Language
{
    
    public static $zones = array(
        'ru'=>'ru',
        'com'=>'en',
    );
    
    
    public static function getZoneForLang($lang)
    {
        $langs = array_flip(self::$zones);
        return isset($langs[$lang]) ? $langs[$lang] : null;
    }

    public static function getLangForZone($zone)
    {
        return isset(self::$zones[$zone]) ? self::$zones[$zone] : null;
    }
    
    public static function getCurrentZone()
    {
        $language = Yii::app()->getLanguage();
        $language = isset(self::$zones[$language]) ? self::$zones[$language] : $language;
        $zones = array_keys(self::$zones, $language);
        return isset($zones[0]) ? $zones[0] : 'ru';
    }
    
    public static function getCurrentLang()
    {
        $language = Yii::app()->request->getParam('language', 'ru');
        $language = isset(self::$zones[$language]) ? self::$zones[$language] : $language;
        return $language;
    }
}