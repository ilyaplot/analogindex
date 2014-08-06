<?php
class Controller extends CController
{

    
    
    public function beforeAction($action) {
        
        Yii::app()->setLanguage(Language::getCurrentLang());
        
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
             header('X-UA-Compatible: IE=edge,chrome=1');
        
        return parent::beforeAction($action);
    }
}
