<?php
class OffersModel extends Model
{
    public $sql;
   
    public function __construct() {
        $this->sql = Yii::app()->db;
    }

    /**
     * Получает список изображений
     * @param int $id id товара
     * @param int $type Тип товара
     */
    public function getList($id, $type)
    {
        $query = "SELECT * FROM offers";
        $list = $this->sql->createCommand($query)->queryAll();
        return $list;
    }
    
    public function getMinPrice($id, $type)
    {
        $query = "SELECT MIN(price) FROM offers_{$type} WHERE goods = :id LIMIT 1";
        $result = $this->sql->createCommand($query)->queryScalar(array('id'=>$id));
        return doubleval($result);
    }

    public function fillCharacteristics()
    {
        $this->characteristics;
    }
}