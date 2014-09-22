<?php
class Proxy extends CActiveRecord
{
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function tableName() {
        return "proxy";
    }
    
    public function getDbConnection() 
    {
        return Yii::app()->reviews;
    }
}