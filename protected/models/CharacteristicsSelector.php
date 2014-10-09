<?php
class CharacteristicsSelector extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{characteristics_selector}}";
    }
    
    public function rules() {
        return array(
            array('goods, type, brand', 'required'),
            
            // Вес
            array('ch3', 'numerical', 'allowEmpty'=>true, 'integerOnly'=>false),
            // Количество ядер процессора
            array('ch5', 'numerical', 'allowEmpty'=>true, 'integerOnly'=>true),
            // Частота процессора (гц)
            array('ch6', 'numerical', 'allowEmpty'=>true, 'integerOnly'=>true),
            // Оперативка байт
            array('ch8', 'numerical', 'allowEmpty'=>true, 'integerOnly'=>true),
            // Память байт
            array('ch9', 'numerical', 'allowEmpty'=>true, 'integerOnly'=>true),
            //Диагональ экрана дюйм
            array('ch13', 'numerical', 'allowEmpty'=>true, 'integerOnly'=>false),
            
            // ОС
            array('ch14', 'length', 'allowEmpty'=>true, 'min'=>2),
            
            
            // Аккумулятор
            array('ch22',  'numerical', 'allowEmpty'=>true, 'integerOnly'=>true),
        );
    }
    
    
    
}
