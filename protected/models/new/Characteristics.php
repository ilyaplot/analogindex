<?php
/**
 * Характеристики
 */
class Characteristics extends CActiveRecord {

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getDbConnection() {
        return Yii::app()->newdb;
    }

    public function tableName()
    {
        return "{{characteristics}}";
    }

    public function relations()
    {
        return array(
            "name"=>array(self::HAS_ONE, "CharacteristicsNames", "characteristic",
               "condition"=>"lang = '".Yii::app()->language."'", 
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "unit"=>Yii::t("model", "Единица измерения"),
            "factor"=>Yii::t("model", "Множитель"),
            "catalog"=>Yii::t("model", "Категория"),
            "parent"=>Yii::t("model", "Родительский элемент"),
            "priotity"=>Yii::t("model", "Порядок отображения"),
        );
    }
}
