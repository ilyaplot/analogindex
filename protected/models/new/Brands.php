<?php
/**
 * Производители
 */
class Brands extends CActiveRecord
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
        return "{{brands}}";
    }
    
    public function relations()
    {
        return array(
            "goods"=>array(self::HAS_MANY, "Goods", "brand"),
            "description"=>array(self::HAS_ONE, "BrandsDescriptions", "brand", 
                "condition"=>"lang = '".Yii::app()->language. "'",
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Наименование"),
            "link"=>Yii::t("model", "Ссылка"),
            "logo"=>Yii::t("model", "Логотип"),
        );
    }
}