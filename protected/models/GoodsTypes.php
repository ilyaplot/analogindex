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
    
    public function tableName() 
    {
        return "{{goods_types}}";
    }
    
    public function relations()
    {
        return array(
            "name"=>array(self::HAS_ONE, "GoodsTypesNames", "type",
                "on" => "lang = '".Yii::app()->language."'",
            ),
            "goods"=>array(self::HAS_MANY, "Goods", "type", 
                "on"=>"goods.is_modification = false"
            ),
            "page_goods"=>array(self::HAS_ONE, "Goods", "type",
                "joinType"=>"INNER JOIN",
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "link"=>Yii::t("model", "Ссылка"),
        );
    }
}