<?php
class WebUser extends CWebUser {
    private $_model = null;
 
    public function setReturnUrl($value)
    {
        $this->setState('__returnUrl',$value);
    }
    
    function getRole() {
        if($user = $this->getModel()){
            return $user->role;
        }
    }
 
    private function getModel(){
        if (!$this->isGuest && $this->_model === null){
            $this->_model = Users::model()->findByPk($this->id, array('select' => 'role'));
        }
        return $this->_model;
    }
}