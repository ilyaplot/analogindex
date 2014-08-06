<?php
/**
 * Изображения товаров
 */
class GoodsImages extends CActiveRecord
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
        return "{{goods_images}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "image"=>Yii::t("model", "Картинка"),
            "priority"=>Yii::t("model", "Порядок сортировки"),
        );
    }
}