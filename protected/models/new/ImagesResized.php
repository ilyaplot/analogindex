<?php
/**
 * Изображения после ресайза
 */
class ImagesResized extends CActiveRecord
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
        return "{{images_resized}}";
    }
    
    public function relations()
    {
        return array(
            "file_data"=>array(self::BELONGS_TO, "Files", "file"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "file"=>Yii::t("model", "Файл"),
            "image"=>Yii::t("model", "Картинка"),
            "size"=>Yii::t("model", "Типовой размер"),
            "width"=>Yii::t("model", "Ширина"),
            "height"=>Yii::t("model", "Высота"),
        );
    }
}