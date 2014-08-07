<?php
/**
 * Видео для товаров
 */
class Videos extends CActiveRecord
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
        return "{{videos}}";
    }
    
    public function relations() 
    {
        return array(
            "rating"=>array(self::HAS_ONE, "RatingsVideos", "video", 
                "select"=>"AVG(rating.value) as value",
            )
        );
    }
    
    public function attributeLabels()
    {
        return array();
    }
}

