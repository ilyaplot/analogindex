<?php
class Controller extends CController
{

    
    
    public function beforeAction($action) {
        
        Yii::app()->setLanguage(Language::getCurrentLang());
        if (!preg_match("~.*/user/login.html~", Yii::app()->request->urlReferrer))
            Yii::app()->user->setReturnUrl(Yii::app()->request->urlReferrer);
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
             header('X-UA-Compatible: IE=edge,chrome=1');
        
        $this->setPageTitle("Analogindex");
        
        return parent::beforeAction($action);
    }
}
