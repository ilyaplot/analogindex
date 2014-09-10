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
    
    
    public function tableName()
    {
        return "{{ratings_faq}}";
    }
}