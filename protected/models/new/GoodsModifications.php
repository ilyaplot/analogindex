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
            "comment_ru"=>array(self::HAS_ONE, "ModificationsComments", "modification",
               "on"=>"lang = 'ru'", 
            ),
            "comment_en"=>array(self::HAS_ONE, "ModificationsComments", "modification",
               "on"=>"lang = 'en'", 
            ),
            "parent"=>array(self::BELONGS_TO, "Goods", "goods_parent"),
            "children"=>array(self::BELONGS_TO, "Goods", "goods_children"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "goods_parent"=>Yii::t("model", "Главный товар"),
            "goods_children"=>Yii::t("model", "Подчиненный товар"),
        );
    }
    
    public function rules()
    {
        return array(
            array('goods_parent, goods_children', 'required'),
            array('goods_parent, goods_children', 'exists', 'class'=>'Goods', 'attributeName'=>'id', 'allowEmpty'=>false)
        );
    }
}