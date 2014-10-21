<?php

/**
 * Переводы характеристик
 */
class CharacteristicsNames extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{characteristics_names}}";
    }

    public function relations()
    {
        return parent::relations();
    }

    public function attributeLabels()
    {
        return array(
            "characteristic" => Yii::t("model", "Характеристика"),
            "lang" => Yii::t("model", "Код языка"),
            "name" => Yii::t("model", "Наименование"),
            "description" => Yii::t("model", "Описание"),
        );
    }

}
