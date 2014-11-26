<?php

class Topics extends CActiveRecord
{
    public $lang = 'ru';
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{topic}}";
    }
    
    public function getDbConnection()
    {
        return Yii::app()->teta;
    }
    
    public function relations()
    {
        return [
            'topic_content'=>[self::BELONGS_TO, 'TopicContent', 'topic_id',
                'joinType'=>'inner join',
            ],
        ];
    }
    
    public function afterFind()
    {
        if ($this->user_id == '5956') {
            $this->lang = 'en';
        }
        return parent::afterFind();
    }
    
    /**
     * Заглушка на сохранение записи
     * @return boolean
     */
    public function beforeSave()
    {
        return false;
    }
}