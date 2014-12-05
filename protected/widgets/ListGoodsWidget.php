<?php

class ListGoodsWidget extends CWidget
{

    public $type;
    public $limit;
    public $in = [];
    public $style;

    public function __call($name, $parameters = array())
    {
        $this->type = isset($parameters['type']) ? $parameters['type'] : 'pda';
        $this->limit = isset($parameters['limit']) ? $parameters['limit'] : 0;
        $this->in = isset($parameters['in']) ? $parameters['in'] : [];
        //$this->style = isset($parameters['style']) ? "_".$parameters['style'] : '';
        return parent::__call($name, $parameters);
    }

    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "brand_data.name asc, t.name asc";
        $criteria->limit = $this->limit;
        if (!empty($this->type)) {
            $type = GoodsTypes::model()->cache(60 * 60 * 48)->findByAttributes(array("link" => $this->type));
            if ($type)
                $criteria->compare("type", $type->id);
        } else {
            $type = (object) ['name'=>(object)['name'=>'']];
        }
        
        if (!empty($this->in)) {
            $criteria->addInCondition('t.id', $this->in);
        }
        
        $criteria->group = "t.id, rating.value";
        $criteria->order = "t.updated desc";
        
        
        $data = Goods::model()->cache(60 * 60 * 24)->with(array(
                    "brand_data" => array(
                        "joinType" => "inner join"
                    ),
                    "primary_image",
                    "rating",
                ))->findAll($criteria);
        if ($this->style)
            $this->style = "_" . $this->style;

        if ($data)
            $this->render("widget_ListGoodsWidget" . $this->style, array('data' => $data, 'type' => $type));
    }

}
