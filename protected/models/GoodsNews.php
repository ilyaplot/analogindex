<?php
class GoodsNews extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{goods_news}}";
    }
    
    public function rules()
    {
        return [
            ['goods, news', 'required'],
            ['goods, news', 'type', 'type' => 'integer', 'allowEmpty' => false],
            ['goods', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'goods', 
                'className'=>'GoodsNews', 
                'criteria'=>['condition'=>'news = :news', 'params'=>['news'=>  $this->news]]
            ]
        ];
    }

}
