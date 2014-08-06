<?php
/**
 * Типы товаров
 */
class GoodsTypes extends CActiveRecord
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
        return "{{goods_types}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Название"),
            "link"=>Yii::t("model", "Ссылка"),
        );
    }
}