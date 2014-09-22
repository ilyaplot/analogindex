<?php
class ReviewsImages extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{reviews_images}}";
    }
    
    public function relations() {
        return array(
            "file_data"=>array(self::BELONGS_TO, "Files", "file"),
        );
    }
}