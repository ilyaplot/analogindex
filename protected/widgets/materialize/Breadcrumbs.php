<?php
class Breadcrumbs extends CWidget
{
    public $items = [];
    
    public function __call($name, $parameters = array()) {
        return parent::__call($name, $parameters);
    }
    
    public function run() 
    {
        $this->items = array_map(function($item){return (object)$item;}, $this->items);
        $this->render('breadcrumbs', ['items'=>$this->items]);
    }
}