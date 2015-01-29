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

    public function getProductGalleryCount1($product)
    {
        $queryCount="select 
            count(f.id) as count
        from ai_goods_images gi 
        inner join ai_images i on i.id = gi.image
        inner join ai_files f on i.size6 = f.id
        where gi.goods = :product and i.size6 > 0 and gi.disabled = 0";
        return $this->getDbConnection()->createCommand($queryCount)->queryScalar([
            'product'=>$product,
        ]);
    }
    
    public function getProductGalleryCount($product)
    {
        $queryCount="select 
            count(a.id) as count
        from ai_goods_articles ga 
        inner join ai_articles_images ai on ai.article = ga.article
        inner join ai_articles a on ai.article = a.id
        where 
            ga.goods = :product
            and a.has_filtered = 1
            
            and ai.has_preview = 1
            and ga.disabled = 0
            and ai.width > 299
            and ai.height > 299";
        return $this->getDbConnection()->createCommand($queryCount)->queryScalar([
            'product'=>$product,
        ]);
    }
    
    public function getProductGallery($product, $page=0)
    {
        $result = [];
        $page = abs(intval($page));

        $query1 = "select 
            f.id as image_preview_id,
            i.file as image_id,
            f.name as image_name,
            f.mime_type as image_mime_type
        from ai_goods_images gi 
        inner join ai_images i on i.id = gi.image
        inner join ai_files f on i.size6 = f.id
        where gi.goods = :product and i.size6 > 0 and gi.disabled = 0
        order by gi.priority desc, i.id asc";
        
        $query="select 
            a.id as article_id,
            a.link as article_link,
            a.type as article_type,
            a.title as article_title,
            a.description as article_description,
            a.lang as article_lang,
            ai.id as image_id,
            ai.alt as image_alt,
            ai.width as image_width,
            ai.height as image_height,
            ai.mime_type as image_mime_type,
            ai.name as image_name
        from ai_goods_articles ga 
        inner join ai_articles_images ai on ai.article = ga.article
        inner join ai_articles a on ai.article = a.id
        where 
            ga.goods = :product
            and a.has_filtered = 1
            and ai.has_preview = 1
            and ga.disabled = 0
            and ai.width > 299
            and ai.height > 299
        order by a.created asc, ai.id asc ";
        
        $countImages1 = $this->getProductGalleryCount1($product);

        $countImages = $this->getProductGalleryCount($product);

        if ($countImages1 > 0) {
            $images = $this->getDbConnection()->createCommand($query1)->queryAll(true, [
                'product'=>$product,
                //'lang'=>Yii::app()->language,
            ]);

            foreach($images as $key=>$image) {
                //$key = ($page+2 > $limit) ? ($page-2)+$key : $key;
                $image = (object)$image;
                $result[$key] = (object)[
                    'page'=>$key,
                    'active'=>($key == $page) ? true : false,
                    'src' => Yii::app()->createAbsoluteUrl("files/image", [
                        'language' => Language::getCurrentZone(),
                        'id'=>$image->image_id,
                        'name'=>$image->image_name,
                    ]),
                    'preview_src' => Yii::app()->createAbsoluteUrl("files/image", [
                        'language' => Language::getCurrentZone(),
                        'id'=>$image->image_preview_id,
                        'name'=>$image->image_name,
                    ]),
                    'alt'=>'',
                ];
            }
        }
        $images = $this->getDbConnection()->createCommand($query)->queryAll(true, [
            'product'=>$product,
            //'lang'=>Yii::app()->language,
        ]);
        
        $articleTypes = [
            'news'=>'новости',
            'opinion'=>'отзыва',
            'review'=>'обзора'
        ];
        
        foreach($images as $key=>$image) {
            $key = $key+$countImages1-1;
            //$key = ($page+2 > $limit) ? ($page-2)+$key : $key;
            $image = (object)$image;
            $result[$key] = (object)[
                'page'=>$key,
                'active'=>($key == $page) ? true : false,
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
                'alt'=>$image->image_alt,
                'article'=>(object)[
                    'url'=>Yii::app()->createAbsoluteUrl("articles/index", [
                        'type'=>$image->article_type,
                        'link'=>$image->article_link, 
                        'id'=>$image->article_id, 
                        'language'=>  Language::getZoneForLang($image->article_lang),
                    ]),
                    'type'=>$image->article_type,
                    'type_name'=>isset($articleTypes[$image->article_type]) ? $articleTypes[$image->article_type] : '',
                    'description'=>$image->article_description,
                    'title'=>$image->article_title,
                ]
            ];
        }
        
        return [$countImages+$countImages1, $result];
    }
}
