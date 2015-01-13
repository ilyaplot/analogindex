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

    
    public function rules()
    {
        return [
            ['from', 'unique', 'allowEmpty'=>false],
            ['to', 'unique', 'allowEmpty'=>false],
        ];
    }
}
