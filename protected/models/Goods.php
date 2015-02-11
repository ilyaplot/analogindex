<?php

/**
 * Товары
 */
class Goods extends CActiveRecord
{

    const IMAGES_LIMIT = 15;
    
    public $generalCharacteristics = array(5, 6, 7, 8, 9, 11, 13, 14, 18, 22);
    public $appendVideos = 3;
    public $videos = [];
    
    public $revs = [];

    public $is_modification = false;
    public $modifications = [];
    
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
            "type_data" => array(self::BELONGS_TO, "GoodsTypes", "type"),
            "brand_data" => array(self::BELONGS_TO, "Brands", "brand"),
            "images" => [self::HAS_MANY, "GoodsImagesCopy", "goods",
                "on" => "images.disabled = 0",
            ],
            "gallery" => [self::HAS_MANY, "Gallery", "goods"],
            "gallery_count" => [self::STAT, "Gallery", "goods"],
            'images_count' => [self::STAT, 'GoodsImages', 'goods'],
            "primary_image" => array(self::HAS_ONE, "GoodsImagesCopy", "goods",
                "order" => "primary_image.priority desc, primary_image.id asc",
            ),
            
            "reviews" => array(self::HAS_MANY, "Reviews", "goods",
                "order" => "reviews.priority desc",
                "on" => "reviews.lang = '" . Yii::app()->language . "'",
            ),
            //"videos" => array(self::HAS_MANY, "Videos", "goods",
            //    "order" => "priority desc",
            //    "on" => "videos.lang = '" . Yii::app()->language . "'",
            //),
            "synonims" => array(self::HAS_MANY, "GoodsSynonims", "goods",
                "select" => "synonims.id, synonims.name, synonims.visibled",
            ),
            "faq" => array(self::HAS_MANY, "Faq", "goods",
                "order" => "priority desc",
                "on" => "lang = '" . Yii::app()->language . "'",
            ),
            "rating" => array(self::HAS_ONE, "RatingsGoods", "goods",
                "select" => "AVG(rating.value) as value",
            ),
            "comments" => array(self::HAS_MANY, "CommentsGoods", "goods"),
        );
    }

    public function getRanking($source, $round = 2, $append = '')
    {
        $criteria = new CDbCriteria();
        $criteria->select = "@goods_type:=:type as type, (t.value / (select max(value) from {{goods_ranking}} where source = :source and type=@goods_type))*100 as value";
        $criteria->params = array("source" => $source, "type" => $this->type, "goods" => $this->id);
        $criteria->condition = "goods = :goods";
        if ($rank = GoodsRanking::model()->cache(60 * 60 * 24)->find($criteria))
            return round($rank->value, $round) . $append;
        return 0;
    }

    public function attributeLabels()
    {
        return array(
            "name" => Yii::t("model", "Наименование"),
            "link" => Yii::t("model", "Ссылка"),
            "type" => Yii::t("model", "Тип"),
            "brand" => Yii::t("model", "Производитель"),
            "source_url" => Yii::t("model", "Url источника"),
            "updated" => Yii::t("model", "Время последнего обновления"),
        );
    }

    public function rules()
    {
        return array(
            array('type, name, brand, link', 'required'),
            array('type, brand', 'numerical', 'integerOnly' => true),
            array('name, link', 'length', 'min' => 1, 'max' => 255),
            array('type', 'exist', 'allowEmpty' => false, 'attributeName' => 'id', 'className' => 'GoodsTypes'),
            ['name', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'name', 
                'className'=>'Goods', 
                'criteria'=>['condition'=>'brand = :brand', 'params'=>['brand'=>  $this->brand]]
            ],
            ['link', 'unique', 'allowEmpty'=>false, 
                'attributeName'=>'link', 
                'className'=>'Goods', 
                'criteria'=>['condition'=>'brand = :brand', 'params'=>['brand'=>  $this->brand]]
            ],
            array('brand', 'exist', 'allowEmpty' => false, 'attributeName' => 'id', 'className' => 'Brands'),
        );
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->link = Yii::app()->urlManager->translitUrl(str_replace("+", " plus", $this->name));
        }
        return parent::beforeValidate();
    }

        public function beforeSave()
    {
        $this->updated = new CDbExpression("NOW()");
        return parent::beforeSave();
    }

    public function getPrimaryImage($size = null)
    {
        return false;
        if (!$size)
            $size = Images::SIZE_WIDGET;

        $size = abs(intval($size));

        $image = GoodsImages::model()->cache(60 * 60 * 48)->with(array(
                    "image_data" => array(
                        'joinType' => 'INNER JOIN',
                        'limit' => '1',
                        'order' => 't.priority desc, t.id asc',
                        'condition' => 't.disabled = 0',
                    ),
                    "resized" => array(
                        'joinType' => 'INNER JOIN',
                        'on' => 'resized.size = :size',
                        'params' => array("size" => $size)
                    ),
                    "resized.file_data",
                ))->findByAttributes(array("goods" => $this->getPrimaryKey()));
        return isset($image->image_data) ? $image->image_data : null;
    }

    public function getVideos($render = true, $lang = '')
    {
        if (empty($lang)) {
            $lang = Yii::app()->language;
        }
        //if (empty($this->videos))
        //{
            $criteria = new CDbCriteria();
            $criteria->order = "priority desc";
            $criteria->condition = "t.goods = :id and t.lang = :lang";
            $criteria->params = ['id'=>  $this->id, 'lang'=>$lang];

            $this->videos = Videos::model()->findAll($criteria);
        //}
        
        /**
         * @todo язык видео не учитывается
         */
        $result = array();
        foreach ($this->videos as $video) {
            $result[] = ($render) ? $video->getTemplate(Videos::TYPE_YOUTUBE, $video->link) : $video->link;
        }

        if (count($result) < $this->appendVideos) {
            $appendVideos = Videos::model()->getYoutube(
                    $this->appendVideos - count($result), $this->type_data->name->video_search_string, $this->brand_data->name, $this->name, $lang);

            foreach ($appendVideos as $video) {
                $model = new Videos();
                $model->goods = $this->id;
                $model->lang = Yii::app()->language;
                $model->type = 1;
                $model->link = $video;
                $model->priority = 100;
                $model->disabled = 0;
                if ($model->validate()) {
                    $model->save();
                }
                $result[] = ($render) ? Videos::model()->getTemplate(Videos::TYPE_YOUTUBE, $video) : $video;
            }
        }
        return $result;
    }

    public function getGeneralCharacteristics()
    {
        $characteristics = $this->getCharacteristics($this->generalCharacteristics);
        $result = array();
        foreach ($characteristics as $ch) {
            foreach ($ch as $c) {
                $result[] = $c;
            }
        }
        return $result;
    }

    public function getCharacteristics($ids = array(), $noCache = false)
    {
        
        if (!empty($ids) && is_array($ids)) {
            $ids = array_unique($ids, SORT_NUMERIC);
            $ids = " and c.id in (" . implode(", ", $ids) . ") ";
        } else {
            $ids = '';
        }
        $query = "select 
                c.id as id,
                ccn.name as catalog_name, 
                cn.name as characteristic_name,
                gc.value as value,
                c.formatter as formatter,
                cn.description as characteristic_description,
                c.link as link
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
                {$ids}
                group by c.id
            order by cc.priority desc, c.priority desc";
        $params = array("lang" => Yii::app()->language, "goods" => $this->getPrimaryKey());

        $result = array();
        if (empty(Yii::app()->cache)) {
            $noCache = true;
        }
        if ($noCache || !$result = Yii::app()->cache->get("goods.characteristics." . serialize($params) . $ids)) {
            $connection = $this->getDbConnection();
            $items = $connection->createCommand($query)->queryAll(true, $params);
            if (!$items)
                $result = array();
            foreach ($items as $item) {
                $item['raw'] = $item['value'];
                $item['value'] = Yii::app()->format->$item['formatter']($item['value']);
                $result[$item['catalog_name']][] = $item;
            }

        }
        if (!$noCache)
            Yii::app()->cache->set("goods.characteristics." . serialize($params) . $ids, $result, 60 * 60 * 12);
        return $result;
    }
    
    
    public function getCharacteristicsCompare()
    {
        $query = "select 
                c.id as id,
                gc.value as value,
                c.formatter as formatter
            from {{goods_characteristics}} gc 
            inner join {{characteristics}} c on gc.characteristic = c.id 
            where 
                gc.lang = :lang 
                and gc.goods = :goods
            order by c.id";
        $params = array("lang" => Yii::app()->language, "goods" => $this->getPrimaryKey());

        $result = [];
        $noCache = false;
        if (empty(Yii::app()->cache)) {
            $noCache = true;
        }
        if ($noCache || !$result = Yii::app()->cache->get("goods.characteristics.compare.with.url" . serialize($params))) {
            $connection = $this->getDbConnection();
            $items = $connection->createCommand($query)->queryAll(true, $params);
            
            foreach ($items as &$item) {
                $item['raw'] = $item['value'];
                $formatter = $item['formatter'];
                $item['value'] = Yii::app()->format->$formatter($item['value']);
            }
            
            //$characteristicsLinks = new CharacteristicsLinks($items);
            //$items = $characteristicsLinks->getCharacteristics($this->type_data->link);

            foreach ($items as $item) {
                $result[$item['id']] = $item;
            }
        }

        //Yii::app()->cache->set("goods.characteristics.compare" . serialize($params), $result, 60 * 60 * 12);
        

                
        
        return $result;
    }
    
    
    public function getCharacteristicsList()
    {
        $query = "select 
                c.id as id,
                ccn.name as catalog_name, 
                cn.name as characteristic_name,
                cn.description as characteristic_description
            from {{characteristics}} c 
            inner join {{characteristics_names}} cn on c.id = cn.characteristic 
            inner join {{characteristics_catalogs}} cc on c.catalog = cc.id 
            inner join {{characteristics_catalogs_names}} ccn on ccn.catalog = cc.id 
            where 
                ccn.lang=:lang
                and cn.lang = :lang
            order by cc.priority desc, c.priority desc";
        $params = array("lang" => Yii::app()->language);

        $result = array();
        $noCache = false;
        if (empty(Yii::app()->cache)) {
            $noCache = true;
        }
        if ($noCache || !$result = Yii::app()->cache->get("goods.characteristics.list_" . serialize($params) )) {
            $connection = $this->getDbConnection();
            $items = $connection->createCommand($query)->queryAll(true, $params);
            if (!$items)
                $result = array();
            foreach ($items as $item) {
                $result[$item['catalog_name']][$item['id']] = $item;
            }
        }
        if (!$noCache) {
            Yii::app()->cache->set("goods.characteristics.list_" . serialize($params), $result, 60 * 60 * 12);
        }
        return $result;
    }
    
    /**
     * Получает характеристики
     * @param array $params
     * @return array CharacteristicItem
     * 
     * @example $characteristics = new getCharacteristicsNew(array(
     *  "in" => array(1,2,3,44,55,66) // Список id для выборки
     *  "cache" => false // не кэшировать список характеристик
     * ));
     */
    public function getCharacteristicsNew($params=array())
    {
        // Если указан список id характеристик для выбора дополняем условие
        if (!empty($params['in']) && is_array($params['in'])) {
            $ids = " and c.id in (" . implode(", ", $params['in']) . ") ";
        } else {
            $ids = '';
        }
        
        // Параметр кэширования зависит от явно указанного и наличия компонента cache
        $params['cache'] = isset($params['cache']) && Yii::app()->cache !== null ? $params['cache'] : (Yii::app()->cache !== null);
        
        $query = "select 
                c.id as id,
                ccn.name as catalog_name, 
                cn.name as characteristic_name,
                gc.value as value,
                c.formatter as formatter,
                cn.description as characteristic_description,
                c.link as link
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
                {$ids}
            order by cc.priority desc, c.priority desc";
                
        $queryParams = array("lang" => Yii::app()->language, "goods" => $this->getPrimaryKey());

        $result = array();
        
        if ($params['cache'] == false || !$result = Yii::app()->cache->get("goods.characteristics" . serialize($queryParams) . $ids)) {
            $connection = $this->getDbConnection();
            $items = $connection->createCommand($query)->queryAll(true, $queryParams);
            if (!empty($items)) {
                $characteristicsSelector = CharacteristicsSelector::model()->findByPk($this->id);
                
                foreach ($items as $item) {
                    $item['link_value'] = (!empty($item['link']) && !empty($characteristicsSelector->$item['link'])) ? $characteristicsSelector->$item['link'] : 0;
                    $result[$item['id']] = new CharacteristicItem(
                        $item['id'], 
                        $item['catalog_name'], 
                        $item['characteristic_name'], 
                        $item['formatter'], 
                        $item['characteristic_description'], 
                        $item['link'], 
                        $item['link_value'],
                        $item['value'],
                        $this,
                        array(
                            "createLinks" => isset($params['createLinks']) ? $params['createLinks'] : null
                        )
                    );
                }
            }
        }
        
        if ($params['cache'] == true)
            Yii::app()->cache->set("goods.characteristics" . serialize($params) . $ids, $result, 60 * 60 * 12);
        
        return $result;
    }
    
}