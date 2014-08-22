<?php
class SourcesSmartphoneua extends CActiveRecord
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
        return "{{sources_smartphoneua}}";
    }
    
    public function relations() {
        return array(
            "file_data"=>array(self::BELONGS_TO, "SourcesSmartphoneuaFiles", "file",
                "joinType"=>"inner join",
            ),
        );
    }
    
    public function rules() {
        return array(
            array("url", "required"),
            array("url", "unique"),
        );
    }
}
