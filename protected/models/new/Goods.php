<?php
/**
 * Товары
 */
class Goods extends CActiveRecord
{
    // Производитель
    public $brand_name;
    // Часть ссылки на производителя
    public $brand_link;
    // Тип товара
    public $type_name;
    // Часть ссылки на тип товара
    public $type_link;
    
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }
    
    public function getDbConnection() {
        return Yii::app()->newdb;
    }
    
    public function tableName() 
    {
        return "{{goods}}";
    }
    
    public function getBrand()
    {
        $query = "select name, link from {{brands}} b where b.id = :brand";
        $connection = $this->getDbConnection();
        $brand = $connection->createCommand($query)->queryRow(true, array('brand'=>$this->brand));
        $this->brand_name = $brand['name'];
        $this->brand_link = $brand['link'];
    }
    
    public function getType()
    {
        $query = "select name, link from {{goods_types}} t where t.id = :type";
        $connection = $this->getDbConnection();
        $type = $connection->createCommand($query)->queryRow(true, array('type'=>$this->type));
        $this->type_name = $type['name'];
        $this->type_link = $type['link'];
    }

    public function afterFind() 
    {
        if (!$this->brand)
            return false;
        if (!$this->type)
            return false;
        
        $this->getBrand();
        $this->getType();
        
        return parent::afterFind();
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Наименование"),
            "link"=>Yii::t("model", "Ссылка"),
            "type"=>Yii::t("model", "Тип"),
            "brand"=>Yii::t("model", "Производитель"),
            "is_modification"=>Yii::t("model", "Модификация"),
        );
    }
}