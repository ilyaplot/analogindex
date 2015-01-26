<?php
class SourcesGoogleTrends extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{sources_google_trends}}";
    }
    
    public function getFolder()
    {
        return ceil($this->getPrimaryKey()/10000);
    }
}
