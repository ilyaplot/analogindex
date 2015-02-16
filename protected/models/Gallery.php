<?php
class Gallery extends CActiveRecord
{

    public $prev_url;
    public $next_url;
    public $self_url;
    
    const GALLERY_SIZE = 102;
    
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
    
    
    /**
     * 
     * @param type $brand
     * @param type $product
     * @param type $link
     * @param type $id
     * @param type $page
     * @return type
     */
    public static function getUrl($brand, $product, $link, $id, $page = null)
    {
        if ($page) {
            return Yii::app()->createAbsoluteUrl("gallery/product",[
                'brand'=>$brand,
                'product'=>$product,
                'link'=>$link,
                'id'=>$id,
                'page'=>$page,
                'language'=> Language::getCurrentZone(),
            ]);
        } else {
            return Yii::app()->createAbsoluteUrl("gallery/product",[
                'brand'=>$brand,
                'product'=>$product,
                'link'=>$link,
                'id'=>$id,
                'language'=>  Language::getCurrentZone(),
            ]);
        }
        
    }

}
