<?php
/**
 * Категории характеристик
 */
class CharacteristicsCatalog extends CActiveRecord 
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
        return "{{characteristics_catalog}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "priotity"=>Yii::t("model", "Порядок отображения"),
        );
    }
}