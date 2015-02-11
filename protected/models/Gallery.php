<?php
class Gallery extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function tableName()
    {
        return "{{gallery}}";
    }
    
    public function relations()
    {
        return [
            'image_data'=>[self::BELONGS_TO, 'NImages', 'image', 'joinType'=>'inner join'],
        ];
    }
    
    public function getCount($product)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 't.goods = :goods';
        $criteria->params = ['goods'=>$product];
        return Gallery::model()->with(['image_data'])->count($criteria);
    }

}
