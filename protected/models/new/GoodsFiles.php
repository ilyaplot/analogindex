<?php
/**
 * Дополнительные файлы для товаров
 */
class GoodsFiles extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{goods_files}}";
    }
    
    public function relations()
    {
        return array(
            "file"=>array(self::BELONGS_TO, "Files", "file"),
            "description"=>array(self::HAS_ONE, "GoodsFilesDescriptions", "file", 
                "on"=>"lang = '".Yii::app()->language."'",
            )
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "file"=>Yii::t("model", "Файл"),
            "priority"=>Yii::t("model", "Порядок сортировки"),
        );
    }
}