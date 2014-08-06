<?php
/**
 * Синонимы для товаров
 */
class GoodsSynonims extends CActiveRecord
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
        return "{{goods_synonims}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "name"=>Yii::t("model", "Синоним"),
            "visibled"=>Yii::t("model", "Отображать"),
        );
    }
}