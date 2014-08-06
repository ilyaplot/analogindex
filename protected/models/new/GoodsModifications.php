<?php
/**
 * Связь товаров - модицикаций
 */
class GoodsModifications extends CActiveRecord
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
        return "{{goods_modifications}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "goods_parent"=>Yii::t("model", "Главный товар"),
            "goods_children"=>Yii::t("model", "Подчиненный товар"),
        );
    }
}