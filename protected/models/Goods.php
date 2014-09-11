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
                "order"=>"images.disabled asc, images.priority desc",
                "on"=>"images.disabled = 0",
            ),
            "primary_image"=>array(self::HAS_ONE, "GoodsImages", "goods",
                "order"=>"primary_image.priority desc",
            ),
            "reviews"=>array(self::HAS_MANY, "Reviews", "goods",
                "order"=>"reviews.priority desc", 
                "on"=>"reviews.lang = '".Yii::app()->language ."'",
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
        return false;
        if (!$size)
            $size = Images::SIZE_WIDGET;
        
        $size = abs(intval($size));
        
        $image = GoodsImages::model()->cache(60*60*48)->with(array(
            "image_data"=>array(
                'joinType'=>'INNER JOIN',
                'limit'=>'1',
                'order'=>'t.priority desc, t.id asc',
                'condition'=>'t.disabled = 0',
            ),
            "resized"=>array(
                'joinType'=>'INNER JOIN',
                'on'=>'resized.size = :size',
                'params'=>array("size"=>$size)
            ),
            "resized.file_data",
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
    
    public function getCharacteristics()
    {
        $query="select 
                ccn.name as catalog_name, 
                cn.name as characteristic_name,
                gc.value as value,
                c.formatter as formatter,
                cn.description as characteristic_description
            from {{goods_characteristics}} gc 
            inner join {{characteristics}} c on gc.characteristic = c.id 
            inner join {{characteristics_names}} cn on c.id = cn.characteristic 
            inner join {{characteristics_catalogs}} cc on c.catalog = cc.id 
            inner join {{characteristics_catalogs_names}} ccn on ccn.catalog = cc.id 
            where 
                ccn.lang=:lang
                and gc.lang = :lang 
                and cn.lang = :lang 
                and gc.goods = :goods
            order by cc.priority desc, c.priority desc";
        $params = array("lang"=>Yii::app()->language, "goods"=>$this->getPrimaryKey());
        
        $result = array();
        if (!$result = Yii::app()->cache->get("goods.characteristics".serialize($params)))
        {
            $connection = $this->getDbConnection();
            $items = $connection->createCommand($query)->queryAll(true, $params);
            if (!$items)
                $result = array();
            foreach ($items as $item)
            {
                $item['value'] = Yii::app()->format->$item['formatter']($item['value']);
                $result[$item['catalog_name']][] = $item;
            }
        }
        Yii::app()->cache->set("goods.characteristics".serialize($params), $result, 60*60*12);
        return $result;
    }
}
/**
 * Сравнение моделей
 * 
 select CONCAT(b.name, ' ', g.name), gc.value as characteristic
from ai_goods_characteristics gc 
inner join ai_characteristics c on gc.characteristic = c.id 
inner join ai_characteristics_names cn on c.id = cn.characteristic 
inner join ai_characteristics_catalogs cc on c.catalog = cc.id 
inner join ai_characteristics_catalogs_names ccn on ccn.catalog = cc.id 
inner join ai_goods g on gc.goods = g.id
inner join ai_brands b on g.brand = b.id
where 
ccn.lang='ru'
and gc.lang = 'ru'
and cn.lang = 'ru'
and c.id = 8
and gc.value > 1*1024*1024*1024 and gc.value < 2.1*1024*1024*1024 
order by cc.priority desc, c.priority desc;
 */