<?php
/**
 * Пользователи
 */
class Users extends CActiveRecord
{
    const ROLE_ADMIN = 'administrator';
    const ROLE_MODER = 'moderator';
    const ROLE_USER  = 'user';
    const ROLE_BANNED = 'banned';
    const ROLE_GUEST = 'guest';
    
    const SALT = "877476ugbkm&%^$#%^$&*()5435645436JKHFRcvhjFKHJ<hd467890-^^^^*****UTgjnvd";
    
    public $rememberMe=false;
    public $password2;
    private $_identity;
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{users}}";
    }
    
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels() {
        return array(
            'email'=>Yii::t("models", "Имя пользователя (Email)"),
            'password'=>Yii::t("models", "Пароль"),
            'rememberMe'=>Yii::t("models", "Запомнить"),
        );
    }
 
    public function rules()
    {
        return array(
            array('email, password', 'required'),
            array('email', 'length', 'min'=>4, 'max'=>255),
            array('email', 'email'),
            array('rememberMe', 'boolean'),
            array('password', 'authenticate'),
        );
    }
 
    public function authenticate()
    {
        $this->_identity=new UserIdentity($this->email, $this->password);
        if(!$this->_identity->authenticate())
            $this->addError('email', $this->_identity->errorMessage);
    }
    
    public function getIdentity()
    {
        return $this->_identity;
    }
    
    public function beforeSave() {
        if ($this->isNewRecord)
        {
            $this->password = $this->cryptPassword($this->password);
        }
        return parent::beforeSave();
    }
    
    public function cryptPassword($password)
    {
        return md5(self::SALT.$password);
    }
}
