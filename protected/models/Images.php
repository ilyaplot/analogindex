<?php

/**
 * Изображения
 */
class Images extends CActiveRecord
{

    /**
     * Константы для ресайза
     */
    const SIZE_BIG = 1;
    const SIZE_PREVIEW = 2;
    const SIZE_LIST = 3;
    const SIZE_WIDGET = 4;
    const SIZE_SEARCH = 5;
    const SIZE_BRAND = 6;

    /**
     * Размеры для ресайза
     * @var array
     */
    public static $sizes = array(
        2 => array(510, 510),
        3 => array(91, 91),
        4 => array(30, 37),
        //5=>array(100, 100, false),
        6 => array(131, 131),
    );

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "{{images}}";
    }

    public function relations()
    {
        return array(
            "file_data" => array(self::BELONGS_TO, "Files", "file",
                "joinType" => 'inner join',
            ),
            "size1_data" => array(self::BELONGS_TO, "Files", "size1",
                "joinType" => 'left join',
            ),
            "size2_data" => array(self::BELONGS_TO, "Files", "size2",
                "joinType" => 'left join',
            ),
            "size3_data" => array(self::BELONGS_TO, "Files", "size3",
                "joinType" => 'left join',
            ),
            "size4_data" => array(self::BELONGS_TO, "Files", "size4",
                "joinType" => 'left join',
            ),
            "size5_data" => array(self::BELONGS_TO, "Files", "size5",
                "joinType" => 'left join',
            ),
            "size6_data" => array(self::BELONGS_TO, "Files", "size6",
                "joinType" => 'left join',
            ),
            "size7_data" => array(self::BELONGS_TO, "Files", "size7",
                "joinType" => 'left join',
            ),
            "size8_data" => array(self::BELONGS_TO, "Files", "size8",
                "joinType" => 'left join',
            ),
            "size9_data" => array(self::BELONGS_TO, "Files", "size9",
                "joinType" => 'left join',
            ),
            "size10_data" => array(self::BELONGS_TO, "Files", "size10",
                "joinType" => 'left join',
            ),
        );
    }

    public function attributeLabels()
    {
        return array(
            "file" => Yii::t("model", "Файл"),
            "size" => Yii::t("model", "Типовой размер"),
            "width" => Yii::t("model", "Ширина"),
            "height" => Yii::t("model", "Высота"),
        );
    }

    public function getProductAllGalleryCount($product) {
        return $this->getProductGalleryCount($product) + $this->getProductGalleryArticlesCount($product);
    }

    public function getProductGalleryCount($product)
    {
        $query="select 
            count(f.id) as count
            from ai_goods_images gi 
            inner join {{goods}} g on g.id = gi.goods
            inner join {{brands}} b on b.id = g.brand
            inner join {{images}} i on i.id = gi.image
            inner join {{files}} f on i.size6 = f.id
            where 
                gi.goods = :product 
                and i.width > 299
                and i.height > 299
                and i.size6 > 0 
                and gi.disabled = 0";
        if (!$count = Yii::app()->cache->get("getProductGalleryCount_{$product}")) {
            $count = $this->getDbConnection()->createCommand($query)->queryScalar([
                'product'=>$product,
            ]);
            Yii::app()->cache->set("getProductGalleryCount_{$product}", $count);
        }
        return $count;
    }
    
    public function getProductGalleryArticlesCount($product)
    {
        $query="select 
            count(a.id) as count
        from {{goods_articles}} ga 
        inner join {{goods}} g on g.id = ga.goods
        inner join {{brands}} b on b.id = g.brand
        inner join {{articles_images}} ai on ai.article = ga.article
        inner join {{articles}} a on ai.article = a.id
        where 
            ga.goods = :product
            
            and ai.has_preview = 1
            and ga.disabled = 0
            and (
                ai.alt like concat('%', REPLACE(g.name, ' ', '_'), '%')
                or ai.alt like concat('%', REPLACE(b.name, ' ', '_'), '%')
            )
            and ai.width > 299
            and ai.height > 299";
        if (!$count = Yii::app()->cache->get("getProductGalleryArticlesCount_{$product}")) {
            $count = $this->getDbConnection()->createCommand($query)->queryScalar([
                'product'=>$product,
            ]);
            Yii::app()->cache->set("getProductGalleryArticlesCount_{$product}", $count);
        }
        return $count;
        //and a.has_filtered = 1
    }
    
    public function getProductGallery($product, $type=null, $id=null)
    {
        $result = [];
        $currentImage = (object)[];
        $countProduct = $this->getProductGalleryCount($product);
        $countArticles = $this->getProductGalleryArticlesCount($product);

        $queryProduct = "select 
            f.id as image_preview_id,
            i.file as image_id,
            f.name as image_name,
            b.link as brand_link,
            g.link as product_link,
            replace(concat(b.name, ' ', g.name), '_', '-') as image_alt
            from {{goods_images}} gi 
            inner join {{goods}} g on g.id = gi.goods
            inner join {{brands}} b on b.id = g.brand
            inner join {{images}} i on i.id = gi.image
            inner join {{files}} f on i.size6 = f.id
            where 
                gi.goods = :product 
                and i.width > 299
                and i.height > 299
                and i.size6 > 0 
                and gi.disabled = 0
        order by gi.priority desc, i.id asc";
        
        
        $queryArticles="select 
            b.link as brand_link,
            g.link as product_link,
            a.id as article_id,
            a.link as article_link,
            a.type as article_type,
            a.title as article_title,
            a.description as article_description,
            a.lang as article_lang,
            ai.id as image_id,
            replace(ai.alt, '_', '-') as image_alt,
            ai.width as image_width,
            ai.height as image_height,
            ai.mime_type as image_mime_type,
            ai.name as image_name
        from {{goods_articles}} ga 
        inner join {{goods}} g on g.id = ga.goods
        inner join {{brands}} b on b.id = g.brand
        inner join {{articles_images}} ai on ai.article = ga.article
        inner join {{articles}} a on ai.article = a.id
        where 
            ga.goods = :product
            
            and ai.has_preview = 1
            and ga.disabled = 0
            and (
                ai.alt like concat('%', REPLACE(g.name, ' ', '_'), '%')
                or ai.alt like concat('%', REPLACE(b.name, ' ', '_'), '%')
            )
            and ai.width > 299
            and ai.height > 299
        order by a.created asc, ai.id asc ";
        //and a.has_filtered = 1
        $prev_url = null;
        $next_url = null;
        $key = 0;
        $assocImages = [];
        if ($countProduct > 0) {
            
            $images = $this->getDbConnection()->createCommand($queryProduct)->queryAll(true, [
                'product'=>$product,
            ]);

            foreach($images as $key=>$image) {
                $image = (object)$image;
                
                $link = Yii::app()->createAbsoluteUrl("gallery/product", [
                    'language'=>Language::getCurrentZone(),
                    'brand'=>$image->brand_link,
                    'product'=>$image->product_link,
                    'prefix'=>'p',
                    'alt'=>Yii::app()->urlManager->translitUrl($image->image_alt),
                    'id'=>$image->image_id,
                ]);
                
                $result[$key] = (object)[
                    'link'=>$link,
                    'prev_url'=>!empty($prev_url) ? $prev_url : null,
                    'next_url'=>null,
                    'alt'=>$image->image_alt,
                    'preview_src' => Yii::app()->createAbsoluteUrl("files/image", [
                        'language' => Language::getCurrentZone(),
                        'id'=>$image->image_preview_id,
                        'name'=>$image->image_name,
                    ]),
                    'src' => Yii::app()->createAbsoluteUrl("files/image", [
                        'language' => Language::getCurrentZone(),
                        'id'=>$image->image_id,
                        'name'=>$image->image_name,
                    ]),
                ];
                
                if ($key > 0) {
                    $result[$key-1]->next_url = $link;
                }
                $assocImages['p'][$image->image_id] = $key;
                $prev_url = $link;
            }
        }
        
        if ($countArticles > 0) {
        
            
            $images = $this->getDbConnection()->createCommand($queryArticles)->queryAll(true, [
                'product'=>$product,
            ]);

            $articleTypes = [
                'news'=>'новости',
                'opinion'=>'отзыва',
                'review'=>'обзора',
                'howto'=>'инструкции',
            ];

            foreach($images as $key=>$image) {
                $key = $key+$countProduct;
                $image = (object)$image;
                $link = Yii::app()->createAbsoluteUrl("gallery/product", [
                    'language'=>Language::getCurrentZone(),
                    'brand'=>$image->brand_link,
                    'product'=>$image->product_link,
                    'prefix'=>'a',
                    'alt'=>  mb_substr(Yii::app()->urlManager->translitUrl($image->image_alt), 0, 100),
                    'id'=>$image->image_id,
                ]);
                $result[$key] = (object)[
                    'link'=>$link,
                    'prev_url'=>!empty($prev_url) ? $prev_url : null,
                    'next_url'=>null,
                    'alt'=>$image->image_alt,
                    'src' => Yii::app()->createAbsoluteUrl("files/newsimage", [
                        'language' => Language::getZoneForLang($image->article_lang),
                        'id'=>$image->image_id,
                        'name'=>$image->image_name,
                    ]),
                    'preview_src' => Yii::app()->createAbsoluteUrl("files/newsimagepreview", [
                        'language' => Language::getZoneForLang($image->article_lang),
                        'id'=>$image->image_id,
                        'name'=>$image->image_name,
                    ]),
                    'article'=>(object)[
                        'url'=>Yii::app()->createAbsoluteUrl("articles/index", [
                            'type'=>$image->article_type,
                            'link'=>$image->article_link, 
                            'id'=>$image->article_id, 
                            'language'=>  Language::getZoneForLang($image->article_lang),
                        ]),
                        'lang'=>$image->article_lang,
                        'type'=>$image->article_type,
                        'type_name'=>isset($articleTypes[$image->article_type]) ? $articleTypes[$image->article_type] : '',
                        'description'=>$image->article_description,
                        'title'=>$image->article_title,
                    ]
                ];
                
                if ($key > (0)) {
                    $result[$key-1]->next_url = $link;
                }
                
                $prev_url = $link;
                $assocImages['a'][$image->image_id] = $key;
            }
        }

        if ($type != null && $id != null) {
            $currentImage = isset($assocImages[$type][$id]) ? $assocImages[$type][$id] : null;
            if (!isset($result[$currentImage])) {
                return [$countArticles+$countProduct, $result, null];
            }
            $currentImage = $result[$currentImage];
        } else {
            $currentImage = reset($result);
        }

        return [$countArticles+$countProduct, $result, $currentImage];
    }
}
