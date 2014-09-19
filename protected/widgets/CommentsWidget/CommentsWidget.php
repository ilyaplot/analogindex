<?php
class CommentsWidget extends CWidget
{
    public $type;
    public $id;
    
    public function __call($name, $parameters = array()) {
        $this->type = isset($parameters['type']) ? $parameters['type'] : null;
        $this->id = isset($parameters['id']) ? $parameters['id'] : 0;
        return parent::__call($name, $parameters);
    }
    
    public function run() 
    {
        $this->render("widget_CommentsWidget");    
    }
}