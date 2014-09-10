<?php
class UserIdentity extends CUserIdentity {
    protected $_id;
    
    const ERROR_NOT_CONFIRMED=3;

    public function authenticate(){

        $user = Users::model()->find('LOWER(email)=?', array(strtolower($this->username)));
        if(($user===null) || ($user->cryptPassword($this->password) !== $user->password)) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif (!$user->confirmed){
            $this->errorCode = self::ERROR_NOT_CONFIRMED;
            $this->errorMessage = Yii::t("models", "Вы еще не подтвердили свой Email. <a href=\"{n}\">Отправить письмо повторно.</a>", array(Yii::app()->createUrl("user/confirm", array("language"=>Language::getCurrentZone()))));
        } else {
            
            $this->_id = $user->id;
            $this->setState("readonly", $user->readonly);
            
            $this->setState("name", !empty($user->name) ? $user->name : $user->username);
            
            $this->errorCode = self::ERROR_NONE;
        }
       return !$this->errorCode;
    }
 
    public function getId(){
        return $this->_id;
    }
}