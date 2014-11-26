<?php
class News extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{news}}";
    }

    public function rules()
    {
        return [
            ['source_url, content', 'required'],
            ['source_url, content', 'type', 'type'=>'string', 'allowEmpty'=>false],
            ['content', 'length', 'min'=>10],
            ['source_url', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'source_url', 
                'className'=>'News', 
                'criteria'=>['condition'=>'source_url = :source_url', 'params'=>['source_url'=>  $this->source_url]]
            ]
        ];
    }
}
