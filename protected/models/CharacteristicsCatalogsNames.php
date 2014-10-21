<?php

/**
 * Переводы для характеристик
 */
class CharacteristicsCatalogsNames extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{characteristics_catalogs_names}}";
    }

    public function relations()
    {
        return parent::relations();
    }

    public function attributeLabels()
    {
        return array(
            "catalog" => Yii::t("model", "Категория"),
            "lang" => Yii::t("model", "Код языка"),
            "name" => Yii::t("model", "Наименование"),
            "description" => Yii::t("model", "Описание"),
        );
    }

}
