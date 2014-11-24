<?php

/**
 * name - название тэга
 * type - тип тэга и первая часть ссылки
 * link - вторая часть ссылки
 * disabled - ключ неактивности тэга
 * 
 */
class Tags extends CActiveRecord
{

    const TYPE_BRAND = 'brand';
    const TYPE_PRODUCT = 'product';
    const TYPE_WORD = 'word';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{tags}}";
    }

    public function rules()
    {
        return [
            ['name, type, link', 'required'],
            ['name', 'length', 'min' => 3, 'max' => 100, 'allowEmpty' => false],
            ['disabled', 'type', 'type' => 'integer', 'allowEmpty' => true],
            ['link', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'link', 
                'className'=>'Tags', 
                'criteria'=>['condition'=>'type = :type', 'params'=>['type'=>  $this->type]]
            ]
        ];
    }

    public function beforeValidate()
    {
        $urlManager = new UrlManager();
        if ($this->isNewRecord) {
            $this->link = $urlManager->translitUrl(trim($this->name));
            $this->link = str_replace("_", "-", $this->link);
        }
        return parent::beforeValidate();
    }

    public function relations()
    {
        return [
            'reviews' => [self::HAS_MANY, 'ReviewsTags', 'tag'],
        ];
    }
}
