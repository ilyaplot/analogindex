<?php
/**
 * Комментарии к модификациям
 */
class ModificationsComments extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    

    public function tableName()
    {
        return "{{modifications_comments}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "modification"=>Yii::t("model", "Товар - модификация"),
            "lang"=>Yii::t("model", "Код языка"),
            "comment"=>Yii::t("model", "Текст комментария"),
        );
    }
    
    public function rules() 
    {
        return array(
            array('comment', 'length', 'min'=>0, 'max'=>255),
            array('modification', 'required'),
            array('modification', 'exists', 'allowEmpty'=>false, 'class'=>'GoodsModifications', 'attributeName'=>'id'),
            array('lang', 'required'),
        );
    }
}