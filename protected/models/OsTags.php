<?php

class OsTags extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{os_tags}}";
    }
    
    public function rules()
    {
        return [
            ['os, tag', 'required'],
            ['os, tag', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['os', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'os', 
                'className'=>'OsTags', 
                'criteria'=>['condition'=>'tag = :tag', 'params'=>['tag'=>  $this->tag]]
            ]
        ];
    }
}

