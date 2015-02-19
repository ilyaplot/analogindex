<?php
class SpecificationsValues extends CActiveRecord
{
    const FORMAT_RAW = 0;
    const FORMAT_OBJECT = 1;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{specifications_values}}";
    }

}
