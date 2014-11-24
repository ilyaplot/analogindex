<?php

class BrandsTags extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{brands_tags}}";
    }
    
    public function rules()
    {
        return [
            ['brand, tag', 'required'],
            ['brand, tag', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['brand', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'brand', 
                'className'=>'BrandsTags', 
                'criteria'=>['condition'=>'tag = :tag', 'params'=>['tag'=>  $this->tag]]
            ]
        ];
    }

}
