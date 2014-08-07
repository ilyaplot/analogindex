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
        return array(
            "name"=>array(self::HAS_ONE, "GoodsTypesNames", "type",
                "on" => "lang = '".Yii::app()->language."'",
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