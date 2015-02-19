<?php
class SpecificationsCategories extends CActiveRecord
{
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return [
            ['lang, name', 'required'],
            ['name', 'length', 'min'=>2, 'max'=>333, 'allowEmpty'=>false],
            ['description', 'length', 'min'=>10, 'max'=>1024, 'allowEmpty'=>true],
        ];
    }

    public function tableName()
    {
        return "{{specifications_categories}}";
    }

    
    public function relations()
    {
        return [
            'specifications'=>[self::HAS_MANY, 'Specifications', ['category'=>'key'],
                'on'=>'t.lang = specifications.lang',
                'joinType' => 'inner join',
            ],
        ];
    }


}
