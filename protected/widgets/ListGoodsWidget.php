<?php
class ListGoodsWidget extends CWidget
{
    public $type;
    public function __call($name, $parameters = array()) {
        $this->type = isset($parameters['type']) ? $parameters['type'] : 'pda';
        return parent::__call($name, $parameters);
    }
    public function run() 
    {
        $criteria = new CDbCriteria();
        $criteria->compare("t.link", $this->type);
        $criteria->order = "brand_data.name asc, goods.name asc";
        $data = GoodsTypes::model()->cache(60*60*20)->with(array(
            "name",
            "goods",
            "goods.brand_data",
            "goods.type_data"
        ))->find($criteria);
        if ($data)
            $this->render("widget_ListGoodsWidget", array('data'=>$data));
    }
}