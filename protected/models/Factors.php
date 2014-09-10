<?php
/**
 * Множители
 */
class Factors extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    
    public function tableName()
    {
        return "{{factors}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "value"=>Yii::t("model", "Значение множителя"),
            "is_double"=>Yii::t("model", "Дробное"),
            "is_unsigned"=>Yii::t("model", "Целое"),
            "default_factor"=>Yii::t("model", "Значение по умолчанию")
        );
    }
    
}