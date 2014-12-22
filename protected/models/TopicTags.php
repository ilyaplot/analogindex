<?php

class TopicTags extends CActiveRecord
{
    public $lang = 'ru';
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{topic_tag}}";
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
    
    public function getNewsByTag($name)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "topic_tag_text like :name";
        $criteria->params = ['name'=>$name];
        $criteria->select = "topic_id";
        
        $items = self::findAll($criteria);
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item['topic_id'];
        }
        return $ids;
    }
}