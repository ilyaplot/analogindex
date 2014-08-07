<?php
/**
 * Изображения
 */
class Images extends CActiveRecord
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
        return "{{images}}";
    }
    
    public function relations()
    {
        return array(
            "file_data"=>array(self::BELONGS_TO, "Files", "file"),
            "resized"=>array(self::HAS_MANY, "ImagesResized", "image"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "file"=>Yii::t("model", "Файл"),
            "disabled"=>Yii::t("model", "Не отображать"),
            "size"=>Yii::t("model", "Типовой размер"),
            "width"=>Yii::t("model", "Ширина"),
            "height"=>Yii::t("model", "Высота"),
        );
    }
}