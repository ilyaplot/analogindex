<?php
/**
 * Файлы
 */
class Files extends CActiveRecord
{
    public $storage;
    
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }
    
    public function getDbConnection() {
        return Yii::app()->newdb;
    }
    
    public function afterConstruct() 
    {
        $this->storage = Yii::app()->storage;
        return parent::afterConstruct();
    }

    public function afterFind() 
    {
        $this->storage = Yii::app()->storage;
        return parent::afterFind();
    }

    public function tableName() 
    {
        return "{{files}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Имя файла"),
            "size"=>Yii::t("model", "Размер"),
            "mime_type"=>Yii::t("model", "Тип"),
            "updated"=>mYii::t("model", "Последнее изменение"),
        );
    }
    
}