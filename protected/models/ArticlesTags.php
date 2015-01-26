<?php

class ArticlesTags extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{articles_tags}}";
    }
    
    public function rules()
    {
        return [
            ['article, tag', 'required'],
            ['article, tag', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['article', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'article', 
                'className'=>'ArticlesTags', 
                'criteria'=>['condition'=>'tag = :tag', 'params'=>['tag'=>  $this->tag]]
            ]
        ];
    }
    
    public function relations()
    {
        return [
            'articles_data' => [self::BELONGS_TO, 'Articles', 'article'],
            'tag_data' => [self::BELONGS_TO, 'Tags', 'tag', 'condition'=>'tag_data.disabled = 0'],
        ];
    }
}


