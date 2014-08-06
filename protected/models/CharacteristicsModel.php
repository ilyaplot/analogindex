<?php
class CharacteristicsModel extends Model
{
    public $id;
    public $name;
    public $sql;
    public $lang;
    
    public function __construct() {
        $this->sql = Yii::app()->db;
        $this->lang = Language::getCurrentLang();
    }
    
    /**
     * Получает список товаров
     * @param type $type Тип товаров
     */
    public function getList($lang = null)
    {
        $lang = ($lang == null) ? $this->lang : $lang;
        $query = "SELECT * FROM characteristics_{$lang}";
        $list = $this->sql->createCommand($query)->queryAll();
        return $list;
    }
    
    public function getById($id, $lang = null)
    {
        $lang = ($lang == null) ? $this->lang : $lang;
        $query = "SELECT * FROM characteristics_{$lang} WHERE id = :id";
        $result = $this->sql->createCommand($query)->queryRow(true, array('id'=>$id));
        return $result;
    }

    public function fillCharacteristics()
    {
        $this->characteristics;
    }
    
    /**
     * Добавляет характеристику в общую таблицу характеристик
     * @param type $type
     * @param type $data
     * @return int id записи
     */
    public function addMain($type, $name, $lang)
    {
        //$transaction = $this->sql->beginTransaction();
        try 
        {
            $lang = ($lang == null) ? $this->lang : $lang;
            $name = ucfirst(trim($name));
            $query = "INSERT IGNORE INTO characteristics (type, name) VALUES (:type, :name)";
            $query2 = "INSERT IGNORE INTO characteristics_names (characteristic, language, name) 
                VALUES (:characteristic, :language, :name)";
            $select = "SELECT id FROM characteristics WHERE type = :type AND name = :name";
            
            if ($this->sql->createCommand($query)->execute(array('name'=>$name,'type'=>$type)))
            {
                $result = $this->sql->lastInsertID;
                $p = array(
                    'characteristic'=>$result,
                    'language'=>$lang, 
                    'name'=>$name
                );
                $this->sql->createCommand($query2)->execute($p);
            }
            else
                $result = $this->sql->createCommand($select)->queryScalar(array(
                'name'=>$name,
                'type'=>$type,
            ));
            //$transaction->commit();
            return $result;
        } catch (Exception $ex) {
            //$transaction->rollback();
            throw $ex;
        }
    }
    
    
    public function updateTranslations($translations)
    {
        $query = "
            INSERT INTO characteristics_names (characteristic, name, language) 
            VALUES (:id, :name, :lang) 
            ON DUPLICATE KEY UPDATE name = :name";
        foreach ($translations as $lang=>$translation)
        {
            foreach ($translation as $t)
            {
                try 
                {
                    $this->sql->createCommand($query)->execute(array(
                        'id'=>$t['id'],
                        'name'=>$t['name'],
                        'lang'=>$lang,
                    ));
                } catch (Exception $ex) {
                    throw $ex;
                }
            }
        }
    }

        /**
     * Добавляет характеристику к товару
     * @param type $type
     * @param type $goods
     * @param type $characterisic
     * @param type $value
     * @return type
     * @throws Exception
     */
    public function add($type, $goods, $characterisic, $value)
    {
        $query = "INSERT INTO goods_{$type}_characteristics 
            (goods, characteristic, value) VALUES (:goods, :characteristic, :value)
            on duplicate key update value = :value ";
        
        try 
        {
            $p = array(
                'goods'=>$goods, 
                'characteristic'=>$characterisic, 
                'value'=>$value
            );
            if ($this->sql->createCommand($query)->execute($p))
            {
                return $this->sql->lastInsertID;
            } 
        } catch (Exception $ex) {
            throw $ex;
        }       
    }
    
    public function getListForTranslations($type=1)
    {
        $select = "
            select 
                c.id, 
                c.name, 
                en.name as en,
                ru.name as ru
            from characteristics c 
            left join characteristics_names en on en.characteristic = c.id and en.language = 'en' 
            left join characteristics_names ru on ru.characteristic = c.id and ru.language = 'ru' 
            where c.type = {$type}";
        return $this->sql->createCommand($select)->queryAll();
    }
}
