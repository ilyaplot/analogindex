<?php

class NewsTags extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{news_tags}}";
    }
    
    public function rules()
    {
        return [
            ['news, tag', 'required'],
            ['news, tag', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['news', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'news', 
                'className'=>'NewsTags', 
                'criteria'=>['condition'=>'tag = :tag', 'params'=>['tag'=>  $this->tag]]
            ]
        ];
    }
    
    public function relations()
    {
        return [
            'news_data' => [self::BELONGS_TO, 'News', 'news'],
            'tag_data' => [self::BELONGS_TO, 'Tags', 'tag', 'condition'=>'tag_data.disabled = 0'],
        ];
    }
}


