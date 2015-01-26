<?php
class GoogleTrendsContent extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{google_trends_content}}";
    }

}
