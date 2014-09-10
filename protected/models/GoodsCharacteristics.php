<?php
/**
 * Характеристики товаров
 */
class GoodsCharacteristics extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    
    public function tableName()
    {
        return "{{goods_characteristics}}";
    }
    
    public function relations()
    {
        return parent::relations();
    }
    
    public function attributeLabels()
    {
        return array(
            "goods"=>Yii::t("model", "Товар"),
            "characteristic"=>Yii::t("model", "Характеристика"),
            "value"=>Yii::t("model", "Значение"),
            "lang"=>Yii::t("model", "Код языка"),
        );
    }
    
    public function rules() {
        return array(
            array("goods, characteristic, value, lang", 'required'),
            array('goods, characteristic', 'numerical', 'integerOnly'=>true),
            array('goods', 'exist', 'allowEmpty'=>false, 'attributeName'=>'id', 'className'=>'Goods'),
            array('characteristic', 'exist', 'allowEmpty'=>false, 'attributeName'=>'id', 'className'=>'Characteristics'),
            array('characteristic', 'unique', 'criteria'=>array(
                'condition'=>'goods = :goods and lang = :lang',
                'params'=>array(
                    'goods'=>$this->goods,
                    'lang'=>$this->lang,
                ),
            )),
        );
    }
}