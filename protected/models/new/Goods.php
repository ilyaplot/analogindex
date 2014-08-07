<?php
/**
 * Товары
 */
class Goods extends CActiveRecord
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
        return "{{goods}}";
    }
    
    public function relations()
    {
        return array(
            "brand_data"=>array(self::BELONGS_TO, "Brands", "brand"),
            "images"=>array(self::HAS_MANY, "GoodsImages", "goods", 
                "order"=>"images.priority"
            ),
            "reviews"=>array(self::HAS_MANY, "Reviews", "goods",
                "order"=>"priority desc", 
                "condition"=>"lang = '".Yii::app()->language ."'",
            ),
            "videos"=>array(self::HAS_MANY, "Videos", "goods",
                "order"=>"priority desc",
                "condition"=>"lang = '".Yii::app()->language ."'",
            ),
            "synonims"=>array(self::HAS_MANY, "GoodsSynonims", "goods",
                "select"=>"synonims.id, synonims.name, synonims.visibled",
            ),
            "faq"=>array(self::HAS_MANY, "Faq", "goods",
                "order"=>"priority desc",
                "condition"=>"lang = '".Yii::app()->language ."'",
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Наименование"),
            "link"=>Yii::t("model", "Ссылка"),
            "type"=>Yii::t("model", "Тип"),
            "brand"=>Yii::t("model", "Производитель"),
            "is_modification"=>Yii::t("model", "Модификация"),
        );
    }
}