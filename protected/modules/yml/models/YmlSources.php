<?php

/**
 * Источники yml 
 */
class YmlSources extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{yml_sources}}";
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название магазина',
            'url' => 'Url источника',
            'last_update' => 'Обновлен',
            'status' => 'Статус',
            'status_message' => 'Сообщение обработчика',
            'status_time' => 'Последнее обновление статуса',
        ];
    }
    
    public function relations()
    {
        return [
            "catalogs_count_all"=>[self::STAT, "YmlCatalog", "source"],
            "catalogs_count_selected"=>[self::STAT, "YmlCatalog", "source", "condition"=>"enabled = 1"],
        ];
    }

    public function getList()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "status > 0";
        $list = self::model()->with("catalogs_count_all", "catalogs_count_selected")->findAll($criteria);
        $items = [];
        foreach ($list as $item) {
            $items[$item->id] = $item->id." ".$item->name." ({$item->catalogs_count_all}/{$item->catalogs_count_selected})";
        }
        return $items;
    }

}
