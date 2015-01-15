<?php

class Redirects extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{redirects}}";
    }

    
    public function beforeSave()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = '`from` = :from or `to` = :to or `from` = :to or `to` = :from';
        $criteria->params = [
            'from'=> $this->from, 
            'to'=>  $this->to
        ];
        self::model()->deleteAll($criteria);
        return parent::beforeSave();
    }
}
