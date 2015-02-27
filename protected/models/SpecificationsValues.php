<?php

class SpecificationsValues extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{specifications_values}}";
    }

    public function rules()
    {
        return [
            ['goods, specification, lang, raw', 'required'],
            ['specification, goods', 'type', 'type' => 'integer'],
            ['goods', 'unique',
                'attributeName' => 'goods',
                'className' => 'SpecificationsValues',
                'criteria' => ['condition' => 'specification = :specification and lang=:lang', 'params' => [
                        'specification' => $this->specification,
                        'lang' => $this->lang,
                    ]]
            ],
            ['goods', 'exists', 'class' => 'Goods', 'attributeName' => 'id'],
            ['specification', 'exists', 'class' => 'Specifications', 'attributeName' => 'key'],
        ];
    }

}
