<?php
class MainNavWidget extends CWidget
{
    public function run() 
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'name.name asc';
        $types = GoodsTypes::model()->with(array("name"))->findAll($criteria);
        $this->render("menu", array("types"=>$types));
    }
}