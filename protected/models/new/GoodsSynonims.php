<?php
/**
 * Синонимы для товаров
 */
class GoodsSynonims extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function getDbConnection() {
        return Yii::app()->newdb;
    }
    
    public function tableName()
    {
        return "{{goods_synonims}}";
    }
    
    public function relations()
    {
        return array(
            'goods_data'=>array(self::BELONGS_TO, "Goods", 'goods',
                "joinType"=>"inner join",
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "name"=>Yii::t("model", "Синоним"),
            "visibled"=>Yii::t("model", "Отображать"),
        );
    }
    
    public function rules()
    {
        return array(
            array('name, goods', 'required'),
            array('name', 'length', 'min'=>1, 'max'=>255),
            array('name', 'unique', 'caseSensitive'=>false, 'criteria'=>array(
                'condition'=>'goods = :goods',
                'params'=>array(
                    'goods'=>$this->goods,
                ),
            )),
            array('goods', 'exist', 'allowEmpty'=>false, 'attributeName'=>'id', 'className'=>'Goods'),
        );
    }
}