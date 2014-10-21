<?php

class Os extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{os}}";
    }

    public function beforeSave()
    {
        if (empty($this->link) || $this->isNewRecord)
            $this->link = Yii::app()->urlManager->translitUrl($this->name);
        return parent::beforeSave();
    }

}
