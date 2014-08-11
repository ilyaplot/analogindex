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
        $data = GoodsTypes::model()->cache(60*60*3)->with(array(
            "name",
            "goods",
            "goods.brand_data",
            "goods.type_data"
        ))->findByAttributes(array("link"=>$this->type));
        if ($data)
            $this->render("widget_ListGoodsWidget", array('data'=>$data));
    }
}