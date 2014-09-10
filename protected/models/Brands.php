<?php
/**
 * Производители
 */
class Brands extends CActiveRecord
{
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }
    
    
    public function tableName() 
    {
        return "{{brands}}";
    }
    
    public function relations()
    {
        return array(
            "goods"=>array(self::HAS_MANY, "Goods", "brand"),
            "description"=>array(self::HAS_ONE, "BrandsDescriptions", "brand", 
                "on"=>"lang = '".Yii::app()->language. "'",
            ),
            "synonims"=>array(self::HAS_MANY, "BrandsSynonims", "brand"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Наименование"),
            "link"=>Yii::t("model", "Ссылка"),
            "logo"=>Yii::t("model", "Логотип"),
        );
    }
    
    public function getTypes()
    {
        //select * from ai_goods_types t inner join ai_goods g on g.type=t.id where g.brand = 10 group by t.id;
        /*
         * "goods"=>array(
                "joinType"=>"inner join",
                "select"=>false,
                "group"=>"t.id, goods.type",
            ),
            "goods.type_data"=>array(
                "joinType"=>"inner join",
                "select"=>"type_data.id",
            ),
         */
        $criteria = new CDbCriteria();
        $criteria->condition = "goods.brand = :brand";
        $criteria->params = array("brand"=>$this->id);
        $criteria->group = "t.id asc";
        $criteria->order = "name.name asc";
        $goods = GoodsTypes::model()->with(array(
            "goods"=>array(
                "joinType"=>"INNER JOIN",
                "select"=>false,
            ),
            "name"=>array(
                "joinType"=>"INNER JOIN",
            )
        ))->findAll($criteria);
        return $goods;
    }
}