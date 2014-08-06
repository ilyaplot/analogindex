<?php
/**
 * Характеристики товаров
 */
class GoodsCharacteristics extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function getDbConnection() {
        return Yii::app()->newdb;
    }
    
    public function tableName()
    {
        return "{{goods_characteristics}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "characteristic"=>Yii::t("model", "Характеристика"),
            "value"=>Yii::t("model", "Значение"),
            "lang"=>Yii::t("model", "Код языка"),
        );
    }
}