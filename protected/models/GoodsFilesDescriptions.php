<?php
/**
 * Описания к дополнительным файлам товара
 */
class GoodsFilesNames extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{goods_files_names}}";
    }
    
    public function attributeLabels()
    {
        return array(
            "lang"=>Yii::t("model", "Код языка"),
            "file"=>Yii::t("model", "Файл товара"),
            "description"=>Yii::t("model", "Описание"),
        );
    }
}

