<?php

class Gpu extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{gpu}}";
    }

    public function rules()
    {
        return array(
            array('name', 'length', 'min'=>3, 'max'=>100),
            array('link', 'unique', 'allowEmpty'=>false),
        );
    }
    
    public function beforeValidate()
    {
        $urlManager = isset(Yii::app()->urlManager) ? Yii::app()->urlManager : new UrlManager();
        
        $this->link = $urlManager->translitUrl($this->name);
        return parent::beforeValidate();
    }

}
