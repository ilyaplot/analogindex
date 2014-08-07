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
        return array(
            "comment"=>array(self::HAS_ONE, "ModificationsComments", "modification",
               "on"=>"lang = '" .Yii::app()->language."'", 
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "goods_parent"=>Yii::t("model", "Главный товар"),
            "goods_children"=>Yii::t("model", "Подчиненный товар"),
        );
    }
}