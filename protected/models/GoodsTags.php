<?php

class GoodsTags extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{goods_tags}}";
    }
    
    public function rules()
    {
        return [
            ['goods, tag', 'required'],
            ['goods, tag', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['goods', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'goods', 
                'className'=>'GoodsTags', 
                'criteria'=>['condition'=>'tag = :tag', 'params'=>['tag'=>  $this->tag]]
            ]
        ];
    }
}

