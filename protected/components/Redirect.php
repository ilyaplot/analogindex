<?php

class Redirect extends CBaseUrlRule
{
    public function createUrl($manager, $route, $params, $ampersand)
    {
        return false;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {

        if ($redirect = Redirects::model()->findByAttributes(["from"=>"/".$rawPathInfo])) {
            Yii::app()->request->redirect("http://analogindex.". Language::getCurrentZone()
                .$redirect->to, true, $redirect->code);
        }
        return false;  
    }
}
