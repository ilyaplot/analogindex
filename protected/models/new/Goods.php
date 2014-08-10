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
            "type_data"=>array(self::BELONGS_TO, "GoodsTypes", "type"),
            "brand_data"=>array(self::BELONGS_TO, "Brands", "brand"),
            "images"=>array(self::HAS_MANY, "GoodsImages", "goods", 
                "order"=>"images.priority"
            ),
            "reviews"=>array(self::HAS_MANY, "Reviews", "goods",
                "order"=>"priority desc", 
                "on"=>"lang = '".Yii::app()->language ."'",
            ),
            "videos"=>array(self::HAS_MANY, "Videos", "goods",
                "order"=>"priority desc",
                "on"=>"lang = '".Yii::app()->language ."'",
            ),
            "synonims"=>array(self::HAS_MANY, "GoodsSynonims", "goods",
                "select"=>"synonims.id, synonims.name, synonims.visibled",
            ),
            "faq"=>array(self::HAS_MANY, "Faq", "goods",
                "order"=>"priority desc",
                "on"=>"lang = '".Yii::app()->language ."'",
            ),
            "rating"=>array(self::HAS_ONE, "RatingsGoods", "goods", 
                "select"=>"AVG(rating.value) as value",
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
    
    public function getPrimaryImage($size = null)
    {
        if (!$size)
            $size = Images::SIZE_WIDGET;
        
        $size = abs(intval($size));
        
        $image = GoodsImages::model()->with(array(
            "image_data"=>array(
                'joinType'=>'INNER JOIN',
                'limit'=>'1',
                'order'=>'t.priority desc, t.id asc',
                'on'=>'image_data.disabled = 0',
            ),
            "image_data.resized"=>array(
                'joinType'=>'INNER JOIN',
                'on'=>'resized.size = '.$size,
            ),
            "image_data.resized.file_data",
        ))->findByAttributes(array("goods"=>$this->getPrimaryKey()));
        return isset($image->image_data) ? $image->image_data : null; 
    }
}