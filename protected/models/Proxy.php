<?php
class Proxy extends CActiveRecord
{
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function tableName() {
        return "proxy";
    }
    
    public function getDbConnection() 
    {
        return Yii::app()->reviews;
    }

        public static function getAlive()
    {
        $conn = Yii::app()->reviews;
        $proxy = "select id, address from proxy where alive = 1 order by counter asc, last desc limit 1";
        $proxy = $conn->createCommand($proxy)->queryRow();
        if (!$proxy)
            return false;
        
        $conn->createCommand ("update proxy set counter = counter+1 where id = :id")->execute (array('id'=>$proxy['id']));
        return $proxy['address'];
    }
}