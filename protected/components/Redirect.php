<?php

class Redirect extends CBaseUrlRule
{
    const MOVED_PERMANENTLY = 301;
    const MOVED_TEMPORARILY = 302;

    public function createUrl($manager, $route, $params, $ampersand)
    {
        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        if ($redirect = Redirects::model()->findByAttributes(["from"=>"/".$rawPathInfo])) {
            
            $redirect->updateCounter();
            
            
            Yii::app()->request->redirect("http://analogindex.". Language::getCurrentZone()
                .$redirect->to, true, $redirect->code);
        }
        return false;  
    }
}
