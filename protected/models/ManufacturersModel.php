<?php
class ManufacturersModel extends Model
{
    public $id;
    public $name;
    public $sql;
    
    public function __construct() {
        $this->sql = Yii::app()->db;
        
    }
    
    /**
     * Получает список производителей
     * @param type $type Тип товаров
     * 
     * @todo Добавить вильтры
     */
    public function getList($type)
    {
        $query = "SELECT * FROM manufacturers";
        $list = $this->sql->createCommand($query)->queryAll();
        return $list;
    }
    
    /**
     * Возвращает производителя по id
     * @param type $id
     * @return type
     */
    public function getById($id)
    {
        $query = "SELECT
            m.id,
            m.name,
            m.link
        FROM manufacturers m 
        WHERE id = :id";
        $result = $this->sql->createCommand($query)->queryRow(true, array('id'=>$id));
        return $result;
    }
    
    /**
     * Добавляет нового производителя или возвращает существующего
     * @param int $type
     * @param str $name
     * @return int Id производителя
     */
    public function add($type, $name, $description = "", $logo = 0)
    {
        $select = "SELECT m.id FROM manufacturers m WHERE link LIKE :link";
        $insert = "INSERT INTO manufacturers (name, link, description, logo) VALUES (:name, :link, :description, :logo)";
        
        $name = self::prepeareName($name);
        $link = $this->str2url($name);
        if (mb_strlen($name, 'UTF-8') < 2)
            throw new Exception ('Manufactirer name {$name} too short.');
        
        // Если не нашли по имени, добавляем нового
        if (!$id = $this->sql->createCommand($select)->queryScalar(array('link'=>$link)))
        {
            try 
            {
                $this->sql->createCommand($insert)->execute(array(
                    'name'=>$name,
                    'link'=>$link,
                    'description'=>$description,
                    'logo'=>$logo,
                ));
                $id = $this->sql->lastInsertID;
            } 
            catch (Exception $ex) 
            {
                throw $ex;
            }
        }
        return $id;
    }
}