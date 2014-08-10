<?php
class ListGoodsWidget extends CWidget
{
    public function run() 
    {
        $data = GoodsTypes::model()->cache(60*60)->with(array(
            "name",
            "goods",
            "goods.brand_data",
            "goods.type_data"
        ))->findByAttributes(array("id"=>1));
        $this->render("widget_ListGoodsWidget", array('data'=>$data));
    }
}