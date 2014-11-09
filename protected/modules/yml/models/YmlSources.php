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

}
