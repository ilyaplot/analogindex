<?php
class ListGoodsWidget extends CWidget
{
    public $type;
    public $limit;
    public function __call($name, $parameters = array()) {
        $this->type = isset($parameters['type']) ? $parameters['type'] : 'pda';
        $this->limit = isset($parameters['limit']) ? $parameters['limit'] : 0;
        return parent::__call($name, $parameters);
    }
    public function run() 
    {
        
        $criteria = new CDbCriteria();
        $criteria->order = "brand_data.name asc, t.name asc";
        $criteria->limit = $this->limit;
        $type = GoodsTypes::model()->cache(60*60*48)->findByAttributes(array("link"=>$this->type));
        $criteria->compare("type", $type->id);
        $criteria->group = "t.id, rating.value";
        $criteria->order = "rating.value desc";
        if (!$type)
            return false;
        $data = Goods::model()->cache(60*60)->with(array(
            "brand_data"=>array(
                "joinType"=>"inner join"
            ),
            "primary_image",
            "rating"
        ))->findAll($criteria);
        
        if ($data)
            $this->render("widget_ListGoodsWidget", array('data'=>$data, 'type'=>$type));
        
    }
}