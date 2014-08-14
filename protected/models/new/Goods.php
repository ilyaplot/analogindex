<?php
/**
 * Товары
 */
class Goods extends CActiveRecord
{    
    
    public $appendVideos = 3;
    
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }
    
    public function getDbConnection() {
        return Yii::app()->newdb;
    }
    
    public function tableName() 
    {
        return "{{goods}}";
    }
    
    public function relations()
    {
        return array(
            "type_data"=>array(self::BELONGS_TO, "GoodsTypes", "type"),
            "brand_data"=>array(self::BELONGS_TO, "Brands", "brand"),
            "images"=>array(self::HAS_MANY, "GoodsImages", "goods", 
                "order"=>"images.disabled asc, images.priority desc"
            ),
            "reviews"=>array(self::HAS_MANY, "Reviews", "goods",
                "order"=>"priority desc", 
                "on"=>"lang = '".Yii::app()->language ."'",
            ),
            "videos"=>array(self::HAS_MANY, "Videos", "goods",
                "order"=>"priority desc",
                "on"=>"lang = '".Yii::app()->language ."'",
            ),
            "synonims"=>array(self::HAS_MANY, "GoodsSynonims", "goods",
                "select"=>"synonims.id, synonims.name, synonims.visibled",
            ),
            "faq"=>array(self::HAS_MANY, "Faq", "goods",
                "order"=>"priority desc",
                "on"=>"lang = '".Yii::app()->language ."'",
            ),
            "rating"=>array(self::HAS_ONE, "RatingsGoods", "goods", 
                "select"=>"AVG(rating.value) as value",
            ),
            "modifications"=>array(self::HAS_MANY, "GoodsModifications", "goods_parent"),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            "name"=>Yii::t("model", "Наименование"),
            "link"=>Yii::t("model", "Ссылка"),
            "type"=>Yii::t("model", "Тип"),
            "brand"=>Yii::t("model", "Производитель"),
            "is_modification"=>Yii::t("model", "Модификация"),
        );
    }
    
    public function rules() 
    {
        return array(
            array('type, name, brand, link','required'),
            array('type, brand', 'numerical', 'integerOnly'=>true),
            array('name, link', 'length', 'min'=>1, 'max'=>255),
            array('type', 'exist', 'allowEmpty'=>false, 'attributeName'=>'id', 'className'=>'GoodsTypes'),
            array('name', 'unique', 'allowEmpty'=>false),
            array('link', 'unique', 'allowEmpty'=>false),
            array('brand', 'exist', 'allowEmpty'=>false, 'attributeName'=>'id', 'className'=>'Brands'),
            array('is_modification', 'boolean'),
        );
    }
    
    
    public function getPrimaryImage($size = null)
    {
        if (!$size)
            $size = Images::SIZE_WIDGET;
        
        $size = abs(intval($size));
        
        $image = GoodsImages::model()->cache(60*60*24)->with(array(
            "image_data"=>array(
                'joinType'=>'INNER JOIN',
                'limit'=>'1',
                'order'=>'t.priority desc, t.id asc',
                'condition'=>'t.disabled = 0',
            ),
            "image_data.resized"=>array(
                'joinType'=>'INNER JOIN',
                'on'=>'resized.size = '.$size,
            ),
            "image_data.resized.file_data",
        ))->findByAttributes(array("goods"=>$this->getPrimaryKey()));
        return isset($image->image_data) ? $image->image_data : null; 
    }
    
    public function getVideos()
    {
        $result = array();
        foreach ($this->videos as $video)
        {
            $result[] = $video->getTemplate(Videos::TYPE_YOUTUBE, $video->link).PHP_EOL;
        }
        if (count($result) < $this->appendVideos)
        {
            $videoModel = new Videos();
            $appendVideos = $videoModel->getYoutube(
                    $this->appendVideos - count($result), 
                    $this->type_data->name->video_search_string, 
                    $this->brand_data->name, 
                    $this->name);
            foreach ($appendVideos as $video)
            {
                $result[] = $videoModel->getTemplate(Videos::TYPE_YOUTUBE, $video).PHP_EOL;
            }
        }
        return $result;
    }
}