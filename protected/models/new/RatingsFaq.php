<?php
/**
 * Оценки вопросов
 */
class RatingsFaq extends CActiveRecord
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
        return "{{ratings_faq}}";
    }
}