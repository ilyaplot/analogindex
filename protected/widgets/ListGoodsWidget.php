<?php
class ListGoodsWidget extends CWidget
{
    public $sorts;
    public $type = 1;
    public $limit = 1000000;
    public $sort;
    public $desc = false;
    
    public function createWidget($className, $properties = array()) 
    {
        if (isset($properties['type']))
            $this->type = intval($properties['type']);
        if (isset($properties['limit']))
            $this->limit = intval($properties['limit']);
        if (isset($properties['sort']))
            $this->sort = $properties['sort'];
        
        return parent::createWidget($className, $properties);
    }

    public function run() 
    {
        if (!$this->sort)
            $this->sort = GoodsModel::LIST_ORDER_NAME;
        $model = new GoodsModel();
        $data = $model->getWidgetList(
            $this->type,
            $this->limit,
            $this->sort,
            $this->desc
        );
        $this->render("widget_ListGoodsWidget", array('list'=>$data));
    }
}