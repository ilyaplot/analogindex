<?php

class BrandsArticles extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{brands_articles}}";
    }

    public function rules()
    {
        return [
            ['brand, article', 'required'],
            ['brand, article', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['brand', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'brand', 
                'className'=>'BrandsArticles', 
                'criteria'=>['condition'=>'article = :article', 'params'=>['article'=>  $this->article]]
            ],
            ['brand', 'exist', 'attributeName'=>'id', 'className'=>'Brands'],
        ];
    }
}
