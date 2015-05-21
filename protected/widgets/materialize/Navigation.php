<?php
class Navigation extends CWidget
{
    public function run() 
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'name.name asc';
        $types = GoodsTypes::model()->with(array("name"))->findAll($criteria);
        $this->render("navigation", array("types"=>$types));
    }
}