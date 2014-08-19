<?php
/**
 * Категории характеристик
 */
class CharacteristicsCatalogs extends CActiveRecord 
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
        return "{{characteristics_catalogs}}";
    }
    
    public function relations()
    {
        return array(
            "name"=>array(self::HAS_ONE, "CharacteristicsCatalogsNames", "catalog",
                "on"=>"lang = :lang",
                "params"=>array("lang"=>Yii::app()->language),
            ),
            "children"=>array(self::HAS_MANY, "CharacteristicsCatalogs", "parent"),
            "characteristics"=>array(self::HAS_MANY, "Characteristics", "catalog"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "priotity"=>Yii::t("model", "Порядок отображения"),
        );
    }
}