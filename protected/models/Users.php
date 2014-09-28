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
    
    public $password2;
    public $verifyCode;
    
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
            'email'=>Yii::t("models", "Email"),
            'name'=>Yii::t("models", "Имя или псевдоним"),
            'password'=>Yii::t("models", "Пароль"),
            'password2'=>Yii::t("models", "Подтверждение пароля"),
            'rememberMe'=>Yii::t("models", "Запомнить"),
            'confirmCode'=>Yii::t("models", "Код подтверждения"),
        );
    }
 
    public function rules()
    {
        $p = new CHtmlPurifier();
        $p->options = array(
            'HTML.Allowed'=>''
        );
        return array(
            array('email, password, name, password2', 'required', 'on'=>'registration'),
            array('name, email', 'unique', 'on'=>'registration'),
            array('name', 'filter', 'filter'=>array($p,'purify'), 'on'=>'registration'),
            array('name', 'filter', 'filter'=>'trim', 'on'=>'registration'),
            array('name', 'filter', 'filter'=>'ucfirst', 'on'=>'registration'),
            array('name', 'in', 'on'=>'registration',
                'not'=>true,
                'range'=>array(
                    'Admin', 
                    'Administrator', 
                    'Moderator', 
                    'Админ', 
                    'Администратор', 
                    'Модератор', 
                    'Аноним',
                    'Гость',
                    'Guest',
                ),
                'message' => $this->getAttributeLabel('name').Yii::t("models", " уже занят."),
            ),
            array('email, name, password', 'length', 'min'=>4, 'max'=>255, 'on'=>'registration'),
            array('password2', 'compare', 'on'=>'registration', 
                'allowEmpty'=>false,
                'compareAttribute'=>'password',
                'message'=>Yii::t("models", "Пароли не совпадают"),
            ),
            array('verifyCode', 'captcha',
                'allowEmpty'=>!CCaptcha::checkRequirements(),
                'on'=>'registrationn',
                'caseSensitive'=>false,
            ),
            
            
            array('email, password', 'required', 'on'=>'login'),
            array('email', 'email'),
            array('password', 'authenticate', 'on'=>'login'),
            
            array('verifyCode', 'captcha',
                'allowEmpty'=>!CCaptcha::checkRequirements(),
                'on'=>'login',
                'caseSensitive'=>false,
            ),
            
            
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
            $this->confirmCode = md5(time().microtime().self::SALT);
        }
        return parent::beforeSave();
    }
    
    public function afterSave() {
        if ($this->isNewRecord)
            $this->password = $this->password2;
        return parent::afterSave();
    }
    
    public function cryptPassword($password)
    {
        return md5(self::SALT.$password);
    }
}
