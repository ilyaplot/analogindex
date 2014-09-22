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
    
    public function rules() {
        return array(
            array('goods, value', 'required'),
            array('user', 'required', 'on'=>'vote'),
            array('value', 'numerical', 'on'=>'vote',
                'allowEmpty'=>false, 
                'min'=>1, 
                'max'=>5, 
                'integerOnly'=>true, 
            ),
            array('goods', 'unique', 'on'=>'vote', 
                'className'=>'RatingsGoods', 
                'attributeName'=>'goods',
                'criteria'=>array(
                    "condition"=>"user = :user",
                    "params" => array("user"=>$this->user),
                ),
            ),
        );
    }
}