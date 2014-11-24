<?php

class Topics extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{topic}}";
    }
    
    public function getDbConnection()
    {
        return Yii::app()->teta;
    }
}