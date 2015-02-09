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
        if ($this->isNewRecord) {
            $criteria = new CDbCriteria();
            $criteria->condition = '`from` = :from or `to` = :to or `from` = :to or `to` = :from';
            $criteria->params = [
                'from'=> $this->from, 
                'to'=>  $this->to
            ];
            self::model()->deleteAll($criteria);
        }
        return parent::beforeSave();
    }
    
    public function updateCounter()
    {
        if (!$this->isNewRecord) {
            $this->counter = $this->counter + 1;
            $this->save();
        }
    }
}
