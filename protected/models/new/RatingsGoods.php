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
}