<?php
class CommentsWidget extends CWidget
{
    public $type;
    public $id;
    
    protected $model;
    
    public function __call($name, $parameters = array()) {
        return parent::__call($name, $parameters);
    }
    
    public function run() 
    {
        $className = "Comments".ucfirst($this->type);
        if (!class_exists($className))
            return;
        $this->model = new $className("comment");
        $commentForm = Yii::app()->request->getPost($className);
        if ($commentForm)
        {
            $this->model->attributes = $commentForm;
            $this->model->user = Yii::app()->user->id;
            if ($this->model->validate())
            {
                $this->model->save();
                Yii::app()->request->redirect(Yii::app()->request->getUrl());
            }
        }
        $this->render("widget_CommentsWidget", array("model"=>$this->model, "id"=>$this->id, 'className'=>$className));    
    }
}