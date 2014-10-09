<?php
class Selector
{
    protected $model;
    protected $prefix = 'sorter_';
    protected $type = 1;
    protected $items = array();
    
    public function __construct($type=1) {
        $this->type = $type;
        $criteria = new CDbCriteria();
        $criteria->condition = "type = :type";
        $criteria->params = array("type"=>  $this->type);
        $select = array();
        
        $this->items = $this->items();
        
        foreach ($this->items as $id=>$item)
        {
            if ($item['type'] == 'range')
                $select[$id] = "max(ch{$id}) as ch{$id}";
        }
        $criteria->select = implode(", ", $select);
        $this->model = CharacteristicsSelector::model()->find($criteria);
        
        foreach ($this->items as $id=>&$item)
        {
            $field = "ch{$id}";
            if ($item['type'] == 'range' && !empty($this->model->$field) && empty($item['max']))
                $item['max'] = doubleval($this->model->$field);
            
            if (empty($item['id']))
                $item['id'] = $field;
            
            if (empty($item['value']) && $item['type'] == 'range')
            {
                $item['value'] = Yii::app()->request->getParam($this->prefix.$field, array($item['min'], $item['max']));
            } elseif(empty($item['value']) && $item['type'] == 'like') {
                $item['value'] = Yii::app()->request->getParam($this->prefix.$field);
            }
            
            if (empty($item['title']))
            {
                $name = CharacteristicsNames::model()->findByAttributes(array(
                    "characteristic"=>$id,
                    "lang"=>Yii::app()->language,
                ));
                $item['title'] = !empty($name->name) ? $name->name : '';
            }
            
            if (empty($item['formatter']))
            {
                $formatterCriteria = new CDbCriteria();
                $criteria->select = "formatter";
                $criteria->condition = "id = :id";
                $criteria->params = array("id"=>$id);
                $formatter = Characteristics::model()->find($criteria);
                $item['formatter'] = !empty($formatter->formatter) ? $formatter->formatter : 'formatNone';
            }
        }
        
        //var_dump($this->items);

    }

    public function render($id)
    {
        if (empty($this->items[$id]))
            return null;
        $item = $this->items[$id];
        $function = "render".ucfirst($item['type']);
        return $this->$function($item['id'], $item['title'], $item['value'], $item);
        
    }
    

    public function items()
    {
        
        //3,5,6,8,9,13,14
        return array(
            3=>array(
                'type'=>'range',
                'min'=>1,
                'step'=>10,
            ),
            5=>array(
                'type'=>'range',
                'min'=>1,
            ),
            6=>array(
                'type'=>'range',
                'min'=>1,
                'step'=>10000000,
            ),
            8=>array(
                'type'=>'range',
                'min'=>1,
                'step'=>1024*1024*128,
            ),
            9=>array(
                'type'=>'range',
                'min'=>1,
                'step'=>1024*1024*128,
            ),
            13=>array(
                'type'=>'range',
                'min'=>1,
                'step'=>0.1,
            ),
            14=>array(
                'type'=>'like',
                'source'=>Os::model()->findAll(array("order"=>"name asc")),
            ),
            22=>array(
                'type'=>'range',
                'min'=>1,
                'step'=>100,
            )
        );
    }
    
    protected function renderLike($id, $title, $values, $params)
    {
        $result = '';
        if (empty($params['source']))
            return false;
        foreach ($params['source'] as $item)
        {
            $result.= CHtml::label($item->name, $this->prefix.$id."-".$item->id);
            $result.= Chtml::checkBox($this->prefix.$id."[".$item->id."]", isset($values[$item->id]), array("id"=>$this->prefix.$id."-".$item->id, "value"=>$item->name));
            $result.= " ";
        }
        return $result;
    }
    
    protected function renderRange($id, $title, $values, $params)
    {

        if (!isset($params['max'], $params['min']))
            return false;
        $v1 = Yii::app()->format->$params['formatter']($values[0]);
        $v2 = Yii::app()->format->$params['formatter']($values[1]);
        $item = CHtml::label($title." <span>{$v1} - {$v2}</span>", $this->prefix.$id, array("data-formatter"=>$params['formatter']));
        
        $item.= Yii::app()->getController()->widget('zii.widgets.jui.CJuiSliderInput',array(
            'name'=>$this->prefix.$id."[0]",
            'maxName'=>$this->prefix.$id."[1]",
            'value'=>!empty($values[0]) ? $values[0] : $params['min'],
            'maxValue'=>!empty($values[1]) ? $values[1] : $params['max'],
            'event' => 'change',
            'options'=>array(
                'min'=>$params['min'],
                'max'=>$params['max'],
                'step'=>!empty($params['step']) ? $params['step'] : 1,
                'range'=>true,
                'slide'=> new CJavaScriptExpression(" 
                    function (event, ui)
                    {
                        v=ui.values; 
                        var label = $('label[for={$this->prefix}{$id}]');
                        var formatter = label.attr('data-formatter');
                        formatter = window[formatter];
                        if (typeof(formatter) == 'undefnded')
                        {
                            console.log(label.attr('data-formatter'));
                            formatter = window['formatNone'];
                        }
                        label.find('span').text(' '+formatter(v[0])+' - '+formatter(v[1]));
                    }
                "),  
            ),
            'htmlOptions'=>array(
                'id'=>  $this->prefix.$id,
            )
        ), true);
        return $item;
    }
    
    public function getParams()
    {
        $paramId = 0;
        $params = array();
        $paramsArray = array();
        foreach ($this->items as $item)
        {
            $param = Yii::app()->request->getParam($this->prefix.$item['id']);
            if (!empty($param))
            {
                switch ($item['type'])
                {
                    case "range":
                        if (empty($param[0]) || empty($param[1]))
                            break;
                        $param[0] = ($param[0] == 1) ? 0 : $param[0];
                        $params[$item['id']] = $item['id']." BETWEEN ".abs(intval($param[0]))." AND ".abs(intval($param[1]));
                    break;
                    case "like":

                        foreach ($param as $p)
                        {
                            $paramId++;
                            $params[$item['id']][] = $item['id']." like :p{$paramId}";
                            $paramsArray["p".$paramId] = '%'.$p.'%';
                        }
                        $params[$item['id']] = "(".implode(" OR ", $params[$item['id']]).")";
                    break;
                }
            }
        }
        $brands = Yii::app()->request->getParam("Brands");
        if (!empty($brands))
        {
            $brands = array_map(function($val){return abs(intval($val));}, array_keys($brands));
            $params['brands'] = 'brand in ('.implode(", ", $brands).")";
        }
        return array("condition"=>implode(" AND ", $params), "params"=>$paramsArray);

    }
}