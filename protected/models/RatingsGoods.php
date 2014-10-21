<?php

/**
 * Оценки товаров
 */
class RatingsGoods extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{ratings_goods}}";
    }

    public function rules()
    {
        return array(
            array('goods, value', 'required'),
            array('user', 'required', 'on' => 'vote'),
            array('user', 'exist', 'on' => 'vote',
                'allowEmpty' => false,
                'className' => 'Users',
                'attributeName' => 'id',
            ),
            array('value', 'numerical', 'on' => 'vote',
                'allowEmpty' => false,
                'min' => 0.5,
                'max' => 5,
                'integerOnly' => false,
            ),
            array('goods', 'unique', 'on' => 'vote',
                'className' => 'RatingsGoods',
                'attributeName' => 'goods',
                'criteria' => array(
                    "condition" => "user = :user",
                    "params" => array("user" => $this->user),
                ),
            ),
        );
    }

}
