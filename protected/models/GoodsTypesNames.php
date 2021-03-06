<?php

/**
 * Названия типов товаров
 */
class GoodsTypesNames extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{goods_types_names}}";
    }

    public function relations()
    {
        return parent::relations();
    }

    public function attributeLabels()
    {
        return array(
            "type" => Yii::t("model", "Тип товара"),
            "name" => Yii::t("model", "Наименование"),
            "lang" => Yii::t("model", "Код языка"),
            "video_search_string" => Yii::t("model", "Строка printf для поиска видео"),
        );
    }

}
