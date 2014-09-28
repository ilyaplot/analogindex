<?php
class CommentsGoods extends CActiveRecord
{
    
    public $verifyCode;
    
    public static $subject = "goods";
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{comments_goods}}";
    }
    
    public function attributeLabels() {
        return array(
            "user"=>'Пользователь',
            "goods"=>'Товар',
            "text"=>'Текст комментария',
        );
    }
    
    public function rules() {
        $p = new CHtmlPurifier();
        $p->options = array(
            'HTML.AllowedElements'=>array('p','i','b'),
        );
        
        return array(
            array('user, goods, text', 'required'),
            array('user', 'exist', 'on'=>'comment',
                'allowEmpty'=>false,
                'className'=>'Users',
                'attributeName'=>'id',
            ),
            array('goods', 'exist', 'on'=>'comment',
                'allowEmpty'=>false,
                'className'=>'Goods',
                'attributeName'=>'id',
            ),
            array('text','filter','filter'=>array($p,'purify')),
        );
    }
}