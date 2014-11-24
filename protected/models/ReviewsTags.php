<?php

class ReviewsTags extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{reviews_tags}}";
    }

    public function rules()
    {
        return [
            ['review, tag', 'required'],
            ['review, tag', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['review', 'unique', 'allowEmpty' => false,
                'attributeName' => 'review',
                'className' => 'ReviewsTags',
                'criteria' => ['condition' => 'tag = :tag', 'params' => ['tag' => $this->tag]]
            ]
        ];
    }

    public function relations()
    {
        return [
            'review_data' => [self::BELONGS_TO, 'Reviews', 'review'],
            'tag_data' => [self::BELONGS_TO, 'Tags', 'tag'],
        ];
    }

}
