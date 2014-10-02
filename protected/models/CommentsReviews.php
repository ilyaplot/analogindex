<?php
class CommentsReviews extends CActiveRecord
{
    
    public $verifyCode;
    
    public static $subject = "review";
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return "{{comments_reviews}}";
    }
    
    public function attributeLabels() {
        return array(
            "user"=>'Пользователь',
            "review"=>'Обзор',
            "text"=>'Текст комментария',
        );
    }
    
    public function rules() {
        $p = new CHtmlPurifier();
        $p->options = array(
            'HTML.AllowedElements'=>array('p','i','b'),
        );
        
        return array(
            array('user, review, text', 'required'),
            array('user', 'exist', 'on'=>'comment',
                'allowEmpty'=>false,
                'className'=>'Users',
                'attributeName'=>'id',
            ),
            array('review', 'exist', 'on'=>'comment',
                'allowEmpty'=>false,
                'className'=>'Reviews',
                'attributeName'=>'id',
            ),
            array('text','filter','filter'=>array($p,'purify')),
        );
    }
}