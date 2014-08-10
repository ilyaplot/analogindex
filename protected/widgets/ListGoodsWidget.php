<?php
class ListGoodsWidget extends CWidget
{
    public function run() 
    {
        $data = GoodsTypes::model()->with(array(
            "name",
            "goods",
            "goods.brand_data",
        ))->findByAttributes(array("id"=>1));
        $this->render("widget_ListGoodsWidget", array('data'=>$data));
    }
}