<?php
/**
 * Вопросы и ответы
 */
class Faq extends CActiveRecord
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
        return "{{faq}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "lang"=>Yii::t("model", "Код языка"),
            "question"=>Yii::t("model", "Текст вопроса"),
            "answer"=>Yii::t("model", "Текст ответа"),
            "source"=>Yii::t("model", "Ссылка на оригинал"),
            "updated"=>Yii::t("model", "Последние изменение"),
            "disabled"=>Yii::t("model", "Не отображать"),
            "priority"=>Yii::t("model", "Порядок сортировки"),
        );
    }
}