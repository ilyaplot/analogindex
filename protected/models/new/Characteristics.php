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
               "on"=>"name.lang = :lang", 
               "params"=>array("lang"=>Yii::app()->language),
            ),
            "value"=>array(self::HAS_ONE, "GoodsCharacteristics", "characteristic", 
                "on"=>"value.lang = :lang",
                "params"=>array("lang"=>Yii::app()->language),
            )
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "formatter"=>Yii::t("model", "Форматтер"),
            "catalog"=>Yii::t("model", "Категория"),
            "parent"=>Yii::t("model", "Родительский элемент"),
            "priotity"=>Yii::t("model", "Порядок отображения"),
        );
    }
    
    public function getFormattedValue()
    {
        if (!isset($this->value))
            return null;
        $formatter = $this->formatter;
        return Yii::app()->format->$formatter($this->value->value);
    }
}
