<?php

class YmlModule extends CWebModule
{
    public function init()
    {
        Yii::import("application.modules.yml.models.*");
        return parent::init();
    }
    
}
