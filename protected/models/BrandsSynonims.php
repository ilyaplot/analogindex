<?php
/**
 * Синонимы брендов
 */
class BrandsSynonims extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{brands_synonims}}";
    }
}
