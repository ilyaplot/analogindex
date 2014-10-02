<?php
class SourcesGsmarena extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{sources_gsmarena}}";
    }
    
    public function relations() {
        return array(
            "file_data"=>array(self::BELONGS_TO, "SourcesGsmarenaFiles", "file",
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
