<?php

class BrandsNews extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{brands_news}}";
    }

    public function rules()
    {
        return [
            ['brand, news', 'required'],
            ['brand, news', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['brand', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'brand', 
                'className'=>'BrandsNews', 
                'criteria'=>['condition'=>'news = :news', 'params'=>['news'=>  $this->news]]
            ]
        ];
    }
}
