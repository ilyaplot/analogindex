<?php
class Colors extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{colors}}";
    }
    
    public function getAll($group=true)
    {
        
        $criteria = new CDbCriteria();
        if ($group) {
            $criteria->group = "code";
        }
        
        return self::model()->findAll($criteria);
    }

}

