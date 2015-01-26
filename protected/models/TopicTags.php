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
            'topics'=>[self::BELONGS_TO, 'Topics', 'topic_id',
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
        $criteria->condition = "t.topic_tag_text like :name and topics.exported = 0";
        $criteria->select = "t.topic_id";
        $criteria->params = ['name'=>$name];
        
        
        try {
            $items = self::model()->with('topics')->findAll($criteria);
        } catch (CDbException $ex) {
            while (true) {
                echo "Ошибка базы данных teta, попытка перезапуска соединения.".PHP_EOL;
                try {
                    sleep(5);
                    Yii::app()->teta->setActive(false);
                    Yii::app()->teta->setActive(true);
                    echo "Repeat items by ids".PHP_EOL;
                    $items = self::model()->with('topics')->findAll($criteria);
                    break;
                } catch (CDbException $ex) {
                    echo $ex->getMessage().PHP_EOL;
                    echo "Повторная попытка перезапуска соединения c teta.".PHP_EOL;
                }
            }
        }
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item['topic_id'];
        }
        return $ids;
    }
}