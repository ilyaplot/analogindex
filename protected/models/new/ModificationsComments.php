<?php
/**
 * Комментарии к модификациям
 */
class ModificationsComments extends CActiveRecord
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
        return "{{modifications_comments}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "modification"=>Yii::t("model", "Товар - модификация"),
            "lang"=>Yii::t("model", "Код языка"),
            "comment"=>Yii::t("model", "Текст комментария"),
        );
    }
}