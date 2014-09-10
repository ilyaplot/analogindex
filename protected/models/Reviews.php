<?php
/**
 * Обзоры
 */
class Reviews extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{reviews}}";
    }
    
    public function relations()
    {
        return array(
            "rating"=>array(self::HAS_ONE, "RatingsReviews", "review", 
                "select"=>"AVG(rating.value) as value",
            )
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "link"=>Yii::t("model", "Ссылка"),
            "lang"=>Yii::t("model", "Код языка"),
            "author"=>Yii::t("model", "Автор"),
            "title"=>Yii::t("model", "Заголовок"),
            "content"=>Yii::t("model", "Текст"),
            "priority"=>Yii::t("model", "Порядок сортировки"),
            "source"=>Yii::t("model", "Ссылка на оригинал"),
            "disabled"=>Yii::t("model", "Не показывать"),
        );
    }
}