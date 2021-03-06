<?php

/**
 * Описания брендов
 */
class BrandsDescriptions extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{brands_descriptions}}";
    }

    public function relations()
    {
        return parent::relations();
    }

    public function attributeLabels()
    {
        return array(
            "brand" => Yii::t("model", "Производитель"),
            "lang" => Yii::t("model", "Код языка"),
            "description" => Yii::t("model", "Описание"),
        );
    }

}
